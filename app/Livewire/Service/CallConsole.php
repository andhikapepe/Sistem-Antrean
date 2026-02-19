<?php

namespace App\Livewire\Service;

use App\Models\Queue;
use App\Models\ServiceUnit;
use App\Events\QueueCalled;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class CallConsole extends Component
{
    public ServiceUnit $unit;
    public $status;
    public $currentQueue = null;

    public function mount(ServiceUnit $unit)
    {
        if (!$unit->is_active) {
            abort(403, 'Loket ini sedang dinonaktifkan oleh Admin.');
        }

        if ($unit->current_user_id !== auth()->id()) {
            return redirect()->route('select-unit');
        }

        $this->unit = $unit;
        $this->status = $unit->status;
        $this->loadCurrentQueue();
    }

    public function loadCurrentQueue()
    {
        $this->currentQueue = Queue::where('service_unit_id', $this->unit->id)
            ->whereIn('status', ['calling', 'serving'])
            ->first();
    }

    public function setStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->unit->update(['status' => $newStatus]);

        $msg = [
            'ready' => 'Anda kembali online.',
            'resting' => 'Status: Sedang Istirahat.',
            'away' => 'Status: Sedang Tugas Luar.',
        ];

        Toaster::info($msg[$newStatus]);
    }

    public function callNext()
    {
        if ($this->status !== 'ready') {
            Toaster::error("Ubah status ke 'Ready' untuk memanggil antrean.");
            return;
        }

        $categoryIds = $this->unit->categories->pluck('id');

        $nextQueue = Queue::whereIn('queue_category_id', $categoryIds)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$nextQueue) {
            Toaster::warning("Tidak ada antrean dalam tunggu.");
            return;
        }

        $nextQueue->update([
            'status' => 'calling',
            'service_unit_id' => $this->unit->id,
            'called_at' => now(),
        ]);

        $this->currentQueue = $nextQueue;

        // PERBAIKAN: Hapus .toOthers() dan kirim unit_id
        broadcast(new QueueCalled(
            $nextQueue->ticket_number,
            $this->unit->name,
            $this->unit->id
        ));

        Toaster::success("Memanggil nomor " . $nextQueue->ticket_number);
    }

    public function recall()
    {
        if ($this->currentQueue) {
            // PERBAIKAN: Hapus .toOthers() agar TV Monitor bisa mendengar
            broadcast(new QueueCalled(
                $this->currentQueue->ticket_number,
                $this->unit->name,
                $this->unit->id
            ));

            Toaster::info("Memanggil ulang nomor " . $this->currentQueue->ticket_number);
        }
    }

    public function complete()
    {
        if ($this->currentQueue) {
            $this->currentQueue->update(['status' => 'completed', 'completed_at' => now()]);
            $this->currentQueue = null;
            Toaster::success("Antrean selesai dilayani.");
        }
    }

    public function quit()
    {
        $this->unit->update([
            'is_occupied' => false,
            'current_user_id' => null,
            'status' => 'ready'
        ]);

        return redirect()->route('select-unit');
    }

    public function render()
    {
        return view('livewire.service.call-console');
    }
}
