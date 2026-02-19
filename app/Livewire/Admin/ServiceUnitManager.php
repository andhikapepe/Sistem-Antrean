<?php

namespace App\Livewire\Admin;

use App\Models\ServiceUnit;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class ServiceUnitManager extends Component
{
    use WithPagination;

    public $name, $type = 'counter', $location, $sort_order = 1, $unitId;
    public $search = '';
    public $selectedUnits = [];
    public $selectAll = false;

    protected $updatesQueryString = ['search'];

    public function mount()
    {
        $this->sort_order = ((int) ServiceUnit::max('sort_order') ?? 0) + 1;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUnits = ServiceUnit::search($this->search)
                ->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUnits = [];
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
            'type' => 'required|in:room,counter',
            'sort_order' => 'required|integer',
        ]);

        ServiceUnit::updateOrCreate(['id' => $this->unitId], [
            'name' => $this->name,
            'type' => $this->type,
            'location' => $this->location,
            'sort_order' => $this->sort_order,
        ]);

        Toaster::success($this->unitId ? 'Unit diperbarui' : 'Unit berhasil ditambahkan');
        $this->cancelEdit();
    }

    public function edit($id)
    {
        $unit = ServiceUnit::findOrFail($id);
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->type = $unit->type;
        $this->location = $unit->location;
        $this->sort_order = $unit->sort_order;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'unitId', 'location']);
        $this->type = 'counter';
        $this->sort_order = ((int) ServiceUnit::max('sort_order') ?? 0) + 1;
    }

    public function bulkDelete()
    {
        ServiceUnit::whereIn('id', $this->selectedUnits)->delete();
        $this->selectedUnits = [];
        $this->selectAll = false;
        Toaster::success('Unit terpilih berhasil dihapus');
    }

    public function moveUp($id)
    {
        $unit = ServiceUnit::findOrFail($id);
        // Cari unit yang urutannya tepat di atasnya
        $previousUnit = ServiceUnit::where('sort_order', '<', $unit->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousUnit) {
            $oldOrder = $unit->sort_order;
            $unit->update(['sort_order' => $previousUnit->sort_order]);
            $previousUnit->update(['sort_order' => $oldOrder]);
            Toaster::info('Urutan dinaikkan');
        }
    }

    public function moveDown($id)
    {
        $unit = ServiceUnit::findOrFail($id);
        // Cari unit yang urutannya tepat di bawahnya
        $nextUnit = ServiceUnit::where('sort_order', '>', $unit->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextUnit) {
            $oldOrder = $unit->sort_order;
            $unit->update(['sort_order' => $nextUnit->sort_order]);
            $nextUnit->update(['sort_order' => $oldOrder]);
            Toaster::info('Urutan diturunkan');
        }
    }

    public function render()
    {
        return view('livewire.admin.service-unit-manager', [
            'units' => ServiceUnit::search($this->search)
                ->orderBy('sort_order', 'asc')
                ->paginate(10)
        ])->title('Manajemen Unit');
    }
}
