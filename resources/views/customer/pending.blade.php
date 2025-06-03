<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Beklemede - Banka Havalesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f5f5f5; padding: 20px; }
        .pending-container { max-width: 800px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .bank-details { margin-top: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #0d6efd; }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; padding: 10px 20px; font-weight: 500; }
        .alert-info { background-color: #e7f5ff; border-color: #d0ebff; color: #1864ab; }
    </style>
</head>
<body>
    <div class="pending-container">
        <h4 class="mb-4">Siparişiniz Beklemede</h4>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Siparişiniz başarıyla oluşturuldu (Sipariş ID: {{ $sale->id }}). Ödemeyi tamamlamak için lütfen aşağıdaki banka hesabına havale yapın. Ödeme onaylandıktan sonra siparişiniz işleme alınacaktır.
        </div>

        <div class="bank-details">
            <h6>Banka Hesap Bilgileri</h6>
            <p>
                <strong>Banka:</strong> Örnek Banka<br>
                <strong>Hesap Sahibi:</strong> Şirket Adı<br>
                <strong>IBAN:</strong> TR12 3456 7890 1234 5678 9012 34<br>
                <strong>Not:</strong> Ödeme yaparken sipariş ID'sini (siparis-{{ $sale->id }}) açıklama kısmına ekleyin.
            </p>
        </div>

        <div class="mt-4">
            <h5>Sipariş Özeti</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Miktar</th>
                        <th>Fiyat</th>
                        <th>Toplam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (json_decode($sale->basket, true) as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($item['price'], 2, ',', '.') }} ₺</td>
                            <td>{{ number_format($item['subtotal'], 2, ',', '.') }} ₺</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Genel Toplam:</strong></td>
                        <td>{{ number_format($sale->total_price, 2, ',', '.') }} ₺</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <a href="{{ route('customer.orders') }}" class="btn btn-primary mt-3">Siparişlerime Dön</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>