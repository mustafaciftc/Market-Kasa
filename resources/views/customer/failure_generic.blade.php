<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Başarısız</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f5f5f5; padding: 20px; }
        .failure-container { max-width: 800px; margin: 0 auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .alert-danger { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; padding: 10px 20px; font-weight: 500; }
    </style>
</head>
<body>
    <div class="failure-container">
        <h4 class="mb-4">Ödeme Başarısız</h4>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> Ödeme işlemi başarısız oldu. Lütfen tekrar deneyin veya farklı bir ödeme yöntemi kullanın.
        </div>

        <a href="{{ route('customer.checkout') }}" class="btn btn-primary mt-3">Tekrar Dene</a>
        <a href="{{ route('customer.orders') }}" class="btn btn-secondary mt-3">Siparişlerime Dön</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>