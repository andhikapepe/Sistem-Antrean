{{-- resources/views/livewire/kiosk.blade.php --}}
<div class="relative flex flex-col justify-center items-center bg-zinc-950 p-6 min-h-screen overflow-hidden">

    {{-- 1. EFEK VISUAL BACKGROUND (Hanya Layar) --}}
    <div
        class="print:hidden top-0 left-1/2 absolute bg-indigo-600/20 blur-[120px] rounded-full w-full h-96 -translate-x-1/2 pointer-events-none">
    </div>

    {{-- 2. HEADER KIOSK --}}
    <div class="print:hidden z-10 mb-12 text-center">
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
            <button wire:click="takeQueue({{ $category->id }})" wire:loading.attr="disabled"
                class="group relative bg-zinc-900 disabled:opacity-50 shadow-2xl p-12 border border-zinc-800 hover:border-indigo-500 rounded-[2.5rem] overflow-hidden text-left hover:scale-[1.02] active:scale-95 transition-all duration-300">

                <span
                    class="-top-4 -right-4 absolute font-black text-[120px] text-white/[0.03] group-hover:text-indigo-500/10 uppercase transition-colors pointer-events-none">
                    {{ $category->prefix }}
                </span>

                <div class="z-10 relative flex items-center gap-8">
                    <div
                        class="flex justify-center items-center bg-indigo-500/10 group-hover:bg-indigo-500 rounded-2xl w-20 h-20 transition-colors duration-300">
                        <flux:icon.users class="w-10 h-10 text-indigo-500 group-hover:text-white" />
                    </div>
                    <div>
                        <h2 class="font-bold text-white group-hover:text-indigo-400 text-3xl transition-colors">
                            {{ $category->name }}
                        </h2>
                        <p class="mt-1 font-bold text-zinc-500 text-sm uppercase tracking-widest">
                            Tekan untuk ambil antrean
                        </p>
                    </div>
                </div>
            </button>
        @endforeach
    </div>

    {{-- 4. TOMBOL TEST PRINT --}}
    <div class="print:hidden z-10 mt-20">
        <button onclick="testPrint()"
            class="px-6 py-2 border border-zinc-800 hover:border-zinc-600 rounded-full text-[10px] text-zinc-600 uppercase tracking-widest transition-all">
            Test Print Mesin
        </button>
    </div>

    {{-- 5. IFRAME TERSEMBUNYI UNTUK PROSES PRINT --}}
    <iframe id="printFrame" style="display:none;"></iframe>

    {{-- 6. SCRIPTS LOGIC --}}
    <script>
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
</div>
