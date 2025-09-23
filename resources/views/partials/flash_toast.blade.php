@php
  $flashStatus = session('status');
  $flashError  = session('error');
  // Cho phép truyền nhiều thông báo (mảng) nếu cần
  $messages = [];
  if ($flashStatus) $messages[] = ['type' => 'success', 'text' => (string) $flashStatus];
  if ($flashError)  $messages[] = ['type' => 'danger',  'text' => (string) $flashError];
@endphp

@if (!empty($messages))
  <style>
    .toast-stack{position:fixed;right:16px;top:16px;z-index:1055;display:flex;flex-direction:column;gap:10px}
    .toast-card{min-width:280px;max-width:360px;background:#fff;border:1px solid #e6e6e6;border-left-width:4px;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,.1);padding:12px 14px 12px 12px;display:flex;align-items:flex-start;gap:10px;opacity:0;transform:translateY(-10px);animation:toastIn .25s ease-out forwards}
    .toast-card .icon{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;flex:0 0 28px}
    .toast-card .body{flex:1;line-height:1.35}
    .toast-card .title{font-weight:600;margin-bottom:2px}
    .toast-card .text{font-size:14px;color:#333}
    .toast-card .close{background:none;border:none;color:#666;cursor:pointer;font-size:16px;line-height:1;padding:2px 4px}
    .toast-success{border-left-color:#22c55e}
    .toast-success .icon{background:#22c55e}
    .toast-danger{border-left-color:#ef4444}
    .toast-danger .icon{background:#ef4444}
    @keyframes toastIn{to{opacity:1;transform:translateY(0)}}
    @keyframes toastOut{to{opacity:0;transform:translateY(-10px)}}
  </style>
  <div class="toast-stack" id="toast-stack">
    @foreach ($messages as $msg)
      @php
        $cls = $msg['type'] === 'danger' ? 'toast-danger' : 'toast-success';
        $title = $msg['type'] === 'danger' ? 'Lỗi' : 'Thành công';
        $icon  = $msg['type'] === 'danger' ? 'fa-circle-xmark' : 'fa-circle-check';
      @endphp
      <div class="toast-card {{ $cls }}">
        <div class="icon"><i class="fa-solid {{ $icon }}"></i></div>
        <div class="body">
          <div class="title">{{ $title }}</div>
          <div class="text">{!! nl2br(e($msg['text'])) !!}</div>
        </div>
        <button class="close" aria-label="Đóng" onclick="this.closest('.toast-card').remove()">×</button>
      </div>
    @endforeach
  </div>
  <script>
    // Tự đóng sau 4s, giống trải nghiệm toast của admin
    (function(){
      const stack = document.getElementById('toast-stack');
      if(!stack) return;
      const toasts = stack.querySelectorAll('.toast-card');
      toasts.forEach((el, idx) => {
        const t = setTimeout(() => {
          el.style.animation = 'toastOut .2s ease-in forwards';
          setTimeout(() => el.remove(), 220);
        }, 4000 + idx*300); // so le nhe neu co nhieu toast
      });
    })();
  </script>
@endif

