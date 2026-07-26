<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1e293b; }
  .page { padding: 40px; }
  .header { text-align: center; padding-bottom: 20px; border-bottom: 3px solid #4338ca; margin-bottom: 24px; }
  .header h1 { font-size: 22px; color: #4338ca; margin-bottom: 4px; }
  .header .sub { color: #64748b; font-size: 13px; }
  .stamp-box { 
    border: 3px solid #22c55e; border-radius: 12px; padding: 30px; 
    text-align: center; margin-bottom: 24px; 
    background: #f0fdf4;
  }
  .stamp-box .approved { 
    font-size: 28px; font-weight: 900; color: #16a34a; 
    text-transform: uppercase; letter-spacing: 4px; 
    border: 3px double #16a34a; display: inline-block; 
    padding: 8px 32px; border-radius: 8px; margin-bottom: 12px;
  }
  .info-table { width: 100%; margin: 16px 0; }
  .info-table td { padding: 6px 12px; border-bottom: 1px solid #e2e8f0; }
  .info-table td:first-child { width: 160px; color: #64748b; }
  .info-table td:last-child { font-weight: 600; }
  .qr-section { text-align: center; margin: 24px 0; }
  .qr-section img { width: 160px; height: 160px; }
  .footer { text-align: center; color: #94a3b8; font-size: 10px; margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <h1>CV. Alpha Multi Organizer</h1>
    <div class="sub">Dokumen Resmi Perusahaan — Final & Approved</div>
  </div>

  <div class="stamp-box">
    <div class="approved">? APPROVED</div>
    <p style="color:#16a34a;font-weight:600;font-size:14px;">Dokumen telah disetujui dan diterbitkan secara resmi</p>
  </div>

  <table class="info-table">
    @if($document->numbering)
    <tr><td>Nomor Dokumen</td><td>{{ $document->numbering->document_number }}</td></tr>
    @endif
    <tr><td>Nama Dokumen</td><td>{{ $document->nama_file }}</td></tr>
    <tr><td>Jenis Dokumen</td><td>{{ $document->tipe_label }}</td></tr>
    <tr><td>Tanggal Terbit</td><td>{{ now()->format("d F Y") }}</td></tr>
    <tr><td>Status</td><td style="color:#16a34a;font-weight:700;">APPROVED</td></tr>
    @if($document->event)
    <tr><td>Event</td><td>{{ $document->event->nama_event }}</td></tr>
    <tr><td>Client</td><td>{{ $document->event->client?->name ?? "-" }}</td></tr>
    @endif
  </table>

  @if($document->qrVerification && $document->qrVerification->qr_path)
  <div class="qr-section">
    <p style="font-weight:600;margin-bottom:8px;">QR Code Verifikasi</p>
    <img src="{{ storage_path("app/public/" . $document->qrVerification->qr_path) }}" alt="QR">
    <p style="color:#64748b;font-size:11px;margin-top:6px;">Scan untuk memverifikasi keaslian dokumen</p>
  </div>
  @endif

  <div class="footer">
    Dokumen ini diterbitkan oleh CV. Alpha Multi Organizer melalui sistem DDMS.<br>
    Document ID: {{ $document->id }} | Dicetak pada: {{ now()->format("d M Y H:i:s") }}
  </div>
</div>
</body>
</html>