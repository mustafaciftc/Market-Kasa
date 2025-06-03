<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Başarılı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f5f5f5; padding: 20px; }
        .success-container { max-width: 800px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; padding: 10px 20px; font-weight: 500; }
        .btn-primary:hover { background-color: #0b5ed7; }
        .alert-info { background-color: #e7f5ff; border-color: #d0ebff; color: #1864ab; }
    </style>
</head>
<body>
    <div class="success-container">
        <h4 class="mb-4">Siparişiniz Başarıyla Oluşturuldu!</h4>
        <p><strong>Sipariş ID:</strong> {{ $sale->id }}</p>
        <p><strong>Toplam Tutar:</strong> {{ number_format($sale->total_price, 2, ',', '.') }} ₺</p>
        <p><strong>Ödeme Yöntemi:</strong> {{ $payTypeText }}</p>

        @if ($sale->pay_type == 4)
            <div class="alert alert-info">
                Banka havalesi ile ödeme yaptığınız için lütfen ödeme dekontunu kontrol ediniz. Ödeme onaylandıktan sonra siparişiniz kargoya verilecektir.
            </div>
        @elseif ($sale->pay_type == 5)
            <div class="alert alert-info">
                Kapıda ödeme seçeneğini tercih ettiniz. Ürün teslimatında ödeme yapabilirsiniz.
            </div>
        @endif

        <a href="{{ route('customer.orders') }}" class="btn btn-primary mt-3">Siparişlerime Git</a>
    </div>
</body>
</html>