<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Manajemen Pengguna</flux:heading>
            <flux:subheading size="lg">Kelola akun akses dan perizinan sistem secara terpusat.</flux:subheading>
        </div>

        <div class="flex gap-2">
            {{-- Tombol Bulk Delete hanya muncul jika ada yang dipilih --}}
            @if(count($selectedUsers) > 0)
                <flux:button variant="danger" icon="trash"
                    x-on:click="$dispatch('confirm-bulk-delete', { userIds: @js($selectedUsers) })">
                    Hapus ({{ count($selectedUsers) }})
                </flux:button>
            @endif

            <flux:button variant="primary" icon="plus" wire:click="create">Tambah User</flux:button>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="flex items-center gap-4">
        <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Cari nama atau email..." class="flex-1" />

        <flux:select wire:model.live="role" placeholder="Semua Role" class="w-64">
            <flux:select.option value="">Semua Role</flux:select.option>
            @foreach ($roles as $roleName)
                <flux:select.option value="{{ $roleName }}">{{ ucfirst($roleName) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:tooltip content="Reset Filter">
            <flux:button icon="arrow-path" wire:click="resetFilter" />
        </flux:tooltip>
    </div>

    <flux:table :paginate="$users">
        <flux:table.columns>
            <flux:table.column>
                <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
            </flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Nama</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Terdaftar</flux:table.column>
            <flux:table.column>Aksi</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($users as $user)
                <flux:table.row :key="$user->id">
                    <flux:table.cell>
                        {{-- Proteksi: Admin tidak bisa mencentang akunnya sendiri untuk dihapus --}}
                        @if($user->id !== auth()->id())
                            <flux:checkbox wire:model.live="selectedUsers" value="{{ $user->id }}" />
                        @else
                            <flux:icon.lock-closed variant="micro" class="ml-1 text-zinc-400" />
                        @endif
                    </flux:table.cell>

                    <flux:table.cell font="medium">{{ $user->name }}</flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($user->roles as $r)
                                <flux:badge size="sm" inset color="zinc">{{ $r->name }}</flux:badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="text-zinc-500 text-sm">
                        {{ $user->created_at->format('d/m/Y') }}
                    </flux:table.cell>

                    <flux:table.cell>
                        {{-- Hanya ada tombol Edit di sini --}}
                        <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $user->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>

    <flux:modal name="user-modal" class="space-y-6 md:w-[450px]">
        <div>
            <flux:heading size="lg">{{ $userId ? 'Edit Pengguna' : 'Tambah Pengguna' }}</flux:heading>
        </div>

        <form wire:submit="save" class="space-y-4">
            <flux:input label="Nama Lengkap" wire:model="name" />
            <flux:input label="Email" type="email" wire:model="email" />
            <flux:input label="Password" type="password" wire:model="password"
                placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter' }}" />

            <flux:select label="Role Akses" wire:model="selectedRole">
                <flux:select.option value="">Pilih Role</flux:select.option>
                @foreach ($roles as $roleName)
                    <flux:select.option value="{{ $roleName }}">{{ ucfirst($roleName) }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2 pt-2">
                <flux:modal.close><flux:button variant="ghost">Batal</flux:button></flux:modal.close>
                <flux:button type="submit" variant="primary">Simpan</flux:button>
            </div>
        </form>
    </flux:modal>
</div>

@script
<script>
    $wire.on('confirm-bulk-delete', (data) => {
        Swal.fire({
            title: `Hapus ${data.userIds.length} pengguna?`,
            text: "Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus',
            background: document.documentElement.classList.contains('dark') ? '#18181b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
        }).then((result) => {
            if (result.isConfirmed) { $wire.bulkDelete(data.userIds); }
        })
    });
</script>
@endscript
