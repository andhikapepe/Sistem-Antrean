{{-- Satu-satunya Root Element --}}
<div class="bg-zinc-950 p-8 min-h-screen overflow-hidden font-sans text-white">

    {{-- 1. Konten UI Utama --}}
    <div class="gap-8 grid grid-cols-12 h-[85vh]">
        {{-- Area Panggilan Sekarang --}}
        <div class="flex flex-col justify-between col-span-8 bg-zinc-900 shadow-2xl p-10 border border-zinc-800 rounded-[3rem]">
            <div class="pt-10 text-center">
                <h2 class="mb-4 font-bold text-zinc-500 text-4xl uppercase tracking-[0.3em]">Panggilan Sekarang</h2>
                <div class="font-black text-[250px] text-indigo-500 leading-none">
                    {{ $callingQueue->ticket_number ?? '---' }}
                </div>
            </div>

            <div class="bg-indigo-600 shadow-lg p-10 rounded-[2rem] text-center">
                <h3 class="opacity-80 mb-2 font-medium text-3xl uppercase">Silakan Menuju Ke</h3>
                <h1 class="font-bold text-8xl uppercase tracking-tight">{{ $callingQueue->serviceUnit->name ?? 'MOHON MENUNGGU' }}</h1>
            </div>
        </div>

        {{-- Sidebar Status Loket --}}
        <div class="space-y-4 col-span-4 pr-2 overflow-y-auto custom-scrollbar">
            @foreach($units as $unit)
                <div class="flex justify-between items-center bg-zinc-900 p-6 border border-zinc-800 rounded-3xl">
                    <div class="space-y-1">
                        <h4 class="font-bold text-zinc-400 text-2xl uppercase">{{ $unit->name }}</h4>
                        <p class="font-medium text-zinc-500 text-sm">{{ $unit->location }}</p>
                    </div>
                    <div class="font-black text-emerald-500 text-5xl">
                        {{ $unit->queues()->whereIn('status', ['calling','serving'])->orderBy('updated_at', 'desc')->first()->ticket_number ?? '---' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 2. Running Text --}}
    <div class="bg-indigo-900/20 mt-8 py-5 border-indigo-500/30 border-y font-bold text-3xl whitespace-nowrap">
        <marquee scrollamount="10">Selamat Datang di Layanan Kami • Harap Menunggu Antrean Anda dengan Sabar •</marquee>
    </div>

    {{-- 3. Script & Logic (Tetap di dalam Root Div) --}}
    <div wire:ignore>
        @script
        <script>
            window.speechQueue = [];
            window.isSpeaking = false;

            $wire.on('add-to-speech-queue', (data) => {
                window.speechQueue.push(data);
                processQueue();
            });

            function processQueue() {
                if (window.isSpeaking || window.speechQueue.length === 0) return;
                const item = window.speechQueue.shift();
                window.isSpeaking = true;

                const text = `Nomor antrean, ${item.prefix}, ${item.number}. Silakan menuju ke ${item.unit_name}`;
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.onend = () => {
                    window.isSpeaking = false;
                    setTimeout(processQueue, 1000);
                };
                window.speechSynthesis.speak(utterance);
            }
        </script>
        @endscript
    </div>

    {{-- 4. CSS Khusus (Tetap di dalam Root Div) --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
    </style>
</div>
