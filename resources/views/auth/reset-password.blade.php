<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Alpha Organizer</title>
    <meta name="description" content="Buat password baru untuk akun Alpha Organizer Anda.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --success:      #22c55e;
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

        .logo-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-box img {
            width: 52px;
            height: 52px;
            object-fit: contain;
            border-radius: 12px;
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

        .left-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 0 40px;
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
            font-size: 44px;
            font-weight: 800;
            line-height: 1.1;
            color: var(--text-primary);
            letter-spacing: -1px;
            margin-bottom: 24px;
        }

        .left-content h1 span {
            color: var(--accent);
        }

        .left-content p {
            font-size: 15px;
            color: var(--text-secondary);
            line-height: 1.75;
            max-width: 380px;
        }

        .strength-info {
            margin-top: 40px;
            background: rgba(14,202,180,0.06);
            border: 1px solid rgba(14,202,180,0.15);
            border-radius: 14px;
            padding: 24px;
        }

        .strength-info h3 {
            font-size: 14px;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 14px;
            letter-spacing: 0.5px;
        }

        .strength-rule {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .strength-rule i {
            color: var(--text-muted);
            font-size: 14px;
            width: 16px;
            transition: color 0.2s;
        }

        .strength-rule.met i { color: var(--success); }
        .strength-rule.met   { color: var(--text-primary); }

        .left-footer {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        /* ── RIGHT PANEL ── */
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
            max-width: 440px;
        }

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

        .auth-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }

        /* Form */
        .form-group { margin-bottom: 22px; }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

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
        }

        .form-control-custom::placeholder { color: var(--text-muted); }

        .form-control-custom:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(14,202,180,0.12);
        }

        .input-wrap:focus-within .input-icon { color: var(--accent); }

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

        /* Toggle password */
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

        /* Password strength bar */
        .strength-bar-wrap {
            margin-top: 8px;
            display: none;
        }

        .strength-bar-wrap.visible { display: block; }

        .strength-segments {
            display: flex;
            gap: 4px;
            margin-bottom: 6px;
        }

        .segment {
            flex: 1;
            height: 3px;
            border-radius: 4px;
            background: var(--border-color);
            transition: background 0.3s;
        }

        .segment.filled-weak   { background: #ef4444; }
        .segment.filled-fair   { background: #f97316; }
        .segment.filled-good   { background: #eab308; }
        .segment.filled-strong { background: var(--success); }

        .strength-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        /* Submit button */
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
        }

        .btn-primary-custom:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(14,202,180,0.3);
        }

        .btn-primary-custom:active { transform: translateY(0); }

        .btn-primary-custom:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 24px;
            transition: color 0.2s;
        }

        .back-link:hover { color: var(--accent); }

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
             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
        <div class="logo-text" style="display:none;">ALPHA<span>ORGANIZER</span></div>
        <div class="logo-text">Alpha<span>ORGANIZER</span></div>
    </div>

    <div class="left-content">
        <p class="eyebrow">Buat Password Baru</p>
        <h1>Amankan Kembali <span>Akun</span> Anda</h1>
        <p>Pilih password baru yang kuat dan mudah Anda ingat. Password yang baik adalah kunci keamanan akun Anda.</p>

        <div class="strength-info">
            <h3><i class="bi bi-shield-check me-2"></i>Kriteria Password Kuat</h3>
            <div class="strength-rule" id="rule-length">
                <i class="bi bi-circle"></i>
                Minimal 8 karakter
            </div>
            <div class="strength-rule" id="rule-upper">
                <i class="bi bi-circle"></i>
                Mengandung huruf kapital (A-Z)
            </div>
            <div class="strength-rule" id="rule-lower">
                <i class="bi bi-circle"></i>
                Mengandung huruf kecil (a-z)
            </div>
            <div class="strength-rule" id="rule-number">
                <i class="bi bi-circle"></i>
                Mengandung angka (0-9)
            </div>
            <div class="strength-rule" id="rule-special">
                <i class="bi bi-circle"></i>
                Mengandung karakter khusus (!@#$...)
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

        <h2>Buat Password Baru</h2>
        <p class="subtitle">
            Masukkan password baru Anda. Pastikan minimal 8 karakter dan mudah Anda ingat.
        </p>

        {{-- Error umum (token invalid/expired) --}}
        @if ($errors->has('email') && !$errors->has('password'))
            <div class="auth-alert auth-alert-error" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                <div>
                    <strong style="display:block; margin-bottom:3px;">Tautan Tidak Valid</strong>
                    {{ $errors->first('email') }} Silakan minta tautan reset password baru.
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" id="resetForm">
            @csrf

            {{-- Token tersembunyi (wajib ada) --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email (readonly, diisi dari URL) --}}
            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <div class="input-wrap">
                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control-custom {{ $errors->has('email') ? 'is-invalid' : '' }}"
                        value="{{ old('email', $request->email) }}"
                        required
                        autocomplete="username"
                        readonly
                        style="opacity: 0.7; cursor: default;">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
                @error('email')
                    <div class="invalid-feedback-custom">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Password baru --}}
            <div class="form-group">
                <label class="form-label" for="password">Password Baru</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control-custom {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Masukkan password baru"
                        required
                        autocomplete="new-password"
                        oninput="checkStrength(this.value)">
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="invalid-feedback-custom">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror

                {{-- Strength bar --}}
                <div class="strength-bar-wrap" id="strengthBarWrap">
                    <div class="strength-segments">
                        <div class="segment" id="seg1"></div>
                        <div class="segment" id="seg2"></div>
                        <div class="segment" id="seg3"></div>
                        <div class="segment" id="seg4"></div>
                    </div>
                    <span class="strength-label" id="strengthLabel">—</span>
                </div>
            </div>

            {{-- Konfirmasi password --}}
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        name="password_confirmation"
                        id="password_confirmation"
                        class="form-control-custom {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                        placeholder="Ulangi password baru"
                        required
                        autocomplete="new-password"
                        oninput="checkMatch()">
                    <i class="bi bi-lock-fill input-icon"></i>
                    <button type="button" class="toggle-password" id="toggleConfirm" aria-label="Toggle confirm visibility">
                        <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                    </button>
                </div>
                @error('password_confirmation')
                    <div class="invalid-feedback-custom">
                        <i class="bi bi-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
                <div class="invalid-feedback-custom" id="matchError" style="display:none;">
                    <i class="bi bi-exclamation-circle"></i>
                    Password dan konfirmasi tidak cocok.
                </div>
            </div>

            <button type="submit" class="btn-primary-custom" id="submitBtn">
                <i class="bi bi-shield-check"></i>
                Reset Password
            </button>
        </form>

        <a href="{{ route('password.request') }}" class="back-link">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Lupa Password
        </a>

    </div>
</div>

<script>
    // ── Toggle password visibility ──
    function setupToggle(btnId, inputId, iconId) {
        document.getElementById(btnId).addEventListener('click', function () {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    }

    setupToggle('togglePassword', 'password', 'toggleIcon');
    setupToggle('toggleConfirm',  'password_confirmation', 'toggleConfirmIcon');

    // ── Password strength checker ──
    function checkStrength(val) {
        const wrap = document.getElementById('strengthBarWrap');
        wrap.classList.add('visible');

        const rules = {
            length:  val.length >= 8,
            upper:   /[A-Z]/.test(val),
            lower:   /[a-z]/.test(val),
            number:  /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val),
        };

        // Update left-panel rules
        updateRule('rule-length',  rules.length);
        updateRule('rule-upper',   rules.upper);
        updateRule('rule-lower',   rules.lower);
        updateRule('rule-number',  rules.number);
        updateRule('rule-special', rules.special);

        const score = Object.values(rules).filter(Boolean).length;

        const segments = ['seg1','seg2','seg3','seg4'];
        const levelClass = score <= 1 ? 'filled-weak'
                         : score === 2 ? 'filled-fair'
                         : score === 3 ? 'filled-good'
                         : 'filled-strong';

        const labelMap = ['', 'Lemah', 'Cukup', 'Baik', 'Kuat', 'Sangat Kuat'];
        const labelColor = ['','#ef4444','#f97316','#eab308','#22c55e','#22c55e'];

        segments.forEach((id, i) => {
            const el = document.getElementById(id);
            el.className = 'segment';
            if (i < score) el.classList.add(levelClass);
        });

        const lbl = document.getElementById('strengthLabel');
        lbl.textContent = labelMap[score] || '—';
        lbl.style.color = labelColor[score] || 'var(--text-muted)';

        checkMatch();
    }

    function updateRule(id, met) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.toggle('met', met);
        el.querySelector('i').className = met ? 'bi bi-check-circle-fill' : 'bi bi-circle';
    }

    // ── Password match checker ──
    function checkMatch() {
        const pw  = document.getElementById('password').value;
        const cpw = document.getElementById('password_confirmation').value;
        const err = document.getElementById('matchError');
        if (cpw.length > 0) {
            err.style.display = pw === cpw ? 'none' : 'flex';
        } else {
            err.style.display = 'none';
        }
    }

    // ── Disable button on submit ──
    document.getElementById('resetForm').addEventListener('submit', function (e) {
        const pw  = document.getElementById('password').value;
        const cpw = document.getElementById('password_confirmation').value;
        if (pw !== cpw) {
            e.preventDefault();
            document.getElementById('matchError').style.display = 'flex';
            return;
        }
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
    });
</script>

</body>
</html>
