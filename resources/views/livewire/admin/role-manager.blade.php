<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Manajemen Role</flux:heading>
            <flux:subheading size="lg">Kelola peran dan hak akses sistem secara terpusat.</flux:subheading>
        </div>

        @if(count($selectedRoles) > 0)
            <flux:button variant="danger" icon="trash"
                x-on:click="$dispatch('confirm-bulk-delete-role', { count: {{ count($selectedRoles) }} })">
                Hapus ({{ count($selectedRoles) }})
            </flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" />

    <div class="items-start gap-8 grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <flux:card>
                <form wire:submit="save" class="space-y-5">
                    <div>
                        <flux:heading size="lg">{{ $roleId ? 'Edit Role' : 'Tambah Role Baru' }}</flux:heading>
                        <flux:subheading>Nama role harus unik (misal: editor, manager).</flux:subheading>
                    </div>

                    <flux:input label="Nama Role" wire:model="name" placeholder="Contoh: editor, manager..." />

                    <div class="flex flex-col gap-2 pt-2">
                        <flux:button type="submit" variant="primary">
                            {{ $roleId ? 'Simpan Perubahan' : 'Tambah Role' }}
                        </flux:button>

                        @if($roleId)
                            <flux:button wire:click="cancelEdit" variant="ghost">Batal Edit</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>

        <div class="space-y-4 lg:col-span-8">
            <div class="flex gap-2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama role..." class="flex-1" />

                <flux:tooltip content="Reset Filter">
                    <flux:button icon="arrow-path" wire:click="$set('search', '')" />
                </flux:tooltip>
            </div>

            <flux:card class="p-4 overflow-hidden">
                <flux:table :paginate="$roles">
                    <flux:table.columns>
                        <flux:table.column class="w-10">
                            <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                        </flux:table.column>

                        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">
                            Nama Role
                        </flux:table.column>

                        <flux:table.column>Guard</flux:table.column>

                        <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">
                            Dibuat Pada
                        </flux:table.column>

                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($roles as $role)
                            <flux:table.row :key="$role->id">
                                <flux:table.cell>
                                    @if($role->name !== 'admin')
                                        <flux:checkbox wire:model.live="selectedRoles" value="{{ $role->id }}" />
                                    @else
                                        <flux:icon.lock-closed variant="micro" class="ml-1 text-zinc-400" />
                                    @endif
                                </flux:table.cell>

                                <flux:table.cell class="font-bold text-zinc-800 dark:text-white">
                                    {{ ucfirst($role->name) }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc" inset>{{ $role->guard_name }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="text-zinc-500 text-sm">
                                    {{ $role->created_at->format('d/M/y') }}
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $role->id }})" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="py-12 text-zinc-400 text-center">
                                    Data role tidak ditemukan.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>
</div>

@script
<script>
    $wire.on('confirm-bulk-delete-role', (data) => {
        Swal.fire({
            title: `Hapus ${data.count} Role?`,
            text: "Role yang terhapus akan menghilangkan akses bagi pengguna terkait!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus Semua',
            cancelButtonText: 'Batal',
            background: document.documentElement.classList.contains('dark') ? '#18181b' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.bulkDelete();
            }
        })
    });
</script>
@endscript
