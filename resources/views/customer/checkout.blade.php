<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Ödeme Sayfası</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .checkout-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .payment-section {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .bank-details {
            display: none;
            margin-top: 15px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0d6efd;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-2px);
        }

        .btn-primary:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .is-valid {
            border-color: #198754 !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .copy-btn {
            cursor: pointer;
            color: #0d6efd;
            margin-left: 10px;
            transition: all 0.3s;
        }

        .copy-btn:hover {
            color: #0b5ed7;
            transform: scale(1.1);
        }

        .copy-btn.copied {
            color: #198754;
            animation: pulse 0.5s ease-in-out;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        #iyzico-form-container {
            display: none;
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .checkout-container {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="checkout-container">
        <h4 class="mb-4">Ödeme Sayfası</h4>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4">
            <h5>Sepetiniz</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Fiyat</th>
                        <th>Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart['items'] as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 2, ',', '.') }} ₺</td>
                            <td>{{ number_format($item['subtotal'], 2, ',', '.') }} ₺</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Genel Toplam:</strong></td>
                        <td>{{ number_format($cart['total'], 2, ',', '.') }} ₺</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="payment-section">
            <h5 class="mb-3">Ödeme Bilgileri</h5>
            <form id="checkoutForm" action="{{ route('customer.order.complete') }}" method="POST">
                @csrf
                <input type="hidden" name="order_token" id="order_token">

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Ödeme Yöntemi <span
                            class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="">Seçiniz</option>
                        <option value="credit_card">Kredi Kartı (İyzico)</option>
                        @if (!$bankAccounts->isEmpty())
                            <option value="bank_transfer">Banka Havalesi</option>
                        @endif
                        <option value="cash_on_delivery">Kapıda Ödeme (Elden Ödeme)</option>
                        <option value="credit">Veresiye</option>
                    </select>
                    <div class="invalid-feedback">Lütfen bir ödeme yöntemi seçin.</div>
                </div>

                <div class="bank-details">
                    <h6><i class="fas fa-university me-2"></i>Banka Hesap Bilgileri</h6>
                    @if ($bankAccounts->isEmpty())
                        <div class="alert alert-warning">
                            Şu anda kullanılabilir banka hesabı bulunmamaktadır.
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="bank_account" class="form-label">Banka Hesabı Seçin <span
                                    class="text-danger">*</span></label>
                            <select name="bank_account_id" id="bank_account" class="form-select">
                                @foreach ($bankAccounts as $account)
                                    <option value="{{ $account->id }}" data-bank-name="{{ $account->bank_name }}"
                                        data-account-holder="{{ $account->account_holder }}" data-iban="{{ $account->iban }}"
                                        data-order-id="siparis-{{ time() }}-{{ $account->id }}">
                                        {{ $account->bank_name }} - {{ $account->account_holder }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bank-account-details">
                            <p><strong>Banka:</strong> <span id="bank_name">{{ $bankAccounts->first()->bank_name }}</span>
                            </p>
                            <p><strong>Hesap Sahibi:</strong> <span
                                    id="account_holder">{{ $bankAccounts->first()->account_holder }}</span></p>
                            <div class="iban-container">
                                <p><strong>IBAN:</strong> <span id="iban">{{ $bankAccounts->first()->iban }}</span>
                                    <i class="fas fa-copy copy-btn" data-iban="{{ $bankAccounts->first()->iban }}"
                                        title="IBAN'ı Kopyala"></i>
                                </p>
                            </div>
                            <p><strong>Sipariş ID:</strong> <span
                                    id="order_id">siparis-{{ time() }}-{{ $bankAccounts->first()->id }}</span></p>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Ödeme yaparken yukarıdaki Sipariş ID'sini açıklama
                            kısmına ekleyin.
                        </div>
                        <div class="mb-3">
                            <label for="bank_receipt" class="form-label">Dekont Numarası</label>
                            <input type="text" name="bank_receipt" id="bank_receipt" class="form-control"
                                placeholder="Otomatik oluşturuldu">
                            <div class="invalid-feedback">Dekont numarası geçersiz.</div>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Telefon Numarası (Opsiyonel)<span class="text-danger">*</span></label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="Ör: 5551234567"
                        pattern="[0-9]{10,15}">
                    <div class="invalid-feedback">Lütfen 10-15 haneli bir telefon numarası girin.</div>
                </div>

                <div class="mb-3">
                    <label for="shipping_address" class="form-label">Teslimat Adresi <span
                            class="text-danger">*</span></label>
                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3"
                        required></textarea>
                    <div class="invalid-feedback">Teslimat adresi gereklidir.</div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notlar (Opsiyonel)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="submitCheckout">Siparişi Tamamla</button>
                <div class="error-message mt-2" id="formError"></div>
            </form>

            <div id="iyzico-form-container"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function generateOrderToken() {
                const timestamp = new Date().toISOString().replace(/[-:.TZ]/g, '');
                return `ORDER-${timestamp}-${Math.random().toString(36).substr(2, 9)}`;
            }

            $('#phone').mask('0000000000');

            $('#payment_method').on('change', function () {
                const paymentMethod = $(this).val();
                $('.bank-details').toggle(paymentMethod === 'bank_transfer');
                $('#iyzico-form-container').hide().empty();

                if (paymentMethod === 'bank_transfer') {
                    const orderId = $('#bank_account option:selected').data('order-id');
                    const timestamp = new Date().toISOString().replace(/[-:.TZ]/g, '');
                    const autoReceipt = `DEKONT-${orderId}-${timestamp}`;
                    $('#bank_receipt').val(autoReceipt).addClass('is-valid');
                }
            });

            $('#bank_account').on('change', function () {
                const selectedOption = $(this).find('option:selected');
                $('#bank_name').text(selectedOption.data('bank-name'));
                $('#account_holder').text(selectedOption.data('account-holder'));
                $('#iban').text(selectedOption.data('iban'));
                $('#order_id').text(selectedOption.data('order-id'));
                $('.copy-btn').data('iban', selectedOption.data('iban'));

                const timestamp = new Date().toISOString().replace(/[-:.TZ]/g, '');
                const autoReceipt = `DEKONT-${selectedOption.data('order-id')}-${timestamp}`;
                $('#bank_receipt').val(autoReceipt).addClass('is-valid');
            });

            $('.copy-btn').on('click', function () {
                const iban = $(this).data('iban');
                navigator.clipboard.writeText(iban).then(() => {
                    const $btn = $(this);
                    $btn.addClass('copied').attr('title', 'Kopyalandı!');
                    toastr.success('IBAN kopyalandı!');
                    setTimeout(() => $btn.removeClass('copied'), 2000);
                }).catch(() => toastr.error('Kopyalama başarısız.'));
            });

            $('#checkoutForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const paymentMethod = $('#payment_method').val();
                const phone = $('#phone').cleanVal();
                const shippingAddress = $('#shipping_address').val().trim();

                $('#order_token').val(generateOrderToken());

                let isValid = true;

                if (!paymentMethod) {
                    toastr.error('Lütfen bir ödeme yöntemi seçin.');
                    $('#payment_method').addClass('is-invalid');
                    isValid = false;
                }

                if (!shippingAddress) {
                    toastr.error('Lütfen teslimat adresini girin.');
                    $('#shipping_address').addClass('is-invalid');
                    isValid = false;
                }

                if (paymentMethod === 'bank_transfer' && !$('#bank_receipt').val().trim()) {
                    toastr.error('Lütfen dekont numarasını girin.');
                    $('#bank_receipt').addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) return;

                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#submitCheckout').prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> İşleniyor...');
                    },
                    success: function (response) {
                        if (response.success) {
                            if (paymentMethod === 'credit_card' && response.iyzico_form) {
                                $('#iyzico-form-container').html(response.iyzico_form).show();

                                const iyzicoForm = $('#iyzico-form-container form');
                                if (iyzicoForm.length) {
                                    iyzicoForm.on('submit', function () {
                                        $('#iyzico-form-container').prepend(
                                            '<div class="text-center p-4">' +
                                            '<div class="spinner-border text-primary"></div>' +
                                            '<p class="mt-2">Ödeme işleniyor, lütfen bekleyin...</p>' +
                                            '</div>'
                                        );
                                    });
                                }
                            } else {
                                window.location.href = response.redirect_url;
                            }
                        } else {
                            toastr.error(response.message);
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            }
                        }
                    },
                    error: function (xhr) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            toastr.error(response.message || 'Bir hata oluştu.');
                            $('#formError').text(response.message || 'Bilinmeyen bir hata oluştu.').show();
                        } catch (e) {
                            toastr.error('İşlem sırasında bir hata oluştu.');
                        }
                    },
                    complete: function () {
                        $('#submitCheckout').prop('disabled', false).html('Siparişi Tamamla');
                    }
                });
            });

            $('input, textarea, select').on('input change', function () {
                const $el = $(this);
                if ($el.val().trim()) {
                    $el.removeClass('is-invalid').addClass('is-valid');
                } else {
                    $el.removeClass('is-valid');
                }
            });
        });
    </script>
</body>

</html>