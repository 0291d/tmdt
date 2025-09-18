<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quen mat khau - BREW</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { background:#f7f7f7; font-family: 'Inter', system-ui, sans-serif; }
    .auth-wrapper { max-width:520px; margin:60px auto; padding:0 16px; }
    .auth-card { background:#fff; border:1px solid #eee; border-radius:8px; padding:32px 28px; box-shadow:0 12px 40px rgba(15,23,42,0.08); }
    .auth-title { font-size:24px; font-weight:700; margin-bottom:8px; color:#111827; }
    .auth-subtitle { color:#6b7280; margin-bottom:24px; }
    .form-label { font-weight:600; color:#111827; }
    .btn-primary { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; font-weight:600; }
    .back-link { margin-top:24px; display:flex; align-items:center; gap:8px; font-size:14px; }
    .back-link a { text-decoration:none; font-weight:600; color:#2563eb; }
    .back-link a:hover { text-decoration:underline; }
    .alert { border-radius:6px; }
  </style>
  @vite([])
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1 class="auth-title">Quên mật khẩu</h1>
      <p class="auth-subtitle">Nhập email của bạn</p>
      @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif
      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fa-solid fa-paper-plane"></i>
          Gửi liên kết đặt lại mật khẩu
        </button>
      </form>
      <div class="back-link">
        <i class="fa-solid fa-arrow-left"></i>
        <a href="{{ route('login') }}">Quay về trang đăng nhập</a>
      </div>
    </div>
  </div>
</body>
</html>
