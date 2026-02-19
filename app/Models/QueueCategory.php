<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambahkan ini

class QueueCategory extends Model
{
    protected $fillable = [
        'name',
        'prefix',
        'sort_order',
        'color',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('prefix', 'like', "%{$term}%");
    }

    /**
     * Relasi ke Unit Layanan (Many-to-Many)
     */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(ServiceUnit::class, 'unit_queue_category');
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'queue_category_id');
    }
}
