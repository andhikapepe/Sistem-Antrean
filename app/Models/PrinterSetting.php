<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = ['name', 'type', 'address', 'port', 'width', 'is_active'];

    public function assignments()
    {
        return $this->hasMany(KioskAssignment::class);
    }
}
