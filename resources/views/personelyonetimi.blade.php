<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Personel Yönetimi</title>
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
        .staff-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .staff-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .staff-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .staff-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .staff-list {
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

        @media (max-width: 768px) {
            .table th, .table td {
                padding: 8px;
                font-size: 0.85rem;
            }
            .btn-sm {
                padding: 5px 10px;
                font-size: 0.75rem;
            }
            .staff-form .form-control {
                font-size: 0.85rem;
            }
        }
        @media (max-width: 768px) {
    .staff-list {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        min-width: 900px;
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

    .staff-list::-webkit-scrollbar {
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
  @if(auth()->check() && auth()->user()->role === \App\Models\User::ROLE_ADMIN)

    <div class="header">
        <h4>Personel Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Staff Add Form -->
                <div class="staff-form">
                    <h5>Yeni Personel Ekle</h5>
                    <form id="addStaffForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Ad Soyad" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="E-posta" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Telefon">
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Pozisyon</label>
                                <input type="text" name="position" id="position" class="form-control" placeholder="Pozisyon" required>
                            </div>
                            <div class="col-md-6">
                                <label for="salary" class="form-label">Maaş (₺)</label>
                                <input type="number" name="salary" id="salary" class="form-control" step="0.01" placeholder="Maaş" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hire_date" class="form-label">İşe Başlama Tarihi</label>
                                <input type="date" name="hire_date" id="hire_date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Personel Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Staff List -->
                <div class="staff-list">
                    <table id="staffTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ad Soyad</th>
                                <th>E-posta</th>
                                <th>Telefon</th>
                                <th>Pozisyon</th>
                                <th>Maaş (₺)</th>
                                <th>İşe Başlama Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($staff as $employee)
                            <tr>
                                <td>{{ $employee->id }}</td>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone ?? 'Telefon Yok' }}</td>
                                <td>{{ $employee->position }}</td>
                                <td>{{ number_format($employee->salary, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary view-details" data-id="{{ $employee->id }}" data-bs-toggle="modal" data-bs-target="#modal-staff-{{ $employee->id }}" title="Detayları Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $employee->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('personelyonetimi.destroy', $employee->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu personeli silmek istediğinize emin misiniz?')">
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

                    <!-- Edit Staff Modal -->
                    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editStaffModalLabel">Personel Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editStaffForm">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_name" class="form-label">Ad Soyad</label>
                                                <input type="text" name="name" id="edit_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_email" class="form-label">E-posta</label>
                                                <input type="email" name="email" id="edit_email" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_phone" class="form-label">Telefon</label>
                                                <input type="text" name="phone" id="edit_phone" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_position" class="form-label">Pozisyon</label>
                                                <input type="text" name="position" id="edit_position" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_salary" class="form-label">Maaş (₺)</label>
                                                <input type="number" name="salary" id="edit_salary" class="form-control" step="0.01" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_hire_date" class="form-label">İşe Başlama Tarihi</label>
                                                <input type="date" name="hire_date" id="edit_hire_date" class="form-control" required>
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

    <!-- Staff Details Modals -->
    @foreach($staff as $employee)
    <div class="modal fade" id="modal-staff-{{ $employee->id }}" tabindex="-1" aria-labelledby="modal-staff-label-{{ $employee->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-staff-label-{{ $employee->id }}">Personel Bilgileri</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>ID:</strong> <span class="text-muted">{{ $employee->id }}</span></p>
                            <p><strong>Ad Soyad:</strong> <span class="text-muted">{{ $employee->name }}</span></p>
                            <p><strong>E-posta:</strong> <span class="text-muted">{{ $employee->email }}</span></p>
                            <p><strong>Telefon:</strong> <span class="text-muted">{{ $employee->phone ?? 'Telefon Yok' }}</span></p>
                            <p><strong>Pozisyon:</strong> <span class="text-muted">{{ $employee->position }}</span></p>
                            <p><strong>Maaş:</strong> <span class="text-muted">{{ number_format($employee->salary, 2) }} ₺</span></p>
                            <p><strong>İşe Başlama Tarihi:</strong> <span class="text-muted">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') }}</span></p>
                            <p><strong>Oluşturma Tarihi:</strong> <span class="text-muted">{{ \Carbon\Carbon::parse($employee->created_at)->format('d/m/Y H:i') }}</span></p>
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
        // DataTable başlatma
        if (!$.fn.DataTable.isDataTable('#staffTable')) {
            $('#staffTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 7 }
                ]
            });
        }

        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Personel Ekleme Formu
        $('#addStaffForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("personelyonetimi.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addStaffForm')[0].reset();
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        alert(response.message || 'Personel eklenirken bir hata oluştu.');
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
            const staffId = $(this).data('id');
            showLoading();

            $.ajax({
                url: `/dashboard/personelyonetimi/${staffId}/edit`,
                type: 'GET',
                success: function(data) {
                    $('#edit_name').val(data.name);
                    $('#edit_email').val(data.email);
                    $('#edit_phone').val(data.phone || '');
                    $('#edit_position').val(data.position);
                    $('#edit_salary').val(data.salary);
                    $('#edit_hire_date').val(data.hire_date);

                    $('#editStaffForm').data('staffId', staffId);
                    $('#editStaffModal').modal('show');
                },
                error: function(xhr) {
                    let errorMessage = 'Personel bilgileri alınırken bir hata oluştu.';
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
        $('#editStaffForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const staffId = $(this).data('staffId');
            const formData = $(this).serialize();

            $.ajax({
                url: `/dashboard/personelyonetimi/${staffId}`,
                type: 'POST',
                data: formData + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#editStaffModal').modal('hide');
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
    @else
    <div class="alert alert-danger text-center">
        <h4>Yetkisiz Erişim</h4>
        <p>Bu sayfayı görüntülemek için admin yetkisine sahip olmalısınız.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Ana Sayfaya Dön</a>
    </div>
    @endif
</body>
</html>
