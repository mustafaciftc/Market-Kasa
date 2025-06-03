<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>İade Taleplerim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .returns-container {
            max-width: 1000px;
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
    <div class="returns-container">
        <h4 class="mb-4">İade Taleplerim</h4>
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
                    <th>Ürün</th>
                    <th>Miktar</th>
                    <th>İade Nedeni</th>
                    <th>Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($returns as $return)
                    <tr>
                        <td>{{ $return->sale_id }}</td>
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
                        <td>{{ $return->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('customer.orders') }}" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left me-1"></i> Geri</a>
        {{ $returns->links() }}
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>