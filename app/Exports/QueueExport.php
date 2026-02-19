<?php

namespace App\Exports;

use App\Models\Queue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class QueueExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    /**
     * Mengambil data antrean hari ini
     */
    public function collection()
    {
        return Queue::with(['category', 'serviceUnit'])
            ->whereDate('created_at', now()->today())
            ->get();
    }

    /**
     * Mengatur Judul Kolom di Excel
     */
    public function headings(): array
    {
        return [
            'Nomor Antrean',
            'Kategori',
            'Loket / Unit',
            'Petugas', // Tambahan
            'Status',
            'Waktu Ambil',
            'Durasi Tunggu (Menit)', // Tambahan
        ];
    }

    /**
     * Memetakan data agar rapi di Excel
     */
    public function map($queue): array
    {
        // Hitung durasi tunggu dalam menit
        $waitTime = $queue->called_at
            ? round($queue->created_at->diffInMinutes($queue->called_at))
            : 0;

        return [
            $queue->ticket_number,
            $queue->category->name ?? '-',
            $queue->serviceUnit->name ?? '-',
            $queue->serviceUnit->currentUser->name ?? '-', // Nama petugas yang login saat itu
            strtoupper($queue->status),
            $queue->created_at->format('H:i:s'),
            $waitTime . ' mnt',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $event->sheet->getHighestRow() + 1;
                $event->sheet->setCellValue("A{$lastRow}", 'TOTAL DATA: ' . ($lastRow - 2));
                $event->sheet->getStyle("A{$lastRow}")->getFont()->setBold(true);
            },
        ];
    }
}
