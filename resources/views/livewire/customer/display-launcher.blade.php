<div class="relative flex flex-col justify-center items-center bg-zinc-950 p-6 min-h-screen overflow-hidden">
    {{-- 1. EFEK VISUAL BACKGROUND (Meniru Kiosk) --}}
    <div class="top-0 left-1/2 absolute bg-indigo-600/20 blur-[120px] rounded-full w-full h-96 -translate-x-1/2 pointer-events-none"></div>

    {{-- 2. HEADER --}}
    <div class="z-10 mb-10 text-center">
        <h1 class="font-black text-white text-4xl uppercase tracking-tight">
            Layar Display
        </h1>
        <p class="mt-2 font-medium text-indigo-400 text-lg uppercase tracking-[0.3em]">
            {{ $appSettings['app_name'] ?? 'Laravel Starter Kit' }}
        </p>
        <p class="mt-4 text-zinc-500">Pilih unit/loket yang ingin ditampilkan pada layar TV ini.</p>
    </div>

    {{-- 3. CONTAINER UTAMA --}}
    <div class="z-10 w-full max-w-3xl">
        <div class="bg-zinc-900/50 shadow-2xl backdrop-blur-xl p-8 border border-white/5 rounded-[2.5rem]">

            {{-- GRID PILIHAN UNIT --}}
            <div class="gap-4 grid grid-cols-1 sm:grid-cols-2 mb-8">
                @foreach ($units as $unit)
                    <div wire:key="unit-{{ $unit->id }}" wire:click="toggleUnit('{{ $unit->id }}')"
                        class="relative flex items-center gap-4 p-5 border rounded-2xl cursor-pointer transition-all duration-300 select-none
                        {{ in_array((string) $unit->id, $selectedUnits)
                            ? 'border-indigo-500 bg-indigo-500/10 ring-2 ring-indigo-500/20'
                            : 'border-zinc-800 bg-zinc-900/50 hover:border-zinc-700 hover:bg-zinc-800' }}">

                        {{-- Custom Checkbox Indikator --}}
                        <div class="flex flex-shrink-0 justify-center items-center border rounded-lg w-6 h-6 transition-colors {{ in_array((string) $unit->id, $selectedUnits) ? 'bg-indigo-500 border-indigo-500' : 'border-zinc-700 bg-zinc-950' }}">
                            @if (in_array((string) $unit->id, $selectedUnits))
                                <flux:icon.check variant="micro" class="text-white" />
                            @endif
                        </div>

                        <div class="flex flex-col">
                            <span class="font-bold text-white leading-tight">{{ $unit->name }}</span>
                            <span class="mt-1 text-[10px] text-zinc-500 uppercase tracking-widest">{{ $unit->location ?? 'Lantai 1' }}</span>
                        </div>

                        {{-- Glow Effect saat terpilih --}}
                        @if (in_array((string) $unit->id, $selectedUnits))
                            <div class="top-0 right-0 absolute bg-indigo-500/10 blur-xl rounded-full w-8 h-8"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- PREVIEW SECTION (Meniru Badge Style Kiosk) --}}
            @if (is_array($selectedUnits) && count($selectedUnits) > 0)
                <div class="slide-in-from-bottom-2 bg-indigo-500/5 mb-8 p-5 border border-indigo-500/20 border-dashed rounded-2xl animate-in fade-in">
                    <span class="block mb-4 font-bold text-[10px] text-indigo-400 uppercase tracking-[0.2em]">Unit Terpilih Untuk Monitor</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($units->whereIn('id', $selectedUnits) as $previewUnit)
                            <span class="bg-indigo-500/20 px-3 py-1 border border-indigo-500/30 rounded-full font-bold text-indigo-300 text-xs uppercase">
                                {{ $previewUnit->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="flex flex-col gap-4">
                <button
                    wire:click="launch"
                    @disabled(!is_array($selectedUnits) || count($selectedUnits) === 0)
                    class="flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-500 disabled:bg-zinc-800 shadow-indigo-500/20 shadow-lg p-4 rounded-2xl w-full font-bold text-white disabled:text-zinc-500 active:scale-[0.98] transition-all"
                >
                    <flux:icon.tv class="w-5 h-5" />
                    Buka Monitor Antrean
                </button>

                <div class="flex items-center gap-4 my-2">
                    <div class="flex-1 bg-zinc-800 h-[1px]"></div>
                    <span class="text-[10px] text-zinc-600 uppercase tracking-widest">Atau</span>
                    <div class="flex-1 bg-zinc-800 h-[1px]"></div>
                </div>

                <a
                    href="{{ route('kiosk') }}"
                    class="flex justify-center items-center gap-2 bg-zinc-800 hover:bg-zinc-700 p-4 rounded-2xl w-full font-bold text-zinc-300 transition-all"
                >
                    <flux:icon.ticket class="w-5 h-5" />
                    Buka Kiosk Pengambilan Tiket
                </a>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="z-10 mt-12 font-medium text-[10px] text-zinc-600 uppercase tracking-[0.3em]">
        {{ now()->translatedFormat('l, d F Y') }}
    </div>

    <style>
        html, body {
            background-color: #09090b; /* zinc-950 */
        }
        /* Menghilangkan scrollbar agar terlihat seperti aplikasi native */
        ::-webkit-scrollbar { display: none; }
    </style>
</div>
