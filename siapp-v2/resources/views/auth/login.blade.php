<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SiAPP</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 100vh; }
        .login-box { margin-top: 10vh; }
        .card { border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .card-header { background: linear-gradient(135deg, #343a40, #1a1a2e); border-radius: 12px 12px 0 0 !important; padding: 30px; text-align: center; }
        .brand-icon { font-size: 48px; color: #4CAF50; margin-bottom: 10px; }
        .brand-title { color: white; font-size: 28px; font-weight: 700; letter-spacing: 2px; }
        .brand-sub { color: rgba(255,255,255,0.6); font-size: 12px; letter-spacing: 1px; }
        .form-control { border-radius: 8px; border: 2px solid #e9ecef; padding: 12px 15px; }
        .form-control:focus { border-color: #007bff; box-shadow: none; }
        .btn-login { border-radius: 8px; padding: 12px; font-weight: 600; letter-spacing: 1px; background: linear-gradient(135deg, #007bff, #0056b3); border: none; }
        .input-group-text { border-radius: 8px 0 0 8px; border: 2px solid #e9ecef; border-right: none; background: #f8f9fa; }
    </style>
</head>
<body>
<div class="login-box mx-auto" style="max-width: 400px;">
    <div class="card">
        <div class="card-header">
            <div class="brand-icon"><i class="fas fa-wifi"></i></div>
            <div class="brand-title">SiAPP</div>
            <div class="brand-sub">Sistem Informasi Absensi Presensi Pintar</div>
        </div>
        <div class="card-body p-4">
            @if($errors->has('login'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first('login') }}
                </div>
            @endif
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                    </div>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        placeholder="Username" value="{{ old('username') }}" autofocus>
                </div>
                <div class="input-group mb-4">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-login btn-primary btn-block">
                    <i class="fas fa-sign-in-alt mr-2"></i> MASUK
                </button>
            </form>
        </div>
        <div class="card-footer text-center text-muted" style="font-size:11px;">
            SiAPP &copy; {{ date('Y') }}
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>