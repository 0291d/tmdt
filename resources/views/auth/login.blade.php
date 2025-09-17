<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My account - BREW</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body { background:#f7f7f7; }
    .auth-wrap { max-width:1100px; margin:40px auto; display:flex; gap:24px; align-items:flex-start; justify-content:space-between; }
    .auth-card { flex:1; background:#fff; border:1px solid #eee; border-radius:6px; padding:24px; }
    .auth-card h2 { font-weight:700; margin-bottom:16px; font-size:20px; }
  </style>
  @vite([])
</head>
<body>
  <div class="container">
    @if (session('status'))
      <div class="alert alert-warning mt-3">{{ session('status') }}</div>
    @endif
    <div class="auth-wrap">
      <div class="auth-card">
        <h2>Đăng nhập</h2>
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">Địa chỉ email *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu *</label>
            <input id="password" type="password" name="password" required class="form-control">
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
          </div>
          <button type="submit" class="btn btn-primary">Đăng nhập</button>
          @if (Route::has('password.request'))
            <a class="btn btn-link" href="{{ route('password.request') }}">Quên mật khẩu?</a>
          @endif
        </form>
      </div>

      <div class="auth-card">
        <h2>Đăng ký</h2>
        @if (Route::has('register'))
        <form method="POST" action="{{ route('register') }}">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">Tên đăng nhập *</label>
            <input id="name" type="text" name="name" required class="form-control" value="{{ old('name') }}">
          </div>
          <div class="mb-3">
            <label for="reg_email" class="form-label">Địa chỉ email *</label>
            <input id="reg_email" type="email" name="email" required class="form-control" value="{{ old('email') }}">
          </div>
          <div class="mb-3">
            <label for="reg_password" class="form-label">Mật khẩu *</label>
            <input id="reg_password" type="password" name="password" required class="form-control">
          </div>
          <div class="mb-3">
            <label for="reg_password2" class="form-label">Nhập lại mật khẩu *</label>
            <input id="reg_password2" type="password" name="password_confirmation" required class="form-control">
          </div>
          <button type="submit" class="btn btn-success">Đăng ký</button>
        </form>
        @else
        <p>Chức năng đăng ký hiện không khả dụng.</p>
        @endif
      </div>
    </div>
  </div>
</body>
</html>
