<div class="space-y-6" wire:poll.30s="refreshAllStatuses">
    {{-- HEADER --}}
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Manajemen Printer & Pintu</flux:heading>
            <flux:subheading size="lg">Hubungkan hardware printer dan petakan ke lokasi tablet kiosk.
            </flux:subheading>
        </div>

        <div>
            {{-- BULK DELETE HARDWARE (Updated to SWAL) --}}
            @if ($activeTab === 'hardware' && count($selectedPrinters) > 0)
                <flux:button variant="danger" icon="trash" wire:click="confirmBulkDelete">
                    Hapus Terpilih ({{ count($selectedPrinters) }})
                </flux:button>
            @endif

            {{-- BULK DELETE ASSIGNMENTS (Updated to SWAL) --}}
            @if ($activeTab === 'assignments' && count($selectedAssignments) > 0)
                <flux:button variant="danger" icon="trash" wire:click="confirmBulkDeleteAssignments">
                    Hapus Terpilih ({{ count($selectedAssignments) }})
                </flux:button>
            @endif

            <flux:button icon="arrow-path" variant="primary" wire:click="refreshAllStatuses" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="refreshAllStatuses">Cek Status Printer</span>
                <span wire:loading wire:target="refreshAllStatuses">Memeriksa...</span>
            </flux:button>
        </div>
    </div>

    {{-- NAVIGASI TAB --}}
    <div class="flex gap-2 bg-zinc-100 dark:bg-zinc-800/50 p-1 rounded-lg w-fit">
        <flux:button wire:key="tab-hw" wire:click="$set('activeTab', 'hardware')"
            :variant="$activeTab === 'hardware' ? 'primary' : 'ghost'" size="sm">
            Hardware Printer
        </flux:button>
        <flux:button wire:key="tab-as" wire:click="$set('activeTab', 'assignments')"
            :variant="$activeTab === 'assignments' ? 'primary' : 'ghost'" size="sm">
            Penugasan Per Pintu
        </flux:button>
    </div>

    <div class="items-start gap-8 grid grid-cols-1 lg:grid-cols-12">
        {{-- KOLOM KIRI: FORM --}}
        <div class="lg:col-span-4">
            @if ($activeTab === 'hardware')
                <flux:card wire:key="form-hardware">
                    <form wire:submit="save" class="space-y-5">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ $printerId ? 'Edit Hardware' : 'Tambah Hardware' }}
                            </flux:heading>
                            @if ($printerId)
                                <flux:button variant="ghost" size="xs" wire:click="cancelEdit">Batal</flux:button>
                            @endif
                        </div>

                        <flux:input label="Nama Label" wire:model="name" placeholder="Contoh: Printer Kasir Utama" />

                        <div class="gap-4 grid grid-cols-2">
                            <flux:select label="Tipe Koneksi" wire:model.live="type">
                                <option value="windows">Lokal (Windows/Linux)</option>
                                <option value="network">Network (Kabel LAN)</option>
                            </flux:select>
                            <flux:select label="Lebar Kertas" wire:model="width">
                                <option value="32">58mm (Kecil)</option>
                                <option value="48">80mm (Besar)</option>
                            </flux:select>
                        </div>

                        @if ($type === 'network')
                            <div class="gap-4 grid grid-cols-3">
                                <div class="col-span-2">
                                    <flux:input label="Alamat IP" wire:model="address" placeholder="192.168.1.100" />
                                </div>
                                <flux:input label="Port" wire:model="port" placeholder="9100" />
                            </div>
                        @else
                            <flux:input label="Nama Share / Nama Alat" wire:model="address"
                                placeholder="Contoh: POS-58" />
                        @endif

                        <flux:button type="submit" variant="primary" class="w-full">Simpan Hardware</flux:button>
                    </form>
                </flux:card>
            @else
                <flux:card wire:key="form-assignment">
                    <form wire:submit="saveAssignment" class="space-y-5">
                        <div class="flex justify-between items-center">
                            <flux:heading size="lg">{{ $assignmentId ? 'Edit Penugasan' : 'Petakan Kiosk' }}
                            </flux:heading>
                            @if ($assignmentId)
                                <flux:button variant="ghost" size="xs" wire:click="cancelEdit">Batal</flux:button>
                            @endif
                        </div>

                        <flux:input label="Nama Pintu/Lokasi" wire:model="kioskName"
                            placeholder="Contoh: Pintu Masuk Utama" />

                        <div class="space-y-2">
                            <flux:label>IP Address / Hostname Tablet</flux:label>
                            <div class="flex gap-2">
                                <div class="relative grow">
                                    <flux:input wire:model="clientIp" placeholder="Contoh: 192.168.1.15 atau TABLET-01"
                                        class="pr-10" />
                                    <div class="right-0 absolute inset-y-0 flex items-center pr-2">
                                        <button type="button" wire:click="useCurrentIp"
                                            class="text-zinc-400 hover:text-indigo-600 transition-colors"
                                            title="Deteksi IP otomatis">
                                            <flux:icon.map-pin variant="micro" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="text-[10px] text-zinc-500 italic">Gunakan Hostname jika IP tablet sering berubah
                                (DHCP).</p>
                        </div>

                        <flux:select label="Printer Tujuan" wire:model="selectedPrinterId">
                            <option value="">-- Pilih Printer untuk Pintu Ini --</option>
                            @foreach ($allPrinters as $ap)
                                <option value="{{ $ap->id }}">{{ $ap->name }}</option>
                            @endforeach
                        </flux:select>

                        <flux:button type="submit" variant="primary" class="w-full">Simpan Penugasan</flux:button>
                    </form>
                </flux:card>
            @endif
        </div>

        {{-- KOLOM KANAN: TABEL --}}
        <div class="space-y-6 lg:col-span-8">
            <div class="space-y-4">
                <div class="flex gap-2">
                    <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari..."
                        class="flex-1" />
                    <flux:button icon="arrow-path" wire:click="$set('search', '')" variant="ghost" />
                </div>

                <flux:card class="p-4" wire:key="table-container-{{ $activeTab }}">
                    @if ($activeTab === 'hardware')
                        <flux:table :paginate="$printers">
                            <flux:table.columns>
                                <flux:table.column class="w-10">
                                    <flux:checkbox wire:model.live="selectAll" wire:click="toggleSelectAll" />
                                </flux:table.column>
                                <flux:table.column>Info Printer</flux:table.column>
                                <flux:table.column>Status</flux:table.column>
                                <flux:table.column align="end">Aksi</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($printers as $p)
                                    <flux:table.row :key="'row-p-'.$p->id">
                                        <flux:table.cell>
                                            <flux:checkbox wire:model.live="selectedPrinters"
                                                value="{{ $p->id }}" />
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex flex-col">
                                                <span class="font-bold">{{ $p->name }}</span>
                                                <span class="text-zinc-500 text-xs">{{ $p->address }}</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            @php
                                                // Ambil status, jika null atau unknown tetap arahkan ke warna zinc/red
                                                $currentStatus = strtolower($p->last_status);
                                            @endphp

                                            <flux:badge size="sm"
                                                :color="match($currentStatus) {
                                                    'online' => 'green',
                                                    'offline' => 'red',
                                                    default => 'zinc',
                                                }"
                                                :icon="match($currentStatus) {
                                                    'online' => 'check',
                                                    'offline' => 'x-mark',
                                                    default => 'clock',
                                                }">
                                                {{ strtoupper($p->last_status ?? 'MENGECEK...') }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="end">
                                            <div class="flex justify-end gap-1">
                                                <flux:button size="sm" variant="ghost" icon="printer"
                                                    wire:click="testPrint({{ $p->id }})"
                                                    tooltip="Test Cetak" />
                                                <flux:button size="sm" variant="ghost" icon="pencil-square"
                                                    wire:click="edit({{ $p->id }})" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="py-10 text-zinc-400 text-center">Data
                                            printer tidak ditemukan.</flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-10">
                                    <flux:checkbox wire:model.live="selectAllAssignments"
                                        wire:click="toggleSelectAllAssignments" />
                                </flux:table.column>
                                <flux:table.column>Lokasi Kiosk</flux:table.column>
                                <flux:table.column>Printer</flux:table.column>
                                <flux:table.column align="end">Aksi</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @forelse ($assignments as $as)
                                    <flux:table.row :key="'row-as-'.$as->id">
                                        <flux:table.cell>
                                            <flux:checkbox wire:model.live="selectedAssignments"
                                                value="{{ $as->id }}" />
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex flex-col">
                                                <span class="font-bold">{{ $as->kiosk_name }}</span>
                                                <span class="text-zinc-500 text-xs italic">{{ $as->client_ip }}</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge size="sm" color="zinc">{{ $as->printerSetting->name }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell align="end">
                                            <div class="flex justify-end gap-1">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square"
                                                    wire:click="editAssignment({{ $as->id }})" />
                                                <flux:button size="sm" variant="ghost" icon="trash"
                                                    wire:click="deleteAssignment({{ $as->id }})" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @empty
                                    <flux:table.row>
                                        <flux:table.cell colspan="4" class="py-10 text-zinc-400 text-center">Belum
                                            ada penugasan pintu.</flux:table.cell>
                                    </flux:table.row>
                                @endforelse
                            </flux:table.rows>
                        </flux:table>
                    @endif
                </flux:card>
            </div>

            {{-- PUSAT BANTUAN KONFIGURASI --}}
            <div class="space-y-6 mt-12">
                <div class="flex items-center gap-2 px-1">
                    <flux:icon.information-circle variant="micro" class="text-zinc-500" />
                    <flux:heading size="lg" class="text-zinc-700 dark:text-zinc-300">Pusat Bantuan Konfigurasi
                    </flux:heading>
                </div>

                {{-- GRID SISTEM: 1 Kolom (HP), 2 Kolom (Tablet), 3 Kolom (Laptop/Desktop) --}}
                <div class="gap-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">

                    {{-- KARTU 1: IP & HOSTNAME --}}
                    <flux:card
                        class="bg-indigo-50/30 dark:bg-indigo-900/10 border-indigo-100 dark:border-indigo-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-indigo-100 dark:bg-indigo-800 p-2 rounded-lg">
                                <flux:icon.magnifying-glass variant="micro"
                                    class="text-indigo-600 dark:text-indigo-300" />
                            </div>
                            <flux:heading size="sm" class="text-indigo-900 dark:text-indigo-100">Cek Alamat
                                Perangkat</flux:heading>
                        </div>
                        <div class="space-y-2 text-[11px] text-indigo-800 dark:text-indigo-300 leading-relaxed">
                            <p><b>Windows:</b> Buka CMD, ketik <code
                                    class="bg-white/60 dark:bg-black/20 px-1 rounded">hostname</code> untuk nama PC
                                atau <code class="bg-white/60 dark:bg-black/20 px-1 rounded">ipconfig</code> untuk IP.
                            </p>
                            <p><b>Printer LAN:</b> Matikan printer, tekan & tahan tombol <b>FEED</b>, lalu nyalakan.
                                Printer akan mencetak status IP-nya.</p>
                        </div>
                    </flux:card>

                    {{-- KARTU 2: SOLUSI DHCP --}}
                    <flux:card class="bg-amber-50/30 dark:bg-amber-900/10 border-amber-100 dark:border-amber-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-amber-100 dark:bg-amber-800 p-2 rounded-lg">
                                <flux:icon.arrow-path variant="micro" class="text-amber-600 dark:text-amber-300" />
                            </div>
                            <flux:heading size="sm" class="text-amber-900 dark:text-amber-100">Tips IP Dinamis
                                (DHCP)</flux:heading>
                        </div>
                        <p class="text-[11px] text-amber-800 dark:text-amber-300 leading-relaxed">
                            Jika IP tablet sering berubah, masukkan <b>Hostname</b> di kolom IP Tablet. Sistem akan
                            mencari perangkat berdasarkan namanya, sehingga koneksi tidak putus saat IP berubah.
                        </p>
                    </flux:card>

                    {{-- KARTU 3: WINDOWS SHARING --}}
                    <flux:card
                        class="bg-emerald-50/30 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-emerald-100 dark:bg-emerald-800 p-2 rounded-lg">
                                <flux:icon.share variant="micro" class="text-emerald-600 dark:text-emerald-300" />
                            </div>
                            <flux:heading size="sm" class="text-emerald-900 dark:text-emerald-100">Sharing
                                Printer Windows</flux:heading>
                        </div>
                        <div class="text-[11px] text-emerald-800 dark:text-emerald-300 leading-relaxed">
                            Format alamat: <code
                                class="bg-white/60 dark:bg-black/20 px-1 rounded">\\NAMA-PC\NamaPrinter</code>.
                            Pastikan <i>"Turn on file and printer sharing"</i> aktif di Network Settings Windows.
                        </div>
                    </flux:card>

                    {{-- KARTU 4: LINUX / CUPS --}}
                    <flux:card
                        class="bg-purple-50/30 dark:bg-purple-900/10 border-purple-100 dark:border-purple-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-purple-100 dark:bg-purple-800 p-2 rounded-lg">
                                <flux:icon.command-line variant="micro"
                                    class="text-purple-600 dark:text-purple-300" />
                            </div>
                            <flux:heading size="sm" class="text-purple-900 dark:text-purple-100">Konfigurasi
                                Linux (CUPS)</flux:heading>
                        </div>
                        <div class="space-y-2 text-[11px] text-purple-800 dark:text-purple-300 leading-relaxed">
                            <p>Gunakan <code class="bg-white/60 dark:bg-black/20 px-1 rounded">lpstat -p</code> untuk
                                melihat nama antrean. Jalankan perintah berikut agar PHP punya izin akses:</p>
                            <code class="block bg-black/20 p-1 rounded text-[10px]">sudo usermod -a -G lpadmin
                                www-data</code>
                        </div>
                    </flux:card>

                    {{-- KARTU 5: FIREWALL & PORT --}}
                    <flux:card class="bg-rose-50/30 dark:bg-rose-900/10 border-rose-100 dark:border-rose-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-rose-100 dark:bg-rose-800 p-2 rounded-lg">
                                <flux:icon.shield-check variant="micro" class="text-rose-600 dark:text-rose-300" />
                            </div>
                            <flux:heading size="sm" class="text-rose-900 dark:text-rose-100">Firewall & Port
                            </flux:heading>
                        </div>
                        <p class="text-[11px] text-rose-800 dark:text-rose-300 leading-relaxed">
                            Printer Network bekerja di <b>Port 9100</b>. Pastikan port ini tidak diblokir oleh Firewall
                            router atau antivirus agar status printer bisa terbaca <b>ONLINE</b>.
                        </p>
                    </flux:card>

                    {{-- KARTU 6: STATUS MONITORING --}}
                    <flux:card class="bg-blue-50/30 dark:bg-blue-900/10 border-blue-100 dark:border-blue-800/50">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-blue-100 dark:bg-blue-800 p-2 rounded-lg">
                                <flux:icon.check-circle variant="micro" class="text-blue-600 dark:text-blue-300" />
                            </div>
                            <flux:heading size="sm" class="text-blue-900 dark:text-blue-100">Status Online
                            </flux:heading>
                        </div>
                        <p class="text-[11px] text-blue-800 dark:text-blue-300 leading-relaxed">
                            Sistem mengecek koneksi setiap <b>30 detik</b>. Jika printer mati, status akan berubah merah
                            (OFFLINE). Gunakan tombol <b>Test Print</b> untuk verifikasi instan.
                        </p>
                    </flux:card>

                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        // SWAL Handler untuk semua aksi hapus
        $wire.on('confirm-bulk-delete', (data) => {
            const payload = Array.isArray(data) ? data[0] : data;

            Swal.fire({
                title: `Hapus ${payload.count} data?`,
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
                    if (payload.id) {
                        $wire[payload.action](payload.id);
                    } else {
                        $wire[payload.action]();
                    }
                }
            })
        });
    </script>
@endscript
