<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diğer Satış İadeleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f0f0; font-family: Arial, sans-serif; }
        .container { max-width: 1200px; margin-top: 20px; }
        .card { margin-bottom: 20px; }
        .table { background-color: #fff; }
        .btn-approve { background-color: #28a745; color: #fff; }
        .btn-reject { background-color: #dc3545; color: #fff; }
        .alert { margin-top: 20px; }
        .modal-body img { max-width: 100px; }
        .status-pending { color: #ffc107; }
        .status-approved { color: #28a745; }
        .status-rejected { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Diğer Satış İadeleri</h2>
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Müşteri</th>
                            <th>Ürün</th>
                            <th>Miktar</th>
                            <th>İade Nedeni</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $return)
                            <tr>
                                <td>{{ $return->sale_id }}</td>
                                <td>{{ $return->customer ? $return->customer->name : 'Bilinmiyor' }}</td>
                                <td>{{ $return->product ? $return->product->name : 'Bilinmiyor' }}</td>
                                <td>{{ $return->quantity }}</td>
                                <td>{{ $return->reason }}</td>
                                <td class="status-{{ $return->status }}">
                                    {{ $return->status === 'pending' ? 'Beklemede' : ($return->status === 'approved' ? 'Onaylandı' : 'Reddedildi') }}
                                    @if ($return->admin_note)
                                        <br><small>(Not: {{ $return->admin_note }})</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($return->status === 'pending')
                                        <button class="btn btn-approve btn-sm" data-bs-toggle="modal" data-bs-target="#processModal" data-return-id="{{ $return->id }}" data-action="approved">Onayla</button>
                                        <button class="btn btn-reject btn-sm" data-bs-toggle="modal" data-bs-target="#processModal" data-return-id="{{ $return->id }}" data-action="rejected">Reddet</button>
                                    @else
                                        <span>{{ $return->status === 'approved' ? 'Onaylandı' : 'Reddedildi' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">İade talebi bulunamadı.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $returns->links() }}
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Geri Dön</a>
    </div>

    <!-- Modal for Processing Return -->
    <div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processModalLabel">İade İşlemi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>İade talebini <span id="actionText"></span> istediğinize emin misiniz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="confirmProcess">Onayla</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#processModal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const returnId = button.data('return-id');
                const action = button.data('action');
                const modal = $(this);
                modal.find('#actionText').text(action === 'approved' ? 'onaylamak' : 'reddetmek');
                modal.find('#confirmProcess').data('return-id', returnId).data('action', action);
            });

            $('#confirmProcess').on('click', function () {
                const returnId = $(this).data('return-id');
                const action = $(this).data('action');
                const adminNote = $('#adminNote').val();

                $.ajax({
                    url: `/customer/return/${returnId}/process`,
                    method: 'POST',
                    data: {
                        status: action,
                        admin_note: adminNote,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#processModal').modal('hide');
                            alert(response.message + (response.admin_note ? `\nNot: ${response.admin_note}` : ''));
                            location.reload();
                        } else {
                            alert('Hata: ' + response.message);
                        }
                    },
                    error: function (xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'İşlem başarısız, lütfen tekrar deneyin.';
                        alert('Hata: ' + errorMsg);
                        if (errorMsg.includes('Ödeme detayı bulunamadı')) {
                            alert('Not: Ödeme detayı eksik olduğu için iade işlemi yapılamadı. Lütfen ödeme kayıtlarını kontrol edin.');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>