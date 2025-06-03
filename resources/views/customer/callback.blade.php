<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.-moon phases
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ödeme İşlemi</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '{{ url('/customer/callback') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    token: new URLSearchParams(window.location.search).get('token'),
                    conversationId: new URLSearchParams(window.location.search).get('conversationId')
                },
                success: function(response) {
                    if (response.success && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        toastr.error(response.message || 'Ödeme işlemi başarısız.');
                        window.location.href = '{{ route('customer.failure') }}';
                    }
                },
                error: function() {
                    toastr.error('Ödeme doğrulama hatası.');
                    window.location.href = '{{ route('customer.failure') }}';
                }
            });
        });
    </script>
</head>
<body>
    <div style="text-align: center; padding: 50px;">
        <h4>Ödeme işlemi tamamlanıyor...</h4>
        <p>Lütfen bekleyin, yönlendiriliyorsunuz.</p>
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>
</body>
</html>