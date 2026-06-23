<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Alpha Organizer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Plus Jakarta Sans',sans-serif;
            min-height:100vh;
            background:#071224;
            overflow:hidden;
        }

        .auth-wrapper{
            min-height:100vh;
            display:flex;
        }

        /* ===== KIRI ===== */
        .auth-left{
            width:50%;
            background:linear-gradient(
                90deg,
                #041222 0%,
                #00142d 45%,
                #0c3642 100%
            );
            position:relative;
            display:flex;
            flex-direction:column;
            justify-content:center;
            padding:80px;
        }

        .logo-box{
            position:absolute;
            top:40px;
            left:40px;
        }

        .logo-box img{
            width:100px;
            height:100px;
            object-fit:contain;
            border-radius:12px;
        }

        .left-content{
            max-width:520px;
        }

        .left-content h1{
            font-size:58px;
            line-height:1.05;
            font-weight:800;
            color:white;
            margin-bottom:24px;
        }

        .left-content p{
            color:#cbd5e1;
            font-size:18px;
            line-height:1.8;
        }

        /* ===== KANAN ===== */
        .auth-right{
            width:50%;
            background:#061021;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px;
        }

        .auth-card{
            width:100%;
            max-width:450px;
        }

        .auth-card h2{
            color:white;
            font-size:42px;
            font-weight:700;
            margin-bottom:10px;
        }

        .auth-card p{
            color:#94a3b8;
            margin-bottom:30px;
        }

        .form-label{
            color:white;
            font-size:14px;
            font-weight:600;
            margin-bottom:8px;
        }

        .input-group-text{
            background:#0f1d33;
            border:1px solid #334155;
            color:#94a3b8;
        }

        .form-control{
            background:#0f1d33;
            border:1px solid #334155;
            color:white;
            height:52px;
        }

        .form-control::placeholder{
            color:#64748b;
        }

        .form-control:focus{
            background:#0f1d33;
            color:white;
            border-color:#14b8a6;
            box-shadow:none;
        }

        .btn-alpha{
            width:100%;
            height:52px;
            border:none;
            border-radius:10px;
            background:#14b8a6;
            color:white;
            font-weight:700;
            transition:.3s;
        }

        .btn-alpha:hover{
            background:#0d9488;
        }

        .back-login{
            margin-top:24px;
            text-align:center;
        }

        .back-login a{
            color:#14b8a6;
            text-decoration:none;
            font-weight:600;
        }

        .back-login a:hover{
            color:#2dd4bf;
        }

        .alert{
            margin-bottom:20px;
        }

        @media(max-width:991px){

            .auth-left{
                display:none;
            }

            .auth-right{
                width:100%;
            }

            .auth-card h2{
                font-size:34px;
            }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">

    {{-- KIRI --}}
    <div class="auth-left">

        <div class="logo-box">
            <img src="{{ asset('images/Logo-bg.png') }}" alt="Alpha Organizer">
        </div>

        <div class="left-content">
            <h1>
                Atur Ulang Kata Sandi Anda.
            </h1>

            <p>
                Masukkan email yang terdaftar untuk menerima tautan reset password
                dan mendapatkan kembali akses ke akun Anda dengan aman.
            </p>
        </div>

    </div>

    {{-- KANAN --}}
    <div class="auth-right">

        <div class="auth-card">

            <h2>Lupa Password?</h2>

            <p>
                Kami akan mengirimkan link reset password ke email Anda.
            </p>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label">
                        Alamat Email
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="Masukkan email Anda"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                    </div>

                    @error('email')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror
                </div>

                <button type="submit" class="btn-alpha">
                    Kirim Link Reset Password
                </button>

                <div class="back-login">
                    <a href="{{ route('login') }}">
                        ← Kembali ke Login
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>
```
