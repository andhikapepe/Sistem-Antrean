<?php
namespace App\Livewire\Admin;

use App\Models\QueueCategory;
use App\Models\ServiceUnit;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class QueueMapping extends Component
{
    // 1. Toggle Kategori Satuan
    public function toggleMapping($unitId, $categoryId)
    {
        $unit = ServiceUnit::findOrFail($unitId);
        $unit->categories()->toggle($categoryId);
        Toaster::info('Pemetaan kategori diperbarui');
    }

    // 2. Toggle Status Aktif Loket (Buka/Tutup Loket)
    public function toggleUnitActive($unitId)
    {
        $unit = ServiceUnit::findOrFail($unitId);
        $unit->is_active = !$unit->is_active;
        $unit->save();

        $status = $unit->is_active ? 'diaktifkan' : 'dinonaktifkan';
        Toaster::success("Unit {$unit->name} berhasil {$status}");
    }

    // 3. Toggle Pilih Semua Kategori
    public function toggleAll($unitId)
    {
        $unit = ServiceUnit::findOrFail($unitId);
        $allCategoryIds = QueueCategory::pluck('id')->toArray();

        if ($unit->categories()->count() === count($allCategoryIds)) {
            $unit->categories()->detach();
            Toaster::warning("Semua kategori dilepas dari {$unit->name}");
        } else {
            $unit->categories()->sync($allCategoryIds);
            Toaster::success("Semua kategori ditugaskan ke {$unit->name}");
        }
    }

    public function render()
    {
        return view('livewire.admin.queue-mapping', [
            'units' => ServiceUnit::with('categories')->orderBy('sort_order')->get(),
            'categories' => QueueCategory::orderBy('sort_order')->get(),
        ])->title('Pemetaan Antrean');
    }
}
