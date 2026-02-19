<?php

namespace App\Livewire\Customer;

use App\Models\ServiceUnit;
use Livewire\Component;

class DisplayLauncher extends Component
{
    // Pastikan defaultnya adalah array kosong
    public $selectedUnits = [];

    public function mount()
    {
        $this->selectedUnits = [];
    }

    // Method pembantu untuk handle klik agar tetap menjadi array
    public function toggleUnit($id)
    {
        $id = (string) $id; // Pastikan ID dalam bentuk string untuk konsistensi array

        if (in_array($id, $this->selectedUnits)) {
            $this->selectedUnits = array_diff($this->selectedUnits, [$id]);
        } else {
            $this->selectedUnits[] = $id;
        }

        // Re-index array agar tidak ada key yang bolong
        $this->selectedUnits = array_values($this->selectedUnits);
    }

    public function launch()
    {
        if (empty($this->selectedUnits)) return;

        $ids = implode(',', $this->selectedUnits);
        return redirect()->route('customer.display', ['ids' => $ids]);
    }

    public function render()
    {
        return view('livewire.customer.display-launcher', [
            'units' => ServiceUnit::where('is_active', true)->get()
        ])->layout('layouts.guest');
    }
}
