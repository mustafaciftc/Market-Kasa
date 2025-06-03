<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Uzak Satış İade Talepleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .remote-returns-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="remote-returns-container">
        <h4 class="mb-4">Uzak Satış İade Talepleri</h4>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sipariş ID</th>
                    <th>Müşteri</th>
                    <th>Ürün</th>
                    <th>Miktar</th>
                    <th>İade Nedeni</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returns as $return)
                    <tr>
                        <td>{{ $return->sale_id }}</td>
                        <td>{{ optional($return->customer)->name ?? 'Bilinmeyen Müşteri' }}</td>
                        <td>{{ optional($return->product)->name ?? 'Bilinmeyen Ürün' }}</td>
                        <td>{{ $return->quantity }}</td>
                        <td>{{ $return->reason }}</td>
                        <td>{{ number_format($return->return_amount, 2, ',', '.') }} ₺</td>
                        <td>
                            @if ($return->status == 'pending')
                                <span class="badge bg-warning">Beklemede</span>
                            @elseif ($return->status == 'approved')
                                <span class="badge bg-success">Onaylandı</span>
                            @else
                                <span class="badge bg-danger">Reddedildi</span>
                            @endif
                        </td>
                        <td>{{ $return->date->format('d.m.Y H:i') }}</td>
                        <td>
                            @if ($return->status == 'pending')
                                <button class="btn btn-success btn-sm process-return" data-id="{{ $return->id }}" data-status="approved">Onayla</button>
                                <button class="btn btn-danger btn-sm process-return" data-id="{{ $return->id }}" data-status="rejected">Reddet</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $returns->links() }}
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.process-return').on('click', function () {
                const returnId = $(this).data('id');
                const status = $(this).data('status');

                $.ajax({
                    url: `{{ url('customer/return') }}/${returnId}/process`,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function (data) {
                        if (data.success) {
                            toastr.success(data.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON?.message || 'İade işlemi başarısız.');
                    }
                });
            });
        });
    </script>
</body>
</html>