<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .dashboard-container {
            background-color: #d3d3d3;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
        }
        .dashboard-title {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }
        .dashboard-button {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
        }
        .dashboard-button:hover {
            background-color: #f5f5f5;
        }
        a {
            text-decoration: none;
        }
        .dashboard-button.green {
            background-color: #28a745;
            color: #fff;
            border: none;
        }
        .dashboard-button.green:hover {
            background-color: #218838;
        }
        .dashboard-button.red {
            background-color: #dc3545;
            color: #fff;
            border: none;
        }
        .dashboard-button.red:hover {
            background-color: #c82333;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
                align-items: flex-start;
            }
            .dashboard-container {
                margin: 10px 0;
                padding: 15px;
                max-height: none;
            }
            .dashboard-title {
                font-size: 20px;
                margin-bottom: 15px;
            }
            .dashboard-button {
                padding: 15px;
                font-size: 16px;
            }
            .row {
                margin-left: 0;
                margin-right: 0;
            }
            .col-12 {
                padding-left: 5px;
                padding-right: 5px;
            }
        }
    </style>
</head>
<body>
    @auth
        @if(auth()->user()->role !== 'customer')
            <div class="container dashboard-container">
                <div class="dashboard-title">ANA MENÜ<br>SATIŞ OTOMASYONU</div>
                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('satisyap') }}" class="dashboard-button">SATIŞ EKRANI</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('satisyonetim') }}" class="dashboard-button">SATIŞ YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('veresiyeyonetimi') }}" class="dashboard-button">VERESİYE YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('musteriyonetimi') }}" class="dashboard-button">MÜŞTERİ YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('urunyonetimi') }}" class="dashboard-button">ÜRÜN YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('kategoriyonetimi') }}" class="dashboard-button">KATEGORİ YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('urun-iade') }}" class="dashboard-button">İADE LİSTESİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('kullaniciyonetimi') }}" class="dashboard-button">KULLANICI YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('personelyonetimi') }}" class="dashboard-button">PERSONEL YÖNETİMİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('other-sales') }}" class="dashboard-button">DİĞER SATIŞLAR</a></div>
                    <div class="col-12 col-md-6 col-lg-4"><a href="{{ route('other-returns') }}" class="dashboard-button">DİĞER SATIŞ İADELERİ</a></div>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="dashboard-button red" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            PROGRAM KAPAT
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="container text-center">
                <div class="alert alert-danger">
                    <h4>Bu sayfaya erişim izniniz yok!</h4>
                    <p>Müşteri paneli için lütfen <a href="{{ route('customer.shopping') }}">buraya</a> tıklayın.</p>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-primary">Çıkış Yap</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        @endif
    @else
        <div class="container text-center">
            <div class="alert alert-warning">
                <h4>Giriş yapmamışsınız!</h4>
                <p>Lütfen <a href="{{ route('login') }}">giriş yapın</a> veya <a href="{{ route('register') }}">kayıt olun</a>.</p>
            </div>
        </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>