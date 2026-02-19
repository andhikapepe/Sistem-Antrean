<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    protected $fillable = [
        'ticket_number',
        'queue_category_id',
        'service_unit_id',
        'status',
        'called_at',
        'completed_at'
    ];

    protected $casts = [
        'called_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi ke Kategori (Misal: Pendaftaran, Pembayaran)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QueueCategory::class, 'queue_category_id');
    }

    /**
     * Relasi ke Unit/Loket yang melayani
     */
    public function serviceUnit(): BelongsTo
    {
        return $this->belongsTo(ServiceUnit::class, 'service_unit_id');
    }
}
