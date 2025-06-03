<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Veresiye Yönetimi</title>
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
        .debt-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .debt-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .debt-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .debt-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .debt-list {
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
        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
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
            .debt-form .form-control {
                font-size: 0.85rem;
            }
        }
        @media (max-width: 768px) {
    .debt-list {
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

    .debt-list::-webkit-scrollbar {
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
        <h4>Veresiye Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Debt List -->
                <div class="debt-list">
                    <table id="debtTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Müşteri Adı</th>
                                <th>Telefon</th>
                                <th>Borç (₺)</th>
                                <th>Vade Süresi</th>
                                <th>Oluşturma Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debts as $debt)
                            <tr>
                                <td>{{ $debt->id }}</td>
               					 <td>{{ $debt->customer->name ?? 'Bilinmeyen Müşteri' }}</td>
                                <td>{{ $debt->customer && $debt->customer->phone ? $debt->customer->phone : 'Telefon Yok' }}</td>
                                <td>{{ number_format($debt->amount, 2) }}</td>
                                <td>{{ $debt->term ? $debt->term . ' Gün' : 'Belirtilmemiş' }}</td>
                                <td>{{ \Carbon\Carbon::parse($debt->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary view-details" data-id="{{ $debt->id }}" data-bs-toggle="modal" data-bs-target="#modal-debt-{{ $debt->id }}" title="Detayları Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $debt->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-info remind-btn" data-id="{{ $debt->id }}" title="Hatırlatma Gönder">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <form action="{{ route('veresiyeyonetimi.destroy', $debt->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu veresiye kaydını silmek istediğinize emin misiniz?')">
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

                    <!-- Edit Debt Modal -->
                    <div class="modal fade" id="editDebtModal" tabindex="-1" aria-labelledby="editDebtModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editDebtModalLabel">Veresiye Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editDebtForm">
                                    @csrf
                                    <input type="hidden" name="_method" value="PUT">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_customer_id" class="form-label">Müşteri</label>
                                                <select name="customer_id" id="edit_customer_id" class="form-control" required>
                                                    <option value="">Müşteri Seçin</option>
                                                    @foreach($customers as $customer)
                                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_amount" class="form-label">Borç Miktarı (₺)</label>
                                                <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_term" class="form-label">Vade Süresi (Gün)</label>
                                                <input type="number" name="term" id="edit_term" class="form-control">
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

   <!-- Debt Details Modals -->
    @foreach($debts as $debt)
    <div class="modal fade" id="modal-debt-{{ $debt->id }}" tabindex="-1" aria-labelledby="modal-debt-label-{{ $debt->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-debt-label-{{ $debt->id }}">Veresiye Bilgileri</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>ID:</strong> <span class="text-muted">{{ $debt->id }}</span></p>
                            <p><strong>Müşteri Adı:</strong> <span class="text-muted">{{ $debt->customer->name ?? 'Bilinmeyen Müşteri' }}</span></p>
                            <p><strong>Telefon:</strong> <span class="text-muted">{{ $debt->customer && $debt->customer->phone ? $debt->customer->phone : 'Telefon Yok' }}</span></p>
                            <p><strong>Borç Miktarı:</strong> <span class="text-muted">{{ number_format($debt->amount, 2) }} ₺</span></p>
                            <p><strong>Vade Süresi:</strong> <span class="text-muted">{{ $debt->term ? $debt->term . ' Gün' : 'Belirtilmemiş' }}</span></p>
                            <p><strong>Alışveriş Detayları:</strong> <span class="text-muted">{{ $debt->shopping_details ?? 'Detay belirtilmemiş' }}</span></p>
                            <p><strong>Notlar:</strong> <span class="text-muted">{{ $debt->notes ?? 'Not yok' }}</span></p>
                            <p><strong>Oluşturma Tarihi:</strong> <span class="text-muted">{{ \Carbon\Carbon::parse($debt->created_at)->format('d/m/Y H:i') }}</span></p>
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
        if (!$.fn.DataTable.isDataTable('#debtTable')) {
            const table = $('#debtTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 6 }
                ]
            });
        }

        // CSRF Token Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Yükleme animasyonu fonksiyonları
        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        // Veresiye Ekleme Formu
        $('#addDebtForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const formData = $(this).serialize();

            $.ajax({
                url: '{{ route("veresiyeekle.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#addDebtForm')[0].reset();
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        alert(response.message || 'Veresiye eklenirken bir hata oluştu.');
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
            console.log('Düzenle butonuna tıklandı:', $(this).data('id')); // Test için
            const debtId = $(this).data('id');
            showLoading();

            $.ajax({
                url: `/dashboard/veresiyeyonetimi/${debtId}/edit`,
                type: 'GET',
                success: function(data) {
                    $('#edit_customer_id').val(data.customer_id);
                    $('#edit_amount').val(data.amount);
                    $('#edit_term').val(data.term || '');

                    $('#editDebtForm').data('debtId', debtId);
                    $('#editDebtModal').modal('show');
                },
                error: function(xhr) {
                    let errorMessage = 'Veresiye bilgileri alınırken bir hata oluştu.';
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
        $('#editDebtForm').submit(function(e) {
            e.preventDefault();
            showLoading();

            const debtId = $(this).data('debtId');
            const formData = $(this).serialize();

            $.ajax({
                url: `/dashboard/veresiyeyonetimi/${debtId}`,
                type: 'POST',
                data: formData + '&_method=PUT',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        $('#editDebtModal').modal('hide');
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

        // Hatırlatma Gönder Butonu
        $(document).on('click', '.remind-btn', function() {
            console.log('Hatırlatma butonuna tıklandı:', $(this).data('id')); // Test için
            const debtId = $(this).data('id');
            if (!confirm('Bu müşteriye hatırlatma e-postası göndermek istediğinize emin misiniz?')) {
                return;
            }

            showLoading();

            $.ajax({
                url: `/dashboard/veresiyeyonetimi/remind`,
                type: 'POST',
                data: {
                    debt_id: debtId,
                    reminder_type: 'email',
                    notes: 'Manuel hatırlatma: Lütfen borcunuzu ödeyin.'
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.message || 'Hatırlatma e-postası başarıyla gönderildi.');
                    } else {
                        alert(response.message || 'Hatırlatma gönderilirken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Hatırlatma gönderilirken bir hata oluştu.';
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
    });
    </script>
</body>
</html>
