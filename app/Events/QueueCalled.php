<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Pastikan $unit_id masuk ke sini
    public function __construct(
        public $ticket_number,
        public $unit_name,
        public $unit_id
    ) {}

    public function broadcastOn()
    {
        // Sekarang $this->unit_id sudah memiliki nilai
        return new Channel('queue-room.' . $this->unit_id);
    }

    public function broadcastAs()
    {
        return 'queue.called';
    }

    /**
     * Data yang akan dikirim ke JavaScript
     */
    public function broadcastWith()
    {
        return [
            'ticket' => $this->ticket_number,
            'unit_name' => $this->unit_name,
            'unit_id' => $this->unit_id,
            // Anda bisa pecah nomor untuk TTS yang lebih natural jika perlu
            'prefix' => substr($this->ticket_number, 0, 1),
            'number' => substr($this->ticket_number, 1),
        ];
    }
}
