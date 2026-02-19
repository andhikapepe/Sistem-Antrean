<?php

namespace App\Livewire\Service;

use App\Models\ServiceUnit;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class SelectUnit extends Component
{
    public function selectUnit($slug, $force = false)
    {
        $unit = ServiceUnit::where('slug', $slug)->first();

        // 1. Cek jika loket sedang dipakai orang lain
        if ($unit->is_occupied && $unit->current_user_id !== auth()->id()) {
            if (!$force) {
                // Jika tidak force, kita biarkan AlpineJS yang handle Swal di frontend
                return;
            }

            // Jika force true, kita ambil alih (logika reset sesi orang tersebut)
            $unit->update([
                'is_occupied' => false,
                'current_user_id' => null,
                'status' => 'ready'
            ]);

            Toaster::info("Sesi petugas sebelumnya telah diakhiri.");
        }

        // 2. BERSIHKAN SESI LAMA USER INI (Jika dia pindah dari loket lain)
        ServiceUnit::where('current_user_id', auth()->id())
            ->where('id', '!=', $unit->id)
            ->update([
                'is_occupied' => false,
                'current_user_id' => null,
                'status' => 'ready'
            ]);

        // 3. Kunci loket baru
        $unit->update([
            'is_occupied' => true,
            'current_user_id' => auth()->id(),
        ]);

        return redirect()->route('call-console', $unit->slug);
    }

    public function render()
    {
        return view('livewire.service.select-unit', [
            'units' => ServiceUnit::where('is_active', true)->get()
        ]);
    }
}
