<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Kategori Antrean</flux:heading>
            <flux:subheading size="lg">Atur kelompok layanan dan prefix nomor antrean (A, B, C).</flux:subheading>
        </div>
    </div>

    <flux:separator variant="subtle" />

    <div class="items-start gap-8 grid grid-cols-1 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <flux:card>
                <form wire:submit="save" class="space-y-5">
                    <div>
                        <flux:heading size="lg">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}
                        </flux:heading>
                        <flux:subheading>Tentukan prefix unik untuk tiap layanan.</flux:subheading>
                    </div>

                    <flux:input label="Nama Layanan" wire:model="name" placeholder="Misal: Layanan Umum" />

                    <div class="gap-4 grid grid-cols-2">
                        <flux:input label="Prefix (Kode)" wire:model="prefix" placeholder="A" maxlength="2" />
                        <flux:input type="number" label="No. Urut" wire:model="sort_order" />
                    </div>

                    <flux:select label="Warna Identitas" wire:model="color">
                        <flux:select.option value="zinc">Zinc (Abu-abu)</flux:select.option>
                        <flux:select.option value="indigo">Indigo (Ungu)</flux:select.option>
                        <flux:select.option value="cyan">Cyan (Biru Muda)</flux:select.option>
                        <flux:select.option value="emerald">Emerald (Hijau)</flux:select.option>
                        <flux:select.option value="rose">Rose (Merah)</flux:select.option>
                        <flux:select.option value="amber">Amber (Kuning)</flux:select.option>
                    </flux:select>

                    <div class="flex flex-col gap-2 pt-2">
                        <flux:button type="submit" variant="primary">
                            {{ $categoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                        </flux:button>

                        @if ($categoryId)
                            <flux:button wire:click="cancelEdit" variant="ghost">Batal Edit</flux:button>
                        @endif
                    </div>
                </form>
            </flux:card>
        </div>

        <div class="space-y-4 lg:col-span-8">
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama atau kode..." />
                <flux:tooltip content="Reset Filter">
                    <flux:button icon="arrow-path" wire:click="$set('search', '')" />
                </flux:tooltip>
            </div>

            <flux:card class="p-4 overflow-hidden">
                <flux:table :paginate="$categories">
                    <flux:table.columns>
                        <flux:table.column class="w-10">Urutan</flux:table.column>
                        <flux:table.column>Kode</flux:table.column>
                        <flux:table.column>Layanan</flux:table.column>
                        <flux:table.column align="end">Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($categories as $cat)
                            <flux:table.row :key="$cat->id">
                                <flux:table.cell>
                                    <span class="font-mono text-zinc-400 text-xs">#{{ $cat->sort_order }}</span>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge :color="$cat->color" variant="solid" size="sm" inset>
                                        {{ $cat->prefix }}</flux:badge>
                                </flux:table.cell>

                                <flux:table.cell class="font-bold text-zinc-800 dark:text-white">
                                    {{ $cat->name }}
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" icon="pencil-square"
                                            wire:click="edit({{ $cat->id }})" />
                                        <flux:button size="sm" variant="ghost" icon="trash"
                                            class="text-red-500 hover:text-red-600"
                                            wire:click="delete({{ $cat->id }})"
                                            wire:confirm="Hapus kategori ini?" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="4" class="py-12 text-zinc-400 text-center">
                                    Kategori belum tersedia.
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>
</div>
