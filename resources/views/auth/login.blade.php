@extends('layouts.auth')

@section('title', 'Giriş Yap')

@section('content')
<div class="container login-container">
    <div class="card">
        <div class="card-body">
            <div class="brand-header">
                <h1>E-Veresiye Defteri</h1>
                <p>Otomasyon Sistemi Girişi</p>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group">
                    <span class="input-group-text">Email:</span>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Şifre:</span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-primary w-50 me-2" onclick="window.location.href='{{ route('register') }}'">Kayıt Ol</button>
                    <button type="submit" class="btn btn-success w-50">Giriş Yap</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection