<?php

namespace App\Livewire;

use App\Models\Queue;
use App\Models\ServiceUnit;
use App\Models\QueueCategory;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Exports\QueueExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class Dashboard extends Component
{
    public function render()
    {
        $today = now()->today();
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        // 1. Statistik Global
        $stats = [
            'total'     => Queue::whereDate('created_at', $today)->count(),
            'waiting'   => Queue::whereDate('created_at', $today)->where('status', 'waiting')->count(),
            'completed' => Queue::whereDate('created_at', $today)->where('status', 'completed')->count(),
            'skipped'   => Queue::whereDate('created_at', $today)->where('status', 'skipped')->count(),
            'avg_wait'  => $this->calculateAvgWait($today, $isSqlite),
        ];

        // 2. Data Unit & Performa per Loket
        $units = ServiceUnit::with(['currentUser'])
            ->withCount([
                'queues as completed_served' => fn($q) => $q->whereDate('created_at', $today)->where('status', 'completed'),
                'queues as skipped_served' => fn($q) => $q->whereDate('created_at', $today)->where('status', 'skipped'),
            ])->get();

        // 3. Data Chart (Per Jam)
        $chartDataRaw = $isSqlite
            ? Queue::whereDate('created_at', $today)->select(DB::raw("strftime('%H', created_at) as hour"), DB::raw('count(*) as count'))->groupBy('hour')->pluck('count', 'hour')->toArray()
            : Queue::whereDate('created_at', $today)->select(DB::raw('HOUR(created_at) as hour'), DB::raw('count(*) as count'))->groupBy('hour')->pluck('count', 'hour')->toArray();

        $chartData = [];
        for ($i = 8; $i <= 17; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartData[$key . ':00'] = $chartDataRaw[$i] ?? $chartDataRaw[$key] ?? 0;
        }

        return view('livewire.dashboard', [
            'stats' => $stats,
            'units' => $units,
            'chartData' => $chartData
        ])->layout('layouts.app');
    }

    private function calculateAvgWait($date, $isSqlite)
    {
        $query = Queue::whereDate('created_at', $date)->whereNotNull('called_at');
        $avg = $isSqlite
            ? $query->select(DB::raw("AVG((julianday(called_at) - julianday(created_at)) * 1440)"))->value(DB::raw("(julianday(called_at) - julianday(created_at)) * 1440"))
            : $query->select(DB::raw("AVG(TIMESTAMPDIFF(MINUTE, created_at, called_at))"))->value(DB::raw("TIMESTAMPDIFF(MINUTE, created_at, called_at)"));
        return round($avg ?? 0) . ' mnt';
    }

    public function exportExcel()
    {
        return Excel::download(new QueueExport, 'Laporan-Antrean-' . now()->format('d-m-Y') . '.xlsx');
    }

    public function exportPdf()
    {
        $today = now()->today();

        // Ambil data antrean lengkap dengan relasi agar tidak ada error di view
        $data = [
            'title' => 'Laporan Antrean Harian',
            'date'  => $today->translatedFormat('d F Y'),
            'queues' => \App\Models\Queue::whereDate('created_at', $today)
                ->with(['category', 'serviceUnit'])
                ->orderBy('created_at', 'asc')
                ->get(),
            // Tambahkan statistik ringkas untuk header PDF
            'stats' => [
                'total' => \App\Models\Queue::whereDate('created_at', $today)->count(),
                'completed' => \App\Models\Queue::whereDate('created_at', $today)->where('status', 'completed')->count(),
            ]
        ];

        // Load view khusus PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', $data);

        // Set kertas ke A4
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan-Antrean-' . $today->format('Y-m-d') . '.pdf');
    }
}
