@extends('layouts.layout')

@section('title','User Information')

@section('content')
<div class="container py-4">
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  <div class="row justify-content-center">
    <div class="col-12 col-md-8">
      <div class="card">
        <div class="card-header">Thông tin người dùng</div>
        <div class="card-body">
          <div class="mb-3">
            <strong>Tên:</strong> {{ $user->name }}
          </div>
          <div class="mb-3">
            <strong>Email:</strong> {{ $user->email }}
          </div>

          <form method="POST" action="{{ route('user.info.update') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Số điện thoại</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}">
              @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Địa chỉ</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $customer->address) }}">
              @error('address')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">Lưu</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

