<?php

namespace App\Livewire\Customer;

use App\Models\Queue;
use App\Models\ServiceUnit;
use Livewire\Component;
use Livewire\Attributes\Url;

class DisplayAntrean extends Component
{
    #[Url(as: 'ids')]
    public $roomIds = ''; // Menangkap filter id dari URL, misal: ?ids=1,2,3

    public $callingQueue;
    public $units;

    public function mount()
    {
        $this->loadData();
    }

    // Listener refresh data saat ada event broadcast dari Reverb
    // Sesuaikan channel dengan Broadcast event Anda (misal: queue-room.{id})
    public function getListeners()
    {
        $listeners = [];
        if ($this->roomIds) {
            $ids = explode(',', $this->roomIds);
            foreach ($ids as $id) {
                $listeners["echo:queue-room.{$id},.queue.called"] = 'handleQueueCalled';
            }
        }
        return $listeners;
    }

    public function handleQueueCalled($event)
    {
        $this->loadData();
        // Trigger suara di sisi JS
        $this->dispatch('add-to-speech-queue',
            prefix: $event['prefix'] ?? '',
            number: $event['number'] ?? '',
            unit_name: $event['room_name'] ?? $event['unit_name'],
            patient_name: $event['patient_name'] ?? ''
        );
    }

    public function loadData()
    {
        $query = ServiceUnit::where('is_active', true)->with('categories');

        // Filter berdasarkan launcher jika ada
        if (!empty($this->roomIds)) {
            $query->whereIn('id', explode(',', $this->roomIds));
        }

        $this->units = $query->get();

        // Ambil panggilan terakhir hanya untuk unit yang sedang dipantau
        $this->callingQueue = Queue::where('status', 'calling')
            ->whereIn('service_unit_id', $this->units->pluck('id'))
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    public function render()
    {
        return view('livewire.customer.display-antrean')->layout('layouts.guest');
    }
}
