<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ServiceUnit extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'location',
        'sort_order',
        'is_active',
        'status',
        'is_occupied',
        'current_user_id'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'is_occupied' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // Otomatis membuat slug saat data baru dibuat
        static::creating(fn($unit) => $unit->slug = $unit->slug ?? Str::slug($unit->name));
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Pencarian Unit
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('location', 'like', "%{$term}%")
            ->orWhere('type', 'like', "%{$term}%");
    }

    /**
     * Relasi ke Kategori Antrean (Many-to-Many)
     * Menggunakan tabel pivot: service_unit_categories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(QueueCategory::class, 'unit_queue_category');
    }

    /**
     * Relasi ke Petugas yang sedang bertugas di unit ini
     */
    public function currentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    /**
     * Relasi ke daftar antrean yang ditangani unit ini
     */
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'service_unit_id');
    }
}
