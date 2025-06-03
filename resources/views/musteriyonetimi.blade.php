<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Müşteri Yönetimi</title>
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
        .customer-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .customer-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .customer-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .customer-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .customer-list {
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
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .filter-container {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-container input {
            max-width: 300px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
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
        @media (max-width: 768px) {
            .table th, .table td {
                padding: 8px;
                font-size: 0.85rem;
            }
            .btn-sm {
                padding: 5px 10px;
                font-size: 0.75rem;
            }
            .customer-form .form-control {
                font-size: 0.85rem;
            }
            .filter-container input {
                max-width: 100%;
            }
        }
        @media (max-width: 768px) {
    .customer-list {
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

    .customer-list::-webkit-scrollbar {
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

    <div class="header">
        <h4>Müşteri Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Customer Add Form -->
                <div class="customer-form">
                    <h5>Yeni Müşteri Ekle</h5>
                    <form action="{{ route('musteriekle.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad & Soyad</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Ad & Soyad" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Telefon">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="E-posta">
                            </div>
                            <div class="col-md-6">
                                <label for="address" class="form-label">Adres</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Adres">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Müşteri Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Customer List -->
                <div class="customer-list">

                    <table id="customerTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ad & Soyad</th>
                                <th>Telefon</th>
                                <th>E-posta</th>
                                <th>Adres</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone ?? 'Telefon Yok' }}</td>
                                <td>{{ $customer->email ?? 'E-posta Yok' }}</td>
                                <td>{{ $customer->address ?? 'Adres Yok' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary view-details" data-id="{{ $customer->id }}" data-bs-toggle="modal" data-bs-target="#modal-basic-{{ $customer->id }}" title="Detayları Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $customer->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('musteri.delete', $customer->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?')">
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

                    <!-- Edit Customer Modal -->
                    <div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCustomerModalLabel">Müşteri Düzenle</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="editCustomerForm" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="customer_id" id="edit_customer_id">
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="edit_name" class="form-label">Ad & Soyad</label>
                                                <input type="text" name="name" id="edit_name" class="form-control" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_phone" class="form-label">Telefon</label>
                                                <input type="text" name="phone" id="edit_phone" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_email" class="form-label">E-posta</label>
                                                <input type="email" name="email" id="edit_email" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="edit_address" class="form-label">Adres</label>
                                                <input type="text" name="address" id="edit_address" class="form-control">
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

    <!-- Customer Details Modals -->
    @foreach($customers as $customer)
    <div class="modal fade" id="modal-basic-{{ $customer->id }}" tabindex="-1" aria-labelledby="modal-basic-label-{{ $customer->id }}" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-basic-label-{{ $customer->id }}">Müşteri Bilgileri</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p><strong>Ad & Soyad:</strong> <span class="text-muted">{{ $customer->name }}</span></p>
                            <p><strong>Telefon:</strong> <span class="text-muted">{{ $customer->phone ?? 'Telefon Yok' }}</span></p>
                            <p><strong>E-posta:</strong> <span class="text-muted">{{ $customer->email ?? 'E-posta Yok' }}</span></p>
                            <p><strong>Adres:</strong> <span class="text-muted">{{ $customer->address ?? 'Adres Yok' }}</span></p>
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
      // Initialize DataTable
      if (!$.fn.DataTable.isDataTable('#customerTable')) {
          const table = $('#customerTable').DataTable({
              language: {
                  url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json'
              },
              pageLength: 10,
              order: [[0, 'desc']],
              columnDefs: [
                  { orderable: false, targets: 5 }
              ]
          });

          // Name filter input
          $('#nameFilter').on('input', function () {
              table.column(1).search(this.value).draw();
          });
      }

      // Define loading functions
      function showLoading() {
          $('.loading-overlay').show();
      }

      function hideLoading() {
          $('.loading-overlay').hide();
      }

      // AJAX setup for CSRF token
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

      // Use event delegation for edit button click
      $(document).on('click', '.edit-btn', function() {
          const customerId = $(this).data('id');
          if (!customerId) {
              alert('Müşteri ID alınamadı! Lütfen tekrar deneyin.');
              return;
          }
          showLoading();

          // Use Laravel route helper to generate the correct URL
          const editUrl = `{{ route('musteri.edit', ':id') }}`.replace(':id', customerId);

          $.ajax({
              url: editUrl,
              type: 'GET',
              dataType: 'json',
              success: function(data) {
                  // Populate the modal fields
                  $('#edit_customer_id').val(customerId);
                  $('#edit_name').val(data.name || '');
                  $('#edit_phone').val(data.phone || '');
                  $('#edit_email').val(data.email || '');
                  $('#edit_address').val(data.address || '');

                  // Show the modal
                  $('#editCustomerModal').modal('show');
              },
              error: function(xhr) {
                  console.error('AJAX Error:', xhr);
                  alert('Müşteri bilgileri alınırken bir hata oluştu: ' + (xhr.responseJSON?.message || xhr.statusText));
              },
              complete: function() {
                  hideLoading();
              }
          });
      });

      // Form submission for updating customer
      $(document).on('submit', '#editCustomerForm', function(e) {
          e.preventDefault();
          showLoading();

          const customerId = $('#edit_customer_id').val();
          if (!customerId) {
              alert('Müşteri ID bulunamadı! Lütfen tekrar deneyin.');
              hideLoading();
              return;
          }

          // Use Laravel route helper for update URL
          const updateUrl = `{{ route('musteri.update', ':id') }}`.replace(':id', customerId);

          $.ajax({
              url: updateUrl,
              type: 'PUT',
              data: $(this).serialize(),
              dataType: 'json',
              success: function(response) {
                  if (response.success) {
                      alert(response.message);
                      $('#editCustomerModal').modal('hide');
                      setTimeout(() => location.reload(), 500);
                  } else {
                      alert(response.message || 'Güncelleme başarısız.');
                  }
              },
              error: function(xhr) {
                  console.error('AJAX Error:', xhr);
                  alert('Hata: ' + (xhr.responseJSON?.message || 'Bilinmeyen bir hata oluştu.'));
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
