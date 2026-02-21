<!doctype html>
<html>
<head>
    <style>
        * {
            font-size: 12px;
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
        }
        .ticket {
            width: 155px;
            text-align: center;
            background: white;
            color: black;
        }
        .centered { text-align: center; }
        .big-number {
            font-size: 42px;
            font-weight: bold;
            line-height: 1;
            margin: 8px 0;
        }
        /* Style untuk kotak border kategori */
        .category-border {
            border: 2px solid black;
            padding: 5px 2px;
            margin: 5px auto;
            width: 90%;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }
        .line { border-top: 1px dashed black; margin: 5px 0; }
        @media print { @page { margin: 0; size: auto; } }
    </style>
</head>
<body>
    <div class="ticket">
        <p><strong>[APP_NAME]</strong><br><span style="font-size: 10px;">[DATE]</span></p>

        <div class="line"></div>

        <p style="font-size: 10px;">NOMOR ANTREAN</p>
        <div class="big-number">[TICKET]</div>

        <div class="category-border">
            [CATEGORY]
        </div>

        <div class="line"></div>

        <p style="margin-top: 8px; font-size: 10px;">Harap tunggu dipanggil.<br><strong>TERIMA KASIH</strong></p>

        <div style="height: 100px;"></div>
    </div>
</body>
</html>
