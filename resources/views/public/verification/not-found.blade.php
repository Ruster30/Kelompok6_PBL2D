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
                            <i class="bi bi-search" style="font-size: 64px; color: #94a3b8;"></i>
                        </div>
                        <h1 class="card-title h3 fw-bold mb-2" style="color: #0f172a;">{{ $message }}</h1>
                        <p class="text-muted mb-4">{{ $detail }}</p>
                        <p class="text-muted small mt-4 mb-0">
                            Token verifikasi tidak valid atau tidak terdaftar dalam sistem.<br>
                            CV. Alpha Multi Organizer
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>