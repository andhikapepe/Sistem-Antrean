<?php

namespace App\Livewire\Admin;

use App\Models\QueueCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class QueueCategoryManager extends Component
{
    use WithPagination;

    public $name, $prefix, $color = 'zinc', $sort_order, $categoryId;
    public $search = '';

    public function mount()
    {
        // Casting ke (int) untuk mencegah error "Unsupported operand types"
        $this->sort_order = ((int) QueueCategory::max('sort_order') ?? 0) + 1;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:3',
            'prefix' => 'required|max:2',
            'sort_order' => 'required|integer',
        ]);

        QueueCategory::updateOrCreate(['id' => $this->categoryId], [
            'name' => $this->name,
            'prefix' => strtoupper($this->prefix),
            'color' => $this->color,
            'sort_order' => $this->sort_order,
        ]);

        Toaster::success($this->categoryId ? 'Kategori berhasil diperbarui' : 'Kategori berhasil ditambahkan');
        $this->cancelEdit();
    }

    public function edit($id)
    {
        $cat = QueueCategory::findOrFail($id);
        $this->categoryId = $cat->id;
        $this->name = $cat->name;
        $this->prefix = $cat->prefix;
        $this->color = $cat->color;
        $this->sort_order = $cat->sort_order;
    }

    public function cancelEdit()
    {
        $this->reset(['name', 'prefix', 'categoryId', 'color']);
        $this->color = 'zinc';
        $this->sort_order = ((int) QueueCategory::max('sort_order') ?? 0) + 1;
        $this->resetErrorBag();
    }

    public function delete($id)
    {
        QueueCategory::find($id)->delete();
        Toaster::success('Kategori berhasil dihapus');
    }

    public function render()
    {
        return view('livewire.admin.queue-category-manager', [
            'categories' => QueueCategory::where('name', 'like', "%{$this->search}%")
                ->orWhere('prefix', 'like', "%{$this->search}%")
                ->orderBy('sort_order', 'asc')
                ->paginate(10)
        ])->title('Kategori Antrean');
    }
}
