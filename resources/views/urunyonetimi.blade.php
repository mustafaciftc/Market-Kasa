<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Ürün Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 15px 25px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-radius: 10px 10px 0 0;
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
        .product-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .product-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .product-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .product-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .product-list {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
        }
        .table thead {
            background: linear-gradient(45deg, #343a40, #6c757d);
            color: white;
        }
        .table tr:hover {
            background-color: #f1f1f1;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f8f9fa;
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
        .modal-body .text-muted {
            color: #6c757d !important;
        }
        .modal-footer {
            border-top: none;
            border-radius: 0 0 10px 10px;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        #islem {
            white-space: nowrap;
        }
        .table img {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 60px;
            height: 60px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
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
        @media (max-width: 768px) {
            .table th, .table td {
                padding: 8px;
                font-size: 0.85rem;
            }
            .btn-sm {
                padding: 5px 10px;
                font-size: 0.75rem;
            }
            .product-form .form-control {
                font-size: 0.85rem;
            }
            .table img {
                width: 40px;
                height: 40px;
            }
        }
        @media (max-width: 768px) {
            .product-list {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .table {
                min-width: 1200px;
            }
            .table th,
            .table td {
                font-size: 0.85rem;
                padding: 8px;
            }
            .table thead th {
                position: sticky;
                top: 0;
                z-index: 1;
            }
            .product-list::-webkit-scrollbar {
                height: 8px;
            }
            .table td.text-center {
                display: flex;
                justify-content: center;
                gap: 5px;
                flex-wrap: nowrap;
            }
            .btn-sm {
                padding: 5px 8px;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="header">
        <h4>Ürün Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Product Add Form -->
                <div class="product-form">
                    <h5>Yeni Ürün Ekle</h5>
                    <form id="addProductForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ürün Adı</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Ürün Adı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">Kategori Seçin</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="barcode" class="form-label">Barkod</label>
                                <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Barkod">
                            </div>
                            <div class="col-md-6">
                                <label for="buy_price" class="form-label">Alış Fiyatı (₺)</label>
                                <input type="number" name="buy_price" id="buy_price" class="form-control" step="0.01" placeholder="Alış Fiyatı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sell_price" class="form-label">Satış Fiyatı (₺)</label>
                                <input type="number" name="sell_price" id="sell_price" class="form-control" step="0.01" placeholder="Satış Fiyatı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="stock_quantity" class="form-label">Stok Miktarı</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" placeholder="Stok Miktarı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="entry_date" class="form-label">Giriş Tarihi</label>
                                <input type="date" name="entry_date" id="entry_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="expiry_date" class="form-label">Son Kullanma Tarihi</label>
                                <input type="date" name="expiry_date" id="expiry_date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="active" class="form-label">Durum</label>
                                <select name="active" id="active" class="form-control" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Taslak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="image" class="form-label">Ürün Görseli</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" onchange="previewImage(event, 'add-preview')">
                                <div id="add-preview" class="mt-2"></div>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">Açıklama</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Açıklama"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Ürün Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Product List -->
                <div class="product-list">
                    <table id="productTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Görsel</th>
                                <th>Ürün Adı</th>
                                <th>Kategori</th>
                                <th>Durum</th>
                                <th>Barkod</th>
                                <th>Mevcut Stok</th>
                                <th>Birim</th>
                                <th>Giriş Tarihi</th>
                                <th>Son Kullanma Tarihi</th>
                                <th>Açıklama</th>
                                <th class="text-center" id="islem">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($products->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">Ürün Yok</td>
                                </tr>
                            @else
                                @foreach($products as $product)
                                <tr data-id="{{ $product->id }}">
                                    <td>{{ $product->id }}</td>
                                    <td class="text-center">
                                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.jpg') }}"
                                             alt="{{ $product->name }}"
                                             onerror="console.log('Image failed to load: {{ asset('storage/' . $product->image) }}'); this.src='https://via.placeholder.com/60?text=Resim+Yok';">
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'Kategori Yok' }}</td>
                                    <td>{{ $product->active ? 'Aktif' : 'Taslak' }}</td>
                                    <td>{{ $product->barcode ?? 'Yok' }}</td>
                                    <td>{{ $product->stock_quantity ?? 0 }}</td>
                                    <td>Adet</td>
                                    <td>{{ $product->entry_date ? \Carbon\Carbon::parse($product->entry_date)->format('d/m/Y') : 'Belirtilmemiş' }}</td>
                                    <td>{{ $product->expiry_date ? \Carbon\Carbon::parse($product->expiry_date)->format('d/m/Y') : 'Belirtilmemiş' }}</td>
                                    <td>{{ Str::limit($product->description ?? 'Açıklama Yok', 50) }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary view-details" data-id="{{ $product->id }}" data-bs-toggle="modal" data-bs-target="#modal-product-{{ $product->id }}" title="Detayları Görüntüle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $product->id }}" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('urun.destroy', $product->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <!-- Edit Product Modal -->
                    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProductModalLabel">Ürün Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editProductForm" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_name" class="form-label">Ürün Adı</label>
                                                <input type="text" name="name" id="edit_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_category_id" class="form-label">Kategori</label>
                                                <select name="category_id" id="edit_category_id" class="form-control" required>
                                                    <option value="">Kategori Seçin</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_barcode" class="form-label">Barkod</label>
                                                <input type="text" name="barcode" id="edit_barcode" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_buy_price" class="form-label">Alış Fiyatı (₺)</label>
                                                <input type="number" name="buy_price" id="edit_buy_price" class="form-control" step="0.01" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_sell_price" class="form-label">Satış Fiyatı (₺)</label>
                                                <input type="number" name="sell_price" id="edit_sell_price" class="form-control" step="0.01" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_stock_quantity" class="form-label">Stok Miktarı</label>
                                                <input type="number" name="stock_quantity" id="edit_stock_quantity" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_entry_date" class="form-label">Giriş Tarihi</label>
                                                <input type="date" name="entry_date" id="edit_entry_date" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_expiry_date" class="form-label">Son Kullanma Tarihi</label>
                                                <input type="date" name="expiry_date" id="edit_expiry_date" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_active" class="form-label">Durum</label>
                                                <select name="active" id="edit_active" class="form-control" required>
                                                    <option value="1">Aktif</option>
                                                    <option value="0">Taslak</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_image" class="form-label">Ürün Görseli</label>
                                                <input type="file" name="image" id="edit_image" class="form-control" accept="image/*" onchange="previewImage(event, 'edit-preview')">
                                                <div id="edit-preview" class="mt-2"></div>
                                            </div>
                                            <div class="col-12">
                                                <label for="edit_description" class="form-label">Açıklama</label>
                                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Güncelle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Modals -->
    @foreach($products as $product)
    <div class="modal fade" id="modal-product-{{ $product->id }}" tabindex="-1" aria-labelledby="modal-product-label-{{ $product->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-product-label-{{ $product->id }}">Ürün Bilgileri</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>ID:</strong> <span class="text-muted">{{ $product->id }}</span></p>
                            <p><strong>Görsel:</strong></p>
                            <div class="text-center mb-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 200px; border-radius: 8px;" onerror="console.log('Modal image failed to load: {{ asset('products/' . $product->image) }}'); this.src='https://via.placeholder.com/200?text=Resim+Yok';">
                                @else
                                    <span class="text-muted">Resim Yok</span>
                                @endif
                            </div>
                            <p><strong>Ürün Adı:</strong> <span class="text-muted">{{ $product->name }}</span></p>
                            <p><strong>Kategori:</strong> <span class="text-muted">{{ $product->category->name ?? 'Kategori Yok' }}</span></p>
                            <p><strong>Durum:</strong> <span class="text-muted">{{ $product->active ? 'Aktif' : 'Taslak' }}</span></p>
                            <p><strong>Barkod:</strong> <span class="text-muted">{{ $product->barcode ?? 'Yok' }}</span></p>
                            <p><strong>Mevcut Stok:</strong> <span class="text-muted">{{ $product->stock_quantity ?? 0 }} Adet</span></p>
                            <p><strong>Alış Fiyatı:</strong> <span class="text-muted">{{ number_format($product->buy_price, 2) }} ₺</span></p>
                            <p><strong>Satış Fiyatı:</strong> <span class="text-muted">{{ number_format($product->sell_price, 2) }} ₺</span></p>
                            <p><strong>Giriş Tarihi:</strong> <span class="text-muted">{{ $product->entry_date ? \Carbon\Carbon::parse($product->entry_date)->format('d/m/Y') : 'Belirtilmemiş' }}</span></p>
                            <p><strong>Son Kullanma Tarihi:</strong> <span class="text-muted">{{ $product->expiry_date ? \Carbon\Carbon::parse($product->expiry_date)->format('d/m/Y') : 'Belirtilmemiş' }}</span></p>
                            <p><strong>Açıklama:</strong> <span class="text-muted">{{ $product->description ?? 'Açıklama Yok' }}</span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
$(document).ready(function () {
    const table = $('#productTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
        },
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1, 11] }
        ]
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function showLoading() {
        $('#loadingOverlay').css('display', 'flex');
    }

    function hideLoading() {
        $('#loadingOverlay').css('display', 'none');
    }

    window.previewImage = function(event, previewId) {
        const file = event.target.files[0];
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '8px';
                img.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    };

   $('#addProductForm').submit(function(e) {
    e.preventDefault();
    showLoading();

    const formData = new FormData(this);
    const $form = $(this);

    $.ajax({
        url: '{{ route("urun.store") }}',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success && response.product) {
                // Formu temizle
                $form[0].reset();
                $('#add-preview').empty();

                // Yeni ürünü tabloya ekle
                addProductToTable(response.product, response.image_url);
                
                // Başarı mesajı göster
                showAlert('success', 'Ürün başarıyla eklendi.');
            } else {
                showAlert('error', response.message || 'Ürün eklenirken bir hata oluştu.');
            }
        },
        error: function(xhr) {
            let errorMessage = 'İşlem sırasında bir hata oluştu.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
            }
            showAlert('error', errorMessage);
        },
        complete: function() {
            hideLoading();
        }
    });
});

function addProductToTable(product, imageUrl) {
    const imageHtml = imageUrl 
        ? `<img src="${imageUrl}" alt="${product.name}" class="img-fluid" onerror="this.src='https://via.placeholder.com/60?text=Resim+Yok';">`
        : '<span class="text-muted">Resim Yok</span>';

    const newRow = [
        product.id,
        imageHtml,
        product.name,
        product.category ? product.category.name : 'Kategori Yok',
        product.active ? 'Aktif' : 'Taslak',
        product.barcode || 'Yok',
        product.stock_quantity || 0,
        'Adet',
        product.entry_date ? formatDate(product.entry_date) : 'Belirtilmemiş',
        product.expiry_date ? formatDate(product.expiry_date) : 'Belirtilmemiş',
        (product.description || 'Açıklama Yok').substring(0, 50),
        createActionButtons(product.id)
    ];

    // DataTable'a yeni satırı ekle
    $('#productTable').DataTable().row.add(newRow).draw(false);

    // Yeni modal oluştur
    createProductModal(product, imageUrl);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR');
}

function createActionButtons(productId) {
    return `
        <div class="text-center">
            <button class="btn btn-sm btn-primary view-details" data-id="${productId}" data-bs-toggle="modal" data-bs-target="#modal-product-${productId}" title="Detayları Görüntüle">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-warning edit-btn" data-id="${productId}" title="Düzenle">
                <i class="fas fa-edit"></i>
            </button>
            <form action="{{ url('/dashboard/urun') }}/${productId}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>`;
}

function createProductModal(product, imageUrl) {
    const modalHtml = `
        <div class="modal fade" id="modal-product-${product.id}" tabindex="-1" aria-labelledby="modal-product-label-${product.id}" aria-hidden="true">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-product-label-${product.id}">Ürün Bilgileri</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>ID:</strong> <span class="text-muted">${product.id}</span></p>
                        <p><strong>Görsel:</strong></p>
                        <div class="text-center mb-3">
                            ${imageUrl ? `<img src="${imageUrl}" alt="${product.name}" style="max-width: 100%; max-height: 200px; border-radius: 8px;" onerror="this.src='https://via.placeholder.com/200?text=Resim+Yok';">` : '<span class="text-muted">Resim Yok</span>'}
                        </div>
                        <p><strong>Ürün Adı:</strong> <span class="text-muted">${product.name}</span></p>
                        <p><strong>Kategori:</strong> <span class="text-muted">${product.category ? product.category.name : 'Kategori Yok'}</span></p>
                        <p><strong>Durum:</strong> <span class="text-muted">${product.active ? 'Aktif' : 'Taslak'}</span></p>
                        <p><strong>Barkod:</strong> <span class="text-muted">${product.barcode || 'Yok'}</span></p>
                        <p><strong>Mevcut Stok:</strong> <span class="text-muted">${product.stock_quantity || 0} Adet</span></p>
                        <p><strong>Alış Fiyatı:</strong> <span class="text-muted">${parseFloat(product.buy_price).toFixed(2)} ₺</span></p>
                        <p><strong>Satış Fiyatı:</strong> <span class="text-muted">${parseFloat(product.sell_price).toFixed(2)} ₺</span></p>
                        <p><strong>Giriş Tarihi:</strong> <span class="text-muted">${product.entry_date ? formatDate(product.entry_date) : 'Belirtilmemiş'}</span></p>
                        <p><strong>Son Kullanma Tarihi:</strong> <span class="text-muted">${product.expiry_date ? formatDate(product.expiry_date) : 'Belirtilmemiş'}</span></p>
                        <p><strong>Açıklama:</strong> <span class="text-muted">${product.description || 'Açıklama Yok'}</span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kapat</button>
                    </div>
                </div>
            </div>
        </div>`;
    
    $('body').append(modalHtml);
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    
    $('body').append(alertHtml);
    
    // 5 saniye sonra uyarıyı otomatik kapat
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

    $(document).on('click', '.edit-btn', function() {
        const productId = $(this).data('id');
        showLoading();

        $.ajax({
            url: `/dashboard/urun/${productId}/edit`,
            type: 'GET',
            success: function(data) {
                $('#edit_name').val(data.name);
                $('#edit_category_id').val(data.category_id);
                $('#edit_barcode').val(data.barcode || '');
                $('#edit_buy_price').val(data.buy_price);
                $('#edit_sell_price').val(data.sell_price);
                $('#edit_stock_quantity').val(data.stock_quantity);
                $('#edit_entry_date').val(data.entry_date || '');
                $('#edit_expiry_date').val(data.expiry_date || '');
                $('#edit_active').val(data.active ? '1' : '0');
                $('#edit_description').val(data.description || '');

                if (data.image_url) {
                    $('#edit-preview').html(`<img src="${data.image_url}" alt="${data.name}" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">`);
                } else {
                    $('#edit-preview').html('<span class="text-muted">Resim Yok</span>');
                }

                $('#editProductForm').data('productId', productId);
                $('#editProductModal').modal('show');
            },
            error: function(xhr) {
                let errorMessage = 'Ürün bilgileri alınırken bir hata oluştu.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                hideLoading();
            }
        });
    });

  $('#editProductForm').submit(function(e) {
    e.preventDefault();
    showLoading();

    const productId = $(this).data('productId');
    const formData = new FormData(this);

    $.ajax({
        url: `/dashboard/urun/${productId}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success && response.product) {
                showAlert('success', response.message);
                $('#editProductModal').modal('hide');
                
                // Sayfayı yenile
                location.reload();
            } else {
                showAlert('error', response.message || 'Güncelleme sırasında bir hata oluştu.');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
            }
            showAlert('error', errorMessage);
        },
        complete: function() {
            hideLoading();
        }
    });
});
});
    </script>
</body>
</html>