<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Satış Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 10px;
            margin: 0;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 10px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header h4 {
            margin: 0;
            font-weight: 600;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .container-fluid {
            padding: 0 10px;
        }
        .summary-card {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .summary-card h5 {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #333;
        }
        .summary-card p {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0;
            color: #007bff;
        }
        .filter-bar {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 10px;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 0.9rem;
            color: #555;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .filter-bar input,
        .filter-bar select {
            width: 100%;
            border-radius: 5px;
            padding: 8px 12px;
            font-size: 0.9rem;
            border: 1px solid #ddd;
        }
        .filter-bar input:focus,
        .filter-bar select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .sales-table {
            background-color: white;
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .sales-table h5 {
            font-size: 1.1rem;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sales-table h5 .export-buttons {
            display: flex;
            gap: 10px;
        }
        .sales-table h5 .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        .table th,
        .table td {
            vertical-align: middle;
            font-size: 1rem;
            padding: 10px 8px;
        }
        .table th {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .table tr:hover {
            background-color: #f1f5f9;
        }
        .modal-content {
            border-radius: 10px;
            border: none;
        }
        .modal-header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-body .sale-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .modal-body .sale-info-item {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        .modal-body .sale-info-item strong {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .modal-body table {
            width: 100%;
            margin-bottom: 15px;
        }
        .modal-body table th,
        .modal-body table td {
            padding: 8px;
            text-align: left;
        }
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #eee;
        }
        .btn-export {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-export i {
            font-size: 0.85rem;
        }
        .quick-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .quick-filter .btn {
            white-space: nowrap;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            .filter-group {
                width: 100%;
                min-width: auto;
            }
            .filter-actions {
                flex-direction: column;
                width: 100%;
            }
            .filter-actions .btn {
                width: 100%;
                margin-bottom: 5px;
            }
            .modal-body .sale-info {
                grid-template-columns: 1fr;
            }
            .quick-filter .btn {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            .quick-filter .btn i {
                display: none;
            }
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        @media (max-width: 768px) {
    .sales-table {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        min-width: 900px;
    }

    .table th,
    .table td {
        font-size: 0.9rem;
        padding: 8px 6px;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 1;
    }

    .sales-table::-webkit-scrollbar {
        height: 8px;
    }

    .sales-table::-webkit-scrollbar-thumb {
        background-color: #007bff;
        border-radius: 4px;
    }

    .sales-table::-webkit-scrollbar-track {
        background-color: #f1f1f1;
    }
}

@media (max-width: 768px) {
    .sales-table h5 {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .sales-table h5 .export-buttons {
        width: 100%;
        justify-content: flex-start;
    }

    .sales-table h5 .btn-sm {
        width: auto;
    }
}
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="header">
        <h4>Satış Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Summary Cards -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Günlük Toplam</h5>
                            <p>{{ number_format($dailyTotal, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Bu Ay Toplam</h5>
                            <p>{{ number_format($currentMonthTotal, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Geçen Ay Toplam</h5>
                            <p>{{ number_format($lastMonthTotal, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Tüm Zamanlar Toplam</h5>
                            <p>{{ number_format($allTimeTotal, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Günlük İndirim</h5>
                            <p>{{ number_format($dailyDiscount, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Bu Ay İndirim</h5>
                            <p>{{ number_format($currentMonthDiscount, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Geçen Ay İndirim</h5>
                            <p>{{ number_format($lastMonthDiscount, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="summary-card">
                            <h5>Tüm Zamanlar İndirim</h5>
                            <p>{{ number_format($allTimeDiscount, 2, ',', '.') }} ₺</p>
                        </div>
                    </div>
                </div>

                <div class="quick-filter">
                    <button class="btn btn-sm btn-outline-primary quick-date" data-range="today">
                        <i class="fas fa-calendar-day"></i> Bugün
                    </button>
                    <button class="btn btn-sm btn-outline-primary quick-date" data-range="yesterday">
                        <i class="fas fa-calendar-minus"></i> Dün
                    </button>
                    <button class="btn btn-sm btn-outline-primary quick-date" data-range="last7days">
                        <i class="fas fa-calendar-week"></i> Son 7 Gün
                    </button>
                    <button class="btn btn-sm btn-outline-primary quick-date" data-range="thisMonth">
                        <i class="fas fa-calendar-alt"></i> Bu Ay
                    </button>
                    <button class="btn btn-sm btn-outline-primary quick-date" data-range="lastMonth">
                        <i class="fas fa-calendar-check"></i> Geçen Ay
                    </button>
                    <button class="btn btn-sm btn-outline-danger quick-payment" data-type="1">
                        <i class="fas fa-money-bill-wave"></i> Nakit
                    </button>
                    <button class="btn btn-sm btn-outline-danger quick-payment" data-type="2">
                        <i class="fas fa-credit-card"></i> Kart
                    </button>
                    <button class="btn btn-sm btn-outline-danger quick-payment" data-type="3">
                        <i class="fas fa-handshake"></i> Veresiye
                    </button>
					    <button class="btn btn-sm btn-outline-danger quick-payment" data-type="4">
                        <i class="fas fa-bank"></i> Banka Havale
                    </button>
					<button class="btn btn-sm btn-outline-danger quick-payment" data-type="5">
                        <i class="fas fa-hand"></i> Kapıda (Elden) Ödeme 
                    </button>
                </div>

                <!-- Filter Bar -->
                <div class="filter-bar">
                    <form id="filterForm">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="dateRange"><i class="fas fa-calendar"></i> Tarih Aralığı</label>
                                <input type="text" id="dateRange" name="dateRange" placeholder="Tarih Aralığı Seçin" readonly>
                            </div>
                            <div class="filter-group">
                                <label for="payTypeFilter"><i class="fas fa-wallet"></i> Ödeme Türü</label>
                                <select id="payTypeFilter" name="payType">
                                    <option value="">Tüm Ödeme Türleri</option>
                                    <option value="1">Nakit</option>
                                    <option value="2">Kart</option>
                                    <option value="3">Veresiye</option>
                                    <option value="4">Banka Havale</option>
                                    <option value="5">Kapıda (Elden) Ödeme </option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="customerFilter"><i class="fas fa-user"></i> Müşteri</label>
                                <input type="text" id="customerFilter" name="customer" placeholder="Müşteri adı giriniz">
                            </div>
                            @if(auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                            <div class="filter-group">
                                <label for="userFilter"><i class="fas fa-user-tie"></i> Kullanıcı</label>
                                <select id="userFilter" name="user_id">
                                    <option value="">Tüm Kullanıcılar</option>
                                    @foreach(\App\Models\User::select('id', 'name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrele
                            </button>
                            <button type="button" id="resetFilters" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Sıfırla
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Daily Sales Table (Varsayılan) -->
                <div class="sales-table" id="dailySalesSection">
                    <h5>
                        <span>Günlük Satışlar ({{ now()->format('d.m.Y') }})</span>
                        <div class="export-buttons">
                            <button id="daily-export-pdf" class="btn btn-sm btn-outline-secondary btn-export">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button id="daily-export-excel" class="btn btn-sm btn-outline-success btn-export">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </h5>
                    <table id="dailySalesTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Müşteri Adı</th>
                                <th>Kullanıcı</th>
                                <th>Ödeme Türü</th>
                                <th>İndirim</th>
                                <th>Ön Tutar</th>
                                <th>Toplam</th>
                                <th>Satış Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailySales as $sale)
                            <tr>
                                <td>{{ $sale->id }}</td>
								<td>{{ $sale->customer ? $sale->customer->name : 'Misafir' }}</td>                                
								<td>{{ $sale->user->role ?? 'Bilinmiyor' }}</td>
                                <td>
                                    @if($sale->pay_type == 1)
                                        <span class="badge bg-success">Nakit</span>
                                    @elseif($sale->pay_type == 2)
                                        <span class="badge bg-primary">Kart</span>
                                    @elseif($sale->pay_type == 3)
                                        <span class="badge bg-warning">Veresiye</span>
									 @elseif($sale->pay_type == 4)
                                        <span class="badge bg-info">Banka Havale</span>
									@elseif($sale->pay_type == 5)
                                        <span class="badge bg-secondary">Kapıda (Elden) Ödeme</span>
                                    @else
                                        <span class="badge bg-secondary">Bilinmiyor</span>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($sale->discount_total, 2, ',', '.') }} ₺
                                    @if ($sale->discount > 0)
                                        <small class="text-muted">(%{{ number_format($sale->discount, 2, ',', '.') }})</small>
                                    @elseif ($sale->discount_fixed > 0)
                                        <small class="text-muted">(Sabit)</small>
                                    @endif
                                </td>
                                <td>{{ number_format($sale->sub_total, 2, ',', '.') }} ₺</td>
                                <td>{{ number_format($sale->total_price, 2, ',', '.') }} ₺</td>
                                <td>{{ $sale->created_at->format('d.m.Y H:i') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary view-details" data-id="{{ $sale->id }}" data-bs-toggle="modal" data-bs-target="#saleDetailsModal" title="Detayları Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form action="{{ route('satisyap.destroy', $sale->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Filtered Sales Table -->
                <div class="sales-table" id="filteredSalesSection" style="display: none;">
                    <h5>
                        <span id="filteredTitle">Filtrelenmiş Satışlar</span>
                        <div class="export-buttons">
                            <button id="filtered-export-pdf" class="btn btn-sm btn-outline-secondary btn-export">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button id="filtered-export-excel" class="btn btn-sm btn-outline-success btn-export">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </h5>
                    <table id="salesTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Müşteri Adı</th>
                                <th>Kullanıcı</th>
                                <th>Ödeme Türü</th>
                                <th>İndirim</th>
                                <th>Ön Tutar</th>
                                <th>Toplam</th>
                                <th>Satış Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- Filter Results Summary -->
                <div class="alert alert-info mt-3" id="filterSummary" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Filtre Sonuçları:</strong>
                            <span id="recordCount">0</span> kayıt bulundu.
                            <span id="totalAmount">0,00</span> ₺ toplam satış.
                            <span id="discountAmount">0,00</span> ₺ toplam indirim.
                        </div>
                        <button class="btn btn-sm btn-outline-primary" id="toggleSummaryBtn">
                            <i class="fas fa-chart-pie"></i> Özet Bilgiler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Details Modal -->
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-labelledby="saleDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saleDetailsModalLabel">Satış Detayları</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="sale-info">
                        <div class="sale-info-item">
                            <strong>Satış ID:</strong>
                            <span id="detailSaleId"></span>
                        </div>
                        <div class="sale-info-item">
                            <strong>Müşteri:</strong>
                            <span id="detailCustomer"></span>
                        </div>
                        <div class="sale-info-item">
                            <strong>Kullanıcı:</strong>
                            <span id="detailUser"></span> <!-- Added User Field -->
                        </div>
                        <div class="sale-info-item">
                            <strong>Ödeme Türü:</strong>
                            <span id="detailPayType"></span>
                        </div>
                        <div class="sale-info-item">
                            <strong>Tarih:</strong>
                            <span id="detailDate"></span>
                        </div>
                        <div class="sale-info-item">
                            <strong>Ara Toplam:</strong>
                            <span id="detailSubTotal"></span> ₺
                        </div>
                        <div class="sale-info-item">
                            <strong>İndirim:</strong>
                            <span id="detailDiscount"></span> ₺
                        </div>
                        <div class="sale-info-item">
                            <strong>Toplam:</strong>
                            <span id="detailTotal"></span> ₺
                        </div>
                    </div>
                    <h6 class="mb-3"><i class="fas fa-shopping-basket me-2"></i>Ürünler</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Birim Fiyat</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody id="detailItems"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="printSaleDetail">
                        <i class="fas fa-print me-1"></i> Yazdır
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Summary Modal -->
    <div class="modal fade" id="filterSummaryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtre Özeti</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="summary-card mb-3">
                        <h5>Toplam Satış Tutarı</h5>
                        <p id="modalTotalAmount">0,00 ₺</p>
                    </div>
                    <div class="summary-card mb-3">
                        <h5>Toplam İndirim</h5>
                        <p id="modalDiscountAmount">0,00 ₺</p>
                    </div>
                    <div class="summary-card mb-3">
                        <h5>Satış Adedi</h5>
                        <p id="modalSaleCount">0</p>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="summary-card">
                                <h5>Nakit</h5>
                                <p id="modalCashAmount">0,00 ₺</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <h5>Kart</h5>
                                <p id="modalCardAmount">0,00 ₺</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-card">
                                <h5>Veresiye</h5>
                                <p id="modalCreditAmount">0,00 ₺</p>
                            </div>
                        </div>
						   <div class="col-md-4">
                            <div class="summary-card">
                                <h5>Banka Havale</h5>
                                <p id="modalBankAmount">0,00 ₺</p>
                            </div>
                        </div>
							   <div class="col-md-4">
                            <div class="summary-card">
                                <h5>Kapıda Ödeme (Elden Ödeme)</h5>
                                <p id="modalHandAmount">0,00 ₺</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/tr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://unpkg.com/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
    $(document).ready(function () {
        const baseUrl = $('meta[name="app-url"]').attr('content');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const dailySalesTable = $('#dailySalesTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' },
            pageLength: 10,
            order: [[7, 'desc']], 
            columnDefs: [{ orderable: false, targets: 8 }], 
        });

        const salesTable = $('#salesTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json' },
            pageLength: 10,
            order: [[7, 'desc']], 
            columnDefs: [{ orderable: false, targets: 8 }], 
            serverSide: false,
        });

        $('#dateRange').daterangepicker({
            locale: {
                format: 'DD.MM.YYYY',
                separator: ' - ',
                applyLabel: 'Uygula',
                cancelLabel: 'İptal',
                fromLabel: 'Başlangıç',
                toLabel: 'Bitiş',
                customRangeLabel: 'Özel',
                daysOfWeek: ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'],
                monthNames: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                firstDay: 1,
            },
            autoUpdateInput: false,
            ranges: {
                'Bugün': [moment(), moment()],
                'Dün': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Son 7 Gün': [moment().subtract(6, 'days'), moment()],
                'Son 30 Gün': [moment().subtract(29, 'days'), moment()],
                'Bu Ay': [moment().startOf('month'), moment().endOf('month')],
                'Geçen Ay': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
        });

        $('#dateRange').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
        });

        $('#dateRange').on('cancel.daterangepicker', function () {
            $(this).val('');
        });

        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        function showError(message) {
            const toast = $(`<div class="toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3"></div>`)
                .css('z-index', '1050')
                .html(`
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `);
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            setTimeout(() => { bsToast.hide(); toast.remove(); }, 5000);
        }

        function showSuccess(message) {
            const toast = $(`<div class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3"></div>`)
                .css('z-index', '1050')
                .html(`
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `);
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            setTimeout(() => { bsToast.hide(); toast.remove(); }, 3000);
        }

        function formatCurrency(amount) {
            return parseFloat(amount || 0).toFixed(2).replace('.', ',') + ' ₺';
        }

        function updateFilterSummary(summary) {
            if (!summary) {
                $('#filterSummary').hide();
                return;
            }
            $('#recordCount').text(summary.count || 0);
            $('#totalAmount').text(formatCurrency(summary.total));
            $('#discountAmount').text(formatCurrency(summary.discount));
            $('#modalTotalAmount').text(formatCurrency(summary.total));
            $('#modalDiscountAmount').text(formatCurrency(summary.discount));
            $('#modalSaleCount').text(summary.count || 0);
            $('#modalCashAmount').text(formatCurrency(summary.cash));
            $('#modalCardAmount').text(formatCurrency(summary.card));
            $('#modalCreditAmount').text(formatCurrency(summary.credit));
            $('#modalBankAmount').text(formatCurrency(summary.bank_transfer));
            $('#modalHandAmount').text(formatCurrency(summary.cash_on_delivery));
            $('#filterSummary').show();
        }

        function fetchFilteredSales(params, updateTitle = true) {
            const filterUrl = "{{ route('satisyonetim.filter') }}";
            console.log("Fetching from URL:", filterUrl);
            if (!filterUrl) {
                showError("Filtreleme rotası tanımlı değil.");
                return;
            }
            showLoading();
            $.ajax({
                url: filterUrl,
                method: 'GET',
                data: {
                    start_date: params.startDate || null,
                    end_date: params.endDate || null,
                    pay_type: params.payType || null,
                    customer: params.customer || null,
                    user_id: params.userId || null,
                    page: params.page || 1,
                    per_page: params.perPage || 20,
                },
                success: function (response) {
                    hideLoading();
                    if (!response.success) {
                        showError(response.message || 'Filtreleme başarısız oldu.');
                        $('#filteredSalesSection').hide();
                        $('#dailySalesSection').show();
                        $('#filterSummary').hide();
                        return;
                    }
                    updateFilteredTable(response, params, updateTitle);
                    showSuccess('Filtreleme başarıyla uygulandı.');
                },
                error: function (xhr) {
                    hideLoading();
                    let errorMessage = 'Filtreleme sırasında bir hata oluştu.';
                    if (xhr.status === 422) {
                        errorMessage = 'Geçersiz veri: ' + Object.values(xhr.responseJSON.errors).flat().join(', ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    console.error("AJAX Error:", xhr.status, xhr.responseText);
                    showError(errorMessage);
                    $('#filteredSalesSection').hide();
                    $('#dailySalesSection').show();
                    $('#filterSummary').hide();
                },
            });
        }

        function updateFilteredTable(response, params, updateTitle) {
            $('#dailySalesSection').hide();
            $('#filteredSalesSection').show();

            if (updateTitle) {
                let titleText = 'Filtrelenmiş Satışlar';
                if (params.startDate && params.endDate) {
                    titleText += ` (${params.startDate} - ${params.endDate})`;
                }
                if (params.payType) {
                    const payTypeTexts = { '1': 'Nakit', '2': 'Kart', '3': 'Veresiye', '4': 'Banka Havale', '5': 'Kapıda Ödeme (Elden Ödeme)' };
                    titleText += ` / ${payTypeTexts[params.payType] || ''}`;
                }
                if (params.customer) {
                    titleText += ` / Müşteri: ${params.customer}`;
                }
                if (params.userId) {
                    const userOption = document.querySelector(`#userFilter option[value="${params.userId}"]`);
                    const userName = userOption ? userOption.text : 'Bilinmeyen Kullanıcı';
                    titleText += ` / Kullanıcı: ${userName}`;
                }
                $('#filteredTitle').text(titleText);
            }

            salesTable.clear();
            if (response.sales && response.sales.length > 0) {
                response.sales.forEach(sale => {
              const payTypeBadge = sale.pay_type == 1 ? '<span class="badge bg-success">Nakit</span>' :
                sale.pay_type == 2 ? '<span class="badge bg-primary">Kart</span>' :
                sale.pay_type == 3 ? '<span class="badge bg-warning">Veresiye</span>' :
                sale.pay_type == 4 ? '<span class="badge bg-info">Banka Havale</span>' :
                sale.pay_type == 5 ? '<span class="badge bg-secondary">Kapıda Ödeme (Elden Ödeme)</span>' : 
                '<span class="badge bg-secondary">Bilinmiyor</span>';

                    const discountText = `${formatCurrency(sale.discount_total)}` +
                        (sale.discount > 0 ? `<small class="text-muted"> (%${parseFloat(sale.discount).toFixed(2).replace('.', ',')})</small>` :
                        sale.discount_fixed > 0 ? '<small class="text-muted">(Sabit)</small>' : '');

                    salesTable.row.add([
                        sale.id,
                        sale.customer?.name || 'Misafir',
                        sale.user?.name || 'Bilinmiyor', 
                        payTypeBadge,
                        discountText,
                        formatCurrency(sale.sub_total),
                        formatCurrency(sale.total_price),
                        moment(sale.created_at).format('DD.MM.YYYY HH:mm'),
                        `<div class="text-center">
                            <button class="btn btn-sm btn-primary view-details" data-id="${sale.id}" data-bs-toggle="modal" data-bs-target="#saleDetailsModal" title="Detayları Görüntüle">
                                <i class="fas fa-eye"></i>
                            </button>
                            <form action="{{ route('satisyap.destroy', '__ID__') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>`.replace('__ID__', sale.id),
                    ]);
                });
            }
            salesTable.draw();
            updateFilterSummary(response.summary);
        }

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            const dateRange = $('#dateRange').val();
            let startDate, endDate;
            if (dateRange) {
                const dates = dateRange.split(' - ');
                if (dates.length !== 2) {
                    showError('Lütfen geçerli bir tarih aralığı seçin.');
                    return;
                }
                startDate = dates[0].trim();
                endDate = dates[1].trim();
            }
            fetchFilteredSales({
                startDate,
                endDate,
                payType: $('#payTypeFilter').val(),
                customer: $('#customerFilter').val(),
                userId: $('#userFilter').val(),
            });
        });

        $('#resetFilters').on('click', function () {
            $('#dateRange').val('');
            $('#payTypeFilter').val('');
            $('#customerFilter').val('');
            $('#userFilter').val('');
            $('#filteredSalesSection').hide();
            $('#filterSummary').hide();
            $('#dailySalesSection').show();
            salesTable.clear().draw();
            showSuccess('Filtreler sıfırlandı.');
        });

        $('.quick-date').on('click', function () {
            const range = $(this).data('range');
            let startDate, endDate;
            switch (range) {
                case 'today':
                    startDate = moment().format('DD.MM.YYYY');
                    endDate = moment().format('DD.MM.YYYY');
                    break;
                case 'yesterday':
                    startDate = moment().subtract(1, 'days').format('DD.MM.YYYY');
                    endDate = moment().subtract(1, 'days').format('DD.MM.YYYY');
                    break;
                case 'last7days':
                    startDate = moment().subtract(6, 'days').format('DD.MM.YYYY');
                    endDate = moment().format('DD.MM.YYYY');
                    break;
                case 'thisMonth':
                    startDate = moment().startOf('month').format('DD.MM.YYYY');
                    endDate = moment().endOf('month').format('DD.MM.YYYY');
                    break;
                case 'lastMonth':
                    startDate = moment().subtract(1, 'month').startOf('month').format('DD.MM.YYYY');
                    endDate = moment().subtract(1, 'month').endOf('month').format('DD.MM.YYYY');
                    break;
            }
            $('#dateRange').val(`${startDate} - ${endDate}`);
            $('#dateRange').data('daterangepicker').setStartDate(startDate);
            $('#dateRange').data('daterangepicker').setEndDate(endDate);
            fetchFilteredSales({
                startDate,
                endDate,
                payType: $('#payTypeFilter').val(),
                customer: $('#customerFilter').val(),
                userId: $('#userFilter').val(),
            });
        });

        $('.quick-payment').on('click', function () {
            const payType = $(this).data('type');
            $('#payTypeFilter').val(payType);
            const dateRange = $('#dateRange').val();
            let startDate, endDate;
            if (dateRange) {
                const dates = dateRange.split(' - ');
                startDate = dates[0]?.trim();
                endDate = dates[1]?.trim();
            } else {
                startDate = moment().format('DD.MM.YYYY');
                endDate = moment().format('DD.MM.YYYY');
                $('#dateRange').val(`${startDate} - ${endDate}`);
                $('#dateRange').data('daterangepicker').setStartDate(startDate);
                $('#dateRange').data('daterangepicker').setEndDate(endDate);
            }
            fetchFilteredSales({
                startDate,
                endDate,
                payType,
                customer: $('#customerFilter').val(),
                userId: $('#userFilter').val(),
            });
        });

        $('#toggleSummaryBtn').on('click', function () {
            $('#filterSummaryModal').modal('show');
        });

        $('body').on('click', '.view-details', function () {
            const saleId = $(this).data('id');
            showLoading();
            $.ajax({
                url: `${baseUrl}/dashboard/satisyonetim/${saleId}/details`,
                method: 'GET',
                success: function (data) {
                    hideLoading();
                    if (data.sale) {
                        $('#detailSaleId').text(data.sale.id || 'N/A');
                        $('#detailCustomer').text(data.sale.customer_name || 'N/A');
                        $('#detailUser').text(data.sale.user_name || 'Bilinmiyor');
                        $('#detailPayType').text(data.payTypeText || 'Bilinmiyor');
                        $('#detailSubTotal').text(parseFloat(data.sale.sub_total || 0).toFixed(2).replace('.', ','));
                        $('#detailDiscount').text(parseFloat(data.sale.discount_total || 0).toFixed(2).replace('.', ','));
                        $('#detailTotal').text(parseFloat(data.sale.total_price || 0).toFixed(2).replace('.', ','));
                        $('#detailDate').text(data.sale.created_at || 'N/A');
                        let itemsHtml = '';
                        if (data.basketItems && data.basketItems.length > 0) {
                            data.basketItems.forEach(item => {
                                const quantity = parseFloat(item.quantity || 0);
                                const price = parseFloat(item.price || 0);
                                const total = quantity * price;
                                itemsHtml += `<tr>
                                    <td>${item.name || 'Bilinmeyen Ürün'}</td>
                                    <td>${quantity}</td>
                                    <td>${price.toFixed(2).replace('.', ',')} ₺</td>
                                    <td>${total.toFixed(2).replace('.', ',')} ₺</td>
                                </tr>`;
                            });
                        } else {
                            itemsHtml = '<tr><td colspan="4" class="text-center">Ürün bulunamadı</td></tr>';
                        }
                        $('#detailItems').html(itemsHtml);
                    } else {
                        showError('Satış detayları alınamadı.');
                    }
                },
                error: function (xhr) {
                    hideLoading();
                    showError('Satış detayları alınamadı: ' + (xhr.responseJSON?.message || 'Bilinmeyen hata'));
                },
            });
        });

        $('#printSaleDetail').on('click', function () {
            const saleId = $('#detailSaleId').text();
            const customerName = $('#detailCustomer').text();
            const userName = $('#detailUser').text(); 
            const payType = $('#detailPayType').text();
            const subTotal = $('#detailSubTotal').text();
            const discount = $('#detailDiscount').text();
            const total = $('#detailTotal').text();
            const date = $('#detailDate').text();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Satış Detayı #${saleId}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
                        .info-table td { padding: 5px; }
                        .items-table { width: 100%; border-collapse: collapse; }
                        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                        .items-table th { background-color: #f2f2f2; }
                        .totals { margin-top: 20px; text-align: right; }
                        @media print { .no-print { display: none; } button { display: none; } }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>Satış Detayı #${saleId}</h2>
                    </div>
                    <table class="info-table">
                        <tr>
                            <td><strong>Müşteri:</strong> ${customerName}</td>
                            <td><strong>Ödeme Türü:</strong> ${payType}</td>
                        </tr>
                        <tr>
                            <td><strong>Kullanıcı:</strong> ${userName}</td> <!-- Added User Name -->
                            <td><strong>Tarih:</strong> ${date}</td>
                        </tr>
                        <tr>
                            <td><strong>Satış No:</strong> ${saleId}</td>
                            <td></td>
                        </tr>
                    </table>
                    <h3>Satın Alınan Ürünler</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Miktar</th>
                                <th>Birim Fiyat</th>
                                <th>Toplam</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${$('#detailItems').html()}
                        </tbody>
                    </table>
                    <div class="totals">
                        <p><strong>Ara Toplam:</strong> ${subTotal} ₺</p>
                        <p><strong>İndirim:</strong> ${discount} ₺</p>
                        <p style="font-size: 1.2em;"><strong>Genel Toplam:</strong> ${total} ₺</p>
                    </div>
                    <div class="no-print" style="margin-top: 30px; text-align: center;">
                        <button onclick="window.print();" style="padding: 10px 20px;">Yazdır</button>
                        <button onclick="window.close();" style="padding: 10px 20px; margin-left: 10px;">Kapat</button>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            setTimeout(() => { printWindow.focus(); printWindow.print(); }, 1000);
        });

        function exportToPDF(dataTable, title) {
            try {
                const { jsPDF } = window.jspdf;
                if (!jsPDF) throw new Error('jsPDF kütüphanesi yüklenemedi.');
                const doc = new jsPDF();
                if (!doc.autoTable) throw new Error('jsPDF autoTable eklentisi yüklenemedi.');
                doc.setFontSize(16);
                doc.text(title, 10, 10);
                const headers = ['ID', 'Müşteri', 'Kullanıcı', 'Ödeme Türü', 'İndirim', 'Ön Tutar', 'Toplam', 'Tarih']; // Added User
                const data = [];
                dataTable.rows({ search: 'applied' }).every(function () {
                    const rowData = this.data();
                    data.push([
                        rowData[0],
                        $(rowData[1]).text().trim(),
                        $(rowData[2]).text().trim(), 
                        $(rowData[3]).text().trim(),
                        $(rowData[4]).text().trim().split(' ')[0].replace('₺', '').trim(),
                        rowData[5].replace(' ₺', '').trim(),
                        rowData[6].replace(' ₺', '').trim(),
                        rowData[7].trim()
                    ]);
                });
                doc.autoTable({
                    head: [headers],
                    body: data,
                    startY: 20,
                    styles: { fontSize: 8, cellPadding: 2 },
                    headStyles: { fillColor: [0, 123, 255], textColor: [255, 255, 255] },
                    theme: 'grid',
                    didParseCell: function (data) {
                        if (data.column.index >= 4 && data.column.index <= 6) {
                            data.cell.text = data.cell.text.map(text => text.replace(',', '.'));
                        }
                    }
                });
                if ($('#filterSummary').is(':visible')) {
                    const y = doc.lastAutoTable.finalY + 10;
                    doc.setFontSize(12);
                    doc.text('Özet Bilgiler:', 10, y);
                    doc.setFontSize(10);
                    doc.text(`Toplam Kayıt: ${$('#recordCount').text()}`, 10, y + 7);
                    doc.text(`Toplam Satış: ${$('#totalAmount').text()}`, 10, y + 14);
                    doc.text(`Toplam İndirim: ${$('#discountAmount').text()}`, 10, y + 21);
                }
                const today = moment().format('YYYYMMDD');
                doc.save(`satisyonetim_${today}.pdf`);
                showSuccess('PDF başarıyla oluşturuldu');
            } catch (error) {
                console.error('PDF oluşturma hatası:', error);
                showError('PDF oluşturulurken hata oluştu: ' + error.message);
            }
        }

        function exportToExcel(dataTable, title) {
            try {
                const wb = XLSX.utils.book_new();
                wb.Props = {
                    Title: title,
                    Subject: 'Satış Yönetim Raporu',
                    Author: 'Satış Yönetim Sistemi',
                    CreatedDate: new Date()
                };
                const headers = ['ID', 'Müşteri', 'Kullanıcı', 'Ödeme Türü', 'İndirim', 'Ön Tutar', 'Toplam', 'Tarih']; // Added User
                const data = [headers];
                dataTable.rows({ search: 'applied' }).every(function () {
                    const rowData = this.data();
                    data.push([
                        rowData[0],
                        $(rowData[1]).text().trim(),
                        $(rowData[2]).text().trim(), // Added User
                        $(rowData[3]).text().trim(),
                        $(rowData[4]).text().trim().split(' ')[0],
                        rowData[5].replace(' ₺', ''),
                        rowData[6].replace(' ₺', ''),
                        rowData[7]
                    ]);
                });
                if ($('#filterSummary').is(':visible')) {
                    data.push([]);
                    data.push(['Özet Bilgiler']);
                    data.push(['Toplam Kayıt', $('#recordCount').text()]);
                    data.push(['Toplam Satış', $('#totalAmount').text()]);
                    data.push(['Toplam İndirim', $('#discountAmount').text()]);
                }
                const ws = XLSX.utils.aoa_to_sheet(data);
                XLSX.utils.book_append_sheet(wb, ws, 'Satışlar');
                const today = moment().format('YYYYMMDD');
                XLSX.writeFile(wb, `satisyonetim_${today}.xlsx`);
                showSuccess('Excel başarıyla oluşturuldu');
            } catch (error) {
                console.error('Excel oluşturma hatası:', error);
                showError('Excel oluşturulurken hata oluştu: ' + error.message);
            }
        }

        $('#daily-export-pdf').on('click', function () {
            exportToPDF(dailySalesTable, `Günlük Satışlar (${moment().format('DD.MM.YYYY')})`);
        });
        $('#filtered-export-pdf').on('click', function () {
            exportToPDF(salesTable, $('#filteredTitle').text());
        });
        $('#daily-export-excel').on('click', function () {
            exportToExcel(dailySalesTable, `Günlük Satışlar (${moment().format('DD.MM.YYYY')})`);
        });
        $('#filtered-export-excel').on('click', function () {
            exportToExcel(salesTable, $('#filteredTitle').text());
        });
    });
    </script>
</body>
</html>
