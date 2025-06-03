<!DOCTYPE html>
<html lang="tr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veresiye Bilgi</title>
    <style>
        body { font-size: 24px; font-family: Arial, sans-serif; text-align: center; padding: 20px; }
        .balance { color: #2c3e50; margin: 20px 0; }
        .notification { background: #f1f1f1; padding: 10px; margin: 10px 0; border-radius: 5px; }
        button { font-size: 20px; padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; }
    </style>
</head>
<body>
	@error('customer')
    <div class="error">Müşteri bilgisi yüklenemedi: {{ $message }}</div>
	@enderror
    <h1>Hoş Geldiniz, {{ customer()->name ?? 'Misafir' }}</h1>
    <div class="balance">Bakiyeniz: <strong>{{ number_format($balance, 2) }} TL</strong></div>
    <h2>Bildirimler</h2>
    @forelse ($notifications as $notification)
        <div class="notification">{{ $notification->data['message'] ?? 'Bildirim' }}</div>
    @empty
        <div class="notification">Bildirim yok.</div>
    @endforelse
    <button onclick="refresh()">Yenile</button>

    <script>
        function refresh() {
            window.location.reload();
        }
    </script>
</body>
</html>