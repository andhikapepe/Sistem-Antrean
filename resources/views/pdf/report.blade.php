<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Antrean</title>
    <style>
        /* Konfigurasi Font & Margin */
        @page { margin: 1cm; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px !important; /* Ukuran font utama */
            color: #333;
            line-height: 1.4;
        }

        /* Header Laporan */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1.5pt solid #444;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 16px !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0;
            font-size: 9px !important;
            color: #555;
        }

        /* Style Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed; /* Penting agar kolom tidak meluber */
        }

        th {
            background-color: #f2f2f2;
            color: #000;
            font-weight: bold;
            font-size: 9px !important;
            padding: 7px 4px;
            border: 0.5pt solid #999;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 6px 4px;
            border: 0.5pt solid #ccc;
            font-size: 9px !important;
            word-wrap: break-word;
            vertical-align: middle;
        }

        tr:nth-child(even) { background-color: #fafafa; }

        /* Pengaturan Lebar Kolom (Total 100%) */
        .col-no { width: 10%; }
        .col-cat { width: 18%; }
        .col-unit { width: 15%; }
        .col-staff { width: 18%; }
        .col-status { width: 12%; }
        .col-time { width: 15%; }
        .col-wait { width: 12%; }

        /* Status Badge Style */
        .status-completed { color: #059669; font-weight: bold; }
        .status-skipped { color: #dc2626; font-weight: bold; }

        /* Kotak Rekapitulasi */
        .rekap-container {
            margin-top: 20px;
            width: 100%;
            border: 1pt solid #ccc;
            background-color: #fdfdfd;
        }
        .rekap-title {
            background-color: #eee;
            padding: 5px 10px;
            font-weight: bold;
            border-bottom: 1pt solid #ccc;
            font-size: 10px !important;
        }
        .rekap-body {
            padding: 10px;
        }
        .rekap-body table { margin: 0; }
        .rekap-body td { border: none !important; padding: 2px 0; background: none !important; }

        /* Footer Halaman */
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: right;
            font-size: 8px !important;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Antrean Harian</h2>
        <p>Unit Kerja: Seluruh Unit | Tanggal: {{ $date }}</p>
        <p>Dicetak otomatis oleh Sistem pada {{ now()->format('H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">Nomor</th>
                <th class="col-cat">Kategori</th>
                <th class="col-unit">Unit</th>
                <th class="col-staff">Petugas</th>
                <th class="col-status">Status</th>
                <th class="col-time">Waktu Ambil</th>
                <th class="col-wait">Tunggu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($queues as $q)
            <tr>
                <td style="font-weight: bold;">{{ $q->ticket_number }}</td>
                <td>{{ $q->category->name }}</td>
                <td>{{ $q->serviceUnit->name ?? '-' }}</td>
                <td>{{ $q->serviceUnit->currentUser->name ?? '-' }}</td>
                <td class="{{ $q->status == 'completed' ? 'status-completed' : ($q->status == 'skipped' ? 'status-skipped' : '') }}">
                    {{ strtoupper($q->status) }}
                </td>
                <td>{{ $q->created_at->format('H:i:s') }}</td>
                <td>{{ $q->called_at ? round($q->created_at->diffInMinutes($q->called_at)) : 0 }} mnt</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="rekap-container">
        <div class="rekap-title">RINGKASAN PELAYANAN</div>
        <div class="rekap-body">
            <table>
                <tr>
                    <td width="30%">Total Antrean: <strong>{{ $queues->count() }}</strong></td>
                    <td width="35%">Selesai Dilayani: <strong>{{ $queues->where('status', 'completed')->count() }}</strong></td>
                    <td width="35%">Rata-rata Tunggu: <strong>{{ round($queues->whereNotNull('called_at')->avg(fn($q) => $q->created_at->diffInMinutes($q->called_at))) }} Menit</strong></td>
                </tr>
                <tr>
                    <td>Dilewati (Skipped): <strong>{{ $queues->where('status', 'skipped')->count() }}</strong></td>
                    <td>Menunggu: <strong>{{ $queues->where('status', 'waiting')->count() }}</strong></td>
                    <td>Petugas Aktif: <strong>{{ $queues->unique('service_unit_id')->count() }} Unit</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Halaman 1 dari 1 - Sistem Antrean Digital v1.0
    </div>
</body>
</html>
