<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Alpha Organizer</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bg-dark:      #0d1117;
            --bg-panel:     #111827;
            --bg-left:      #0b1628;
            --bg-input:     #1a2233;
            --border-color: #2a3448;
            --text-primary: #f0f4ff;
            --text-secondary:#8a99b3;
            --text-muted:   #5a6882;
            --accent:       #0ecab4;
            --accent-hover: #0db8a4;
            --accent-dark:  #0a8f82;
            --error:        #ef4444;
        }

        html { height: 100%; }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            height: 100%;
            display: flex;
            overflow: hidden;
        }

        /* â”€â”€ LEFT PANEL â”€â”€ */
        .left-panel {
            width: 42%;
            height: 100vh;
            min-height: 100vh;
            background: linear-gradient(145deg, #091220 0%, #0d1f35 40%, #0b2240 70%, #082030 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .logo-box{
            background: transparent;
            border-radius: 0;
            width: auto;
            height: auto;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            box-shadow: none;
            padding: 0;
            margin-bottom: 30px;
        }

        .logo-box img{
            width:120px;
            height:auto;
            display:block;
        }

        .logo-box .logo-text {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
        }

        .logo-box .logo-text span {
            font-size: 11px;
            font-weight: 500;
            color: var(--accent);
            display: block;
            letter-spacing: 2px;
        }

        .left-content{
            flex:1;
            display:flex;
            flex-direction:column;
            justify-content:center;
            position:relative;
            z-index:1;

            transform:translateY(-40px);
        }

        .left-content .eyebrow {
            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .left-content h1 {
            font-size: 35px;
            font-weight: 800;
            line-height: 1.15;
            color: var(--text-primary);
            letter-spacing: -1px;
            margin-bottom: 30px;
        }

        .left-content h1 span {
            color: var(--accent);
        }

        .left-content p {
            font-size: 15.5px;
            color: var(--text-secondary);
            line-height: 1.8;
            max-width: 380px;
        }

        .steps-list {
            margin-top: 56px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }

        .step-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: rgba(14,202,180,0.12);
            border: 1px solid rgba(14,202,180,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 18px;
            flex-shrink: 0;
        }

        .step-text {
            font-size: 13.5px;
            color: var(--text-secondary);
            line-height: 1.55;
            padding-top: 8px;
        }

        .step-text strong {
            color: var(--text-primary);
            display: block;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .left-footer {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* â”€â”€ RIGHT PANEL â”€â”€ */
        .right-panel {
            margin-left: 42%;
            width: 58%;
            min-height: 100vh;
            background: var(--bg-panel);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 60px;
            overflow-y: auto;
        }

        .form-wrapper {
            width: 100%;
            transform: translateY(-15px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 36px;
            transition: color 0.2s;
        }

        .back-link:hover { color: var(--accent); }

        .form-wrapper h2 {
            font-size: 34px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-wrapper .subtitle {
            font-size: 14.5px;
            color: var(--text-secondary);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* Alerts */
        .auth-alert {
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .auth-alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            color: #fca5a5;
        }

        .auth-alert-success {
            background: rgba(14,202,180,0.1);
            border: 1px solid rgba(14,202,180,0.3);
            color: #5eead4;
        }

        .auth-alert i {
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: 0.1px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .form-control-custom {
            width: 100%;
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 13px 46px;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14.5px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
            height:54px;
        }

        .form-control-custom::placeholder {
            color: var(--text-muted);
        }

        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14,202,180,0.12);
        }

        .form-control-custom:focus ~ .input-icon,
        .input-wrap:focus-within .input-icon {
            color: var(--accent);
        }

        .form-control-custom.is-invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        .invalid-feedback-custom {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Button */
        .btn-primary-custom {
            width: 100%;
            padding: 14px;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
            height:56px;
        }

        .btn-primary-custom:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(14,202,180,0.3);
        }

        .btn-primary-custom:active {
            transform: translateY(0);
        }

        .btn-primary-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel  { display: none; }
            .right-panel { width: 100%; margin-left: 0; padding: 40px 24px; }
        }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="logo-box">
        <img src="{{ asset('images/Logo-bg.png') }}" alt="Alpha Organizer"
             onerror="this.style.display='none';">
    </div>

    <div class="left-content">
        <p class="eyebrow">Keamanan Akun</p>
        <h1>Atur Ulang <span>Kata Sandi</span> Anda</h1>
        <p>Masukkan alamat email terdaftar dan kami akan segera mengirimkan tautan untuk mereset kata sandi Anda.</p>

        <div class="steps-list">
            <div class="step-item">
                <div class="step-icon"><i class="bi bi-envelope-at"></i></div>
                <div class="step-text">
                    <strong>Masukkan Email</strong>
                    Email yang terdaftar di akun Alpha Organizer Anda.
                </div>
            </div>
            <div class="step-item">
                <div class="step-icon"><i class="bi bi-send"></i></div>
                <div class="step-text">
                    <strong>Cek Kotak Masuk</strong>
                    Kami akan mengirim tautan reset dalam beberapa detik.
                </div>
            </div>
            <div class="step-item">
                <div class="step-icon"><i class="bi bi-shield-lock"></i></div>
                <div class="step-text">
                    <strong>Buat Password Baru</strong>
                    Klik tautan dan buat kata sandi yang aman dan baru.
                </div>
            </div>
        </div>
    </div>

    <div class="left-footer">
        &copy; {{ date('Y') }} Alpha Organizer. All rights reserved.
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="form-wrapper">

        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Login
        </a>

        <h2>Lupa Password?</h2>
        <p class="subtitle">
            Tidak perlu khawatir. Masukkan email Anda dan kami akan mengirimkan tautan reset password.
        </p>

        {{-- Pesan sukses setelah email berhasil dikirim --}}
        @if (session('status'))
            <div class="auth-alert auth-alert-success" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    <strong style="display:block; margin-bottom:3px;">Email Berhasil Dikirim!</strong>
                    {{ session('status') }} Silakan cek folder Inbox atau Spam Anda.
                </div>
            </div>
        @endif

        {{-- Pesan error umum --}}
        @if ($errors->any() && !$errors->has('email'))
            <div class="auth-alert auth-alert-error" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan email yang terdaftar"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                @error('email')
                    <div class="invalid-feedback-custom">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn-primary-custom" id="submitBtn">
                <i class="bi bi-send"></i>
                Kirim Link Reset Password
            </button>
        </form>

    </div>
</div>

<script>
    // Disable button saat submit untuk mencegah double-click
    document.getElementById('forgotForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mengirim...';
    });
</script>

</body>
</html>
