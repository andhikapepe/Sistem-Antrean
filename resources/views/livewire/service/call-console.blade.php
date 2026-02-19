<div class="gap-6 grid grid-cols-1 md:grid-cols-12">
    {{-- AREA UTAMA PANGGILAN --}}
    <div class="space-y-6 md:col-span-8">
        <flux:card
            class="relative overflow-hidden py-12 text-center {{ $status !== 'ready' ? 'bg-zinc-50 dark:bg-zinc-950' : '' }}">

            {{-- Overlay saat Offline / Istirahat --}}
            @if ($status !== 'ready')
                <div
                    class="z-20 absolute inset-0 flex flex-col justify-center items-center bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm transition-all">
                    <flux:icon.pause-circle class="mb-2 w-16 h-16 text-zinc-400" />
                    <flux:heading size="xl">Loket Sedang Non-Aktif</flux:heading>
                    <flux:subheading class="mb-6">Status Anda saat ini adalah
                        <strong>{{ strtoupper($status) }}</strong></flux:subheading>

                    <flux:button variant="filled" color="indigo" wire:click="setStatus('ready')" class="px-8">
                        Mulai Melayani (Go Online)
                    </flux:button>
                </div>
            @endif

            {{-- Konten Utama --}}
            <div class="{{ $status !== 'ready' ? 'opacity-20 pointer-events-none' : '' }} transition-opacity">
                <flux:heading size="sm" class="font-bold text-zinc-400 uppercase tracking-[0.3em]">
                    Nomor Antrean Saat Ini
                </flux:heading>

                {{-- Nomor Tiket Besar --}}
                <div
                    class="my-10 font-black text-indigo-600 text-9xl tracking-tighter animate-in duration-500 fade-in zoom-in">
                    {{ $currentQueue ? $currentQueue->ticket_number : '---' }}
                </div>

                {{-- Tombol Kontrol --}}
                <div class="space-y-4 mx-auto max-w-md">
                    <flux:button wire:click="callNext" variant="primary"
                        class="shadow-indigo-500/20 shadow-lg py-10 w-full text-3xl">
                        PANGGIL BERIKUTNYA
                    </flux:button>

                    <div class="gap-4 grid grid-cols-2">
                        <flux:button wire:click="recall" icon="speaker-wave" :disabled="!$currentQueue" class="py-4">
                            Panggil Ulang
                        </flux:button>

                        <flux:button wire:click="complete" color="emerald" icon="check" :disabled="!$currentQueue"
                            class="py-4">
                            Selesai
                        </flux:button>
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- INFO UNIT & STATISTIK SINGKAT --}}
        <div class="gap-4 grid grid-cols-2">
            <flux:card class="flex items-center gap-4">
                <div class="bg-zinc-100 dark:bg-zinc-800 p-3 rounded-xl">
                    <flux:icon.building-office class="text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="sm">Unit Kerja</flux:heading>
                    <flux:subheading>{{ $unit->name }} ({{ $unit->location }})</flux:subheading>
                </div>
            </flux:card>

            <flux:card class="flex items-center gap-4">
                <div class="bg-zinc-100 dark:bg-zinc-800 p-3 rounded-xl">
                    <flux:icon.users class="text-zinc-500" />
                </div>
                <div>
                    <flux:heading size="sm">Total Melayani</flux:heading>
                    <flux:subheading>
                        {{ $unit->queues()->whereDate('created_at', today())->where('status', 'completed')->count() }}
                        Pelanggan Hari Ini</flux:subheading>
                </div>
            </flux:card>
        </div>
    </div>

    {{-- SIDEBAR: STATUS & DAFTAR TUNGGU --}}
    <div class="space-y-6 md:col-span-4">
        {{-- Card Status --}}
        <flux:card>
            <flux:heading class="mb-4">Status Petugas</flux:heading>
            <div class="space-y-2">
                @php
                    $options = [
                        'ready' => ['color' => 'emerald', 'label' => 'Online / Siap'],
                        'resting' => ['color' => 'orange', 'label' => 'Istirahat'],
                        'away' => ['color' => 'rose', 'label' => 'Tugas Luar'],
                    ];
                @endphp

                @foreach ($options as $key => $opt)
                    <button wire:click="setStatus('{{ $key }}')"
                        class="w-full flex items-center justify-between p-3 rounded-xl border transition-all {{ $status === $key ? "border-{$opt['color']}-500 bg-{$opt['color']}-50 dark:bg-{$opt['color']}-950/20" : 'border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-3">
                            <div class="bg-{{ $opt['color'] }}-500 rounded-full w-2.5 h-2.5 shadow-sm"></div>
                            <span
                                class="font-bold text-sm {{ $status === $key ? "text-{$opt['color']}-700 dark:text-{$opt['color']}-400" : 'text-zinc-600 dark:text-zinc-400' }}">
                                {{ $opt['label'] }}
                            </span>
                        </div>
                        @if ($status === $key)
                            <flux:icon.check variant="micro" class="text-{{ $opt['color'] }}-600" />
                        @endif
                    </button>
                @endforeach
            </div>
        </flux:card>

        {{-- Daftar Tunggu --}}
        <flux:card class="p-0 overflow-hidden">
            <div class="bg-zinc-50/50 dark:bg-white/5 p-4 border-zinc-100 dark:border-zinc-800 border-b">
                <flux:heading size="sm">Antrean Menunggu</flux:heading>
            </div>

            <div class="space-y-1 p-2 max-h-[300px] overflow-y-auto">
                @php
                    $waitingQueues = \App\Models\Queue::whereIn('queue_category_id', $unit->categories->pluck('id'))
                        ->where('status', 'waiting')
                        ->whereDate('created_at', today())
                        ->orderBy('created_at', 'asc')
                        ->get();
                @endphp

                @forelse($waitingQueues as $q)
                    <div
                        class="flex justify-between items-center bg-white dark:bg-zinc-900 p-3 border border-zinc-100 dark:border-zinc-800 rounded-lg">
                        <span class="font-mono font-bold text-indigo-600">{{ $q->ticket_number }}</span>
                        <flux:badge size="sm" variant="subtle">{{ $q->category->name }}</flux:badge>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <flux:icon.information-circle class="mx-auto mb-2 text-zinc-300" />
                        <p class="text-zinc-400 text-xs italic">Belum ada antrean masuk</p>
                    </div>
                @endforelse
            </div>
        </flux:card>

        {{-- Logout Sesi --}}
        <flux:button variant="ghost"
            class="justify-start hover:bg-rose-50 dark:hover:bg-rose-950/20 w-full font-bold text-rose-500"
            x-on:click="
        Swal.fire({
            title: 'Tutup Sesi Loket?',
            text: 'Loket akan menjadi kosong dan bisa digunakan petugas lain.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48', {{-- rose-600 --}}
            cancelButtonColor: '#71717a', {{-- zinc-500 --}}
            confirmButtonText: 'Ya, Tutup Sesi!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.quit() {{-- Memanggil fungsi PHP quit() di Livewire --}}
            }
        })
    "
            icon="arrow-left-end-on-rectangle">
            Tutup Sesi & Keluar
        </flux:button>
    </div>
</div>
