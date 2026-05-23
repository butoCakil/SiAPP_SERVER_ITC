<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password | SiAPP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .login-box { margin-top: 10vh; }
        .card { border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .card-header { background: linear-gradient(135deg, #343a40, #1a1a2e); border-radius: 12px 12px 0 0 !important; padding: 30px; text-align: center; }
        .brand-icon { font-size: 48px; color: #ffc107; margin-bottom: 10px; }
        .brand-title { color: white; font-size: 28px; font-weight: 700; letter-spacing: 2px; }
        .brand-sub { color: rgba(255,255,255,0.6); font-size: 12px; letter-spacing: 1px; }
        .form-control { border-radius: 8px; border: 2px solid #e9ecef; padding: 12px 15px; }
        .form-control:focus { border-color: #007bff; box-shadow: none; }
        .btn-submit { border-radius: 8px; padding: 12px; font-weight: 600; letter-spacing: 1px; background: linear-gradient(135deg, #ffc107, #e0a800); border: none; color: #212529; }
        .input-group-text { border-radius: 8px 0 0 8px; border: 2px solid #e9ecef; border-right: none; background: #f8f9fa; }
        .metode-card { border: 2px solid #e9ecef; border-radius: 8px; padding: 12px 16px; cursor: pointer; transition: all 0.2s; }
        .metode-card:hover { border-color: #007bff; background: #f0f7ff; }
        .metode-card input[type=radio]:checked + label .metode-card,
        .metode-card.active { border-color: #007bff; background: #f0f7ff; }
    </style>
</head>
<body>
<div class="login-box mx-auto" style="max-width: 420px;">
    <div class="card">
        <div class="card-header">
            <div class="brand-icon"><i class="fas fa-key"></i></div>
            <div class="brand-title">SiAPP</div>
            <div class="brand-sub">Reset Password</div>
        </div>
        <div class="card-body p-4">
            @if(session('error'))
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i>{{ session('info') }}</div>
            @endif

            <p class="text-muted" style="font-size:13px;">Masukkan username atau email akun Anda, lalu pilih metode pengiriman kode reset.</p>

            <form action="{{ route('password.request') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                    </div>
                    <input type="text" name="identity" class="form-control"
                        placeholder="Username atau Email" value="{{ old('identity') }}" required autofocus>
                </div>

                <label style="font-size:12px; font-weight:600;" class="mb-2">Kirim kode via:</label>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="metode-card" id="card-email" onclick="pilihMetode('email')">
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <i class="fas fa-envelope text-primary"></i>
                                <div>
                                    <div style="font-size:12px; font-weight:600;">Email</div>
                                    <div style="font-size:10px; color:#999;">Link & OTP</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="metode-card" id="card-wa" onclick="pilihMetode('wa')">
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <i class="fab fa-whatsapp text-success"></i>
                                <div>
                                    <div style="font-size:12px; font-weight:600;">WhatsApp</div>
                                    <div style="font-size:10px; color:#999;">Link & OTP</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="metode" id="metode-input" value="email">

                <button type="submit" class="btn btn-submit btn-block">
                    <i class="fas fa-paper-plane mr-2"></i>Kirim Kode Reset
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
<script>
function pilihMetode(m) {
    document.getElementById('metode-input').value = m;
    document.getElementById('card-email').classList.toggle('active', m === 'email');
    document.getElementById('card-wa').classList.toggle('active', m === 'wa');
}
pilihMetode('email');
</script>
</body>
</html>
