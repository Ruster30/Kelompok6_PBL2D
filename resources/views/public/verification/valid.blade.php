<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle" style="font-size: 64px; color: #16a34a;"></i>
                        </div>
                        <h1 class="card-title h3 fw-bold mb-2" style="color: #0f172a;">Dokumen Terverifikasi</h1>
                        <p class="text-muted mb-4">Dokumen ini telah dipublikasikan dan terverifikasi resmi oleh sistem DDMS.</p>
                        <div class="alert alert-light border-1" style="text-align: left;">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="fw-semibold" style="width: 40%; color: #475569;">Nomor Surat</td>
                                    <td style="color: #0f172a;">{{ $document->numbering->document_number }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Jenis Dokumen</td>
                                    <td style="color: #0f172a;">{{ $document->tipe_label }}</td>
                                </tr>
                                @if($document->event)
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Event</td>
                                    <td style="color: #0f172a;">{{ $document->event->nama_event }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Client</td>
                                    <td style="color: #0f172a;">{{ $document->event->client->name ?? '-' }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Tanggal Publikasi</td>
                                    <td style="color: #0f172a;">{{ $document->updated_at->format('d M Y') }}</td>
                                </tr>
                                @if($approval)
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Disetujui Oleh</td>
                                    <td style="color: #0f172a;">{{ $approval->approvedBy->name ?? '-' }} (Director)</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-semibold" style="color: #475569;">Status</td>
                                    <td><span class="badge bg-success">Published</span></td>
                                </tr>
                            </table>
                        </div>
                        <p class="text-muted small mt-4 mb-0">
                            Verifikasi dilakukan melalui sistem DDMS (Digital Document Management System)<br>
                            CV. Alpha Multi Organizer
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>