<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Alpha Organizer</title>
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
            --accent-dark: #0a8f82;
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
            overflow: hidden;
            flex-shrink: 0;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -80px;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(14,202,180,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -60px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(14,202,180,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        /*back home*/
        .back-home{
            margin-bottom:24px;
        }

        .back-home a{
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:var(--text-secondary);
            text-decoration:none;
            font-size:14px;
            font-weight:600;
            transition:.25s;
        }

        .back-home a:hover{
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

        /* Fallback jika tidak ada gambar */
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
            font-size: clamp(28px, 3vw, 42px);
            font-weight: 800;
            line-height: 1.15;
            color: var(--text-primary);
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .left-content p {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text-secondary);
            max-width: 340px;
        }

        .avatar-stack {
            display: flex;
        }

        .avatar-stack .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2.5px solid var(--bg-left);
            background: var(--bg-input);
            margin-left: -10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-stack .avatar:first-child { margin-left: 0; }

        .avatar-stack .avatar:nth-child(1) { background: linear-gradient(135deg, #667eea, #764ba2); }
        .avatar-stack .avatar:nth-child(2) { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .avatar-stack .avatar:nth-child(3) { background: linear-gradient(135deg, #4facfe, #00f2fe); }

        /* â”€â”€ RIGHT PANEL â”€â”€ */
        .right-panel {
            width: 58%;
            min-height: 100vh;
            height: 100vh;
            background: var(--bg-panel);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 56px;
            margin-left: 42%;
            overflow-y: auto;
        }

        .form-wrapper {
            width: 100%;
            max-width: 480px;
        }

        .form-wrapper h2 {
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .form-wrapper .subtitle {
            font-size: 14.5px;
            color: var(--text-secondary);
            margin-bottom: 36px;
        }

        /* Error Alert */
        .auth-alert {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 24px;
            color: #fca5a5;
            font-size: 13.5px;
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
            padding: 13px 46px 13px 46px;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14.5px;
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

        .form-control-custom:focus + .input-icon,
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
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
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
            font-size: 16px;
            transition: color 0.2s;
        }

        .toggle-password:hover { color: var(--text-secondary); }

        /* Checkbox row */
        .form-check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .custom-checkbox input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--accent);
            border-radius: 4px;
            cursor: pointer;
        }

        .custom-checkbox span {
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        .forgot-link {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover { color: var(--accent-hover); text-decoration: underline; }

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

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 28px 0;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            font-size: 13px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        /* Social buttons */
       .social-buttons {
            display: flex;
            justify-content: center;
        }

        .google-btn {
            width: 100%;
            max-width: 220px;
        }

        .btn-social {
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 12px;
            color: var(--text-primary);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14   px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: border-color 0.2s, background 0.2s;
            text-decoration: none;
        }

        .btn-social:hover {
            border-color: var(--accent);
            background: rgba(14,202,180,0.05);
            color: var(--text-primary);
        }

        .btn-social img { width: 18px; height: 18px; }

        /* Footer link */
        .auth-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 13.5px;
            color: var(--text-secondary);
        }

        .auth-footer a {
            color: var(--accent);
            font-weight: 700;
            text-decoration: none;
        }

        .auth-footer a:hover { text-decoration: underline; }

        /* Phone/Email toggle */
        .login-type-toggle {
            display: flex;
            gap: 0;
            background: var(--bg-input);
            border: 1.5px solid var(--border-color);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .login-type-toggle button {
            flex: 1;
            background: none;
            border: none;
            border-radius: 8px;
            padding: 9px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .login-type-toggle button.active {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 10px rgba(14,202,180,0.3);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; margin-left: 0; padding: 40px 24px; }
        }
    
        /* ===== RESPONSIVE: Login Page ===== */
        @media (max-width: 991px) {
            .left-panel {
                display: none !important;
            }
            .right-panel {
                width: 100% !important;
                margin-left: 0 !important;
                padding: 40px 24px !important;
                height: 100vh;
                overflow-y: auto;
            }
            .right-panel-inner {
                max-width: 400px !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }
            .login-header h2 {
                font-size: 24px !important;
            }
        }

        @media (max-width: 575px) {
            .right-panel {
                padding: 24px 16px !important;
            }
            .right-panel-inner {
                max-width: 100% !important;
            }
            .login-header h2 {
                font-size: 20px !important;
            }
            .social-buttons .btn-social {
                width: 100% !important;
                justify-content: center !important;
            }
            .auth-footer {
                font-size: 13px !important;
            }
        }
        </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
    <div class="back-home">
        <a href="{{ url('/') }}">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Beranda
        </a>
    </div>
    <div class="logo-box">
        {{-- Ganti src dengan path logo Anda --}}
        <img src="{{ asset('images/Logo-bg.png') }}" alt="Alpha Organizer"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
        <div class="logo-text" style="display:none;">ALPHA<br><span style="font-size:8px;color:#aaa;font-weight:500;">ORGANIZER</span></div>
    </div>

    <div class="left-content">
        <h1>Menciptakan Pengalaman Tak Terlupakan.</h1>
        <p>Masuk untuk mengakses timeline event, mengelola anggaran, dan berkolaborasi dengan spesialis event premium kami.</p>
    </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
    <div class="form-wrapper">
        <h2>Selamat datang kembali</h2>
        <p class="subtitle">Silakan masukkan detail Anda untuk masuk.</p>

        @if ($errors->any())
        <div class="auth-alert">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @if (session('status'))
        <div class="auth-alert" style="background:rgba(14,202,180,0.1); border-color:rgba(14,202,180,0.3); color:#5eead4;">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('status') }}
        </div>
        @endif

        <!-- Toggle Email / Nomor Telepon -->
        <div class="login-type-toggle" role="tablist" aria-label="Pilih metode login">
            <button type="button" class="active" id="btn-email-toggle" onclick="switchLoginType('email')" role="tab" aria-selected="true" aria-controls="field-email">
                <i class="bi bi-envelope"></i> Email
            </button>
            <button type="button" id="btn-phone-toggle" onclick="switchLoginType('phone')" role="tab" aria-selected="false" aria-controls="field-phone">
                <i class="bi bi-telephone"></i> No. Telepon
            </button>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <input type="hidden" name="login" id="login">

            <!-- Field Email -->
            <div class="form-group form-field-group" id="field-email" aria-hidden="false">
                <label class="form-label" for="email">Alamat Email</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan email Anda"
                        value="{{ old('email') }}"
                        autocomplete="email">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                @error('email')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Field Nomor Telepon (tersembunyi secara default) -->
            <div class="form-group form-field-group" id="field-phone" aria-hidden="true" style="display:none;">
                <label class="form-label" for="phone">Nomor Telepon</label>
                <div class="input-wrap">
                    <input
                        type="tel"
                        name="phone"
                        id="phone"
                        class="form-control-custom {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                        placeholder="+62 812-3456-7890"
                        value="{{ old('phone') }}"
                        autocomplete="tel"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="13">
                    <i class="bi bi-telephone input-icon"></i>
                </div>
                @error('phone')
                <div class="invalid-feedback-custom">
                    <i class="bi bi-exclamation-circle"></i> {{ $message }}
                </div>
                @enderror
            </div>

            <!-- Hidden field to track login type -->
            <input type="hidden" name="login_type" id="login_type" value="email">

            <!-- Kata Sandi -->
            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control-custom {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="+62 800-0000-0000"
                        autocomplete="current-password">
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
            </div>

            <!-- Ingat saya + Lupa kata sandi -->
            <div class="form-check-row">
                <label class="custom-checkbox">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa kata sandi?</a>
                @endif
            </div>

            <!-- Tombol Masuk -->
            <button type="submit" class="btn-primary-custom">
                Masuk <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="divider"><span>Atau lanjutkan dengan</span></div>

        <!-- Social Login -->
        <div class="social-buttons flex justify-center">
            <a href="{{ Route::has('auth.google') ? route('auth.google') : '#' }}" class="btn-social google-btn">
                <svg width="18" height="18" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>
        </div>

        <!-- Register link -->
        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>
    </div>
</div>


<script>
    function switchLoginType(type) {
        const emailField = document.getElementById('field-email');
        const phoneField = document.getElementById('field-phone');
        const emailToggle = document.getElementById('btn-email-toggle');
        const phoneToggle = document.getElementById('btn-phone-toggle');
        const loginTypeInput = document.getElementById('login_type');

        if (type === 'email') {
            emailField.style.display = 'block';
            emailField.setAttribute('aria-hidden', 'false');
            phoneField.style.display = 'none';
            phoneField.setAttribute('aria-hidden', 'true');
            emailToggle.classList.add('active');
            emailToggle.setAttribute('aria-selected', 'true');
            phoneToggle.classList.remove('active');
            phoneToggle.setAttribute('aria-selected', 'false');
            document.getElementById('email').required = true;
            document.getElementById('phone').required = false;
            loginTypeInput.value = 'email';
        } else {
            emailField.style.display = 'none';
            emailField.setAttribute('aria-hidden', 'true');
            phoneField.style.display = 'block';
            phoneField.setAttribute('aria-hidden', 'false');
            emailToggle.classList.remove('active');
            emailToggle.setAttribute('aria-selected', 'false');
            phoneToggle.classList.add('active');
            phoneToggle.setAttribute('aria-selected', 'true');
            document.getElementById('email').required = false;
            document.getElementById('phone').required = true;
            loginTypeInput.value = 'phone';
        }
    }

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

    document.getElementById('loginForm').addEventListener('submit', function () {

        const loginType = document.getElementById('login_type').value;

        if (loginType === 'email') {
            document.getElementById('login').value =
                document.getElementById('email').value.trim();
        } else {
            document.getElementById('login').value =
                document.getElementById('phone').value.trim();
        }

    });

    // Hanya izinkan angka pada input nomor telepon
    document.getElementById('phone').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
    });

    // Set initial required state
    document.getElementById('email').required = true;
    document.getElementById('phone').required = false;
    
</script>
</body>
</html>
