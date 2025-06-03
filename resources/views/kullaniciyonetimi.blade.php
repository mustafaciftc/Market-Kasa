<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <title>Kullanıcı Yönetimi</title>
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
        .user-form, .bank-form {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .user-form h5, .bank-form h5 {
            margin-bottom: 15px;
            font-weight: 600;
        }
        .user-form .form-control, .bank-form .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }
        .user-form button, .bank-form button {
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .user-list, .bank-list {
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
        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .role-admin { background-color: #dc3545; color: white; }
        .role-customer { background-color: #17a2b8; color: white; }
        .role-personnel { background-color: #28a745; color: white; }

        @media (max-width: 768px) {
            .table th, .table td { padding: 8px; font-size: 0.85rem; }
            .btn-sm { padding: 5px 10px; font-size: 0.75rem; }
            .user-form .form-control, .bank-form .form-control { font-size: 0.85rem; }
        }
        @media (max-width: 768px) {
            .user-list, .bank-list { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .table { min-width: 900px; }
            .table th, .table td { font-size: 0.85rem; padding: 8px; }
            .table thead th { position: sticky; top: 0; z-index: 1; }
            .user-list::-webkit-scrollbar, .bank-list::-webkit-scrollbar { height: 8px; }
            .table td.text-center { display: flex; justify-content: center; gap: 5px; flex-wrap: nowrap; }
            .btn-sm { padding: 5px 8px; font-size: 0.75rem; }
        }
    </style>
</head>
<body>
    @if(auth()->check() && auth()->user()->role === \App\Models\User::ROLE_ADMIN)

    <div class="header">
        <h4>Kullanıcı Yönetimi</h4>
        <div>
            <a href="{{ route('dashboard') }}" style="text-decoration: none;">Ana Ekran</a>
        </div>
    </div>

    <div class="container-fluid">
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

        <div class="row">
            <div class="col-lg-12">
                <!-- User Add Form -->
                <div class="user-form">
                    <h5>Yeni Kullanıcı Ekle</h5>
                    <form id="addUserForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad & Soyad</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Ad & Soyad" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Kullanıcı Adı" required>
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
                                <label for="active" class="form-label">Durum</label>
                                <select name="active" id="active" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rol</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="personel">Personel</option>
                                    <option value="admin">Admin</option>
                                    <option value="customer">Müşteri</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Kullanıcı Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- User List -->
                <div class="user-list">
                    <table id="userTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ad & Soyad</th>
                                <th>Kullanıcı Adı</th>
                                <th>E-posta</th>
                                <th>Rol</th>
                                <th>Durum</th>
                                <th>Oluşturma Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="role-badge 
                                        {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'role-admin' : 
                                        ($user->role === \App\Models\User::ROLE_CUSTOMER ? 'role-customer' : 'role-personnel') }}">
                                        {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'Admin' : 
                                        ($user->role === \App\Models\User::ROLE_CUSTOMER ? 'Müşteri' : 'Personel') }}
                                    </span>
                                </td>
                                <td>{{ $user->active ? 'Aktif' : 'Pasif' }}</td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary view-details" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#modal-basic-{{ $user->id }}" title="Detayları Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $user->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('kullanici.delete', $user->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- User Details Modals -->
                    @foreach($users as $user)
                    <div class="modal fade" id="modal-basic-{{ $user->id }}" tabindex="-1" aria-labelledby="modal-basic-label-{{ $user->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-basic-label-{{ $user->id }}">Kullanıcı Bilgileri</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><strong>Ad & Soyad:</strong> <span class="text-muted">{{ $user->name }}</span></p>
                                            <p><strong>Kullanıcı Adı:</strong> <span class="text-muted">{{ $user->username }}</span></p>
                                            <p><strong>E-posta:</strong> <span class="text-muted">{{ $user->email }}</span></p>
                                            <p><strong>Telefon:</strong> <span class="text-muted">{{ $user->phone ?? 'Telefon Yok' }}</span></p>
                                            <p><strong>Rol:</strong>
                                                <span class="role-badge 
                                                    {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'role-admin' : 
                                                    ($user->role === \App\Models\User::ROLE_CUSTOMER ? 'role-customer' : 'role-personnel') }}">
                                                    {{ $user->role === \App\Models\User::ROLE_ADMIN ? 'Admin' : 
                                                    ($user->role === \App\Models\User::ROLE_CUSTOMER ? 'Müşteri' : 'Personel') }}
                                                </span>
                                            </p>
                                            <p><strong>Durum:</strong> <span class="text-muted">{{ $user->active ? 'Aktif' : 'Pasif' }}</span></p>
                                            <p><strong>Oluşturma Tarihi:</strong> <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span></p>
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
                </div>

                <!-- Bank Account Add Form -->
                <div class="bank-form">
                    <h5>Yeni Banka Hesabı Ekle</h5>
                    <form id="addBankAccountForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label">Banka Adı</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Banka Adı" required>
                            </div>
                            <div class="col-md-6">
                                <label for="account_holder" class="form-label">Hesap Sahibi</label>
                                <input type="text" name="account_holder" id="account_holder" class="form-control" placeholder="Hesap Sahibi" required>
                            </div>
                            <div class="col-md-6">
                                <label for="iban" class="form-label">IBAN</label>
                                <input type="text" name="iban" id="iban" class="form-control" placeholder="TRXX XXXX XXXX XXXX XXXX XX" required>
                            </div>
                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Durum</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Pasif</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Banka Hesabı Ekle</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bank Account List -->
                <div class="bank-list">
                    <table id="bankAccountTable" class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Banka Adı</th>
                                <th>Hesap Sahibi</th>
                                <th>IBAN</th>
                                <th>Durum</th>
                                <th>Oluşturma Tarihi</th>
                                <th class="text-center">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bankAccounts as $account)
                            <tr>
                                <td>{{ $account->id }}</td>
                                <td>{{ $account->bank_name }}</td>
                                <td>{{ $account->account_holder }}</td>
                                <td>{{ $account->iban }}</td>
                                <td>{{ $account->is_active ? 'Aktif' : 'Pasif' }}</td>
                                <td>{{ $account->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning edit-bank-btn" data-id="{{ $account->id }}" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('bankaccount.delete', $account->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Bu banka hesabını silmek istediğinize emin misiniz?')">
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
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Kullanıcı Düzenle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editUserForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_name" class="form-label">Ad & Soyad</label>
                                    <input type="text" name="name" id="edit_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_username" class="form-label">Kullanıcı Adı</label>
                                    <input type="text" name="username" id="edit_username" class="form-control" required>
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
                                    <label for="edit_active" class="form-label">Durum</label>
                                    <select name="active" id="edit_active" class="form-control">
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_role" class="form-label">Rol</label>
                                    <select name="role" id="edit_role" class="form-control">
                                        <option value="personel">Personel</option>
                                        <option value="admin">Admin</option>
                                        <option value="customer">Müşteri</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Bank Account Modal -->
        <div class="modal fade" id="editBankModal" tabindex="-1" aria-labelledby="editBankModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBankModalLabel">Banka Hesabı Düzenle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editBankForm">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="edit_bank_name" class="form-label">Banka Adı</label>
                                    <input type="text" name="bank_name" id="edit_bank_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_account_holder" class="form-label">Hesap Sahibi</label>
                                    <input type="text" name="account_holder" id="edit_account_holder" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_iban" class="form-label">IBAN</label>
                                    <input type="text" name="iban" id="edit_iban" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="edit_is_active" class="form-label">Durum</label>
                                    <select name="is_active" id="edit_is_active" class="form-control">
                                        <option value="1">Aktif</option>
                                        <option value="0">Pasif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                        </div>
                    </form>
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
        if (!$.fn.DataTable.isDataTable('#userTable')) {
            $('#userTable').DataTable({
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
        if (!$.fn.DataTable.isDataTable('#bankAccountTable')) {
            $('#bankAccountTable').DataTable({
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

        // Kullanıcı Ekleme Formu
        $('#addUserForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            const formData = $(this).serialize();
            $.ajax({
                url: '{{ route("kullaniciekle.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#addUserForm')[0].reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showError(response.message || 'Kullanıcı eklenirken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'İşlem sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Düzenle butonlarına click eventi
        $(document).on('click', '.edit-btn', function() {
            const userId = $(this).data('id');
            showLoading();
            $.ajax({
                url: `/dashboard/kullanici/edit/${userId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#edit_name').val(data.name);
                        $('#edit_username').val(data.username);
                        $('#edit_email').val(data.email);
                        $('#edit_phone').val(data.phone);
                        $('#edit_active').val(data.active);
                        const validRoles = ['admin', 'personel', 'customer'];
                        if (validRoles.includes(data.role)) {
                            $('#edit_role').val(data.role);
                        } else {
                            console.warn('Invalid role received:', data.role);
                            $('#edit_role').val('personel');
                        }
                        $('#editUserForm').data('userId', userId);
                        $('#editUserModal').modal('show');
                    } else {
                        showError(response.message || 'Kullanıcı bilgileri alınırken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Kullanıcı bilgileri alınırken bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Güncelleme Formu
        $('#editUserForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            const userId = $(this).data('userId');
            const formData = $(this).serialize();
            $.ajax({
                url: `/dashboard/kullanici/update/${userId}`,
                type: 'POST', // PUT yerine POST kullanıyoruz, çünkü @method('PUT') ile simüle ediliyor
                data: formData + '&_method=PUT', // PUT metodunu simüle etmek için
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#editUserModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showError(response.message || 'Güncelleme sırasında bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Banka Hesabı Ekleme Formu
        $('#addBankAccountForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            const formData = $(this).serialize();
            $.ajax({
                url: '{{ route("bankaccount.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#addBankAccountForm')[0].reset();
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showError(response.message || 'Banka hesabı eklenirken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'İşlem sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Banka Hesabı Düzenleme
        $(document).on('click', '.edit-bank-btn', function() {
            const accountId = $(this).data('id');
            showLoading();
            $.ajax({
                url: `/dashboard/bankaccount/edit/${accountId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#edit_bank_name').val(data.bank_name);
                        $('#edit_account_holder').val(data.account_holder);
                        $('#edit_iban').val(data.iban);
                        $('#edit_is_active').val(data.is_active);
                        $('#editBankForm').data('accountId', accountId);
                        $('#editBankModal').modal('show');
                    } else {
                        showError(response.message || 'Banka hesabı bilgileri alınırken bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Banka hesabı bilgileri alınırken bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Banka Hesabı Güncelleme Formu
        $('#editBankForm').submit(function(e) {
            e.preventDefault();
            showLoading();
            const accountId = $(this).data('accountId');
            const formData = $(this).serialize();
            $.ajax({
                url: `/dashboard/bankaccount/update/${accountId}`,
                type: 'POST', // PUT yerine POST kullanıyoruz, çünkü @method('PUT') ile simüle ediliyor
                data: formData + '&_method=PUT', // PUT metodunu simüle etmek için
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        $('#editBankModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showError(response.message || 'Güncelleme sırasında bir hata oluştu.');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Güncelleme sırasında bir hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    }
                    showError(errorMessage);
                },
                complete: function() {
                    hideLoading();
                }
            });
        });

        // Toast Notification Functions
        function showSuccess(message) {
            const toast = $(`<div class="toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3"></div>`)
                .css('z-index', '1050')
                .html(`
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `);
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            setTimeout(() => { bsToast.hide(); toast.remove(); }, 3000);
        }

        function showError(message) {
            const toast = $(`<div class="toast align-items-center text-white bg-danger border-0 position-fixed top-0 end-0 m-3"></div>`)
                .css('z-index', '1050')
                .html(`
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `);
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            setTimeout(() => { bsToast.hide(); toast.remove(); }, 5000);
        }

        // Loading Indicator Functions
        function showLoading() {
            if (!$('#loadingOverlay').length) {
                $('body').append('<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1050;"><div class="spinner-border text-light" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></div></div>');
            }
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