<?php

namespace App\Livewire\Customer;

use App\Models\Queue;
use App\Models\QueueCategory;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;

class Kiosk extends Component
{
    /**
     * Proses pengambilan nomor antrean
     */
    public function takeQueue($categoryId)
    {
        try {
            $queue = DB::transaction(function () use ($categoryId) {
                $category = QueueCategory::findOrFail($categoryId);

                // 1. Ambil nomor urut terakhir hari ini (Lock untuk anti-duplikat)
                $todayCount = Queue::where('queue_category_id', $categoryId)
                    ->whereDate('created_at', now()->today())
                    ->lockForUpdate()
                    ->count();

                $nextNumber = $todayCount + 1;
                $ticketNumber = $category->prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

                // 2. Simpan ke database
                return Queue::create([
                    'ticket_number' => $ticketNumber,
                    'queue_category_id' => $categoryId,
                    'status' => 'waiting',
                ]);
            });

            // 3. Trigger Event untuk Frontend (Cetak & Suara/Notif)
            $this->dispatch('ticket-created', [
                'ticket' => $queue->ticket_number,
                'category' => $queue->category->name,
                'date' => $queue->created_at->format('d/m/Y H:i')
            ]);

            Toaster::success("Nomor antrean " . $queue->ticket_number . " berhasil diambil.");

        } catch (\Exception $e) {
            Toaster::error("Terjadi kesalahan. Silakan coba lagi.");
        }
    }

    public function render()
    {
        return view('livewire.customer.kiosk', [
            'categories' => QueueCategory::where('is_active', true)
                ->orderBy('sort_order', 'asc')
                ->get()
        ])->layout('layouts.guest');
    }
}
