<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Alpha Organizer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-dark: #0d1117;
            --bg-panel: #111827;
            --bg-left: #0b1628;
            --bg-input: #1a2233;
            --border-color: #2a3448;
            --text-primary: #f0f4ff;
            --text-secondary: #8a99b3;
            --text-muted: #5a6882;
            --accent: #0ecab4;
            --accent-hover: #0db8a4;
            --error: #ef4444;
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

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 40%;
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
            overflow: hidden;
            flex-shrink: 0;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -60px;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(14,202,180,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -40px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(14,202,180,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Tombol Kembali */
        .back-login{
            margin-bottom:24px;
        }

        .back-login a{
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:var(--text-secondary);
            text-decoration:none;
            font-size:14px;
            font-weight:600;
            transition:.25s;
        }

        .back-login a:hover{
            color:var(--accent);
        }

        .logo-box {
            background: transparent;
            border-radius: 0;
            width: auto;
            height: auto;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            box-shadow: none;
            position: relative;
            z-index: 1;
            padding: 0;
        }

        .logo-box img {
            width: 120px;
            height: auto;
            display: block;
        }

        .logo-box .logo-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 13px;
            color: var(--accent);
            letter-spacing: 0.5px;
            text-align: center;
            line-height: 1.2;
        }

        .left-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;

            transform: translateY(-70px);
        }

        .left-content h1 {
            font-size: clamp(24px, 2.6vw, 38px);
            font-weight: 800;
            line-height: 1.15;
            color: var(--text-primary);
            margin-bottom: 28px;
            letter-spacing: -0.5px;
        }

        .feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .feature-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .feature-icon {
            width: 30px;
            height: 30px;
            background: rgba(14,202,180,0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 14px;
            color: var(--accent);
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 60%;
            min-height: 100vh;
            background: var(--bg-panel);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 70px 56px 50px;
            margin-left: 40%;
            overflow-y: auto;
        }

        .form-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 20px 0 40px;
        }

        .form-wrapper h2 {
            font-size: 30px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .form-wrapper .subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        /* Error Alert */
        .auth-alert {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #fca5a5;
            font-size: 13.5px;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 7px;
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
            font-size: 15px;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-wrap:focus-within .input-icon {
            color: var(--accent);
        }

        .form-control-custom {
            width: 100%;
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 12px 46px 12px 46px;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-control-custom::placeholder {
            color: var(--text-muted);
        }

        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14,202,180,0.12);
        }

        .form-control-custom.is-invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        .invalid-feedback-custom {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Password strength */
        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 3px;
            border-radius: 2px;
            background: var(--border-color);
            overflow: hidden;
            margin-bottom: 4px;
        }

        .strength-bar-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.3s, background 0.3s;
            width: 0%;
        }

        .strength-label {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Phone prefix */
        .phone-input-group {
            display: flex;
            gap: 0;
        }

        .phone-prefix {
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-right: none;
            border-radius: 10px 0 0 10px;
            padding: 12px 14px;
            color: var(--text-secondary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .phone-prefix img { width: 20px; }

        .phone-input-group .form-control-custom {
            border-radius: 0 10px 10px 0;
            padding-left: 16px;
        }

        /* Password toggle */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 15px;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: var(--text-secondary); }

        /* Terms checkbox */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
            margin-top: 4px;
        }

        .terms-row input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--accent);
            border-radius: 4px;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .terms-row span {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .terms-row a {
            color: var(--accent);
            font-weight: 600;
            text-decoration: none;
        }

        .terms-row a:hover { text-decoration: underline; }

        /* Button */
        .btn-primary-custom {
            width: 100%;
            background: var(--accent);
            border: none;
            border-radius: 10px;
            padding: 14px;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(14,202,180,0.25);
            letter-spacing: 0.2px;
        }

        .btn-primary-custom:hover {
            background: var(--accent-hover);
            box-shadow: 0 6px 28px rgba(14,202,180,0.35);
            transform: translateY(-1px);
        }

        .btn-primary-custom:active { transform: translateY(0); }

        /* Footer link */
        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover { text-decoration: underline; }

        /* Two-column grid for name/phone or password */
        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; margin-left: 0; padding: 40px 24px; }
            .form-row-2col { grid-template-columns: 1fr; }
        }

        @media (max-width: 480px) {
            .right-panel { padding: 32px 20px; }
        }
    </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="back-login">
        <a href="{{ route('login') }}">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Login
        </a>
    </div>
    <div class="logo-box">
        <img src="{{ asset('images/Logo-bg.png') }}" alt="Alpha Organizer"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
        <div class="logo-text" style="display:none;">ALPHA<br><span style="font-size:8px;color:#aaa;font-weight:500;">ORGANIZER</span></div>
    </div>

    <div class="left-content">
        <h1>Mulai Perjalanan Anda Bersama Kami.</h1>
        <ul class="feature-list">
            <li>
                <div class="feature-icon"><i class="bi bi-calendar-check"></i></div>
                Akses alat perencanaan event premium
            </li>
            <li>
                <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                Pelacakan anggaran transparan
            </li>
            <li>
                <div class="feature-icon"><i class="bi bi-people"></i></div>
                Kolaborasi waktu nyata
            </li>
        </ul>
    </div>

    <div style="position: relative; z-index:1;">
        <!-- Placeholder bottom area -->
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="form-wrapper">
        <h2>Buat akun</h2>
        <p class="subtitle">Bergabung dengan ALPHA.CORP untuk mulai<br>merencanakan event besar Anda berikutnya.</p>

        @if ($errors->any())
        <div class="auth-alert">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control-custom {{ $errors->has('name') ? 'is-invalid' : '' }}"
                        placeholder="Budi Santoso"
                        value="{{ old('name') }}"
                        required
                        autocomplete="name">
                    <i class="bi bi-person input-icon"></i>
                </div>
                @error('name')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Alamat Email -->
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="budi@example.com"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                @error('email')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Nomor Telepon -->
            <div class="form-group">
                <label class="form-label" for="phone">Nomor Telepon</label>
                <div class="phone-input-group">
                    <div class="phone-prefix">
                        <span>🇮🇩</span> +62
                    </div>
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        class="form-control-custom {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                        placeholder="812-3456-7890"
                        value="{{ old('phone') }}"
                        required
                        autocomplete="tel">
                </div>
                @error('phone')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Kata Sandi -->
            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control-custom {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                        oninput="checkPasswordStrength(this.value)">
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                @error('password')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
                <!-- Password strength bar -->
                <div class="password-strength" id="strength-indicator" style="display:none;">
                    <div class="strength-bar">
                        <div class="strength-bar-fill" id="strength-bar-fill"></div>
                    </div>
                    <span class="strength-label" id="strength-label"></span>
                </div>
            </div>

            <!-- Konfirmasi Kata Sandi -->
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control-custom"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                        oninput="checkPasswordMatch()">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
                <div class="invalid-feedback-custom" id="match-error" style="display:none;">
                    <i class="bi bi-exclamation-circle"></i> Konfirmasi kata sandi tidak cocok
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="terms-row">
                <input type="checkbox" name="terms" id="terms" required {{ old('terms') ? 'checked' : '' }}>
                <span>
                    Saya setuju dengan
                    <a href="{{ Route::has('terms') ? route('terms') : '#' }}">Syarat Ketentuan</a>
                    dan
                    <a href="{{ Route::has('privacy') ? route('privacy') : '#' }}">Kebijakan Privasi</a>.
                </span>
            </div>

            <!-- Tombol Daftar -->
            <button type="submit" class="btn-primary-custom" id="submitBtn">
                Buat Akun <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- Login link -->
        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>
    </div>
</div>


<script>
    function togglePassword(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            field.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }

    function checkPasswordStrength(password) {
        const indicator = document.getElementById('strength-indicator');
        const bar = document.getElementById('strength-bar-fill');
        const label = document.getElementById('strength-label');

        if (password.length === 0) {
            indicator.style.display = 'none';
            return;
        }

        indicator.style.display = 'block';

        let score = 0;
        if (password.length >= 8) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        const levels = [
            { width: '25%', color: '#ef4444', text: 'Lemah' },
            { width: '50%', color: '#f97316', text: 'Cukup' },
            { width: '75%', color: '#eab308', text: 'Baik' },
            { width: '100%', color: '#0ecab4', text: 'Kuat' },
        ];

        const level = levels[Math.max(0, score - 1)];
        bar.style.width = level.width;
        bar.style.background = level.color;
        label.textContent = level.text;
        label.style.color = level.color;
    }

    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('password_confirmation').value;
        const matchError = document.getElementById('match-error');
        const confirmField = document.getElementById('password_confirmation');

        if (confirm.length > 0 && password !== confirm) {
            matchError.style.display = 'flex';
            confirmField.classList.add('is-invalid');
        } else {
            matchError.style.display = 'none';
            confirmField.classList.remove('is-invalid');
        }
    }

    // Phone number auto-format
    document.getElementById('registerForm').addEventListener('submit', function () {
        const phone = document.getElementById('phone');

        phone.value = phone.value.replace(/\D/g, '');

        if (phone.value.startsWith('0')) {
            phone.value = phone.value.substring(1);
        }
    });
</script>
</body>
</html>