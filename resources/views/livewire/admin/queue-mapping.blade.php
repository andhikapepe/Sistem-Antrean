<div class="space-y-6">
    {{-- HEADER --}}
    <header class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Pemetaan & Status Unit</flux:heading>
            <flux:subheading>Atur operasional unit dan kategori layanan dalam satu panel.</flux:subheading>
        </div>
    </header>

    <flux:separator variant="subtle" />

    {{-- GRID UNIT --}}
    <div class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
        @forelse($units as $unit)
            @php
                $assignedCount = $unit->categories->count();
                $totalCats = count($categories);
                $isAllSelected = $assignedCount === $totalCats && $totalCats > 0;
            @endphp

            <flux:card
                class="flex flex-col h-full border-zinc-200 dark:border-zinc-800 transition-all {{ !$unit->is_active ? 'opacity-75 grayscale-[0.5] bg-zinc-50/50' : '' }}">

                <div class="flex-1 space-y-4">
                    {{-- AREA IDENTITAS UNIT --}}
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg {{ $unit->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-zinc-200 text-zinc-500' }}">
                                <flux:icon.building-office-2 />
                            </div>
                            <div>
                                <flux:heading size="lg" class="{{ !$unit->is_active ? 'text-zinc-500' : '' }}">
                                    {{ $unit->name }}
                                </flux:heading>
                                <div class="font-bold text-[10px] text-zinc-400 uppercase tracking-tighter">
                                    {{ $unit->location ?? 'Tanpa Lokasi' }}
                                </div>

                                {{-- INFO PETUGAS (LOCK STATUS) --}}
                                @if($unit->is_occupied && $unit->currentUser)
                                    <div class="flex items-center gap-2 bg-indigo-50 dark:bg-indigo-950/30 mt-2 px-2 py-1 border border-indigo-100 dark:border-indigo-900 rounded-md w-fit">
                                        <flux:icon.user size="xs" class="text-indigo-500" />
                                        <span class="font-bold text-[10px] text-indigo-700 dark:text-indigo-400">
                                            {{ $unit->currentUser->name }}
                                        </span>

                                        <button
                                            wire:click="forceRelease({{ $unit->id }})"
                                            wire:confirm="Paksa keluarkan petugas ini dari loket? Petugas akan diarahkan kembali ke halaman pilih loket."
                                            class="ml-1 text-rose-500 hover:text-rose-600 transition-colors"
                                            title="Putus Sesi Petugas"
                                        >
                                            <flux:icon.x-mark variant="micro" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- TOGGLE AKTIF/NONAKTIF --}}
                        <div class="flex flex-col items-end gap-1">
                            <flux:checkbox wire:click="toggleUnitActive({{ $unit->id }})"
                                :checked="$unit->is_active" variant="toggle" />
                            <span class="text-[9px] font-bold {{ $unit->is_active ? 'text-emerald-600' : 'text-rose-500' }} uppercase">
                                {{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>

                    <flux:separator variant="subtle" />

                    {{-- PEMETAAN KATEGORI --}}
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-[11px] text-zinc-400 italic uppercase tracking-widest">Kategori Layanan</span>
                            <button wire:click="toggleAll({{ $unit->id }})" class="font-bold text-[10px] text-indigo-600 hover:underline">
                                {{ $isAllSelected ? 'Kosongkan' : 'Pilih Semua' }}
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                @php $isActive = $unit->categories->contains($category->id); @endphp
                                <button wire:click="toggleMapping({{ $unit->id }}, {{ $category->id }})"
                                    @disabled(!$unit->is_active)
                                    class="relative group flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm transition-all
                                    {{ $isActive
                                        ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-900/20 dark:border-indigo-800'
                                        : 'bg-white border-zinc-200 text-zinc-400 dark:bg-zinc-900 dark:border-zinc-700' }} {{ !$unit->is_active ? 'cursor-not-allowed opacity-50' : 'hover:border-indigo-300' }}">
                                    <span class="font-bold text-xs">{{ $category->prefix }}</span>
                                    <span class="font-medium text-xs">{{ $category->name }}</span>
                                    @if ($isActive && $unit->is_active)
                                        <div class="bg-indigo-500 rounded-full w-1 h-1"></div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- FOOTER KARTU: STATUS OPERASIONAL --}}
                <div class="flex justify-between items-center mt-6 pt-4 border-zinc-100 dark:border-zinc-800 border-t">
                    <div class="flex items-center gap-2">
                        @if (!$unit->is_active)
                            <flux:badge color="zinc" size="sm" variant="subtle">LOKET TUTUP</flux:badge>
                        @else
                            @php
                                $statusColors = ['ready' => 'emerald', 'resting' => 'orange', 'away' => 'rose'];
                                $statusLabels = ['ready' => 'Siap Melayani', 'resting' => 'Istirahat', 'away' => 'Tugas Luar'];
                                $currentStatus = $unit->status ?? 'ready';
                            @endphp
                            <flux:badge :color="$statusColors[$currentStatus]" size="sm" variant="solid">
                                {{ $statusLabels[$currentStatus] }}
                            </flux:badge>
                        @endif
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="font-mono text-[10px] text-zinc-400">{{ $assignedCount }}/{{ $totalCats }} Kategori</span>
                    </div>
                </div>
            </flux:card>
        @empty
            <div class="col-span-full py-20 border-2 border-zinc-200 border-dashed rounded-xl text-center">
                <flux:subheading>Belum ada data unit atau kategori yang tersedia.</flux:subheading>
            </div>
        @endforelse
    </div>

    {{-- LEGENDA STATUS --}}
    <div class="flex justify-center gap-6 mt-8 py-4 border-zinc-100 dark:border-zinc-800 border-t">
        <div class="flex items-center gap-1.5 font-bold text-[10px] text-zinc-400 uppercase tracking-widest">
            <div class="bg-emerald-500 rounded-full w-2 h-2"></div> Siap
        </div>
        <div class="flex items-center gap-1.5 font-bold text-[10px] text-zinc-400 uppercase tracking-widest">
            <div class="bg-orange-500 rounded-full w-2 h-2"></div> Istirahat
        </div>
        <div class="flex items-center gap-1.5 font-bold text-[10px] text-zinc-400 uppercase tracking-widest">
            <div class="bg-rose-500 rounded-full w-2 h-2"></div> Tugas Luar
        </div>
    </div>
</div>
