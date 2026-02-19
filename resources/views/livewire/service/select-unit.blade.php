<div class="mx-auto py-10 max-w-2xl" x-data="{
    handleSelect(isOccupied, slug, petugas) {
        if (isOccupied) {
            Swal.fire({
                title: 'Ambil Alih Loket?',
                text: 'Sesi petugas ' + petugas + ' akan dihentikan paksa agar Anda bisa masuk.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ambil Alih',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#71717a',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.selectUnit(slug, true);
                }
            });
        } else {
            $wire.selectUnit(slug);
        }
    }
}">
    <header class="mb-10 text-center">
        <flux:heading size="xl">Selamat Datang, {{ auth()->user()->name }}</flux:heading>
        <flux:subheading>Silakan pilih unit/loket tempat Anda bertugas hari ini.</flux:subheading>
    </header>

    <div class="gap-4 grid grid-cols-1 md:grid-cols-2">
        @foreach ($units as $unit)
            @php
                $isMine = $unit->current_user_id === auth()->id();
                $isOccupied = ($unit->is_occupied && !$isMine) ? 'true' : 'false';
                $petugasNama = $unit->currentUser->name ?? 'Petugas';
            @endphp

            <flux:card
                x-on:click="handleSelect({{ $isOccupied }}, '{{ $unit->slug }}', '{{ $petugasNama }}')"
                class="group relative overflow-hidden transition-all hover:border-indigo-500 cursor-pointer {{ $unit->is_occupied && !$isMine ? 'bg-zinc-50 dark:bg-zinc-900/50' : '' }}"
            >
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="bg-zinc-100 dark:bg-zinc-800 dark:group-hover:bg-indigo-900/30 group-hover:bg-indigo-50 p-3 rounded-xl transition-colors">
                            <flux:icon.building-office-2 class="text-zinc-500 group-hover:text-indigo-600" />
                        </div>
                        <div>
                            <flux:heading size="lg">{{ $unit->name }}</flux:heading>
                            <div class="flex flex-col">
                                <span class="font-bold text-zinc-500 text-xs uppercase tracking-wider">{{ $unit->location }}</span>
                                @if($unit->is_occupied && !$isMine)
                                    <span class="mt-1 font-medium text-[10px] text-rose-600 italic">Aktif: {{ $petugasNama }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-end">
                        @if ($unit->is_occupied && !$isMine)
                            <flux:badge color="rose" size="xs" variant="solid" class="animate-pulse">Sesi Aktif</flux:badge>
                        @elseif($isMine)
                            <flux:badge color="emerald" size="xs">Sesi Anda</flux:badge>
                        @else
                            <flux:badge color="zinc" size="xs" variant="subtle">Tersedia</flux:badge>
                        @endif
                    </div>
                </div>

                {{-- Indikator Visual Overlay Kecil jika Occupied --}}
                @if ($unit->is_occupied && !$isMine)
                    <div class="absolute inset-0 bg-rose-500/5 pointer-events-none"></div>
                @endif
            </flux:card>
        @endforeach
    </div>
</div>
