<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judul }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .header  { background: linear-gradient(135deg, #0f172a, #134e4a); padding: 32px 36px; }
        .brand   { color: #2dd4bf; font-size: 20px; font-weight: 700; letter-spacing: 1px; }
        .body    { padding: 32px 36px; }
        .greeting { font-size: 15px; color: #64748b; margin-bottom: 20px; }
        .title   { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
        .message { font-size: 14px; color: #374151; line-height: 1.7; background: #f8fafc; border-radius: 10px; padding: 18px; border-left: 4px solid #14b8a6; }
        .footer  { padding: 20px 36px; border-top: 1px solid #f1f5f9; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="brand">Alpha Organizer</div>
        <div style="color:#94a3b8;font-size:13px;margin-top:4px;">CV. Alpha Multi Organizer</div>
    </div>
    <div class="body">
        <p class="greeting">Halo, <strong>{{ $recipient->name }}</strong>!</p>
        <p class="title">{{ $judul }}</p>
        <div class="message">{{ $pesan }}</div>
        <p style="font-size:13px;color:#64748b;margin-top:20px;">
            Pesan ini dikirim oleh tim Alpha Organizer. Jika ada pertanyaan, silakan hubungi kami melalui dashboard klien Anda.
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} CV. Alpha Multi Organizer &bull; Semua hak dilindungi.
    </div>
</div>
</body>
</html>
