<div class="space-y-6">
    {{-- HEADER --}}
    <header class="flex justify-between items-center">
        <div>
            <flux:heading size="xl">Selamat Datang, {{ auth()->user()->name }}</flux:heading>
            <flux:subheading size="lg">Ringkasan antrean hari ini, {{ now()->translatedFormat('d F Y') }}.
            </flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:dropdown>
                {{-- Tombol Utama --}}
                <flux:button variant="subtle" icon="document-arrow-down">Export Laporan</flux:button>

                {{-- Menu Dropdown --}}
                <flux:menu>
                    <flux:menu.item wire:click="exportExcel" icon="table-cells">
                        Excel (.xlsx)
                    </flux:menu.item>

                    <flux:menu.item wire:click="exportPdf" icon="document-text">
                        PDF (.pdf)
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </header>

    {{-- STATS CARDS --}}
    <div class="gap-4 grid md:grid-cols-5">
        <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <span class="text-neutral-500 text-sm uppercase">Total</span>
            <div class="mt-2 font-bold text-4xl">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <span class="text-neutral-500 text-sm uppercase">Menunggu</span>
            <div class="mt-2 font-bold text-orange-500 text-4xl">{{ $stats['waiting'] }}</div>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <span class="text-neutral-500 text-sm uppercase">Selesai</span>
            <div class="mt-2 font-bold text-emerald-500 text-4xl">{{ $stats['completed'] }}</div>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <span class="text-neutral-500 text-sm uppercase">Dilewati</span>
            <div class="mt-2 font-bold text-rose-500 text-4xl">{{ $stats['skipped'] }}</div>
        </div>
        <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl">
            <span class="text-neutral-500 text-sm uppercase text-nowrap">Rata Tunggu</span>
            <div class="mt-2 font-bold text-indigo-500 text-4xl">{{ $stats['avg_wait'] }}</div>
        </div>
    </div>

    {{-- CHART --}}
    <div class="bg-white dark:bg-neutral-900 p-6 border border-neutral-200 dark:border-neutral-700 rounded-xl"
        wire:ignore>
        <flux:heading size="lg" class="mb-4">Tren Antrean Per Jam</flux:heading>
        <div class="h-64">
            <canvas id="queueChart"></canvas>
        </div>
    </div>

    {{-- TABEL MONITOR LOKET --}}
    <div
        class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-700 rounded-xl overflow-hidden">
        <div class="p-6 border-neutral-200 dark:border-neutral-700 border-b">
            <flux:heading size="lg">Monitor Loket Real-time</flux:heading>
        </div>
        <div class="p-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Unit Kerja</flux:table.column>
                    <flux:table.column>Petugas</flux:table.column>
                    <flux:table.column>Selesai / Skip</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Nomor Saat Ini</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($units as $unit)
                        <flux:table.row>
                            <flux:table.cell class="font-bold">{{ $unit->name }}</flux:table.cell>
                            <flux:table.cell>{{ $unit->currentUser->name ?? '---' }}</flux:table.cell>
                            <flux:table.cell>
                                <span class="font-bold text-emerald-600">{{ $unit->completed_served }}</span> /
                                <span class="font-bold text-rose-500">{{ $unit->skipped_served }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$unit->is_occupied ? 'emerald' : 'zinc'">
                                    {{ $unit->is_occupied ? 'ONLINE' : 'OFFLINE' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-mono font-bold text-indigo-600 text-lg">
                                {{ $unit->queues()->where('status', 'calling')->first()->ticket_number ?? '---' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>

{{-- CHART JS SCRIPT --}}
@script
    <script>
        document.addEventListener('livewire:navigated', () => {
            const ctx = document.getElementById('queueChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @js(array_keys($chartData)),
                        datasets: [{
                            label: 'Jumlah Tiket',
                            data: @js(array_values($chartData)),
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
@endscript
