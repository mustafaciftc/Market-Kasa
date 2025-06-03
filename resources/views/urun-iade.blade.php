<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ürün İadeleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .header {
            background: linear-gradient(45deg, #007bff, #00c4ff);
            color: white;
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            border-radius: 10px 10px 0 0 !important;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .badge {
            padding: 6px 10px;
            font-weight: 500;
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
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }
        .filter-section {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container-fluid py-4">
        <div class="header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Ürün İadeleri</h4>
                <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Geri Dön
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="filter-section">
                    <form id="filterForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="dateFrom" class="form-label">Başlangıç Tarihi</label>
                                <input type="date" class="form-control" id="dateFrom" name="date_from">
                            </div>
                            <div class="col-md-3">
                                <label for="dateTo" class="form-label">Bitiş Tarihi</label>
                                <input type="date" class="form-control" id="dateTo" name="date_to">
                            </div>
                            <div class="col-md-3">
                                <label for="productSearch" class="form-label">Ürün Ara</label>
                                <input type="text" class="form-control" id="productSearch" placeholder="Ürün adı veya barkodu">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i> Filtrele
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">İade Kayıtları</h5>
                        <div>
                            <span class="badge bg-primary">
                                Toplam İade: {{ $returns->count() }}
                            </span>
                            <span class="badge bg-danger ms-2">
                                Toplam Tutar: {{ number_format($returns->sum('return_amount'), 2) }} ₺
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($returns->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Henüz iade kaydı bulunmamaktadır.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>İade No</th>
                                            <th>Ürün</th>
                                            <th>Satış No</th>
                                            <th>Miktar</th>
                                            <th>İade Tutarı</th>
                                            <th>Sebep</th>
                                            <th>Tarih</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($returns as $return)
                                            <tr>
                                                <td>#{{ $return->id }}</td>
                                                <td>
                                                    @if($return->product)
                                                        {{ $return->product->name }}
                                                        @if($return->product->barcode)
                                                            <br><small class="text-muted">Barkod: {{ $return->product->barcode }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-danger">Ürün silinmiş</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($return->sale_id)
                                                        <a href="{{ route('satisislem') }}?sale_id={{ $return->sale_id }}" target="_blank">
                                                            #{{ $return->sale_id }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted">Satış bağlantısı yok</span>
                                                    @endif
                                                </td>
                                                <td>{{ $return->quantity }}</td>
                                                <td>{{ number_format($return->return_amount, 2) }} ₺</td>
                                                <td>
                                                    <span class="d-inline-block text-truncate" style="max-width: 150px;" 
                                                        title="{{ $return->reason }}">
                                                        {{ $return->reason }}
                                                    </span>
                                                </td>
                                                <td>{{ $return->date ? $return->date->format('d.m.Y H:i') : 'Tarih Belirtilmemiş' }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger delete-return" 
                                                        data-id="{{ $return->id }}" 
                                                        title="İadeyi Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-center mt-3">
                                {{ $returns->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">İade Kaydını Sil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bu iade kaydını silmek istediğinize emin misiniz? Bu işlem geri alınamaz!</p>
                    <p><strong>İade No:</strong> <span id="returnIdText"></span></p>
                    <p><strong>Ürün:</strong> <span id="returnProductText"></span></p>
                    <p><strong>Tutar:</strong> <span id="returnAmountText"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Sil</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const baseUrl = document.querySelector('meta[name="app-url"]')?.content + '/dashboard' || '/dashboard';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            let returnToDelete = null;

            function showLoading() {
                document.getElementById('loadingOverlay').style.display = 'flex';
            }

            function hideLoading() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }

            function showToast(type, message) {
                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toast);
                new bootstrap.Toast(toast).show();
                setTimeout(() => toast.remove(), 3000);
            }

            // Delete return record
            document.querySelectorAll('.delete-return').forEach(btn => {
                btn.addEventListener('click', function() {
                    const returnId = this.dataset.id;
                    const row = this.closest('tr');
                    const productName = row.querySelector('td:nth-child(2)').textContent.trim();
                    const returnAmount = row.querySelector('td:nth-child(5)').textContent.trim();
                    
                    returnToDelete = returnId;
                    document.getElementById('returnIdText').textContent = returnId;
                    document.getElementById('returnProductText').textContent = productName;
                    document.getElementById('returnAmountText').textContent = returnAmount;
                    
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    deleteModal.show();
                });
            });

            document.getElementById('confirmDelete').addEventListener('click', async function() {
                if (!returnToDelete) return;
                
                try {
                    showLoading();
                    const response = await fetch(`https://everesiyedefteri.com.tr/dashboard/urun-iade/${returnToDelete}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Silme işlemi başarısız oldu.');
                    }

                    if (data.success) {
                        showToast('success', data.message || 'İade kaydı başarıyla silindi.');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showToast('danger', data.message || 'İade kaydı silinirken bir hata oluştu.');
                    }
                } catch (error) {
                    showToast('danger', 'Hata: ' + error.message);
                } finally {
                    hideLoading();
                    returnToDelete = null;
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    deleteModal.hide();
                }
            });

            // Filter form submission
            document.getElementById('filterForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const params = new URLSearchParams(formData).toString();
                window.location.href = `${window.location.pathname}?${params}`;
            });

            // Initialize date pickers with default values if they exist in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('date_from')) {
                document.getElementById('dateFrom').value = urlParams.get('date_from');
            }
            if (urlParams.has('date_to')) {
                document.getElementById('dateTo').value = urlParams.get('date_to');
            }
            if (urlParams.has('productSearch')) {
                document.getElementById('productSearch').value = urlParams.get('productSearch');
            }
        });
    </script>
</body>
</html>