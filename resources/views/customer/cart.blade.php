<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Sepetim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header h4 {
            margin: 0;
            font-size: 1.2rem;
        }
        .header a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .cart-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            position: relative;
            top: -10px;
            left: -5px;
        }
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
        }
        .card-header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
        .empty-cart {
            text-align: center;
            padding: 40px 0;
            color: #6c757d;
        }
        .empty-cart i {
            font-size: 50px;
            color: #ddd;
            margin-bottom: 15px;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .order-summary-item.total {
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h4>Sepetim</h4>
        <div>
            <a href="{{ route('customer.shopping') }}"><i class="fas fa-arrow-left me-1"></i> Alışverişe Dön</a>
            @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Çıkış Yap</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}">Giriş Yap</a>
            @endauth
        </div>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-shopping-cart me-2"></i> Sepetim
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Fiyat</th>
                                        <th>Miktar</th>
                                        <th>Toplam</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @if (!empty($cart['items']))
                                        @php
                                            $subtotal = 0;
                                        @endphp
                                        @foreach ($cart['items'] as $item)
                                            @php
                                                $itemTotal = $item['price'] * $item['quantity'];
                                                $subtotal += $itemTotal;
                                            @endphp
                                            <tr data-product-id="{{ $item['product_id'] }}">
                                                <td>
                                                    <img src="{{ isset($item['image']) && $item['image'] ? asset('storage/' . $item['image']) : '/images/no-image.png' }}" 
                                                         alt="{{ $item['name'] }}" class="product-img me-2">
                                                    {{ $item['name'] }}
                                                </td>
                                                <td>{{ number_format($item['price'], 2) }} ₺</td>
                                                <td>
                                                    <input type="number" class="form-control quantity-input" 
                                                           value="{{ $item['quantity'] }}" min="0" 
                                                           data-product-id="{{ $item['product_id'] }}">
                                                </td>
                                                <td>{{ number_format($itemTotal, 2) }} ₺</td>
                                                <td class="text-end">
                                                    <button class="btn btn-outline-danger btn-sm remove-item" 
                                                            data-product-id="{{ $item['product_id'] }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="empty-cart" id="empty-cart-message" style="{{ !empty($cart['items']) ? 'display: none;' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Sepetiniz boş</p>
                            <a href="{{ route('customer.shopping') }}" class="btn btn-primary">Alışverişe Başla</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-receipt me-2"></i> Sipariş Özeti
                    </div>
                    <div class="card-body">
                        <div class="order-summary">
                            @php
                                $subtotal = $cart['sub_total'] ?? 0;
                                $total = $subtotal; // KDV kaldırıldığı için total = subtotal
                            @endphp
                            <div class="order-summary-item">
                                <span>Ara Toplam:</span>
                                <span id="subtotal">{{ number_format($subtotal, 2) }} ₺</span>
                            </div>
                            <div class="order-summary-item total">
                                <span>Toplam:</span>
                                <span id="total">{{ number_format($total, 2) }} ₺</span>
                            </div>
                        </div>
                        <a href="{{ route('customer.checkout') }}" class="btn btn-primary w-100 mt-3" id="checkout-btn" 
                           style="{{ empty($cart['items']) ? 'display: none;' : '' }}">
                            <i class="fas fa-credit-card me-2"></i> Ödemeye Geç
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        class CustomerCartManager {
            constructor() {
                this.baseUrl = document.querySelector('meta[name="app-url"]').content;
                this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                this.initEventListeners();
                this.loadCart();
                this.updateCartBadge();
            }

            initEventListeners() {
                $(document).on('change', '.quantity-input', (e) => {
                    const productId = $(e.target).data('product-id');
                    const quantity = parseInt($(e.target).val()) || 0;
                    this.updateCartItem(productId, quantity);
                });

                $(document).on('click', '.remove-item', (e) => {
                    const productId = $(e.target).closest('button').data('product-id');
                    if (confirm('Bu ürünü sepetten çıkarmak istediğinize emin misiniz?')) {
                        this.removeFromCart(productId);
                    }
                });
            }

            showLoading() {
                $('#loadingOverlay').show();
            }

            hideLoading() {
                $('#loadingOverlay').hide();
            }

            showToast(message, type = 'success') {
                toastr[type](message, '', {
                    timeOut: 3000,
                    closeButton: true,
                    positionClass: 'toast-top-right'
                });
            }

            async refreshCsrfToken() {
                try {
                    const response = await fetch(`${this.baseUrl}/csrf-token`);
                    const data = await response.json();
                    this.csrfToken = data.csrf_token;
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', this.csrfToken);
                    return true;
                } catch (error) {
                    console.error('CSRF token refresh failed:', error);
                    this.showToast('Oturum yenileme başarısız', 'error');
                    return false;
                }
            }

            async loadCart() {
                try {
                    this.showLoading();
                    const response = await fetch(`${this.baseUrl}/customer/cart`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Sepet bilgileri alınamadı');
                    }

                    const cartData = await response.json();
                    if (!cartData.success) {
                        throw new Error(cartData.message || 'Sepet yüklenemedi');
                    }

                    this.displayCart(cartData);
                } catch (error) {
                    console.error('Load cart error:', error);
                    this.showToast(error.message, 'error');
                } finally {
                    this.hideLoading();
                }
            }

           displayCart(cartData) {
    console.log('Cart data received:', cartData);
    const cart = cartData.cart || [];
    const tbody = $('#cart-items');
    tbody.empty();

    if (cart.length > 0) {
        $('#empty-cart-message').hide();
        $('#cart-table').show();
        $('#checkout-btn').show();

        let subtotal = 0;

        cart.forEach(item => {
            const price = parseFloat(item.price) || 0;
            const quantity = parseInt(item.quantity) || 0;
            const itemTotal = price * quantity;
            subtotal += itemTotal;

            // Use the image URL directly, with fallback to default
            const imageSrc = item.image || `${this.baseUrl}/images/no-image.png`;

            const row = `
                <tr data-product-id="${item.product_id}">
                    <td>
                        <img src="${imageSrc}" alt="${item.name}" class="product-img me-2"
                             onerror="this.src='${this.baseUrl}/images/no-image.png'">
                        ${item.name || 'Bilinmeyen Ürün'}
                    </td>
                    <td>${price.toFixed(2)} ₺</td>
                    <td>
                        <input type="number" class="form-control quantity-input"
                               value="${quantity}" min="0"
                               data-product-id="${item.product_id}">
                    </td>
                    <td>${itemTotal.toFixed(2)} ₺</td>
                    <td class="text-end">
                        <button class="btn btn-outline-danger btn-sm remove-item"
                                data-product-id="${item.product_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        const total = subtotal;

        $('#subtotal').text(subtotal.toFixed(2) + ' ₺');
        $('#total').text(total.toFixed(2) + ' ₺');
    } else {
        $('#empty-cart-message').show();
        $('#cart-table').hide();
        $('#checkout-btn').hide();
        $('#subtotal').text('0.00 ₺');
        $('#total').text('0.00 ₺');
    }

    this.updateCartBadge();
}
            async updateCartItem(productId, quantity) {
                try {
                    this.showLoading();
                    const response = await fetch(`${this.baseUrl}/customer/update-cart`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId, quantity })
                    });

                    if (response.status === 419) {
                        if (await this.refreshCsrfToken()) {
                            return this.updateCartItem(productId, quantity); 
                        }
                        throw new Error('Oturum süresi doldu');
                    }

                    if (response.status === 401) {
                        this.showToast('Giriş yapmalısınız', 'error');
                        window.location.href = `${this.baseUrl}/login`;
                        return;
                    }

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Sepet güncellenemedi');
                    }

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Sepet güncellenemedi');
                    }

                    this.displayCart(data);
                    this.showToast(data.message || 'Sepet güncellendi');
                    await this.updateCartBadge();
                } catch (error) {
                    console.error('Update cart error:', error);
                    this.showToast(error.message, 'error');
                    this.loadCart();
                } finally {
                    this.hideLoading();
                }
            }

            async removeFromCart(productId) {
                try {
                    this.showLoading();
                    const response = await fetch(`${this.baseUrl}/customer/remove-from-cart`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    });

                    if (response.status === 419) {
                        if (await this.refreshCsrfToken()) {
                            return this.removeFromCart(productId); 
                        }
                        throw new Error('Oturum süresi doldu');
                    }

                    if (response.status === 401) {
                        this.showToast('Giriş yapmalısınız', 'error');
                        window.location.href = `${this.baseUrl}/login`;
                        return;
                    }

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Ürün sepetten kaldırılamadı');
                    }

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Ürün sepetten kaldırılamadı');
                    }

                    this.displayCart(data);
                    this.showToast(data.message || 'Ürün sepetten kaldırıldı');
                    await this.updateCartBadge();
                } catch (error) {
                    console.error('Remove from cart error:', error);
                    this.showToast(error.message, 'error');
                    this.loadCart();
                } finally {
                    this.hideLoading();
                }
            }

            async updateCartBadge() {
                try {
                    const response = await fetch(`${this.baseUrl}/customer/cart`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Sepet bilgileri alınamadı');
                    }

                    const data = await response.json();
                    if (!data.success) {
                        throw new Error(data.message || 'Sepet bilgileri yüklenemedi');
                    }

                    const itemCount = data.cart && Array.isArray(data.cart) ? 
                        data.cart.reduce((sum, item) => sum + (item.quantity || 0), 0) : 0;
                    $('#cartBadge').text(itemCount);
                } catch (error) {
                    console.error('Cart badge update error:', error);
                    this.showToast('Sepet durumu güncellenemedi', 'error');
                    $('#cartBadge').text('0'); 
                }
            }
        }

        $(document).ready(() => {
            new CustomerCartManager();
        });
    </script>
</body>
</html>