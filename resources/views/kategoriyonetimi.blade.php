<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Kategori Yönetimi</title>
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
        .category-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .category-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .category-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .category-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .category-list {
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
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
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
            .category-form .form-control {
                font-size: 0.85rem;
            }
        }
        @media (max-width: 768px) {
    .category-list {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        min-width: 700px;
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

    .category-list::-webkit-scrollbar {
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
        <h4>Kategori Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Category Add Form -->
                <div class="category-form">
                    <h5>Yeni Kategori Ekle</h5>
                    <form id="addCategoryForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Kategori Adı</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Kategori Adı" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Kategori Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Category List -->
                <div class="category-list">
                    <table id="categoryTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kategori Adı</th>
                                <th>Oluşturulma Tarihi</th>
                                <th>Güncellenme Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($category->created_at)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($category->updated_at)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $category->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('kategoriyonetimi.destroy', $category->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
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

                    <!-- Edit Category Modal -->
                    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCategoryModalLabel">Kategori Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editCategoryForm">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label for="edit_name" class="form-label">Kategori Adı</label>
                                                <input type="text" name="name" id="edit_name" class="form-control" required>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
    $(document).ready(function () {
        // DataTable başlatma
        if (!$.fn.DataTable.isDataTable('#categoryTable')) {
            $('#categoryTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 4 }
                ]
            });
        }

        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Kategori Ekleme Formu
        $('#addCategoryForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("kategoriyonetimi.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addCategoryForm')[0].reset();
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        alert(response.message || 'Kategori eklenirken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'İşlem sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Düzenle butonlarına click eventi
        $(document).on('click', '.edit-btn', function() {
            const categoryId = $(this).data('id');
            showLoading();

            $.ajax({
                url: `/dashboard/kategoriyonetimi/${categoryId}/edit`,
                type: 'GET',
                success: function(data) {
                    $('#edit_name').val(data.name);

                    $('#editCategoryForm').data('categoryId', categoryId);
                    $('#editCategoryModal').modal('show');
                },
                error: function(xhr) {
                    let errorMessage = 'Kategori bilgileri alınırken bir hata oluştu.';
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

        // Güncelleme Formu
        $('#editCategoryForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const categoryId = $(this).data('categoryId');
            const formData = $(this).serialize();

            $.ajax({
                url: `/dashboard/kategoriyonetimi/${categoryId}`,
                type: 'POST',
                data: formData + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#editCategoryModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        alert(response.message || 'Güncelleme sırasında bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    alert(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Loading Indicator Functions
        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }
    });
    </script>
</body>
</html>
