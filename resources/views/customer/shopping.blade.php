<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Ürünler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            height: 100vh;
            overflow: hidden;
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
            margin-left: 10px;
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
        .category-list {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            height: calc(100vh - 60px);
            display: flex;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .category-sidebar {
            width: 30%;
            padding: 10px;
            border-right: 1px solid #eee;
            overflow-y: auto;
        }
        .category-item {
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .category-item:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }
        .category-item.active {
            background-color: #007bff;
            color: white;
            border-color: #006fe6;
        }
        .product-display {
            width: 70%;
            padding: 10px;
            overflow-y: auto;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }
        .product-item {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            text-align: center;
        }
        .product-item:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .product-item img {
            width: 100%;
            height: 100px;
            border-radius: 5px;
            object-fit: cover;
        }
        .product-item p {
            margin: 8px 0 0;
            font-size: 0.95rem;
        }
        .product-item small {
            color: #6c757d;
            font-size: 0.8rem;
        }
        .add-to-cart-btn {
            margin-top: 5px;
            padding: 5px;
            font-size: 0.85rem;
            width: 100%;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 5px;
            gap: 5px;
        }
        .quantity-controls input {
            width: 50px;
            text-align: center;
            padding: 2px;
            font-size: 0.85rem;
        }
       
        @media (max-width: 576px) {
            .category-list {
                flex-direction: column;
            }
            .category-sidebar, .product-display {
                width: 100%;
            }
            .category-sidebar {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            .header a {
                font-size: 0;
            }
            .header a i {
                font-size: 1rem;
            }
            .header a::after {
                content: none;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h4>Müşteri Alışveriş</h4>
        <div>
		<a href="{{ route('customer.returns') }}">
    <i class="fas fa-undo me-1"></i> İadeler
</a>
<a href="{{ route('customer.orders') }}">
    <i class="fas fa-list-alt me-1"></i> Siparişler
</a>
            <a href="{{ route('customer.cart') }}">
                <i class="fas fa-shopping-cart me-1"></i> Sepet
                <span class="cart-badge" id="cartBadge">0</span>
            </a>
            <a href="#" id="clearCartBtn">
                <i class="fas fa-trash-alt me-1"></i> Sepeti Temizle
            </a>
            @auth
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i> Çıkış Yap
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}">Giriş Yap</a>
            @endauth
        </div>
    </div>

    <div class="container-fluid">
        <div class="category-list">
            <div class="category-sidebar">
                <h6>Kategoriler</h6>
                @if ($categories->isEmpty())
                    <p class="text-muted">Kategori bulunmamaktadır.</p>
                @else
                    @foreach ($categories as $category)
                        <div class="category-item" data-id="{{ $category->id }}">{{ $category->name }}</div>
                    @endforeach
                @endif
            </div>
            <div class="product-display">
                <h5 id="productDisplayTitle">Ürünler</h5>
                <div class="product-grid" id="productGrid"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
  class CustomerShoppingManager {
    constructor() {
        this.baseUrl = document.querySelector('meta[name="app-url"]').content;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        this.isAuthenticated = !!document.querySelector('#logout-form');
        this.initClearCartButton();
        this.initEventListeners();
        this.loadFirstCategory();
        this.updateCartBadge();
    }

    initClearCartButton() {
        $('#clearCartBtn').on('click', (e) => {
            e.preventDefault();
            this.clearCart();
        });
    }

    async clearCart() {
        try {
            if (!confirm('Sepeti temizlemek istediğinize emin misiniz?')) {
                return;
            }
            this.showLoading();
            const response = await fetch(`${this.baseUrl}/customer/clear-cart`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            if (response.status === 419) {
                if (await this.refreshCsrfToken()) {
                    return this.clearCart(); // Retry with new token
                }
                throw new Error('Oturum süresi doldu');
            }

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Sepet temizlenemedi');
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Sepet temizlenemedi');
            }

            this.showToast(data.message);
            await this.updateCartBadge();
        } catch (error) {
            console.error('Clear cart error:', error);
            this.showToast(error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }

    initEventListeners() {
        $('.category-item').on('click', (e) => {
            $('.category-item').removeClass('active');
            $(e.currentTarget).addClass('active');
            const categoryId = $(e.currentTarget).data('id');
            this.fetchProducts(categoryId);
        });

        $('#productGrid').on('click', '.add-to-cart-btn', (e) => {
            const button = $(e.currentTarget);
            const productId = button.data('product-id');
            const quantity = 1; 
            this.addToCart(productId, quantity);
        });
    }

    showLoading() {
        console.log('Loading...');
    }

    hideLoading() {
        console.log('Loading complete');
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

async fetchProducts(categoryId) {
    try {
        const response = await fetch(`${this.baseUrl}/customer/urun/get-by-category?category_id=${categoryId}`);
        if (!response.ok) throw new Error('Ürünler alınamadı');
        
        const products = await response.json();
        console.log('Alınan ürünler:', products); 
        
        if (!Array.isArray(products)) throw new Error('Geçersiz ürün verisi');
        
        this.displayProducts(products);
    } catch (error) {
        console.error('Hata:', error);
        this.showToast(error.message, 'error');
    }
}

displayProducts(products) {
    const productGrid = document.getElementById('productGrid');
    productGrid.innerHTML = '';

    if (products.length === 0) {
        productGrid.innerHTML = '<p class="text-muted">Bu kategoride ürün bulunmamaktadır.</p>';
        return;
    }

    products.forEach(product => {
        const productItem = document.createElement('div');
        productItem.className = 'product-item';
        
        const imageUrl = product.image || `${this.baseUrl}/images/default-product.jpg`;
        
        productItem.innerHTML = `
            <img src="${imageUrl}" alt="${product.name}" 
                 onerror="this.src='${this.baseUrl}/images/default-product.jpg'">
            <p class="mt-2 mb-1"><strong>${product.name}</strong></p>
            <small class="text-success">${product.sell_price} TL</small>
            <div class="mt-2">
                <button class="btn btn-sm btn-primary add-to-cart-btn" 
                        data-product-id="${product.id}">
                    Sepete Ekle
                </button>
            </div>
        `;
        
        productGrid.appendChild(productItem);
    });
}
        
    async addToCart(productId, quantity = 1) {
        try {
            if (!Number.isInteger(quantity) || quantity < 1) {
                throw new Error('Geçersiz miktar');
            }
            this.showLoading();
            const response = await fetch(`${this.baseUrl}/customer/add-to-cart`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId, quantity })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Ürün sepete eklenemedi');
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Sepet güncellenemedi');
            }

            this.showToast(data.message);
            await this.updateCartBadge();
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showToast(error.message, 'error');
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

    loadFirstCategory() {
        const firstCategory = $('.category-item').first();
        if (firstCategory.length) {
            firstCategory.addClass('active');
            this.fetchProducts(firstCategory.data('id'));
        }
    }
}

$(document).ready(() => {
    new CustomerShoppingManager();
});
    </script>
</body>
</html>