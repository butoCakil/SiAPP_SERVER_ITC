<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi OTP | SiAPP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .login-box { margin-top: 10vh; }
        .card { border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .card-header { background: linear-gradient(135deg, #343a40, #1a1a2e); border-radius: 12px 12px 0 0 !important; padding: 30px; text-align: center; }
        .brand-icon { font-size: 48px; color: #28a745; margin-bottom: 10px; }
        .brand-title { color: white; font-size: 28px; font-weight: 700; letter-spacing: 2px; }
        .brand-sub { color: rgba(255,255,255,0.6); font-size: 12px; }
        .otp-input { font-size: 28px; font-weight: 700; letter-spacing: 12px; text-align: center; border-radius: 8px; border: 2px solid #e9ecef; padding: 12px; }
        .otp-input:focus { border-color: #28a745; box-shadow: none; }
        .btn-verify { border-radius: 8px; padding: 12px; font-weight: 600; background: linear-gradient(135deg, #28a745, #1e7e34); border: none; color: #fff; }
    </style>
</head>
<body>
<div class="login-box mx-auto" style="max-width: 400px;">
    <div class="card">
        <div class="card-header">
            <div class="brand-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="brand-title">SiAPP</div>
            <div class="brand-sub">Verifikasi OTP</div>
        </div>
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
            @endif

            <p class="text-muted text-center" style="font-size:13px;">
                Masukkan 6 digit kode OTP yang dikirim ke email atau WhatsApp Anda.<br>
                <small>Atau klik link yang dikirim untuk langsung reset password.</small>
            </p>

            <form action="{{ route('password.otp.verify', $token) }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="otp" class="form-control otp-input"
                        placeholder="000000" maxlength="6" required autofocus
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                </div>
                <button type="submit" class="btn btn-verify btn-block">
                    <i class="fas fa-check-circle mr-2"></i>Verifikasi OTP
                </button>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('login') }}" class="text-muted" style="font-size:12px;">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Login
            </a>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</body>
</html>
