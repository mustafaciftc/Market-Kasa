<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Diğer Satışlar</title>
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
            max-width: 1200px;
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
        <h4><i class="fas fa-shopping-bag me-2"></i> Diğer Satışlar</h4>
        <div>
            <a href="{{ route('dashboard') }}"><i class="fas fa-arrow-left me-1"></i>Geri Dön</a>
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
                <h5>Henüz Müşteri Satışı Yok</h5>
                <p class="text-muted">Müşteri satışları burada listelenecektir.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Ana Menü</a>
            </div>
        @else
            @foreach ($sales as $sale)
                <div class="order-card">
                    <div class="order-card-header">
                        <h5>Sipariş #{{ $sale->id }} ({{ $sale->user->name ?? 'Misafir' }})</h5>
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
                                    @case(1) Nakit @break
                                    @case(2) Kredi Kartı @break
                                    @case(3) Veresiye @break
                                    @case(4) Banka Havalesi @break
									@case(5) Kapıda (Elden) Ödeme @break
                                    @default Bilinmiyor
                                @endswitch
                            </div>
                            <div>
                                <strong>Müşteri:</strong> {{ $sale->user->name ?? 'Misafir' }}
                            </div>
                        </div>
                        <div class="action-buttons">
                            <button class="btn btn-primary btn-sm view-details" data-sale-id="{{ $sale->id }}"><i class="fas fa-eye me-1"></i> Detayları Görüntüle</button>

                        </div>
                    </div>
                </div>
            @endforeach
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

  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
       $(document).ready(function () {
    $('.view-details').on('click', function () {
        const saleId = $(this).data('sale-id');
        $.ajax({
            url: `{{ url('customer/sales/remote-details') }}/${saleId}`,
            method: 'GET',
            success: function (data) {
                if (data.success) {
                    $('#detail_sale_id').text(data.sale.id);
                    $('#detail_customer').text(data.sale.customer_name);
                    $('#detail_total').text(`${data.sale.total_price.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' })}`);
                    // Ödeme yöntemini manuel eşleme ile güncelle
                    const payTypeMap = {
                        1: 'Nakit',
                        2: 'Kredi Kartı',
                        3: 'Veresiye',
                        4: 'Banka Havalesi',
                        5: 'Kapıda (Elden) Ödeme'
                    };
                    const payTypeText = payTypeMap[data.sale.pay_type] || 'Bilinmiyor';
                    $('#detail_pay_type').text(payTypeText);
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
                toastr.error(xhr.responseJSON?.message || 'Sipariş detayları alınamadı.');
            }
        });
    });

    $('.edit-order').on('click', function () {
        const saleId = $(this).data('sale-id');
        $.ajax({
            url: `{{ url('customer/sales/remote-details') }}/${saleId}`,
            method: 'GET',
            success: function (data) {
                if (data.success) {
                    $('#edit_sale_id').val(data.sale.id);
                    $('#edit_total_price').val(data.sale.total_price);
                    $('#edit_pay_type').val(data.sale.pay_type); // pay_type seçimi için
                    $('#edit_basket').val(JSON.stringify(data.basketItems.map(item => ({
                        product_id: item.product_id || 0,
                        name: item.name,
                        price: item.price,
                        quantity: item.quantity,
                        image: item.image.replace('{{ url('/') }}/storage/', ''),
                        barcode: item.barcode || 'Barkodsuz'
                    })), null, 2));
                    $('#editOrderModal').modal('show');
                } else {
                    toastr.error(data.message || 'Sipariş detayları alınamadı.');
                }
            },
            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || 'Sipariş detayları alınamadı.');
            }
        });
    });

    $('#editOrderForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const saleId = $('#edit_sale_id').val();

        try {
            const basket = JSON.parse($('#edit_basket').val());
            if (!Array.isArray(basket)) {
                throw new Error('Sepet geçerli bir JSON dizisi olmalı.');
            }
        } catch (error) {
            $('#editError').text('Sepet JSON formatı geçersiz: ' + error.message).removeClass('d-none');
            return;
        }

        $.ajax({
            url: `{{ url('dashboard/other-sales') }}/${saleId}`,
            method: 'PUT',
            data: formData,
            beforeSend: function () {
                $('#editOrderForm button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Kaydediliyor...');
            },
            success: function (data) {
                if (data.success) {
                    toastr.success(data.message);
                    $('#editOrderModal').modal('hide');
                    $('#editOrderForm')[0].reset();
                    $('#editError').addClass('d-none');
                    location.reload();
                } else {
                    $('#editError').text(data.message || 'Sipariş güncellenemedi.').removeClass('d-none');
                }
            },
            error: function (xhr) {
                $('#editError').text(xhr.responseJSON?.message || 'Sipariş güncellenemedi.').removeClass('d-none');
            },
            complete: function () {
                $('#editOrderForm button[type="submit"]').prop('disabled', false).text('Kaydet');
            }
        });
    });
});
    </script>
</body>
</html>