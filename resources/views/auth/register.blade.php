@extends('layouts.auth')

@section('title', 'Kayıt Ol')

@section('content')
<div class="container login-container">
    <div class="card">
        <div class="card-body">
            <div class="brand-header">
                <h1>E-Veresiye Defteri</h1>
                <p>Otomasyon Sistemi Kayıt Formu</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="input-group">
                    <span class="input-group-text">Ad & Soyad:</span>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Kullanıcı Adı:</span>
                    <input type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Email Adresi:</span>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Telefon:</span>
                    <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Şifre:</span>
                    <input type="password" class="form-control" id="password-field" name="password" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Şifre Tekrar:</span>
                    <input type="password" class="form-control" id="password-confirmation-field" name="password_confirmation" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-primary w-50 me-2" onclick="window.location.href='{{ route('login') }}'">Giriş Yap</button>
                    <button type="submit" class="btn btn-success w-50">Kayıt Ol</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection