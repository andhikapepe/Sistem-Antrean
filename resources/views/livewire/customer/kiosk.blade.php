{{-- resources/views/livewire/kiosk.blade.php --}}
<div class="relative flex flex-col justify-center items-center bg-zinc-950 p-6 min-h-screen overflow-hidden">

<<<<<<< HEAD
    {{-- Glow Effect --}}
    <div
        class="top-0 left-1/2 absolute bg-indigo-600/20 blur-[120px] rounded-full w-full h-96 -translate-x-1/2 pointer-events-none">
    </div>

    {{-- Branding Section --}}
    <div class="z-10 mb-12 text-center">
=======
    {{-- 1. EFEK VISUAL BACKGROUND (Hanya Layar) --}}
    <div
        class="print:hidden top-0 left-1/2 absolute bg-indigo-600/20 blur-[120px] rounded-full w-full h-96 -translate-x-1/2 pointer-events-none">
    </div>

    {{-- 2. HEADER KIOSK --}}
    <div class="print:hidden z-10 mb-12 text-center">
>>>>>>> 9c17a938b01e3565c9a9b45b4215741d9e234415
        <div class="flex justify-center mb-6">
            <div class="bg-white/5 shadow-2xl backdrop-blur-xl p-4 border border-white/10 rounded-3xl">
                @if (!empty($appSettings['app_logo']))
                    <img src="{{ asset('storage/' . $appSettings['app_logo']) }}" class="w-24 h-24 object-contain">
                @else
                    <flux:icon.ticket class="w-20 h-20 text-indigo-500" />
                @endif
            </div>
        </div>

        <h1 class="font-black text-white text-5xl uppercase tracking-tight">
            {{ $appSettings['app_name'] ?? 'SMART QUEUE' }}
        </h1>
        <p class="mt-4 font-medium text-zinc-400 text-xl uppercase tracking-widest">
            {{ $appSettings['app_slogan'] ?? 'Silakan Ambil Antrean Anda' }}
        </p>
    </div>

    {{-- Categories Grid --}}
    <div class="z-10 gap-8 grid grid-cols-1 md:grid-cols-2 w-full max-w-5xl">
        @foreach ($categories as $category)
            <button wire:click="takeQueue({{ $category->id }})" wire:loading.attr="disabled"
<<<<<<< HEAD
                class="group relative bg-zinc-900 disabled:opacity-50 shadow-2xl p-10 border border-zinc-800 hover:border-indigo-500 rounded-[2.5rem] overflow-hidden text-left hover:scale-[1.02] active:scale-95 transition-all duration-300">

                <span
                    class="-top-4 -right-4 absolute font-black text-[120px] text-white/[0.03] group-hover:text-indigo-500/10 uppercase pointer-events-none">
                    {{ $category->prefix }}
                </span>

                <div class="z-10 relative flex items-center gap-6">
                    <div
                        class="flex justify-center items-center bg-indigo-500/10 group-hover:bg-indigo-500 rounded-2xl w-20 h-20 transition-all">
                        <div wire:loading wire:target="takeQueue({{ $category->id }})" class="absolute">
                            <flux:icon.arrow-path class="w-10 h-10 text-white animate-spin" />
                        </div>
                        <div wire:loading.remove wire:target="takeQueue({{ $category->id }})">
                            <flux:icon.users class="w-10 h-10 text-indigo-500 group-hover:text-white" />
                        </div>
=======
                class="group relative bg-zinc-900 disabled:opacity-50 shadow-2xl p-12 border border-zinc-800 hover:border-indigo-500 rounded-[2.5rem] overflow-hidden text-left hover:scale-[1.02] active:scale-95 transition-all duration-300">

                <span
                    class="-top-4 -right-4 absolute font-black text-[120px] text-white/[0.03] group-hover:text-indigo-500/10 uppercase transition-colors pointer-events-none">
                    {{ $category->prefix }}
                </span>

                <div class="z-10 relative flex items-center gap-8">
                    <div
                        class="flex justify-center items-center bg-indigo-500/10 group-hover:bg-indigo-500 rounded-2xl w-20 h-20 transition-colors duration-300">
                        <flux:icon.users class="w-10 h-10 text-indigo-500 group-hover:text-white" />
>>>>>>> 9c17a938b01e3565c9a9b45b4215741d9e234415
                    </div>
                    <div>
                        <h2 class="font-bold text-white group-hover:text-indigo-400 text-3xl transition-colors">
                            {{ $category->name }}
                        </h2>
<<<<<<< HEAD
                        <p class="mt-1 font-bold text-zinc-500 text-xs uppercase tracking-widest">Sentuh untuk mencetak
                            tiket</p>
=======
                        <p class="mt-1 font-bold text-zinc-500 text-sm uppercase tracking-widest">
                            Tekan untuk ambil antrean
                        </p>
>>>>>>> 9c17a938b01e3565c9a9b45b4215741d9e234415
                    </div>
                </div>
            </button>
        @endforeach
    </div>

<<<<<<< HEAD
    {{-- Footer & Utilities --}}
    <div class="z-10 mt-16 text-center">
        <div id="kiosk-clock" class="mb-6 font-mono text-zinc-500 text-xl tracking-widest">
            {{ now()->translatedFormat('l, d F Y | H:i:s') }}
        </div>

        <button wire:click="testPrintDirect" wire:loading.attr="disabled"
            class="flex items-center gap-2 mx-auto px-6 py-2 border border-zinc-800 hover:border-indigo-500 rounded-full text-[10px] text-zinc-600 hover:text-indigo-400 uppercase tracking-widest transition-all">
            <div wire:loading wire:target="testPrintDirect">
                <flux:icon.arrow-path class="w-3 h-3 animate-spin" />
            </div>
            <span wire:loading.remove wire:target="testPrintDirect">Test Print Mesin</span>
            <span wire:loading wire:target="testPrintDirect">Sedang Mencoba...</span>
        </button>
    </div>
=======
    {{-- 4. TOMBOL TEST PRINT --}}
    <div class="print:hidden z-10 mt-20 text-center">
        <div id="kiosk-clock" class="mb-2 font-medium text-[14px] text-zinc-400 uppercase tracking-[0.3em]">
            {{ now()->translatedFormat('l, d F Y | H:i:s') }}
        </div>
        <button onclick="testPrint()"
            class="px-6 py-2 border border-zinc-800 hover:border-zinc-600 rounded-full text-[10px] text-zinc-600 uppercase tracking-widest transition-all">
            Test Print Mesin
        </button>
    </div>

    {{-- 5. IFRAME TERSEMBUNYI UNTUK PROSES PRINT --}}
    <iframe id="printFrame" style="display:none;"></iframe>

    {{-- 6. SCRIPTS LOGIC --}}
    <script>
        function updateKioskTime() {
            const clockElement = document.getElementById('kiosk-clock');
            if (clockElement) {
                const now = new Date();
                const options = {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                // Format Indonesia
                clockElement.innerText = now.toLocaleDateString('id-ID', options).replace(/\./g, ':');
            }
        }

        // Jalankan jam setiap detik
        setInterval(updateKioskTime, 1000);

        // Fungsi Utama Print
        function executePrint(ticket, category, date) {
            const frame = document.getElementById('printFrame');

            // 1. Ambil template dari file kiosk.php (Blade merendernya menjadi string)
            let htmlTemplate = {!! json_encode(view('print.kiosk')->render()) !!};

            // 2. Ganti Placeholder dengan Data Asli
            htmlTemplate = htmlTemplate.replace('[APP_NAME]', "{{ $appSettings['app_name'] ?? config('app.name') }}");
            htmlTemplate = htmlTemplate.replace('[TICKET]', ticket);
            htmlTemplate = htmlTemplate.replace('[CATEGORY]', category);
            htmlTemplate = htmlTemplate.replace('[DATE]', date);
            htmlTemplate = htmlTemplate.replace('[COPYRIGHT]', "{{ $appSettings['app_copyright'] ?? '© ' . date('Y') }}");

            // 3. Masukkan ke Iframe menggunakan srcdoc (Lebih stabil & cepat)
            frame.srcdoc = htmlTemplate;

            // 4. Tampilkan SweetAlert
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'SEDANG MENCETAK',
                    html: 'Mohon tunggu nomor Anda...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 2000,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // 5. Jalankan Print setelah Iframe selesai memuat kontennya
            frame.onload = function() {
                setTimeout(() => {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                }, 250);
            };
        }

        // Listener Event Livewire (Pastikan event name sesuai dengan di Component)
        window.addEventListener('ticket-created', event => {
            // Ambil data detail dengan cara yang lebih aman
            const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;

            if (detail) {
                executePrint(
                    detail.ticket || '---',
                    detail.category || '---',
                    detail.date || new Date().toLocaleString('id-ID')
                );
            }
        });

        // Fungsi Test Print Manual
        function testPrint() {
            console.log("Menjalankan Test Print...");
            executePrint("T-000", "TEST PRINTER", new Date().toLocaleString('id-ID'));
        }
    </script>

    {{-- 7. STYLES KIOSK --}}
    <style>
        html,
        body {
            overflow: hidden;
            height: 100%;
            background-color: #09090b;
            -webkit-user-select: none;
            user-select: none;
        }

        ::-webkit-scrollbar {
            display: none;
        }
    </style>
>>>>>>> 9c17a938b01e3565c9a9b45b4215741d9e234415
</div>

@script
<script>
    // Update Jam Real-time
    setInterval(() => {
        const clock = document.getElementById('kiosk-clock');
        if (clock) {
            clock.innerText = new Date().toLocaleString('id-ID', {
                weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            }).replace(/\./g, ':');
        }
    }, 1000);

    // Handler Tiket Berhasil (Antrean)
    $wire.on('ticket-created', (event) => {
        // Pada Livewire 3, event biasanya adalah array [data]
        const data = Array.isArray(event) ? event[0] : event;

        Swal.fire({
            title: 'TIKET DICETAK',
            html: `<div class="font-bold text-indigo-500 text-2xl">${data.ticket}</div><div class="text-sm">Kategori: ${data.category}</div>`,
            icon: 'success',
            timer: 3500,
            showConfirmButton: false,
            background: '#121214',
            color: '#fff',
            customClass: { popup: 'rounded-[2rem] border border-zinc-800' }
        });
    });

    // Handler Jika Printer Error (Kabel lepas / IP salah)
    $wire.on('printer-error', (event) => {
        const data = Array.isArray(event) ? event[0] : event;
        Swal.fire({
            title: 'PRINTER OFFLINE',
            text: data.message || 'Gagal terhubung ke mesin printer.',
            icon: 'error',
            background: '#121214',
            color: '#fff',
            confirmButtonColor: '#6366f1'
        });
    });
</script>
@endscript
