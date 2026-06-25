<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.7;
            color: #111827;
            padding: 30px 40px;
        }

        /* Kop Surat */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #14b8a6;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .kop-left  { display: table-cell; width: 60%; vertical-align: middle; }
        .kop-right { display: table-cell; width: 40%; vertical-align: middle; text-align: right; font-size: 10.5px; color: #475569; line-height: 1.5; }
        .logo-box {
            display: inline-block;
            background: #14b8a6;
            color: white;
            font-size: 20px;
            font-weight: 900;
            width: 44px;
            height: 44px;
            text-align: center;
            line-height: 44px;
            border-radius: 6px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .company-name { font-size: 16px; font-weight: 900; color: #0f172a; }
        .company-sub  { font-size: 10.5px; color: #64748b; }

        /* Info Surat */
        .info-surat { margin-bottom: 16px; }
        .info-surat table { border: none; font-size: 12px; }
        .info-surat td { padding: 2px 0; border: none; }
        .underline { border-bottom: 1px solid #666; padding: 0 40px; }

        /* Kepada */
        .kepada { margin-bottom: 14px; }

        /* Isi */
        p { margin-bottom: 10px; text-align: justify; }

        /* Tabel Detail */
        .detail-table { width: 90%; margin: 14px auto; border-collapse: collapse; font-size: 12px; }
        .detail-table td { padding: 7px 12px; border: 1px solid #d1d5db; }
        .detail-table tr:nth-child(even) td { background: #f8fafc; }

        /* List */
        ul { margin: 6px 0 12px 20px; }
        li { margin-bottom: 3px; }

        /* TTD */
        .ttd { text-align: right; margin-top: 36px; }
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: 700; border-top: 1px solid #374151; display: inline-block; padding-top: 4px; min-width: 120px; }
    </style>
</head>
<body>

    {{-- KOP --}}
    <div class="kop">
        <div class="kop-left">
            <span class="logo-box">α</span>
            <span>
                <div class="company-name">CV. ALPHA MULTI ORGANIZER</div>
                <div class="company-sub">Event Management &amp; Production</div>
            </span>
        </div>
        <div class="kop-right">
            Jl. Kenangan Air Dingin No.25, Padang<br>
            Telp: +62 812-3456-7890<br>
            Email: hello@alphacorp.events
        </div>
    </div>

    {{-- INFO SURAT --}}
    <div class="info-surat">
        <table style="width:100%;">
            <tr>
                <td style="width:28%;">No. Surat</td>
                <td style="width:2%;">:</td>
                <td style="width:35%; border-bottom:1px solid #666; padding-bottom:1px;">{{ $data['nomor_surat'] }}</td>
                <td style="padding-left:16px;">
                    Padang, <span class="underline">{{ \Carbon\Carbon::parse($data['tanggal_surat'])->translatedFormat('d F Y') }}</span>
                </td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><strong>Surat Penawaran Event</strong></td>
                <td></td>
            </tr>
        </table>
    </div>

    {{-- KEPADA --}}
    <div class="kepada">
        <div>Kepada Yth.</div>
        <div><strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong></div>
        <div>Di,</div>
        <div style="margin-left:16px;">Tempat</div>
    </div>

    {{-- PEMBUKA --}}
    <p>Dengan hormat,</p>
    <p>
        Kami dari CV. Alpha Multi Organizer, perusahaan yang bergerak di bidang Event Organizer.
        Berdasarkan permintaan yang telah diajukan oleh pihak Bapak/Ibu, kami menawarkan kegiatan
        event dengan detail sebagai berikut:
    </p>

    {{-- DETAIL EVENT --}}
    <table class="detail-table">
        <tr>
            <td style="width:38%;"><strong>I. Nama Event</strong></td>
            <td>: {{ $event->nama_event }}</td>
        </tr>
        <tr>
            <td><strong>II. Jenis Kegiatan</strong></td>
            <td>: {{ $event->jenis_event ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>III. Lokasi</strong></td>
            <td>: {{ $event->lokasi_event ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>IV. Jadwal</strong></td>
            <td>: {{ $event->tanggal_event?->format('Y-m-d') ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>V. Estimasi Tamu</strong></td>
            <td>: {{ $event->jumlah_tamu ? $event->jumlah_tamu . ' Orang' : '-' }}</td>
        </tr>
        <tr>
            <td><strong>VI. Estimasi Anggaran</strong></td>
            <td><strong>: {{ $event->rentang_anggaran ?? '-' }}</strong></td>
        </tr>
        @if($event->detail_kebutuhan)
        <tr>
            <td style="vertical-align:top;"><strong>VII. Deskripsi Kebutuhan</strong></td>
            <td>: {{ $event->detail_kebutuhan }}</td>
        </tr>
        @endif
    </table>

    {{-- FASILITAS --}}
    <p><strong>VIII. Fasilitas Standar:</strong></p>
    <ul>
        <li>Manajemen acara penuh (Full Event Management)</li>
        <li>Koordinasi vendor dan logistik</li>
        <li>Setup dan dekorasi standar sesuai tema</li>
        <li>Tim lapangan profesional selama acara berlangsung</li>
    </ul>

    {{-- KETENTUAN --}}
    <p><strong>IX. Ketentuan:</strong></p>
    <ul>
        <li>Pembayaran dilakukan melalui transfer bank ke rekening CV. Alpha Multi Organizer.</li>
        <li>Ketentuan event mengikuti aturan standar dari Alpha Organizer.</li>
        <li>Biaya di atas merupakan estimasi awal dan dapat disesuaikan kembali setelah diskusi lebih lanjut.</li>
        <li>Biaya belum termasuk pajak (jika ada).</li>
    </ul>

    {{-- PENUTUP --}}
    <p>
        Apabila ada hal yang ingin ditanyakan atau didiskusikan lebih lanjut mengenai penawaran ini,
        silakan menghubungi kami melalui telepon atau email yang tertera di atas.
    </p>
    <p>Demikian surat penawaran ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>

    {{-- TTD --}}
    <div class="ttd">
        <div>Hormat kami,</div>
        <div class="ttd-space"></div>
        <div>
            <strong>{{ auth()->user()->name ?? 'Admin' }}</strong><br>
            <span style="font-size:11px; color:#64748b;">Direktur</span>
        </div>
    </div>

</body>
</html>
