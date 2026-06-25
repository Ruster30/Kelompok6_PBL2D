<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        h1 { font-size: 22px; margin-bottom: 4px; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #e5e7eb; padding: 14px; margin-top: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { border: 1px solid #e5e7eb; padding: 9px; text-align: left; }
        th { background: #f9fafb; }
        .total { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Invoice {{ $invoice->nomor_invoice }}</h1>
    <div class="muted">Tanggal: {{ $invoice->tanggal_invoice?->format('d M Y') }}</div>

    <div class="box">
        <strong>Client</strong><br>
        {{ $invoice->event->client->name ?? '-' }}<br>
        {{ $invoice->event->client->email ?? '' }}
    </div>

    <table>
        <tr>
            <th>Event</th>
            <td>{{ $invoice->event->nama_event ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ $invoice->status_label }}</td>
        </tr>
        <tr>
            <th>Total Invoice</th>
            <td class="total">{{ $invoice->formatted_total }}</td>
        </tr>
    </table>
</body>
</html>
