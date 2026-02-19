<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Manajemen Unit Layanan</flux:heading>
            <flux:subheading size="lg">Kelola titik layanan antrean (Loket/Ruangan).</flux:subheading>
        </div>

        @if (count($selectedUnits) > 0)
            <flux:button variant="danger" icon="trash"
                x-on:click="$dispatch('confirm-bulk-delete-unit', { count: {{ count($selectedUnits) }} })">
                Hapus ({{ count($selectedUnits) }})
            </flux:button>
        @endif
    </div>

    <flux:separator variant="subtle" />

    <div class="items-start gap-8 grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <flux:card>
                <form wire:submit="save" class="space-y-5">
                    <div>
                        <flux:heading size="lg">{{ $unitId ? 'Edit Unit' : 'Tambah Unit Baru' }}</flux:heading>
                        <flux:subheading>Input informasi detail titik layanan.</flux:subheading>
                    </div>

                    <flux:input label="Nama Unit" wire:model="name" placeholder="Misal: Loket 1" />

                    <div class="gap-4 grid grid-cols-2">
                        <flux:input type="number" label="No. Urut" wire:model="sort_order" />
                        <flux:select label="Tipe" wire:model="type">
                            <flux:select.option value="counter">Loket</flux:select.option>
                            <flux:select.option value="room">Ruangan</flux:select.option>
                        </flux:select>
                    </div>

                    <flux:input label="Lokasi" wire:model="location" placeholder="Misal: Lantai 1" />

                    <div class="flex flex-col gap-2 pt-2">
                        <flux:button type="submit" variant="primary">
                            {{ $unitId ? 'Simpan Perubahan' : 'Tambah Unit' }}
                        </flux:button>
                        @if ($unitId)
                            <flux:button wire:click="cancelEdit" variant="ghost">Batal Edit</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>

        <div class="space-y-4 lg:col-span-8">
            <div class="flex gap-2">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari unit..." />
                <flux:tooltip content="Reset Filter">
                    <flux:button icon="arrow-path" wire:click="$set('search', '')" />
                </flux:tooltip>
            </div>

            <flux:card class="p-4 overflow-hidden">
                <flux:table :paginate="$units">
                    <flux:table.columns>
                        <flux:table.column class="w-10">
                            <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                        </flux:table.column>
                        <flux:table.column>Urutan</flux:table.column>
                        <flux:table.column>Unit</flux:table.column>
                        <flux:table.column>Tipe</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($units as $unit)
                            <flux:table.row :key="$unit->id">
                                <flux:table.cell>
                                    <flux:checkbox wire:model.live="selectedUnits" value="{{ $unit->id }}" />
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex items-center gap-1">
                                        <flux:button variant="ghost" size="sm" icon="chevron-up"
                                            wire:click="moveUp({{ $unit->id }})"
                                            class="{{ $loop->first ? 'invisible' : '' }}" />

                                        <span class="w-8 font-mono text-zinc-500 text-xs text-center">
                                            {{ $unit->sort_order }}
                                        </span>

                                        <flux:button variant="ghost" size="sm" icon="chevron-down"
                                            wire:click="moveDown({{ $unit->id }})"
                                            class="{{ $loop->last ? 'invisible' : '' }}" />
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="font-bold">{{ $unit->name }}</flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" :color="$unit->type === 'room' ? 'indigo' : 'zinc'">
                                        {{ ucfirst($unit->type) }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square"
                                        wire:click="edit({{ $unit->id }})" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="5" class="py-10 text-zinc-400 text-center">
                                    Data unit tidak ditemukan.
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
        $wire.on('confirm-bulk-delete-unit', (data) => {
            Swal.fire({
                title: `Hapus ${data.count} Unit?`,
                text: "Data akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus',
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
