<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Satış Ekranı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        html {
            font-size: 15px;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            padding: 0;
        }
     .container-fluid {
    padding: 10px !important;
}
		.row {
    height: 100% !important;
}
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
			margin-bottom: 0 !important;
    		border-radius: 0 !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
		
        .header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            padding: 10px;
            height: 100%;
            border-radius: 5px;
            overflow-y: auto;
        }
        .sidebar h5 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .sidebar .totals {
            background-color: #495057;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .sidebar .totals h6 {
            font-size: 0.9rem;
            margin: 5px 0;
        }
        .sidebar .totals h5 {
            font-size: 1rem;
            font-weight: bold;
            color: #ff4444;
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 68, 68, 0.5);
        }
        .sidebar button, .sidebar select, .sidebar input {
            width: 100%;
            margin: 5px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }
        .sidebar .button-group {
            display: flex;
            flex-direction: row;
            gap: 5px;
            flex-wrap: wrap;
        }
        .sidebar .button-group button {
            flex: 1;
            min-width: 100px;
        }
        .center-area {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            flex-wrap: nowrap;
            align-items: center;
        }
        .search-bar input, .search-bar select, .search-bar button {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
            flex: 1;
        }
        .cart-area {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .cart-table {
            flex-grow: 1;
            overflow-y: auto;
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 8px;
            font-size: 0.85rem;
        }
        .table tr:hover {
            background-color: #f1f1f1;
        }
        .table tr.table-active {
            background-color: #e9ecef;
        }
        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
			position: relative;
			bottom: 5px;
        }
        .keypad button {
            padding: 10px;
            font-size: 0.9rem;
            border-radius: 5px;
            border: none;
            background-color: #e9ecef;
            transition: background-color 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .keypad button:hover {
            background-color: #d1d5db;
        }
        .keypad .hold-btn {
            background-color: #ffc107;
            color: black;
        }
        .keypad .held-sale-btn {
            background-color: #17a2b8;
            color: white;
        }
        .category-list {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            height: 100%;
            display: flex;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .category-sidebar {
            width: 40%;
            padding: 10px;
            overflow-y: auto;
        }
        .category-item {
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: block;
            width: 100%;
        }
        .category-item:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: #adb5bd;
        }
        .category-item.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            border-color: #006fe6;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .category-item p {
            margin: 0;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .product-display {
            width: 60%;
            padding-left: 5px;
            overflow-y: auto;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }
        .product-item {
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }
        .product-item:hover {
            background-color: #e9ecef;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .product-item img {
            width: 100%;
            height: 80px;
            border-radius: 5px;
            object-fit: cover;
        }
        .product-item p {
            margin: 5px 0 0;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .product-item small {
            color: #6c757d;
            font-size: 0.75rem;
            display: block;
        }
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }
        .modal-body p {
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .invoice-modal .modal-body {
            font-family: 'Arial', sans-serif;
        }
        .invoice-modal .modal-body h5 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .invoice-modal .modal-body table {
            width: 100%;
            margin-bottom: 15px;
        }
        .invoice-modal .modal-body table th,
        .invoice-modal .modal-body table td {
            padding: 8px;
            text-align: left;
        }
        .invoice-modal .modal-body .totals {
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85rem;
            display: none;
        }
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
        .barcode-scanner {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .barcode-scanner-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .barcode-scanner-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        .barcode-scanner-btn.active {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
        }
        .barcode-scanner-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1050;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .barcode-scanner-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            position: relative;
        }
        .barcode-scanner-video {
            width: 100%;
            border-radius: 8px;
            border: 2px solid #007bff;
            background-color: #000;
        }
        .barcode-scanner-instruction {
            margin: 15px 0;
            font-size: 1.1rem;
            color: #333;
            text-align: center;
        }
        .barcode-scanner-result {
            margin-top: 10px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-size: 1rem;
            text-align: center;
            width: 100%;
        }
        .barcode-scanner-close {
            position: absolute;
            top: 0;
            right: 5px;
            font-size: 2rem;
            color: #333;
            cursor: pointer;
            transition: color 0.2s;
        }
        .barcode-scanner-close:hover {
            color: #dc3545;
        }
        @media (max-width: 1200px) {
            .container-fluid {
                height: auto;
                overflow-y: auto;
            }
      .sidebar, .center-area, .category-list {
    height: 100% !important;
    margin-bottom: 0 !important;
}
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
            .product-item img {
                height: 60px;
            }
            .keypad button {
                padding: 8px;
                font-size: 0.85rem;
            }
            .search-bar {
                flex-wrap: wrap;
            }
            .search-bar input, .search-bar select, .search-bar button {
                flex: 1 1 100%;
                margin-bottom: 5px;
            }
        }
        @media (max-width: 992px) {
            body {
                overflow-y: auto;
                height: auto;
            }
            .container-fluid {
                padding: 10px;
            }
            .row {
                flex-direction: column;
            }
            .sidebar, .center-area, .category-list {
                margin-bottom: 15px;
                max-height: 400px;
                overflow-y: auto;
            }
            .sidebar .totals h5 {
                font-size: 1.3rem;
            }
            .category-list {
                flex-direction: column;
            }
            .category-sidebar {
                width: 100%;
                border-bottom: 1px solid #dee2e6;
                max-height: 200px;
            }
            .product-display {
                width: 100%;
                padding-left: 0;
                padding-top: 10px;
            }
            .cart-table {
                overflow-x: auto;
            }
            .table th, .table td {
                font-size: 0.8rem;
                padding: 6px;
            }
            .keypad {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            body {
                overflow-y: auto;
                height: auto;
            }
            .container-fluid {
                padding: 5px;
            }
            .header {
                padding: 8px 10px;
                height: 50px;
            }
            .header h4 {
                font-size: 1rem;
            }
            .header a {
                font-size: 0.9rem;
            }
            .sidebar {
                padding: 10px;
                max-height: 350px;
            }
            .sidebar h5 {
                font-size: 1rem;
            }
            .sidebar .totals h6 {
                font-size: 0.85rem;
            }
            .sidebar .totals h5 {
                font-size: 1.2rem;
            }
            .sidebar .button-group button {
                min-width: 80px;
                padding: 8px;
                font-size: 0.85rem;
            }
            .search-bar {
                flex-direction: column;
                gap: 5px;
            }
            .search-bar input, .search-bar select, .search-bar button {
                width: 100%;
                padding: 8px;
                font-size: 0.85rem;
            }
            #findProduct, #productSelect {
                position: static;
                width: 100% !important;
            }
            .cart-area {
                padding: 5px;
            }
            .cart-table {
                overflow-x: auto;
            }
            .table th, .table td {
                font-size: 0.75rem;
                padding: 5px;
                white-space: nowrap;
            }
            .keypad {
                display: none;
            }
            .category-item {
                padding: 6px 8px;
                margin-bottom: 5px;
            }
            .category-item p {
                font-size: 0.8rem;
            }
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            }
            .product-item p {
                font-size: 0.8rem;
            }
            .product-item small {
                font-size: 0.7rem;
            }
            .barcode-scanner {
                bottom: 10px;
                right: 10px;
            }
            .barcode-scanner-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            .barcode-scanner-container {
                padding: 15px;
                width: 95%;
            }
            .barcode-scanner-instruction {
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="header">
        <h4>Satış Ekranı</h4>
        <div>
            <a href="{{ route('dashboard') }}">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row h-100">
            <div class="col-lg-5 col-md-3 col-sm-12">
                <div class="sidebar">
                    <h5>İşlemler</h5>
                    <div class="totals">
                        <div class="d-flex justify-content-between">
                            <h6>Ara Toplam</h6>
                            <h5 id="subTotal">0.00 ₺</h5>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>İndirim</h6>
                            <h5 id="discountTotal">-0.00 ₺</h5>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>Toplam</h6>
                            <h5 id="totalPrice">0.00 ₺</h5>
                        </div>
                    </div>
                    <label for="discountType">İndirim Türü</label>
                    <select id="discountType" class="form-control mb-2">
                        <option value="percentage">Yüzde (%)</option>
                        <option value="fixed">Sabit Tutar (₺)</option>
                    </select>
                    <label for="discountAmount">İndirim Miktarı</label>
                    <div class="d-flex gap-2 mb-2">
                        <input id="discountAmount" type="number" class="form-control" placeholder="İndirim girin" min="0" step="0.01" />
                        <button id="applyDiscount" class="btn btn-primary">Uygula</button>
                    </div>
                    <label for="customerSelect">Müşteri Seçiniz</label>
                    <select name="customer_id" id="customerSelect" class="form-control mb-2">
                        <option value="">Müşteri seçmeyeceğim</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->name }} - {{ $customer->phone ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    <div class="button-group">
                        <button id="cashButton" class="btn btn-primary" disabled>Nakit (N)</button>
                        <button id="cardButton" class="btn btn-success" disabled>Kart (K)</button>
                        <button id="creditButton" class="btn btn-warning" disabled>Veresiye (V)</button>
                    </div>
                    <div class="button-group">
                        <button id="recallSale" class="btn btn-secondary">Bekletilen Satış</button>
                        <button id="returnButton" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#returnModal">İade Yap</button>
                        <button id="clearCart" class="btn btn-danger">Sepeti Temizle</button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-5 col-sm-12">
                <div class="center-area">
                    <div class="search-bar">
                        <input id="barcodeInput" type="text" class="form-control" placeholder="Barkod giriniz" autofocus />
                        <button id="findProduct" class="btn btn-primary">Ürün Bul</button>
                        <select id="productSelect" class="form-control">
                            <option value="">Ürün Seçin</option>
                            @foreach ($products as $product)
                                <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cart-area">
                        <div class="cart-table">
                            <table id="cartTable" class="table table-borderless table-hover">
                                <thead>
                                    <tr class="product-cart__header">
                                        <th>Ürün</th>
                                        <th>Fiyat</th>
                                        <th>Miktar</th>
                                        <th>İndirim</th>
                                        <th class="text-center">Toplam</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="keypad" id="keypad">
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 1</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 2</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 3</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 4</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 5</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 6</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 7</button>
                            <button class="held-sale-btn" data-held-id="" disabled>Müşteri 8</button>
                            <button class="hold-btn" id="holdSale">Beklet</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-4 col-sm-12">
                <div class="category-list">
                    <div class="category-sidebar">
                        <h6>Kategoriler</h6>
                        @if ($categories->isEmpty())
                            <p class="text-muted">Kategori bulunmamaktadır.</p>
                        @else
                            @foreach ($categories as $category)
                                <div class="category-item" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                    <p>{{ $category->name }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="product-display">
                        <h5 id="productDisplayTitle">Ürünler</h5>
                        <div class="product-grid" id="productGrid"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="barcode-scanner">
        <button id="barcodeScannerBtn" class="barcode-scanner-btn" title="Barkod Okuyucu">
            <i class="fas fa-barcode fa-2x"></i>
        </button>
    </div>

    <div id="barcodeScannerModal" class="barcode-scanner-modal" style="display: none;">
        <div class="barcode-scanner-container">
            <span class="barcode-scanner-close" id="closeBarcodeScanner">×</span>
            <div class="barcode-scanner-loading" id="barcodeScannerLoading"></div>
            <video id="barcodeScannerVideo" class="barcode-scanner-video" playsinline></video>
            <div class="barcode-scanner-instruction">
                Barkodu kameraya tutun, otomatik olarak taranacaktır.
            </div>
            <div id="barcodeScannerResult" class="barcode-scanner-result"></div>
        </div>
    </div>

    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnModalLabel">İade İşlemi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="returnProductSelect" class="form-label">Ürün Seçiniz</label>
                        <select id="returnProductSelect" class="form-control">
                            <option value="">Ürün seçin</option>
                            @foreach ($products as $product)
                                <option value="{{ $product['id'] }}" data-price="{{ $product['sell_price'] }}" data-stock="{{ $product['stock_quantity'] }}">
                                    {{ $product['name'] }} ({{ $product['barcode'] ?? 'Barkodsuz' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Lütfen bir ürün seçiniz.</div>
                        <div id="productInfo" class="mt-2" style="display: none;">
                            <p><strong>Ürün:</strong> <span id="productName"></span></p>
                            <p><strong>Fiyat:</strong> <span id="productPrice"></span> ₺</p>
                            <p><strong>Stok:</strong> <span id="productStock"></span></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="returnQuantity" class="form-label">İade Miktarı</label>
                        <input type="number" class="form-control" id="returnQuantity" min="1" placeholder="Miktar giriniz">
                        <div class="invalid-feedback">Lütfen geçerli bir miktar giriniz (en az 1).</div>
                    </div>
                    <div class="mb-3">
                        <label for="returnReason" class="form-label">İade Sebebi</label>
                        <select class="form-control" id="returnReason">
                            <option value="">Seçiniz</option>
                            <option value="Hatalı Ürün">Hatalı Ürün</option>
                            <option value="Müşteri İptali">Müşteri İptali</option>
                            <option value="Stok Fazlası">Stok Fazlası</option>
                        </select>
                        <div class="invalid-feedback">Lütfen bir iade sebebi seçiniz.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="submitReturn">Onayla</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade invoice-modal" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalLabel">Satış Faturası</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>Fatura Detayları</h5>
                    <p><strong>Fatura No:</strong> <span id="invoiceId"></span></p>
                    <p><strong>Tarih:</strong> <span id="invoiceDate"></span></p>
                    <p><strong>Müşteri:</strong> <span id="invoiceCustomer"></span></p>
                    <p><strong>Ödeme Türü:</strong> <span id="invoicePayType"></span></p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Miktar</th>
                                <th>Birim Fiyat</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItems"></tbody>
                    </table>
                    <div class="totals">
                        <p><strong>Ara Toplam:</strong> <span id="invoiceSubTotal"></span> ₺</p>
                        <p><strong>İndirim:</strong> <span id="invoiceDiscount"></span> ₺</p>
                        <p><strong>Genel Toplam:</strong> <span id="invoiceTotal"></span> ₺</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="printInvoice">Yazdır</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="heldSalesModal" tabindex="-1" aria-labelledby="heldSalesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="heldSalesModalLabel">Bekletilen Satışları Seç</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="heldSalesList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="cartData" value='{"items":[],"discount_percentage":0,"discount_fixed":0,"sub_total":0,"discount_total":0,"total_price":0}'>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <script>
        class SalesManager {
            constructor() {
                this.baseUrl = document.querySelector('meta[name="app-url"]').content + '/dashboard';
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                this.storeUrl = `${this.baseUrl}/satisyap`;
                this.userRole = '{{ auth()->user()->role }}';
                this.ROLE_ADMIN = '{{ \App\Models\User::ROLE_ADMIN }}';
                this.ROLE_PERSONNEL = '{{ \App\Models\User::ROLE_PERSONNEL }}';
                this.cart = {
                    items: [],
                    discount_percentage: 0,
                    discount_fixed: 0,
                    sub_total: 0,
                    discount_total: 0,
                    total_price: 0
                };
                this.selectedRow = null;
                this.barcodeScannerActive = false;
                this.lastScannedBarcode = null;
                this.scanTimeout = null;
                this.initBarcodeScanner();
                this.initEventListeners();
                this.loadFirstCategory();
                this.updateKeypadButtons();
            }

            initBarcodeScanner() {
                this.barcodeScannerConfig = {
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#barcodeScannerVideo'),
                        constraints: {
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
                            facingMode: "environment"
                        }
                    },
                    decoder: {
                        readers: [
                            "ean_reader",
                            "ean_8_reader",
                            "code_128_reader",
                            "upc_reader",
                            "upc_e_reader"
                        ]
                    },
                    locate: true,
                    numOfWorkers: navigator.hardwareConcurrency || 4,
                    frequency: 10
                };
            }

            initEventListeners() {
                document.getElementById('barcodeScannerBtn').addEventListener('click', () => {
                    if (this.barcodeScannerActive) {
                        this.stopBarcodeScanner();
                    } else {
                        this.startBarcodeScanner();
                    }
                });

                document.getElementById('closeBarcodeScanner').addEventListener('click', () => {
                    this.stopBarcodeScanner();
                });

                document.getElementById('barcodeInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const barcode = e.target.value.trim();
                        if (barcode) this.fetchProductByBarcode(barcode);
                        else this.showError('Lütfen bir barkod giriniz.');
                    }
                });

                document.getElementById('findProduct').addEventListener('click', () => {
                    const barcode = document.getElementById('barcodeInput').value.trim();
                    if (barcode) this.fetchProductByBarcode(barcode);
                    else this.showError('Lütfen bir barkod giriniz.');
                });

                document.getElementById('productSelect').addEventListener('change', (e) => {
                    const productId = e.target.value;
                    if (productId) {
                        this.fetchProductById(productId);
                        e.target.value = '';
                    }
                });

                document.querySelectorAll('.category-item').forEach(item => {
                    item.addEventListener('click', () => {
                        document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
                        item.classList.add('active');
                        const categoryId = item.dataset.id;
                        const categoryName = item.dataset.name;
                        this.fetchProductsByCategory(categoryId, categoryName);
                    });
                });

                document.getElementById('productGrid').addEventListener('click', (e) => {
                    const productItem = e.target.closest('.product-item');
                    if (productItem) {
                        const productId = productItem.dataset.id;
                        this.fetchProductById(productId);
                    }
                });

                document.getElementById('cartTable').addEventListener('change', (e) => {
                    if (e.target.classList.contains('qty')) {
                        const row = e.target.closest('tr');
                        this.updateSubtotal(row);
                    }
                });

                document.getElementById('cartTable').addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-btn')) {
                        if (confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')) {
                            e.target.closest('tr').remove();
                            this.updateTotals();
                            this.updatePaymentButtons();
                        }
                    } else if (!e.target.classList.contains('qty') && !e.target.classList.contains('remove-btn')) {
                        document.querySelectorAll('#cartTable tr').forEach(row => row.classList.remove('table-active'));
                        e.target.closest('tr').classList.add('table-active');
                        this.selectedRow = e.target.closest('tr');
                    }
                });

                document.getElementById('applyDiscount').addEventListener('click', () => {
                    const discountType = document.getElementById('discountType').value;
                    const discountValue = parseFloat(document.getElementById('discountAmount').value) || 0;

                    if (discountValue < 0) {
                        this.showError('İndirim miktarı negatif olamaz.');
                        return;
                    }

                    if (discountType === 'percentage') {
                        if (discountValue > 100) {
                            this.showError('Yüzde indirim 100\'den büyük olamaz.');
                            return;
                        }
                        this.cart.discount_percentage = discountValue;
                        this.cart.discount_fixed = 0;
                    } else {
                        if (discountValue > this.cart.sub_total) {
                            this.showError('Sabit indirim miktarı ara toplamdan büyük olamaz.');
                            return;
                        }
                        this.cart.discount_fixed = discountValue;
                        this.cart.discount_percentage = 0;
                    }

                    this.updateTotals();
                    this.showSuccess('İndirim uygulandı.');
                });

                document.getElementById('clearCart').addEventListener('click', () => {
                    if (confirm('Sepeti temizlemek istediğinize emin misiniz?')) {
                        document.getElementById('cartTable').innerHTML = '';
                        this.resetCart();
                        this.updateTotals();
                        this.updatePaymentButtons();
                        document.getElementById('customerSelect').value = '';
                        this.checkCustomerDebt('');
                    }
                });

                document.getElementById('holdSale').addEventListener('click', () => {
                    if (!this.cart.items.length) {
                        this.showError('Bekletilecek bir satış yok.');
                        return;
                    }

                    let heldSales = JSON.parse(localStorage.getItem('heldSales')) || [];
                    if (heldSales.length >= 8) {
                        this.showError('Maksimum 8 bekletilen satış olabilir.');
                        return;
                    }

                    const saleData = {
                        id: Date.now(),
                        basket: JSON.stringify(this.cart),
                        timestamp: new Date().toLocaleString('tr-TR'),
                        customer_id: document.getElementById('customerSelect').value
                    };
                    heldSales.push(saleData);
                    localStorage.setItem('heldSales', JSON.stringify(heldSales));

                    this.showSuccess('Satış bekletildi.');
                    document.getElementById('cartTable').innerHTML = '';
                    this.resetCart();
                    this.updateTotals();
                    this.updatePaymentButtons();
                    document.getElementById('customerSelect').value = '';
                    this.checkCustomerDebt('');
                    this.updateKeypadButtons();
                });

                document.getElementById('recallSale').addEventListener('click', () => {
                    this.listHeldSales();
                    const heldSalesModal = new bootstrap.Modal(document.getElementById('heldSalesModal'));
                    heldSalesModal.show();
                });

                document.getElementById('cashButton').addEventListener('click', () => this.processSale('Nakit'));
                document.getElementById('cardButton').addEventListener('click', () => this.processSale('Kart'));
                document.getElementById('creditButton').addEventListener('click', () => this.processSale('Veresiye'));

                document.getElementById('customerSelect').addEventListener('change', (e) => {
                    this.checkCustomerDebt(e.target.value);
                });
                this.checkCustomerDebt(document.getElementById('customerSelect').value);

                const returnModal = document.getElementById('returnModal');
                returnModal.addEventListener('show.bs.modal', () => this.resetReturnModal());

                document.getElementById('returnProductSelect').addEventListener('change', (e) => {
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const productId = e.target.value;
                    const productName = selectedOption.text.split(' (')[0];
                    const productPrice = selectedOption.dataset.price;
                    const productStock = selectedOption.dataset.stock;

                    if (!productId) {
                        document.getElementById('productInfo').style.display = 'none';
                        e.target.classList.add('is-invalid');
                        return;
                    }

                    e.target.classList.remove('is-invalid');
                    document.getElementById('productName').textContent = productName;
                    document.getElementById('productPrice').textContent = parseFloat(productPrice).toFixed(2);
                    document.getElementById('productStock').textContent = productStock;
                    document.getElementById('productInfo').style.display = 'block';
                });

                document.getElementById('returnQuantity').addEventListener('input', (e) => {
                    const quantity = parseInt(e.target.value) || 0;
                    if (quantity <= 0) {
                        e.target.classList.add('is-invalid');
                    } else {
                        e.target.classList.remove('is-invalid');
                    }
                });

                document.getElementById('returnReason').addEventListener('change', (e) => {
                    if (!e.target.value) {
                        e.target.classList.add('is-invalid');
                    } else {
                        e.target.classList.remove('is-invalid');
                    }
                });

                document.getElementById('submitReturn').addEventListener('click', () => this.submitReturn());

                document.getElementById('printInvoice').addEventListener('click', () => {
                    const printContent = document.querySelector('#invoiceModal .modal-body').innerHTML;
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(`
                        <html>
                            <head>
                                <title>Fatura</title>
                                <style>
                                    body { font-family: Arial, sans-serif; padding: 20px; }
                                    table { width: 100%; border-collapse: collapse; }
                                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                    th { background-color: #f2f2f2; }
                                    .totals { margin-top: 20px; }
                                </style>
                            </head>
                            <body onload="window.print(); window.close();">
                                ${printContent}
                            </body>
                        </html>
                    `);
                    printWindow.document.close();
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'n' || e.key === 'N') document.getElementById('cashButton').click();
                    if (e.key === 'k' || e.key === 'K') document.getElementById('cardButton').click();
                    if (e.key === 'v' || e.key === 'V') document.getElementById('creditButton').click();
                });

                document.getElementById('keypad').addEventListener('click', (e) => {
                    if (e.target.classList.contains('held-sale-btn') && e.target.dataset.heldId) {
                        this.recallSale(e.target.dataset.heldId);
                    }
                });
            }

            updateKeypadButtons() {
                const heldSales = JSON.parse(localStorage.getItem('heldSales')) || [];
                const buttons = document.querySelectorAll('.held-sale-btn');
                buttons.forEach((btn, index) => {
                    if (index < heldSales.length) {
                        const sale = heldSales[index];
                        const customerSelect = document.getElementById('customerSelect');
                        const customerOption = customerSelect.querySelector(`option[value="${sale.customer_id}"]`);
                        const customerName = customerOption ? customerOption.text.split(' - ')[0] : `Müşteri ${index + 1}`;
                        btn.textContent = customerName;
                        btn.dataset.heldId = sale.id;
                        btn.disabled = false;
                    } else {
                        btn.textContent = `Müşteri ${index + 1}`;
                        btn.dataset.heldId = '';
                        btn.disabled = true;
                    }
                });
            }

            async startBarcodeScanner() {
                if (this.barcodeScannerActive) return;

                try {
                    document.getElementById('barcodeScannerModal').style.display = 'flex';
                    document.getElementById('barcodeScannerBtn').classList.add('active');
                    document.getElementById('barcodeScannerResult').textContent = '';
                    document.getElementById('barcodeScannerLoading').style.display = 'block';

                    await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });

                    Quagga.init(this.barcodeScannerConfig, (err) => {
                        if (err) {
                            console.error('Barkod okuyucu başlatılamadı:', err);
                            this.showError('Barkod okuyucu başlatılamadı. Lütfen kamera erişimine izin verin.');
                            this.stopBarcodeScanner();
                            return;
                        }

                        document.getElementById('barcodeScannerLoading').style.display = 'none';
                        Quagga.start();
                        this.barcodeScannerActive = true;

                        this.scanTimeout = setTimeout(() => {
                            this.stopBarcodeScanner();
                            this.showError('Barkod tarama zaman aşımına uğradı.');
                        }, 30000);

                        Quagga.onDetected((result) => {
                            const code = result.codeResult.code;
                            if (code === this.lastScannedBarcode) return;

                            console.log('Barkod okundu:', code);
                            document.getElementById('barcodeScannerResult').textContent = `Okunan Barkod: ${code}`;
                            this.lastScannedBarcode = code;

                            clearTimeout(this.scanTimeout);
                            setTimeout(() => {
                                this.stopBarcodeScanner();
                                this.fetchProductByBarcode(code);
                            }, 500);
                        });
                    });
                } catch (err) {
                    console.error('Kamera erişim hatası:', err);
                    this.showError('Kamera erişimi sağlanamadı. Lütfen izinleri kontrol edin.');
                    this.stopBarcodeScanner();
                }
            }

            stopBarcodeScanner() {
                if (!this.barcodeScannerActive) {
                    // Ensure modal is hidden even if scanner isn't active
                    document.getElementById('barcodeScannerModal').style.display = 'none';
                    return;
                }

                try {
                    Quagga.stop();
                } catch (err) {
                    console.error('Error stopping Quagga:', err);
                }

                this.barcodeScannerActive = false;
                this.lastScannedBarcode = null;
                clearTimeout(this.scanTimeout);
                document.getElementById('barcodeScannerModal').style.display = 'none';
                document.getElementById('barcodeScannerBtn').classList.remove('active');
                document.getElementById('barcodeScannerLoading').style.display = 'none';
                document.getElementById('barcodeScannerResult').textContent = '';

                // Release camera stream
                const video = document.getElementById('barcodeScannerVideo');
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                }
            }

            loadFirstCategory() {
                const firstCategory = document.querySelector('.category-item');
                if (firstCategory) {
                    firstCategory.classList.add('active');
                    const categoryId = firstCategory.dataset.id;
                    const categoryName = firstCategory.dataset.name;
                    this.fetchProductsByCategory(categoryId, categoryName);
                }
            }

            async fetchProductById(productId) {
                try {
                    this.showLoading();
                    console.log(`Fetching product by ID: ${productId}`);
                    const response = await fetch(`${this.baseUrl}/urun/get-by-id`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ id: productId })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Ürün bilgileri alınamadı.');
                    }

                    const product = await response.json();
                    console.log('Product data:', product);
                    this.addProductToCart(product);
                } catch (error) {
                    console.error('Fetch product by ID error:', error);
                    this.showError(`Ürün eklenemedi: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }

            async fetchProductByBarcode(barcode) {
                try {
                    this.showLoading();
                    console.log(`Fetching product by barcode: ${barcode}`);
                    const response = await fetch(`${this.baseUrl}/urun/get-by-barcode`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ barcode })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.error || 'Barkod ile ürün bulunamadı.');
                    }

                    const product = await response.json();
                    console.log('Product data:', product);
                    this.addProductToCart(product);
                    document.getElementById('barcodeInput').value = '';
                } catch (error) {
                    console.error('Fetch product by barcode error:', error);
                    this.showError(`Ürün eklenemedi: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }

            async fetchProductsByCategory(categoryId, categoryName) {
                try {
                    if (!categoryId || isNaN(categoryId) || categoryId <= 0) {
                        throw new Error('Geçersiz kategori ID.');
                    }

                    this.showLoading();
                    console.log(`Fetching products for category ID: ${categoryId}`);
                    const response = await fetch(`${this.baseUrl}/urun/get-by-category`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ category_id: parseInt(categoryId) })
                    });

                    if (!response.ok) {
                        const contentType = response.headers.get('content-type');
                        let errorMessage = 'Kategoriye ait ürünler alınamadı.';

                        if (contentType && contentType.includes('application/json')) {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorMessage;
                        } else {
                            const text = await response.text();
                            console.error('Non-JSON response received:', text);
                            errorMessage = 'Sunucudan beklenmeyen bir yanıt alındı.';
                        }

                        throw new Error(errorMessage);
                    }

                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        const text = await response.text();
                        console.error('Non-JSON response received:', text);
                        throw new Error('Sunucudan beklenmeyen bir yanıt alındı: JSON formatı bekleniyordu.');
                    }

                    const products = await response.json();
                    console.log('Category products:', products);
                    this.displayCategoryProducts(products, categoryName);
                } catch (error) {
                    console.error('Fetch products by category error:', error);
                    this.showError(`Ürünler alınamadı: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }

            displayCategoryProducts(products, categoryName) {
                const grid = document.getElementById('productGrid');
                grid.innerHTML = '';
                document.getElementById('productDisplayTitle').textContent = `${categoryName} - Ürünler`;

                if (!products || products.length === 0) {
                    grid.innerHTML = `
                        <div class="w-100">
                            <p class="text-center text-muted">Bu kategoride ürün bulunmamaktadır.</p>
                        </div>
                    `;
                } else {
                    products.forEach(product => {
                        const productItem = document.createElement('div');
                        productItem.className = 'product-item';
                        productItem.dataset.id = product.id;
                        productItem.innerHTML = `
                            <img src="${product.image || 'https://via.placeholder.com/80?text=Resim+Yok'}" alt="${product.name}">
                            <p>${product.name}</p>
                            <small>Fiyat: ${parseFloat(product.sell_price).toFixed(2)} ₺<br>Stok: ${product.stock_quantity}</small>
                        `;
                        grid.appendChild(productItem);
                    });
                }
            }

            addProductToCart(product, quantity = 1) {
                if (!product) {
                    console.error('Product is undefined or null');
                    this.showError('Ürün verisi bulunamadı.');
                    return;
                }

                const requiredFields = ['id', 'name', 'sell_price', 'stock_quantity'];
                const missingFields = requiredFields.filter(field => product[field] === undefined || product[field] === null);
                if (missingFields.length > 0) {
                    console.error(`Missing product fields: ${missingFields.join(', ')}`);
                    this.showError(`Geçersiz ürün verisi: ${missingFields.join(', ')} eksik.`);
                    return;
                }

                console.log(`Adding product to cart: ${product.name}, Quantity: ${quantity}`);
                const existingRow = document.querySelector(`#cartTable tr[data-id="${product.id}"]`);
                let tbody = document.querySelector('#cartTable tbody');

                if (!tbody) {
                    tbody = document.createElement('tbody');
                    document.getElementById('cartTable').appendChild(tbody);
                }

                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.qty');
                    let newQty = parseInt(qtyInput.value) + quantity;
                    if (newQty > product.stock_quantity) {
                        console.warn(`Stock insufficient: ${product.name}, Available: ${product.stock_quantity}`);
                        this.showError(`Stok yetersiz! ${product.name} için mevcut stok: ${product.stock_quantity}`);
                        return;
                    }
                    qtyInput.value = newQty;
                    this.updateSubtotal(existingRow);
                } else {
                    if (quantity > product.stock_quantity) {
                        console.warn(`Stock insufficient: ${product.name}, Available: ${product.stock_quantity}`);
                        this.showError(`Stok yetersiz! ${product.name} için mevcut stok: ${product.stock_quantity}`);
                        return;
                    }
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', product.id);
                    row.setAttribute('data-barcode', product.barcode || '');
                    row.innerHTML = `
                        <td class="description">${product.name}</td>
                        <td class="price" data-price="${product.sell_price}">${parseFloat(product.sell_price).toFixed(2)} ₺</td>
                        <td><input type="number" class="qty" value="${quantity}" min="1" max="${product.stock_quantity}" data-stock="${product.stock_quantity}"></td>
                        <td class="discount">0.00 ₺</td>
                        <td class="subtotal text-center">${(parseFloat(product.sell_price) * quantity).toFixed(2)} ₺</td>
                        <td><button class="btn btn-sm btn-danger remove-btn">Sil</button></td>
                    `;
                    tbody.appendChild(row);
                    this.updateSubtotal(row);
                }
                this.updatePaymentButtons();
                this.showSuccess(`${product.name} sepete eklendi.`);
            }

            updateSubtotal(row) {
                if (!row) {
                    console.error('Row is null in updateSubtotal');
                    return;
                }
                const qtyInput = row.querySelector('.qty');
                const price = parseFloat(row.querySelector('.price').dataset.price);
                const stock = parseInt(qtyInput.dataset.stock);
                let qty = parseInt(qtyInput.value);

                if (isNaN(qty) || qty <= 0) {
                    qty = 1;
                    qtyInput.value = 1;
                }

                if (qty > stock) {
                    console.warn(`Stock exceeded: Max ${stock}`);
                    this.showError(`Stok yetersiz! Mevcut stok: ${stock}`);
                    qtyInput.value = stock;
                    qty = stock;
                }

                const subtotal = price * qty;
                const subtotalCell = row.querySelector('.subtotal');
                if (subtotalCell) {
                    subtotalCell.textContent = subtotal.toFixed(2) + ' ₺';
                } else {
                    console.error('Subtotal cell not found in row');
                }
                this.updateTotals();
            }

            updateTotals() {
                const subtotalElements = document.querySelectorAll('#cartTable .subtotal');
                this.cart.sub_total = subtotalElements.length > 0
                    ? Array.from(subtotalElements).reduce((total, el) => {
                          const value = el.textContent ? parseFloat(el.textContent.replace(' ₺', '')) : 0;
                          return total + (isNaN(value) ? 0 : value);
                      }, 0)
                    : 0;

                this.cart.discount_total = this.cart.discount_percentage > 0
                    ? this.cart.sub_total * (this.cart.discount_percentage / 100)
                    : this.cart.discount_fixed;

                this.cart.total_price = this.cart.sub_total - this.cart.discount_total;

                document.getElementById('subTotal').textContent = this.cart.sub_total.toFixed(2) + ' ₺';
                document.getElementById('discountTotal').textContent = '-' + this.cart.discount_total.toFixed(2) + ' ₺';
                document.getElementById('totalPrice').textContent = this.cart.total_price.toFixed(2) + ' ₺';

                this.saveCartData();
            }

            saveCartData() {
                const rows = document.querySelectorAll('#cartTable tr');
                this.cart.items = Array.from(rows).map(row => {
                    const subtotalCell = row.querySelector('.subtotal');
                    return {
                        product_id: row.dataset.id,
                        barcode: row.dataset.barcode,
                        name: row.querySelector('.description')?.textContent || '',
                        price: parseFloat(row.querySelector('.price')?.dataset.price || 0),
                        quantity: parseInt(row.querySelector('.qty')?.value || 0),
                        subtotal: subtotalCell ? parseFloat(subtotalCell.textContent.replace(' ₺', '')) : 0
                    };
                }).filter(item => item.product_id && item.quantity > 0);

                try {
                    document.getElementById('cartData').value = JSON.stringify(this.cart);
                    console.log('Cart data saved:', this.cart);
                } catch (error) {
                    console.error('Error saving cart data:', error);
                    this.showError('Sepet verisi kaydedilemedi.');
                }
            }

            resetCart() {
                this.cart = {
                    items: [],
                    discount_percentage: 0,
                    discount_fixed: 0,
                    sub_total: 0,
                    discount_total: 0,
                    total_price: 0
                };
                document.getElementById('cartData').value = JSON.stringify(this.cart);
            }

            updatePaymentButtons() {
                const hasItems = this.cart.items.length > 0;
                document.getElementById('cashButton').disabled = !hasItems;
                document.getElementById('cardButton').disabled = !hasItems;
                document.getElementById('creditButton').disabled = !hasItems;
            }

            async processSale(paymentType) {
                if (!this.cart.items.length) {
                    console.warn('Attempted sale with empty cart');
                    this.showError('Sepet boş! Lütfen ürün ekleyin.');
                    return;
                }

                let customerId = document.getElementById('customerSelect').value;
                if (paymentType === 'Veresiye') {
                    if (!customerId) {
                        this.showError('Veresiye satış için müşteri seçimi zorunludur.');
                        return;
                    }
                } else {
                    customerId = customerId || null;
                }

                try {
                    this.showLoading();
                    console.log(`Processing sale: ${paymentType}, Customer ID: ${customerId}, Cart:`, this.cart);
                    const response = await fetch(this.storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            basket: JSON.stringify(this.cart),
                            customer_id: customerId,
                            payment_type: paymentType,
                            _token: this.csrfToken
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Satış işlemi başarısız.');
                    }

                    const data = await response.json();
                    console.log('Sale response:', data);
                    if (data.success && data.invoice) {
                        this.showSuccess(data.message);
                        document.getElementById('cartTable').innerHTML = '';
                        this.resetCart();
                        this.updateTotals();
                        this.updatePaymentButtons();
                        document.getElementById('customerSelect').value = '';
                        this.checkCustomerDebt('');
                        this.updateKeypadButtons();

                        const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
                        const invoice = data.invoice;

                        document.getElementById('invoiceId').textContent = invoice.sale_id || 'N/A';
                        document.getElementById('invoiceDate').textContent = invoice.created_at || new Date().toLocaleString('tr-TR');
                        document.getElementById('invoiceCustomer').textContent = invoice.customer_name || 'Müşteri Seçilmedi';
                        document.getElementById('invoicePayType').textContent = invoice.pay_type_text || paymentType;
                        document.getElementById('invoiceItems').innerHTML = invoice.items.map(item => `
                            <tr>
                                <td>${item.name || 'Bilinmeyen Ürün'}</td>
                                <td>${item.quantity || 0}</td>
                                <td>${(item.price || 0).toFixed(2)} ₺</td>
                                <td>${((item.quantity || 0) * (item.price || 0)).toFixed(2)} ₺</td>
                            </tr>
                        `).join('');
                        document.getElementById('invoiceSubTotal').textContent = (invoice.sub_total || 0).toFixed(2);
                        document.getElementById('invoiceDiscount').textContent = (invoice.discount_total || 0).toFixed(2);
                        document.getElementById('invoiceTotal').textContent = (invoice.total_price || 0).toFixed(2);
                        invoiceModal.show();
                    }
                } catch (error) {
                    console.error('Sale processing error:', error);
                    this.showError(`Satış işlemi sırasında hata: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }

            async checkCustomerDebt(customerId) {
                const creditButton = document.getElementById('creditButton');
                creditButton.disabled = this.cart.items.length === 0;

                if (!customerId || this.userRole === this.ROLE_ADMIN) return;

                try {
                    console.log(`Checking customer debt: ${customerId}`);
                    const response = await fetch(`${this.baseUrl}/check-customer-debt`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({ customer_id: customerId })
                    });

                    const data = await response.json();
                    console.log('Customer debt response:', data);
                    if (!data.success) {
                        throw new Error(data.message || 'Borç kontrolü başarısız.');
                    }

                    if (!data.hasDebt && this.userRole === this.ROLE_PERSONNEL) {
                        creditButton.disabled = true;
                        this.showError('Bu müşteri için veresiye hesabı bulunmamaktadır.');
                    }
                } catch (error) {
                    console.error('Customer debt check error:', error);
                    this.showError(`Müşteri borç durumu kontrol edilirken hata: ${error.message}`);
                    creditButton.disabled = true;
                }
            }

            resetReturnModal() {
                document.getElementById('returnProductSelect').value = '';
                document.getElementById('returnQuantity').value = '';
                document.getElementById('returnReason').value = '';
                document.getElementById('returnProductSelect').classList.remove('is-invalid');
                document.getElementById('returnQuantity').classList.remove('is-invalid');
                document.getElementById('returnReason').classList.remove('is-invalid');
                document.getElementById('productInfo').style.display = 'none';
            }

            async submitReturn() {
                const productId = document.getElementById('returnProductSelect').value;
                const quantity = parseInt(document.getElementById('returnQuantity').value) || 0;
                const reason = document.getElementById('returnReason').value;

                let hasError = false;

                if (!productId) {
                    document.getElementById('returnProductSelect').classList.add('is-invalid');
                    hasError = true;
                }
                if (quantity <= 0) {
                    document.getElementById('returnQuantity').classList.add('is-invalid');
                    hasError = true;
                }
                if (!reason) {
                    document.getElementById('returnReason').classList.add('is-invalid');
                    hasError = true;
                }

                if (hasError) {
                    this.showError('Lütfen tüm alanları doğru şekilde doldurun.');
                    return;
                }

                try {
                    this.showLoading();
                    console.log(`Submitting return: Product ID: ${productId}, Quantity: ${quantity}, Reason: ${reason}`);
                    const response = await fetch(`${this.baseUrl}/return`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: quantity,
                            reason: reason
                        })
                    });

                    const data = await response.json();
                    console.log('Return response:', data);

                    if (!response.ok) {
                        throw new Error(data.message || 'İade işlemi başarısız.');
                    }

                    if (data.success) {
                        this.showSuccess(data.message);
                        bootstrap.Modal.getInstance(document.getElementById('returnModal')).hide();
                        this.resetReturnModal();
                    } else {
                        this.showError(data.message || 'İade işlemi başarısız.');
                    }
                } catch (error) {
                    console.error('Return error:', error);
                    this.showError(`İade işlemi sırasında hata: ${error.message}`);
                } finally {
                    this.hideLoading();
                }
            }

            listHeldSales() {
                const heldSales = JSON.parse(localStorage.getItem('heldSales')) || [];
                const listContainer = document.getElementById('heldSalesList');
                listContainer.innerHTML = '';

                if (heldSales.length === 0) {
                    listContainer.innerHTML = '<li class="list-group-item">Bekletilen satış bulunamadı.</li>';
                    return;
                }

                heldSales.forEach(sale => {
                    const customerSelect = document.getElementById('customerSelect');
                    const customerOption = customerSelect.querySelector(`option[value="${sale.customer_id}"]`);
                    const customerName = customerOption ? customerOption.text.split(' - ')[0] : 'Müşteri Seçilmedi';

                    const li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.innerHTML = `
                        <span>${customerName} - ${sale.timestamp}</span>
                        <div>
                            <button class="btn btn-sm btn-primary recall-btn" data-id="${sale.id}">Geri Yükle</button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${sale.id}">Sil</button>
                        </div>
                    `;
                    listContainer.appendChild(li);
                });

                document.querySelectorAll('.recall-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.recallSale(btn.dataset.id);
                        bootstrap.Modal.getInstance(document.getElementById('heldSalesModal')).hide();
                    });
                });

                document.querySelectorAll('.delete-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        this.deleteHeldSale(btn.dataset.id);
                        this.listHeldSales();
                    });
                });
            }

            recallSale(saleId) {
                const heldSales = JSON.parse(localStorage.getItem('heldSales')) || [];
                const sale = heldSales.find(s => s.id == saleId);

                if (!sale) {
                    this.showError('Bekletilen satış bulunamadı.');
                    return;
                }

                const basketData = JSON.parse(sale.basket);
                document.getElementById('cartTable').innerHTML = '';
                basketData.items.forEach(item => {
                    const product = {
                        id: item.product_id,
                        barcode: item.barcode,
                        name: item.name,
                        sell_price: item.price,
                        stock_quantity: 9999
                    };
                    this.addProductToCart(product, item.quantity);
                });

                this.cart.discount_percentage = basketData.discount_percentage || 0;
                this.cart.discount_fixed = basketData.discount_fixed || 0;
                this.cart.sub_total = basketData.sub_total || 0;
                this.cart.discount_total = basketData.discount_total || 0;
                this.cart.total_price = basketData.total_price || 0;
                document.getElementById('customerSelect').value = sale.customer_id || '';
                this.updateTotals();
                this.updatePaymentButtons();
                this.showSuccess('Bekletilen satış geri yüklendi.');
                this.updateKeypadButtons();
            }

            deleteHeldSale(saleId) {
                let heldSales = JSON.parse(localStorage.getItem('heldSales')) || [];
                heldSales = heldSales.filter(s => s.id != saleId);
                localStorage.setItem('heldSales', JSON.stringify(heldSales));
                this.showSuccess('Bekletilen satış silindi.');
                this.updateKeypadButtons();
            }

            showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            showError(message) {
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '1050';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                setTimeout(() => {
                    bsToast.hide();
                    toast.remove();
                }, 5000);
            }

            showSuccess(message) {
                const toast = document.createElement('div');
                toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '1050';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
                setTimeout(() => {
                    bsToast.hide();
                    toast.remove();
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => new SalesManager());
    </script>
</body>
</html>