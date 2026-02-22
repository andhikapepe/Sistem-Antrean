<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KioskAssignment extends Model
{
    protected $fillable = ['kiosk_name', 'client_ip', 'printer_setting_id'];

    public function printerSetting(): BelongsTo
    {
        return $this->belongsTo(PrinterSetting::class);
    }
}
