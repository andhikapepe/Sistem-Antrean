<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Masmerise\Toaster\Toaster;

class AccountManager extends Component
{
    use WithPagination;

    // Filter & Sorting
    public $search = '';
    public $role = '';
    public $sortBy = 'id';
    public $sortDirection = 'desc';

    // Form Properties
    public $userId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRole = '';

    // Bulk Action Properties
    public $selectedUsers = [];
    public $selectAll = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingRole() { $this->resetPage(); }

    public function sort($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function users()
    {
        return User::query()
            ->with('roles')
            ->when($this->search, function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->role, fn($q) => $q->role($this->role))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->modal('user-modal')->show();
    }

    public function edit($id)
    {
        $this->resetInputFields();
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRole = $user->getRoleNames()->first() ?? '';

        $this->modal('user-modal')->show();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)],
            'selectedRole' => 'required',
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
        ]);

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password
                    ? Hash::make($this->password)
                    : ($this->userId ? User::find($this->userId)->password : Hash::make('password123')),
            ]
        );

        $user->syncRoles($this->selectedRole);

        $this->modal('user-modal')->close();
        Toaster::success($this->userId ? 'User diperbarui' : 'User ditambahkan');
        $this->resetInputFields();
    }

    /**
     * Bulk Delete sebagai satu-satunya cara menghapus
     */
    public function bulkDelete($userIds)
    {
        // Proteksi: Filter agar ID yang sedang login tidak ikut terhapus
        $userIds = array_filter($userIds, fn($id) => (int)$id !== auth()->id());

        if (empty($userIds)) {
            Toaster::error('Tidak ada user valid yang dipilih.');
            return;
        }

        User::whereIn('id', $userIds)->delete();

        $this->selectedUsers = [];
        $this->selectAll = false;
        Toaster::success(count($userIds) . ' pengguna berhasil dihapus.');
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            // Mapping ID ke string agar match dengan value di checkbox flux
            $this->selectedUsers = $this->users()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    private function resetInputFields()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'selectedRole']);
        $this->resetErrorBag();
    }

    public function resetFilter()
    {
        $this->reset(['search','role']);
    }

    public function render()
    {
        return view('livewire.admin.account-manager', [
            'users' => $this->users(),
            'roles' => Role::pluck('name')
        ])->title('Manajemen Pengguna');
    }
}
