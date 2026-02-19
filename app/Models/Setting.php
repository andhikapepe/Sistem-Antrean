<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Helper statis untuk mengambil value berdasarkan key.
     * Contoh penggunaan: Setting::get('app_name')
     */
    public static function get($key, $default = null)
    {
        // Kita gunakan cache agar tidak query database terus-menerus setiap refresh halaman
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Otomatis hapus cache saat data diupdate agar perubahan langsung terlihat
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            // Hapus cache individual berdasarkan key
            Cache::forget("setting_{$setting->key}");

            // Hapus cache global untuk AppServiceProvider
            Cache::forget('app_settings');
        });

        static::deleted(function ($setting) {
            // Tambahkan ini agar saat setting dihapus, cache juga hilang
            Cache::forget("setting_{$setting->key}");
            Cache::forget('app_settings');
        });
    }
}
