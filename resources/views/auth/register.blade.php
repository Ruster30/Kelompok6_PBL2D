<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            padding:0;
            background-color:#0f1117;
            font-family:Arial, sans-serif;
            overflow-x:hidden;
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
            padding:70px;
        }

        .title-register{
            font-size:52px;
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

        .btn-register{
            background-color:#0f9f90;
            border:none;
            height:48px;
            font-weight:600;
            color:white;
        }

        .btn-register:hover{
            background-color:#0b8175;
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

        .logo-box{
            width:60px;
            height:60px;
            background-color:white;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .feature-item{
            margin-bottom:20px;
        }

        .feature-icon{
            width:22px;
            height:22px;
            background-color:#14b8a6;
            border-radius:50%;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            color:white;
            font-size:12px;
            margin-right:10px;
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

            .title-register,
            .title-login{
                font-size:38px;
            }

        }

    </style>

</head>
<body>

<div class="container-fluid">

    <div class="row vh-100">

        <!-- LEFT -->
        <div class="col-md-6 d-flex flex-column justify-content-center left-side">

            <div class="logo-box mb-5">
                <strong class="text-dark">
                    APM
                </strong>
            </div>

            <h1 class="title-register mb-5">
                Mulai Perjalanan Anda <br>
                Bersama Kami.
            </h1>

            <div class="feature-item">

                <span class="feature-icon">
                    ✓
                </span>

                <span class="small-text">
                    Akses alat perencanaan event premium
                </span>

            </div>

            <div class="feature-item">

                <span class="feature-icon">
                    ✓
                </span>

                <span class="small-text">
                    Pelacakan anggaran transparan
                </span>

            </div>

            <div class="feature-item">

                <span class="feature-icon">
                    ✓
                </span>

                <span class="small-text">
                    Kolaborasi waktu nyata
                </span>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-md-6 d-flex align-items-center justify-content-center right-side">

            <div style="width:100%; max-width:450px;">

                <h2 class="fw-bold mb-2">
                    Buat akun
                </h2>

                <p class="small-text mb-5">
                    Bergabung dan mulai merencanakan event besar Anda berikutnya.
                </p>

                <!-- ERROR -->
                @if ($errors->any())

                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>

                @endif

                <!-- FORM -->
                <form method="POST"
                      action="{{ route('register') }}">

                    @csrf

                    <!-- NAME -->
                    <div class="mb-3">

                        <label class="form-label small-text">
                            Nama Lengkap
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control custom-input"
                               placeholder="Masukkan nama lengkap"
                               value="{{ old('name') }}"
                               required autofocus>

                    </div>

                    <!-- EMAIL -->
                    <div class="mb-3">

                        <label class="form-label small-text">
                            Alamat Email
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control custom-input"
                               placeholder="Masukkan email"
                               value="{{ old('email') }}"
                               required>

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

                    <!-- CONFIRM PASSWORD -->
                    <div class="mb-4">

                        <label class="form-label small-text">
                            Konfirmasi Kata Sandi
                        </label>

                        <div class="password-wrapper">

                            <input type="password"
                                   id="confirmPassword"
                                   name="password_confirmation"
                                   class="form-control custom-input pe-5"
                                   placeholder="Konfirmasi password"
                                   required>

                            <span id="toggleConfirmPassword"
                                  class="toggle-password">
                                👁️
                            </span>

                        </div>

                    </div>

                    <!-- TERMS -->
                    <div class="form-check mb-4">

                        <input class="form-check-input"
                               type="checkbox"
                               required>

                        <label class="form-check-label small-text">

                            Saya setuju dengan

                            <a href="#">
                                Syarat Ketentuan
                            </a>

                            dan

                            <a href="#">
                                Kebijakan Privasi
                            </a>

                        </label>

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="btn btn-register w-100 mb-4">

                        Buat Akun →

                    </button>

                    <!-- LOGIN -->
                    <div class="text-center small-text">

                        Sudah punya akun?

                        <a href="{{ route('login') }}">

                            Masuk di sini

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<!-- SHOW PASSWORD -->
<script>

    // PASSWORD
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

    // CONFIRM PASSWORD
    const toggleConfirmPassword =
        document.getElementById('toggleConfirmPassword');

    const confirmPassword =
        document.getElementById('confirmPassword');

    toggleConfirmPassword.addEventListener('click', function(){

        const type =
            confirmPassword.getAttribute('type') === 'password'
            ? 'text'
            : 'password';

        confirmPassword.setAttribute('type', type);

        this.innerHTML =
            type === 'password'
            ? '👁️'
            : '🙈';

    });

</script>

</body>
</html>