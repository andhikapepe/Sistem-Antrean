<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Masmerise\Toaster\Toaster;

class RoleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    public $roleId = null;
    public $name = '';
    public $guard_name = 'web';

    public $selectedRoles = [];
    public $selectAll = false;

    public function updatingSearch() { $this->resetPage(); }

    public function sort($column)
    {
        $this->sortDirection = ($this->sortBy === $column && $this->sortDirection === 'asc') ? 'desc' : 'asc';
        $this->sortBy = $column;
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'min:3', Rule::unique('roles', 'name')->ignore($this->roleId)],
        ]);

        Role::updateOrCreate(
            ['id' => $this->roleId],
            ['name' => strtolower($this->name), 'guard_name' => $this->guard_name]
        );

        Toaster::success($this->roleId ? 'Role diperbarui!' : 'Role berhasil dibuat!');
        $this->cancelEdit();
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
    }

    public function cancelEdit()
    {
        $this->reset(['roleId', 'name']);
        $this->resetErrorBag();
    }

    public function bulkDelete()
    {
        $validIds = Role::whereIn('id', $this->selectedRoles)
            ->where('name', '!=', 'admin')
            ->pluck('id')
            ->toArray();

        if (empty($validIds)) {
            Toaster::error('Tidak ada role valid yang dapat dihapus.');
            return;
        }

        Role::whereIn('id', $validIds)->delete();
        $this->selectedRoles = [];
        $this->selectAll = false;
        Toaster::success(count($validIds) . ' Role berhasil dihapus.');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedRoles = Role::where('name', 'like', "%{$this->search}%")
                ->where('name', '!=', 'admin')
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
        } else {
            $this->selectedRoles = [];
        }
    }

    public function render()
    {
        $roles = Role::query()
            ->withCount('permissions')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.role-manager', [
            'roles' => $roles
        ])->title('Manajemen Role');
    }
}
