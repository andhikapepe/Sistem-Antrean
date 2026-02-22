<?php

namespace App\Livewire\Customer;

use App\Models\Queue;
use App\Models\QueueCategory;
use App\Models\PrinterSetting;
use App\Models\KioskAssignment; // Import model yang benar
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class Kiosk extends Component
{
    public $appSettings;

    public function mount()
    {
        $this->appSettings = Cache::get('app_settings', []);
    }

    public function render()
    {
        return view('livewire.customer.kiosk', [
            'categories' => QueueCategory::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get()
        ])->layout('layouts.guest');
    }

    public function takeQueue($categoryId)
    {
        try {
            $queue = DB::transaction(function () use ($categoryId) {
                $category = QueueCategory::findOrFail($categoryId);
                $todayCount = Queue::where('queue_category_id', $categoryId)
                    ->whereDate('created_at', now()->today())
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $todayCount + 1;
                $ticketNumber = $category->prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                return Queue::create([
                    'ticket_number' => $ticketNumber,
                    'queue_category_id' => $categoryId,
                    'status' => 'waiting',
                ]);
            });

            // Jalankan perintah cetak
            $this->printDirect($queue->id);

            $this->dispatch('ticket-created', [
                'ticket' => $queue->ticket_number,
                'category' => $queue->category->name
            ]);

            Toaster::success("Antrean " . $queue->ticket_number . " berhasil diambil.");
        } catch (\Exception $e) {
            Log::error("Kiosk Error: " . $e->getMessage());
            Toaster::error("Gagal mengambil antrean.");
        }
    }

    /**
     * Test Print Manual dari layar Kiosk
     */
    public function testPrintDirect()
    {
        $clientIp = request()->ip();

        if ($clientIp === '127.0.0.1' || $clientIp === '::1') {
            $hostname = getenv('COMPUTERNAME') ?: 'localhost';
        } else {
            $hostname = gethostbyaddr($clientIp);
        }

        // Log inisiasi test print (Opsional, untuk tracking siapa yang menekan tombol)
        Log::channel('single')->info("Inisiasi Test Print dari Kiosk: [IP: $clientIp][Host: $hostname]");

        try {
            $assignment = KioskAssignment::with('printerSetting')
                ->where(function ($query) use ($clientIp, $hostname) {
                    $query->where('client_ip', $clientIp)
                        ->orWhere('client_ip', $hostname);
                })
                ->first();

            if (!$assignment || !$assignment->printerSetting) {
                $info = ($clientIp === $hostname) ? "IP: $clientIp" : "IP: $clientIp / Host: $hostname";

                // Log kegagalan otorisasi perangkat
                Log::warning("Test Print Ditolak: Perangkat tidak terdaftar. Detail: $info");

                Toaster::error("Akses Ditolak: Perangkat ini ($info) belum terdaftar.");
                return;
            }

            $setting = $assignment->printerSetting;

            $this->executeTechnicalPrint($setting, [
                'header'   => 'TEST KONEKSI KIOSK',
                'ticket'   => 'BERHASIL',
                'category' => $assignment->kiosk_name,
                'date'     => now()->format('d/m/Y H:i:s')
            ]);

            $setting->update(['last_status' => 'online']);

            // Log Berhasil
            Log::info("Test Print Berhasil: Printer {$setting->name} ({$setting->address}) oleh Kiosk {$assignment->kiosk_name}");

            Toaster::success("Berhasil! Test print dikirim ke printer: {$setting->name}");
        } catch (\Exception $e) {
            if (isset($setting)) {
                $setting->update(['last_status' => 'offline']);
            }

            // Log Error mendalam untuk debugging
            Log::error("Kiosk Test Print Error!", [
                'kiosk_ip'   => $clientIp,
                'kiosk_host' => $hostname,
                'printer'    => $setting->name ?? 'Unknown',
                'address'    => $setting->address ?? 'Unknown',
                'message'    => $e->getMessage(),
                'trace'      => $e->getTraceAsString() // Mengetahui titik error tepat di file mana
            ]);

            Toaster::error("Gagal cetak: " . $e->getMessage());
        }
    }

    protected function executeTechnicalPrint($setting, $data)
    {
        // Inisialisasi konektor ke printer (menggunakan Nama Printer di Windows)
        if ($setting->type === 'network') {
            // Tambahkan timeout agar tidak membuat halaman 'freeze' jika IP salah
            $connector = new NetworkPrintConnector($setting->address, $setting->port ?? 9100, 3);
        } else {
            // Untuk Windows, pastikan alamat sesuai: Contoh: \\NAMA-PC\PrinterShare atau langsung NamaPrinter
            $connector = new WindowsPrintConnector($setting->address);
        }

        $printer = new Printer($connector);

        $clientIp = request()->ip();
        if ($clientIp === '127.0.0.1' || $clientIp === '::1') {
            $hostname = getenv('COMPUTERNAME') ?: 'localhost';
        } else {
            $hostname = gethostbyaddr($clientIp);
        }

        try {
            /* HEADER */
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            if (!empty($this->appSettings['app_logo'])) {
                try {
                    // Asumsi logo disimpan di folder 'storage'
                    // Kita ambil path absolutnya
                    $logoPath = public_path('storage/' . $this->appSettings['app_logo']);

                    if (file_exists($logoPath)) {
                        $logo = EscposImage::load($logoPath, false);

                        // Gunakan bitImage untuk printer thermal standar
                        $printer->bitImage($logo);
                        $printer->feed();
                    }
                } catch (\Exception $e) {
                    // Log jika gambar korup atau format tidak didukung
                    Log::error("Printer Logo Error: " . $e->getMessage());
                }
            }

            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TEST PRINTER OK\n");
            $printer->selectPrintMode(); // Kembali ke mode normal
            $printer->text("--------------------------------\n");

            /* DETAIL PERANGKAT */
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Printer  : " . $setting->name . "\n");
            $printer->text("Alamat   : " . $setting->address . "\n");
            $printer->text("IP/Host  : " . $clientIp . " / " . $hostname . "\n");
            $printer->text("Lokasi   : " . $data['category'] . "\n");
            $printer->text("Tanggal  : " . $data['date'] . "\n");

            /* FOOTER */
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("--------------------------------\n");
            $printer->text($this->appSettings['app_name'] ?? config('app.name', 'Laravel Starter Kit') . "\n");

            $printer->feed(3);
            $printer->cut();
        } catch (\Exception $e) {
            // Melempar error agar ditangkap catch utama dan status jadi 'offline'
            throw new \Exception("Gagal Cetak: " . $e->getMessage());
        } finally {
            $printer->close();
        }
    }

    /**
     * Inti Logika Penentuan Printer
     */
    protected function printDirect($queueId = null)
    {
        // 1. Identifikasi IP & Hostname (Logika Anti-Corel)
        $clientIp = request()->ip();
        if ($clientIp === '127.0.0.1' || $clientIp === '::1') {
            $hostname = getenv('COMPUTERNAME') ?: 'localhost';
        } else {
            $hostname = gethostbyaddr($clientIp);
        }

        // 2. Cari Mapping di KioskAssignment (Bisa cocok dengan IP atau Hostname)
        $assignment = KioskAssignment::with('printerSetting')
            ->where(function ($query) use ($clientIp, $hostname) {
                $query->where('client_ip', $clientIp)
                    ->orWhere('client_ip', $hostname);
            })
            ->first();

        // 3. Tentukan Printer
        if ($assignment && $assignment->printerSetting) {
            $setting = $assignment->printerSetting;
            $locationName = $assignment->kiosk_name;
        } else {
            $setting = PrinterSetting::where('is_active', true)->first();
            $locationName = 'DEFAULT / UNKNOWN';
        }

        if (!$setting) {
            Log::warning("Cetak Gagal: Tidak ada printer aktif untuk [IP: $clientIp][Host: $hostname]");
            return;
        }

        // 4. Siapkan Data
        if ($queueId) {
            $queue = Queue::with('category')->find($queueId);
            $printData = [
                'ticket'   => $queue->ticket_number,
                'category' => $queue->category->name,
                'date'     => $queue->created_at->format('d/m/Y H:i:s'),
                'location' => $locationName,
                'client'   => $clientIp . " / " . $hostname
            ];
        }

        // 5. Eksekusi
        try {
            $this->executePrintProcess($setting, $printData);
            $setting->update(['last_status' => 'online']);
        } catch (\Exception $e) {
            $setting->update(['last_status' => 'offline']);
            Log::error("Print Process Error: " . $e->getMessage());
            // Jangan lupa lempar exception agar Toaster di takeQueue bisa menangkapnya
            throw $e;
        }
    }

    private function executePrintProcess($setting, $data)
    {
        // Penentuan Konektor
        if ($setting->type == 'network') {
            $connector = new NetworkPrintConnector($setting->address, $setting->port ?? 9100, 3);
        } elseif ($setting->type == 'linux') {
            $connector = new FilePrintConnector($setting->address);
        } else {
            // Windows: Memastikan nama share printer benar
            $connector = new WindowsPrintConnector($setting->address);
        }

        $printer = new Printer($connector);

        try {
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            if (!empty($this->appSettings['app_logo'])) {
                try {
                    // Asumsi logo disimpan di folder 'storage'
                    // Kita ambil path absolutnya
                    $logoPath = public_path('storage/' . $this->appSettings['app_logo']);

                    if (file_exists($logoPath)) {
                        $logo = EscposImage::load($logoPath, false);

                        // Gunakan bitImage untuk printer thermal standar
                        $printer->bitImage($logo);
                        $printer->feed();
                    }
                } catch (\Exception $e) {
                    // Log jika gambar korup atau format tidak didukung
                    Log::error("Printer Logo Error: " . $e->getMessage());
                }
            }

            // 1. HEADER (Dibuat Bold saja, tidak Double Width agar tidak terlalu besar)
            $printer->setEmphasis(true);
            $appName = $this->appSettings['app_name'] ?? config('app.name', 'ANTREAN');
            $printer->text($appName . "\n");
            $printer->setEmphasis(false);

            // Garis Pemisah Header
            $printer->text(str_repeat("-", $setting->width ?? 32) . "\n");

            // 2. NOMOR TIKET (Dibuat Maksimal)
            // Menggunakan setTextSize(8, 8) adalah ukuran terbesar pada standar ESC/POS
            $printer->feed();
            $printer->setTextSize(8, 8);
            $printer->setEmphasis(true);
            $printer->text($data['ticket'] . "\n");
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1); // Reset ke ukuran normal
            $printer->feed();

            // 3. KATEGORI DENGAN BORDER OUTLINE
            $printer->feed();
            $label = " " . $data['category'] . " ";
            $len = strlen($label);
            $printer->text("+" . str_repeat("-", $len) . "+\n");
            $printer->text("|" . $label . "|\n");
            $printer->text("+" . str_repeat("-", $len) . "+\n");

            // 4. METADATA (Font Normal)
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Waktu      : " . $data['date'] . "\n");
            $printer->text("Cetak dari : " . $data['location'] . "\n");
            //$printer->text("Terminal   : " . $data['client'] . "\n");

            // 5. FOOTER (Menggunakan Font B agar lebih kecil dan ramping)
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text(str_repeat("-", $setting->width ?? 32) . "\n");

            // Mengaktifkan FONT_B (Font kecil)
            $printer->setFont(Printer::FONT_B);
            $printer->text("Silahkan tunggu nomor Anda dipanggil\n");
            $printer->text("Terima Kasih Atas Kunjungan Anda\n");

            // Mengembalikan ke FONT_A (Font standar) agar tidak merusak cetakan berikutnya
            $printer->setFont(Printer::FONT_A);

            $printer->feed(3);
            $printer->cut();
        } finally {
            $printer->close();
        }
    }
}
