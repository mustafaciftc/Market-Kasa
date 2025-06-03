<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ödeme İyzico Sayfası</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .payment-container { max-width: 900px; margin: 30px auto; background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .payment-header { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; padding: 30px; text-align: center; }
        .payment-body { padding: 40px; }
        .loading-spinner { display: none; text-align: center; padding: 40px; }
        .payment-info { background: #f8fafc; border-radius: 10px; padding: 20px; margin-bottom: 30px; border-left: 4px solid #4f46e5; }
        
        /* Campaign banner'ı tamamen gizle */
        .campaign-banner,
        .iyzico-campaign-banner,
        [class*='campaign'],
        [id*='campaign'],
        [class*='Campaign'],
        [id*='Campaign'],
        .promotional-banner,
        .promo-area,
        [class*='banner'],
        [class*='Banner'] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            max-height: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
        }

        /* Iyzico iframe içindeki campaign alanlarını gizle */
        iframe[src*='campaign'],
        iframe[src*='Campaign'],
        div[data-campaign],
        div[data-banner] {
            display: none !important;
        }

        /* Iyzico formu içindeki tüm banner alanlarını hedefle */
        #iyzico-checkout-form .campaign-banner,
        #iyzico-checkout-form [class*='campaign'],
        #iyzico-checkout-form [class*='Campaign'],
        #iyzico-checkout-form [class*='banner'],
        #iyzico-checkout-form [class*='Banner'],
        #iyzico-checkout-form [id*='campaign'],
        #iyzico-checkout-form [id*='Campaign'] {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>🔒 Güvenli Ödeme</h2>
            <p>Kredi kartı bilgileriniz SSL ile şifrelenir</p>
        </div>
        <div class="payment-body">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="payment-info">
                <h5>📋 Ödeme Bilgileri</h5>
                <div class="row">
                    <div class="col-md-6"><p><strong>Tutar:</strong> ₺{{ number_format($amount, 2) }}</p></div>
                    <div class="col-md-6"><p><strong>İşlem No:</strong> #{{ transaction_id }}</p></div>
                </div>
            </div>

            <div id="iyzico-checkout-form">{!! $form_content !!}</div>

            <div class="loading-spinner" id="loading-spinner">
                <div class="spinner-border text-primary"></div>
                <h4 class="mt-3">Ödeme İşleniyor</h4>
                <p>Lütfen sayfayı kapatmayın...</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Campaign banner'ları gizleme fonksiyonu
            function hideCampaignBanners() {
                // Tüm campaign banner elementlerini bul ve gizle
                $('[class*="campaign"], [class*="Campaign"], [class*="banner"], [class*="Banner"], [id*="campaign"], [id*="Campaign"]').each(function() {
                    $(this).hide().css({
                        'display': 'none',
                        'visibility': 'hidden',
                        'height': '0',
                        'overflow': 'hidden',
                        'opacity': '0'
                    });
                });
                
                // Iframe içindeki campaign alanlarını da hedefle
                $('iframe').each(function() {
                    try {
                        var iframeDoc = this.contentDocument || this.contentWindow.document;
                        if (iframeDoc) {
                            $(iframeDoc).find('[class*="campaign"], [class*="Campaign"], [class*="banner"], [class*="Banner"]').hide();
                        }
                    } catch(e) {
                        // Cross-origin iframe erişim hatası - normal
                    }
                });
            }

            // Sayfa yüklendiğinde ve form değiştiğinde banner'ları gizle
            hideCampaignBanners();
            
            // DOM değişikliklerini izle
            var observer = new MutationObserver(function(mutations) {
                hideCampaignBanners();
            });
            
            observer.observe(document.getElementById('iyzico-checkout-form'), {
                childList: true,
                subtree: true
            });

            const iyzicoForm = $('#iyzico-checkout-form form');
            if (iyzicoForm.length) {
                iyzicoForm.on('submit', function(e) {
                    e.preventDefault();
                    $('#loading-spinner').show();
                    $('#iyzico-checkout-form').hide();

                    $.ajax({
                        url: iyzicoForm.attr('action'),
                        method: 'POST',
                        data: iyzicoForm.serialize(),
                        success: function(response) {
                            if (response.success && response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                $('#loading-spinner').hide();
                                $('#iyzico-checkout-form').show();
                                hideCampaignBanners(); // Banner'ları tekrar gizle
                                alert('Ödeme işlemi başarısız: ' + (response.message || 'Bilinmeyen hata'));
                            }
                        },
                        error: function(xhr) {
                            $('#loading-spinner').hide();
                            $('#iyzico-checkout-form').show();
                            hideCampaignBanners(); // Banner'ları tekrar gizle
                            alert('Ödeme işlemi sırasında bir hata oluştu: ' + (xhr.responseJSON?.message || 'Bilinmeyen hata'));
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>