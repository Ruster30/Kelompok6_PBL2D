<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            margin:0;
            padding:0;
            background-color:#0f1117;
            overflow-x:hidden;
            font-family: Arial, sans-serif;
        }

        .left-side{
            background:
                radial-gradient(circle at top right,
                rgba(0,255,255,0.2),
                transparent 30%),
                linear-gradient(to bottom,
                #02142d,
                #000814);

            color:white;
            padding:80px;
        }

        .right-side{
            background-color:#111418;
            color:white;
            padding:80px;
        }

        .title-login{
            font-size:55px;
            font-weight:700;
            line-height:1.1;
        }

        .description{
            color:#9ca3af;
            font-size:15px;
            line-height:1.8;
        }

        .custom-input{
            background-color:transparent;
            border:1px solid #2d3748;
            color:white;
            height:48px;
        }

        .custom-input:focus{
            background-color:transparent;
            border-color:#14b8a6;
            color:white;
            box-shadow:none;
        }

        .custom-input::placeholder{
            color:#6b7280;
        }

        .btn-login{
            background-color:#0f9f90;
            border:none;
            height:48px;
            font-weight:600;
            color:white;
        }

        .btn-login:hover{
            background-color:#0b8175;
            color:white;
        }

        .social-btn{
            border:1px solid #2d3748;
            color:white;
            height:48px;
        }

        .social-btn:hover{
            background-color:#1f2937;
            color:white;
        }

        .small-text{
            color:#9ca3af;
            font-size:14px;
        }

        a{
            text-decoration:none;
            color:#14b8a6;
        }

        .avatar{
            width:45px;
            height:45px;
            border-radius:50%;
            border:2px solid white;
            margin-left:-10px;
        }

        .logo-box{
            width:60px;
            height:60px;
            background-color:white;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .divider{
            display:flex;
            align-items:center;
            text-align:center;
            color:#6b7280;
            font-size:13px;
        }

        .divider::before,
        .divider::after{
            content:'';
            flex:1;
            border-bottom:1px solid #2d3748;
        }

        .divider:not(:empty)::before{
            margin-right:.75em;
        }

        .divider:not(:empty)::after{
            margin-left:.75em;
        }

        .password-wrapper{
            position:relative;
        }

        .toggle-password{
            position:absolute;
            top:50%;
            right:15px;
            transform:translateY(-50%);
            cursor:pointer;
            font-size:18px;
            color:#9ca3af;
            user-select:none;
        }

        @media (max-width: 768px){

            .left-side{
                display:none;
            }

            .right-side{
                padding:40px 25px;
            }

            .title-login{
                font-size:38px;
            }
        }
    </style>

</head>
<body>

<div class="container-fluid">

    <div class="row vh-100">

        <!-- LEFT SIDE -->
        <div class="col-md-6 d-flex flex-column justify-content-center left-side">

            <div class="logo-box mb-5">
                <strong class="text-dark">
                    APM
                </strong>
            </div>

            <h1 class="title-login mb-4">
                Menciptakan <br>
                Pengalaman Tak <br>
                Terlupakan.
            </h1>

            <p class="description w-75">
                Masuk untuk mengakses timeline event,
                mengelola anggaran, dan berkolaborasi
                dengan spesialis event premium kami.
            </p>

            <div class="d-flex align-items-center mt-5">

                <img src="https://i.pravatar.cc/45?img=1"
                     class="avatar">

                <img src="https://i.pravatar.cc/45?img=2"
                     class="avatar">

                <img src="https://i.pravatar.cc/45?img=3"
                     class="avatar">

                <span class="ms-3 small-text">
                    Bergabung dengan 500+ klien puas
                </span>

            </div>

        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-6 d-flex align-items-center justify-content-center right-side">

            <div style="width:100%; max-width:450px;">

                <h2 class="fw-bold mb-2">
                    Selamat datang kembali
                </h2>

                <p class="small-text mb-5">
                    Silakan masukkan detail Anda untuk masuk.
                </p>

                <!-- ERROR -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST"
                      action="{{ route('login') }}">

                    @csrf

                    <!-- EMAIL -->
                    <div class="mb-4">

                        <label class="form-label small-text">
                            Alamat Email
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control custom-input"
                               placeholder="Masukkan email Anda"
                               required autofocus>

                    </div>

                    <!-- PASSWORD -->
                    <div class="mb-3">

                        <label class="form-label small-text">
                            Kata Sandi
                        </label>

                        <div class="password-wrapper">

                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-control custom-input pe-5"
                                   placeholder="Masukkan password"
                                   required>

                            <span id="togglePassword"
                                  class="toggle-password">
                                👁️
                            </span>

                        </div>

                    </div>

                    <!-- REMEMBER -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div class="form-check">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="remember"
                                   id="remember">

                            <label class="form-check-label small-text"
                                   for="remember">

                                Ingat saya

                            </label>

                        </div>

                        @if (Route::has('password.request'))

                            <a href="{{ route('password.request') }}"
                               class="small-text">

                                Lupa kata sandi?

                            </a>

                        @endif

                    </div>

                    <!-- LOGIN BUTTON -->
                    <button type="submit"
                            class="btn btn-login w-100 mb-4">

                        Masuk →

                    </button>

                    <!-- DIVIDER -->
                    <div class="divider mb-4">
                        Atau lanjutkan dengan
                    </div>

                    <!-- SOCIAL -->
                    <div class="row g-3 mb-4">

                        <div class="col-6">

                            <button type="button"
                                    class="btn social-btn w-100">

                                Google

                            </button>

                        </div>

                        <div class="col-6">

                            <button type="button"
                                    class="btn social-btn w-100">

                                Apple

                            </button>

                        </div>

                    </div>

                    <!-- REGISTER -->
                    <div class="text-center small-text">

                        Belum punya akun?

                        <a href="{{ route('register') }}">

                            Daftar di sini

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- SHOW PASSWORD -->
<script>

    const togglePassword =
        document.getElementById('togglePassword');

    const password =
        document.getElementById('password');

    togglePassword.addEventListener('click', function(){

        const type =
            password.getAttribute('type') === 'password'
            ? 'text'
            : 'password';

        password.setAttribute('type', type);

        this.innerHTML =
            type === 'password'
            ? '👁️'
            : '🙈';

    });

</script>

</body>
</html>