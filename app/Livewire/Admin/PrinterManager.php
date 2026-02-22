<?php

namespace App\Livewire\Admin;

use App\Models\PrinterSetting;
use App\Models\KioskAssignment;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class PrinterManager extends Component
{
    use WithPagination;

    public $activeTab = 'hardware';
    public $search = '';

    public $printerId = null, $name, $type = 'windows', $address, $port = 9100, $width = 32;
    public $selectedPrinters = [], $selectAll = false;

    public $assignmentId = null, $kioskName, $clientIp, $selectedPrinterId;
    public $selectedAssignments = [], $selectAllAssignments = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        // Langsung cek status semua printer saat halaman pertama kali dibuka
        $this->refreshAllStatuses();
    }

    /**
     * LOGIKA KONFIRMASI (SWAL TRIGGER)
     */
    public function confirmBulkDelete()
    {
        if (count($this->selectedPrinters) > 0) {
            $this->dispatch('confirm-bulk-delete', ['count' => count($this->selectedPrinters), 'action' => 'bulkDelete']);
        }
    }

    public function confirmBulkDeleteAssignments()
    {
        if (count($this->selectedAssignments) > 0) {
            $this->dispatch('confirm-bulk-delete', ['count' => count($this->selectedAssignments), 'action' => 'bulkDeleteAssignments']);
        }
    }

    public function deleteAssignment($id)
    {
        $this->dispatch('confirm-bulk-delete', ['count' => 1, 'id' => $id, 'action' => 'destroyAssignment']);
    }

    /**
     * LOGIKA EKSEKUSI HAPUS
     */
    public function bulkDelete()
    {
        PrinterSetting::whereIn('id', $this->selectedPrinters)->delete();
        $this->reset(['selectedPrinters', 'selectAll']);
        Toaster::success('Printer berhasil dihapus.');
    }

    public function bulkDeleteAssignments()
    {
        KioskAssignment::whereIn('id', $this->selectedAssignments)->delete();
        $this->reset(['selectedAssignments', 'selectAllAssignments']);
        Toaster::success('Penugasan berhasil dihapus.');
    }

    public function destroyAssignment($id)
    {
        KioskAssignment::destroy($id);
        Toaster::success('Penugasan dihapus.');
    }

    /**
     * LOGIKA SELECTION
     */
    public function toggleSelectAll()
    {
        $this->selectedPrinters = $this->selectAll ? PrinterSetting::where('name', 'like', "%$this->search%")->pluck('id')->map(fn($id) => (string)$id)->toArray() : [];
    }

    public function toggleSelectAllAssignments()
    {
        $this->selectedAssignments = $this->selectAllAssignments ? KioskAssignment::where('kiosk_name', 'like', "%$this->search%")->pluck('id')->map(fn($id) => (string)$id)->toArray() : [];
    }

    /**
     * KONEKTIVITAS & PRINT
     */

    public function refreshAllStatuses()
    {
        $printers = PrinterSetting::all();

        foreach ($printers as $p) {
            $isOnline = $this->checkConnectivity($p);
            // Update status ke database
            $p->update([
                'last_status' => $isOnline ? 'online' : 'offline'
            ]);
        }
    }

    private function checkConnectivity($printer)
    {
        if ($printer->type === 'network') {
            // Cek IP Printer LAN
            $connection = @fsockopen($printer->address, $printer->port ?? 9100, $errno, $errstr, 1.5);
            if ($connection) {
                fclose($connection);
                return true;
            }
        } else {
            // Cek Printer USB/Lokal (Windows/Linux)
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $check = shell_exec("powershell -Command \"Get-Printer -Name '{$printer->address}'\" 2>&1");
                return !empty($check) && !str_contains($check, 'was not found');
            } else {
                $check = shell_exec("lpstat -p {$printer->address} 2>&1");
                return str_contains($check, 'is idle') || str_contains($check, 'printer');
            }
        }
        return false;
    }

    public function testPrint($id)
    {
        // Pastikan kita mengambil dari PrinterSetting, bukan dari Assignment ID
        $setting = \App\Models\PrinterSetting::find($id);

        if (!$setting) {
            Toaster::error("Data printer tidak ditemukan di sistem.");
            return;
        }

        try {
            // Logika Connector
            if ($setting->type === 'network') {
                // Tambahkan timeout agar tidak membuat halaman 'freeze' jika IP salah
                $connector = new NetworkPrintConnector($setting->address, $setting->port ?? 9100, 3);
            } else {
                // Untuk Windows, pastikan alamat sesuai: Contoh: \\NAMA-PC\PrinterShare atau langsung NamaPrinter
                $connector = new WindowsPrintConnector($setting->address);
            }

            $printer = new Printer($connector);

            /* ISI TEST PRINT */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------\n");
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TEST PRINTER OK\n");
            $printer->selectPrintMode();
            $printer->text($setting->name . "\n");
            $printer->text("Alamat: " . $setting->address . "\n");
            $printer->text(now()->format('d/m/Y H:i:s') . "\n");
            $printer->text("--------------------------------\n");
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            Toaster::success("Berhasil! Printer {$setting->name} merespon.");
        } catch (\Exception $e) {
            // Tangkap pesan error aslinya agar kita tahu masalahnya (Permission/IP Salah/Port Tutup)
            Toaster::error("Gagal Cetak: " . $e->getMessage());
        }
    }

    /**
     * SAVE LOGIC
     */
    public function save()
    {
        $this->validate(['name' => 'required|min:3', 'type' => 'required', 'address' => 'required', 'port' => 'required_if:type,network', 'width' => 'required']);
        PrinterSetting::updateOrCreate(['id' => $this->printerId], ['name' => $this->name, 'type' => $this->type, 'address' => $this->address, 'port' => $this->port, 'width' => $this->width]);
        Toaster::success('Printer disimpan.');
        $this->cancelEdit();
    }

    public function saveAssignment()
    {
        $this->validate(['kioskName' => 'required', 'clientIp' => 'required', 'selectedPrinterId' => 'required']);
        KioskAssignment::updateOrCreate(['id' => $this->assignmentId], ['kiosk_name' => $this->kioskName, 'client_ip' => $this->clientIp, 'printer_setting_id' => $this->selectedPrinterId]);
        Toaster::success('Penugasan disimpan.');
        $this->cancelEdit();
    }

    public function useCurrentIp()
    {
        $this->clientIp = request()->ip();
        Toaster::info("IP Terdeteksi: " . $this->clientIp);
    }

    public function edit($id)
    {
        $p = PrinterSetting::findOrFail($id);
        $this->printerId = $p->id;
        $this->name = $p->name;
        $this->type = $p->type;
        $this->address = $p->address;
        $this->port = $p->port;
        $this->width = $p->width;
    }

    public function editAssignment($id)
    {
        $as = KioskAssignment::findOrFail($id);
        $this->assignmentId = $as->id;
        $this->kioskName = $as->kiosk_name;
        $this->clientIp = $as->client_ip;
        $this->selectedPrinterId = $as->printer_setting_id;
    }

    public function cancelEdit()
    {
        $this->reset(['printerId', 'name', 'address', 'port', 'width', 'type', 'assignmentId', 'kioskName', 'clientIp', 'selectedPrinterId']);
    }

    public function render()
    {
        return view('livewire.admin.printer-manager', [
            'printers' => PrinterSetting::where('name', 'like', "%$this->search%")->orderBy('id', 'desc')->paginate(10),
            'assignments' => KioskAssignment::with('printerSetting')->where('kiosk_name', 'like', "%$this->search%")->get(),
            'allPrinters' => PrinterSetting::all()
        ])->title('Printer Manager');
    }
}
