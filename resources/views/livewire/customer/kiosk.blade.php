<div class="relative flex flex-col justify-center items-center bg-zinc-950 p-6 min-h-screen overflow-hidden">
    {{-- 1. EFEK VISUAL BACKGROUND --}}
    <div class="print:hidden top-0 left-1/2 absolute bg-indigo-600/20 blur-[120px] rounded-full w-full h-96 -translate-x-1/2 pointer-events-none"></div>

    {{-- 2. HEADER KIOSK DENGAN LOGO DARI APP SETTINGS --}}
    <div class="print:hidden z-10 mb-12 text-center">
        <div class="flex justify-center mb-6">
            <div class="bg-white/5 shadow-2xl backdrop-blur-xl p-4 border border-white/10 rounded-3xl hover:rotate-3 transition-transform duration-500">
                @if (!empty($appSettings['app_logo']))
                    <img src="{{ asset('storage/' . $appSettings['app_logo']) }}"
                         alt="{{ $appSettings['app_name'] ?? 'Logo' }}"
                         class="w-24 h-24 object-contain">
                @else
                    {{-- Fallback jika logo kosong: Gunakan Icon --}}
                    <flux:icon.ticket class="w-20 h-20 text-indigo-500" />
                @endif
            </div>
        </div>

        <h1 class="font-black text-white text-5xl uppercase tracking-tight">
            {{ $appSettings['app_name'] ?? config('app.name') }}
        </h1>

        <div class="flex justify-center items-center gap-3 mt-4">
            <span class="bg-indigo-500 w-8 h-[1px]"></span>
            <p class="font-medium text-zinc-400 text-xl uppercase tracking-widest">
                {{ $appSettings['app_slogan'] ?? 'Pusat Layanan Terpadu' }}
            </p>
            <span class="bg-indigo-500 w-8 h-[1px]"></span>
        </div>
    </div>

    {{-- 3. GRID TOMBOL KATEGORI --}}
    <div class="print:hidden z-10 gap-8 grid grid-cols-1 md:grid-cols-2 w-full max-w-5xl">
        @foreach ($categories as $category)
            <button
                wire:click="takeQueue({{ $category->id }})"
                wire:loading.attr="disabled"
                class="group relative bg-zinc-900 shadow-2xl p-12 border border-zinc-800 hover:border-indigo-500 rounded-[2.5rem] overflow-hidden text-left hover:scale-[1.02] active:scale-95 transition-all duration-300"
            >
                {{-- Watermark Prefix --}}
                <span class="-top-4 -right-4 absolute font-black text-[120px] text-white/[0.03] group-hover:text-indigo-500/10 uppercase transition-colors pointer-events-none">
                    {{ $category->prefix }}
                </span>

                <div class="z-10 relative flex items-center gap-8">
                    <div class="flex justify-center items-center bg-indigo-500/10 group-hover:bg-indigo-500 rounded-2xl w-20 h-20 transition-colors duration-300">
                        <flux:icon.users class="w-10 h-10 text-indigo-500 group-hover:text-white" />
                    </div>

                    <div>
                        <h2 class="font-bold text-white group-hover:text-indigo-400 text-3xl transition-colors">
                            {{ $category->name }}
                        </h2>
                        <p class="mt-1 font-bold text-zinc-500 text-sm uppercase tracking-widest">Tekan untuk ambil antrean</p>
                    </div>
                </div>

                {{-- Loading Spinner Overlay --}}
                <div wire:loading wire:target="takeQueue({{ $category->id }})" class="z-20 absolute inset-0 flex flex-col justify-center items-center bg-zinc-900/90 backdrop-blur-md">
                    <div class="mb-4 border-4 border-indigo-500 border-t-transparent rounded-full w-12 h-12 animate-spin"></div>
                    <p class="font-bold text-white animate-pulse">SEDANG MENCETAK...</p>
                </div>
            </button>
        @endforeach
    </div>

    {{-- 4. AREA STRUK (Tetap minimalis untuk printer thermal) --}}
    <div id="print-area" class="hidden print:block p-0 w-full font-mono text-black text-center">
        <div class="mb-4 pb-2 border-black border-b">
            <h1 class="font-bold text-lg uppercase">{{ $appSettings['app_name'] ?? config('app.name') }}</h1>
            <p class="text-[10px]">{{ now()->translatedFormat('l, d F Y | H:i') }}</p>
        </div>

        <p class="text-xs uppercase">Nomor Antrean</p>
        <div class="my-2 font-black text-[60px] leading-none" id="print-ticket-number">---</div>

        <div class="my-2 py-1 border-black border-y font-bold uppercase">
            <span id="print-category-name">---</span>
        </div>

        <div class="mt-4 text-[9px]">
            <p>Silakan tunggu panggilan nomor Anda.</p>
            <p class="mt-2 font-bold italic">Terima Kasih</p>
        </div>
    </div>

    {{-- 5. FOOTER --}}
    <div class="print:hidden z-10 mt-20 font-medium text-[10px] text-zinc-600 uppercase tracking-[0.3em]">
        {{ now()->translatedFormat('l, d F Y') }}
    </div>

    {{-- 6. SCRIPT CETAK --}}
    <script>
        window.addEventListener('ticket-created', event => {
            const data = event.detail[0];

            document.getElementById('print-ticket-number').innerText = data.ticket;
            document.getElementById('print-category-name').innerText = data.category;

            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>

    <style>
        @media print {
            body { background: white !important; }
            .print\:hidden { display: none !important; }
            .print\:block { display: block !important; }
            @page { margin: 0; size: auto; }
        }
        ::-webkit-scrollbar { display: none; }
        html, body {
            overflow: hidden;
            height: 100%;
            -webkit-user-select: none;
            user-select: none;
        }
    </style>
</div>
