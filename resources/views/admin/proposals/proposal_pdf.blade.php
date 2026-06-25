<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; line-height: 1.6; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        h2 { font-size: 15px; margin-top: 22px; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; }
        .muted { color: #6b7280; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Proposal Event</h1>
    <div class="muted">{{ $event->nama_event }}</div>

    @if(in_array('profil', $sections))
        <h2>Profil Event</h2>
        <p>
            Proposal ini disusun untuk event <strong>{{ $event->nama_event }}</strong>
            milik {{ $event->client->name ?? 'client' }} yang direncanakan pada
            {{ $event->tanggal_event?->format('d M Y') }} di {{ $event->lokasi_event ?? '-' }}.
        </p>
    @endif

    @if(in_array('kebutuhan', $sections))
        <h2>Kebutuhan Event</h2>
        <p>{{ $event->detail_kebutuhan ?: 'Detail kebutuhan belum diisi.' }}</p>
    @endif

    @if(in_array('rab', $sections))
        <h2>Rencana Anggaran Biaya</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rabItems as $item)
                    <tr>
                        <td>{{ $item->nama_biaya }}</td>
                        <td>{{ $item->kategori_biaya ?? '-' }}</td>
                        <td class="right">{{ $item->jumlah_item }}</td>
                        <td class="right">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="right">Rp {{ number_format($item->subtotal_biaya, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada item RAB.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
