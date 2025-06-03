<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Siparişlerim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header h4 {
            margin: 0;
            font-size: 1.4rem;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
            font-size: 0.9rem;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .orders-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 15px;
        }
        .order-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-5px);
        }
        .order-card-header {
            background: #e9f4ff;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            color: #005566;
        }
        .order-card-body {
            padding: 20px;
        }
        .order-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .order-info div {
            flex: 1 1 200px;
        }
        .order-info strong {
            color: #333;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .no-orders {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background: #e9f4ff;
            border-radius: 10px 10px 0 0;
        }
        @media (max-width: 576px) {
            .header h4 {
                font-size: 1.2rem;
            }
            .header a {
                font-size: 0.8rem;
                margin-left: 10px;
            }
            .order-card-header h5 {
                font-size: 1rem;
            }
            .order-info div {
                flex: 1 1 100%;
            }
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h4><i class="fas fa-shopping-bag me-2"></i> Siparişlerim</h4>
        <div>
            <a href="{{ route('customer.returns') }}"><i class="fas fa-undo me-1"></i> İade Taleplerim</a>
            <a href="{{ route('customer.shopping') }}"><i class="fas fa-arrow-left me-1"></i> Alışverişe Dön</a>
            @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-1"></i> Çıkış Yap</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-1"></i> Giriş Yap</a>
            @endauth
        </div>
    </div>

    <div class="orders-container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($sales->isEmpty())
            <div class="no-orders">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5>Henüz Siparişiniz Yok</h5>
                <p class="text-muted">Mağazamızda alışverişe başlayarak ilk siparişinizi oluşturabilirsiniz!</p>
                <a href="{{ route('customer.shopping') }}" class="btn btn-primary">Alışverişe Başla</a>
            </div>
        @else
            @foreach ($sales as $sale)
                <div class="order-card">
                    <div class="order-card-header">
                        <h5>Sipariş #{{ $sale->id }}</h5>
                        <span>{{ $sale->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                    <div class="order-card-body">
                        <div class="order-info">
                            <div>
                                <strong>Toplam Tutar:</strong> {{ number_format($sale->total_price, 2, ',', '.') }} ₺
                            </div>
                         <div>
							<strong>Ödeme Yöntemi:</strong>
							@switch($sale->pay_type)
								@case(2) Kredi Kartı @break
								@case(3) Veresiye @break
								@case(4) Banka Havalesi @break
								@case(5) Kapıda Ödeme (Elden Ödeme) @break
								@default Bilinmiyor
							@endswitch
						</div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm view-details" data-sale-id="{{ $sale->id }}"><i class="fas fa-eye me-1"></i> Detayları Görüntüle</button>
                            <button class="btn btn-warning btn-sm request-return" data-sale-id="{{ $sale->id }}"><i class="fas fa-undo me-1"></i> İade Talebi Oluştur</button>
                        </div>
                    </div>
                </div>
            @endforeach
            <a href="{{ route('customer.shopping') }}" class="btn btn-primary mt-3"><i class="fas fa-shopping-cart me-1"></i> Alışverişe Devam Et</a>
        @endif
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Sipariş Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Sipariş ID:</strong> <span id="detail_sale_id"></span></p>
                    <p><strong>Müşteri:</strong> <span id="detail_customer"></span></p>
                    <p><strong>Toplam Tutar:</strong> <span id="detail_total"></span></p>
                    <p><strong>Ödeme Yöntemi:</strong> <span id="detail_pay_type"></span></p>
                    <p><strong>Tarih:</strong> <span id="detail_date"></span></p>
                    <h6 class="mt-4">Ürünler</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Fiyat</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody id="detail_items"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Request Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnModalLabel">İade Talebi Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="returnForm">
                        @csrf
                        <input type="hidden" name="sale_id" id="sale_id">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Ürün Seçin</label>
                            <select name="product_id" id="product_id" class="form-select" required>
                                <option value="">Seçiniz</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Miktar</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">İade Nedeni</label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" required placeholder="İade nedeninizi belirtin..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">İade Talebi Gönder</button>
                    </form>
                    <div class="alert alert-danger mt-3 d-none" id="returnError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            // View Order Details
            $('.view-details').on('click', function () {
                const saleId = $(this).data('sale-id');
                console.log('Fetching details for sale ID:', saleId);
                $.ajax({
                    url: `{{ url('customer/sales/remote-details') }}/${saleId}`,
                    method: 'GET',
                    success: function (data) {
                        if (data.success) {
                            $('#detail_sale_id').text(data.sale.id);
                            $('#detail_customer').text(data.sale.customer_name);
                            $('#detail_total').text(`${data.sale.total_price.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' })}`);
                            $('#detail_pay_type').text(data.payTypeText);
                            $('#detail_date').text(data.sale.created_at);

                            const itemsHtml = data.basketItems.length > 0
                                ? data.basketItems.map(item => `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>${item.quantity}</td>
                                        <td>${item.price.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' })}</td>
                                        <td>${(item.price * item.quantity).toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' })}</td>
                                    </tr>
                                `).join('')
                                : '<tr><td colspan="4">Ürün bulunamadı.</td></tr>';
                            $('#detail_items').html(itemsHtml);

                            $('#detailsModal').modal('show');
                        } else {
                            toastr.error(data.message || 'Sipariş detayları alınamadı.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Details AJAX error:', xhr.responseText);
                        toastr.error(xhr.responseJSON?.message || 'Sipariş detayları alınamadı.');
                    }
                });
            });

            // Request Return - Open Modal and Load Products
            $('.request-return').on('click', function () {
                const saleId = $(this).data('sale-id');
                console.log('Fetching products for sale ID:', saleId);
                $('#sale_id').val(saleId);

                $.ajax({
                    url: `{{ url('customer/sales/remote-products') }}`,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        sale_id: saleId
                    },
                    success: function (data) {
                        if (Array.isArray(data)) {
                            if (data.length === 0) {
                                toastr.error('İade edilecek ürün bulunamadı.');
                                return;
                            }
                            const options = data.map(item => `
                                <option value="${item.product_id}" data-max-quantity="${item.quantity}">${item.name} (Mevcut: ${item.quantity})</option>
                            `).join('');
                            $('#product_id').html('<option value="">Seçiniz</option>' + options);
                            $('#quantity').val(1).attr('max', data[0]?.quantity || 1);
                            $('#returnModal').modal('show');
                        } else {
                            toastr.error('Sipariş ürünleri alınamadı: Geçersiz veri formatı.');
                        }
                    },
                    error: function (xhr) {
                        console.error('Products AJAX error:', xhr.responseText);
                        toastr.error(xhr.responseJSON?.message || 'Sipariş ürünleri alınamadı.');
                    }
                });
            });

            // Update max quantity when product is selected
            $('#product_id').on('change', function () {
                const maxQuantity = $(this).find(':selected').data('max-quantity') || 1;
                $('#quantity').attr('max', maxQuantity).val(1);
            });

            // Submit Return Request
            $('#returnForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                const quantity = parseInt($('#quantity').val());
                const maxQuantity = parseInt($('#product_id').find(':selected').data('max-quantity'));

                if (quantity > maxQuantity) {
                    $('#returnError').text('İade miktarı sipariş miktarını aşıyor.').removeClass('d-none');
                    return;
                }

                $.ajax({
                    url: "{{ route('customer.return.request') }}",
                    method: 'POST',
                    data: formData,
                    beforeSend: function () {
                        $('#returnForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gönderiliyor...');
                    },
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data.message);
                            $('#returnModal').modal('hide');
                            $('#returnForm')[0].reset();
                            $('#returnError').addClass('d-none');
                            // Sayfayı yenilemek için (isteğe bağlı)
                            location.reload();
                        } else {
                            $('#returnError').text(data.message || 'İade talebi gönderilemedi.').removeClass('d-none');
                        }
                    },
                    error: function (xhr) {
                        $('#returnError').text(xhr.responseJSON?.message || 'İade talebi gönderilemedi.').removeClass('d-none');
                    },
                    complete: function () {
                        $('#returnForm button[type="submit"]').prop('disabled', false).text('İade Talebi Gönder');
                    }
                });
            });
        });
    </script>
</body>
</html>