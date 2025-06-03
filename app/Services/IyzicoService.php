<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Currency;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;

class IyzicoService
{
    protected $options;

     public function __construct()
    {
        try {
            if (!file_exists(base_path('vendor/autoload.php'))) {
                Log::critical('Vendor autoload file missing during initialization', ['path' => base_path('vendor/autoload.php')]);
                throw new \Exception('Composer autoload dosyası eksik. Lütfen bağımlılıkları yeniden yükleyin.');
            }
            require_once base_path('vendor/autoload.php');

            $this->options = new \Iyzipay\Options();
            $this->options->setApiKey(env('IYZICO_API_KEY'));
            $this->options->setSecretKey(env('IYZICO_SECRET_KEY'));
            $this->options->setBaseUrl(env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'));
            
            Log::info('IyzicoService initialized successfully', [
                'api_key' => substr(env('IYZICO_API_KEY'), 0, 10) . '...',
                'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
                'environment' => env('APP_ENV', 'local'),
            ]);
        } catch (\Exception $e) {
            Log::error('IyzicoService initialization failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Iyzico servis başlatma hatası: ' . $e->getMessage());
        }
    }

    public function initializeCheckoutForm($sale, $cart, $userData, $shippingAddress)
    {
        try {
            if (!is_array($cart) || !isset($cart['items']) || !is_array($cart['items']) || empty($cart['items'])) {
                throw new \Exception('Sepet verileri geçersiz veya boş.');
            }

            $request = new CreateCheckoutFormInitializeRequest();
            $request->setLocale(Locale::TR);
            $request->setConversationId('sale-' . $sale->id);
            $request->setPrice($sale->total_price);
            $request->setPaidPrice($sale->total_price);
            $request->setCurrency(Currency::TL);
            $request->setBasketId($sale->order_token);
            $request->setPaymentGroup(PaymentGroup::PRODUCT);
            $request->setCallbackUrl(env('IYZICO_CALLBACK_URL', url('/customer/checkout/iyzico-callback')));
			$request->setEnabledInstallments(array(2, 3, 6, 9));
			$request->setForceThreeDS(0);
            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId($userData['id']);
            $buyer->setName($userData['name']);
            $buyer->setSurname($userData['surname'] ?? '');
            $buyer->setGsmNumber($userData['phone']);
            $buyer->setEmail($userData['email']);
            $buyer->setIdentityNumber('11111111111'); 
            $buyer->setRegistrationAddress($shippingAddress);
            $buyer->setIp(request()->ip());
            $buyer->setCity('Istanbul');
            $buyer->setCountry('Turkey');
            $request->setBuyer($buyer);

            $shipping = new \Iyzipay\Model\Address();
            $shipping->setContactName($userData['name'] . ' ' . ($userData['surname'] ?? ''));
            $shipping->setCity('Istanbul');
            $shipping->setCountry('Turkey');
            $shipping->setAddress($shippingAddress);
            $request->setShippingAddress($shipping);

            $billing = new \Iyzipay\Model\Address();
            $billing->setContactName($userData['name'] . ' ' . ($userData['surname'] ?? ''));
            $billing->setCity('Istanbul');
            $billing->setCountry('Turkey');
            $billing->setAddress($shippingAddress);
            $request->setBillingAddress($billing);

            $basketItems = [];
            foreach ($cart['items'] as $index => $item) {
                if (!isset($item['product_id']) || !isset($item['name']) || !isset($item['category']) || !isset($item['price']) || !isset($item['quantity'])) {
                    throw new \Exception('Sepet öğesi eksik veya geçersiz: ' . json_encode($item));
                }
                $basketItem = new \Iyzipay\Model\BasketItem();
                $basketItem->setId($item['product_id']);
                $basketItem->setName($item['name']);
                $basketItem->setCategory1($item['category']);
                $basketItem->setItemType(BasketItemType::PHYSICAL);
                $basketItem->setPrice(number_format($item['price'] * $item['quantity'], 2, '.', ''));
                $basketItems[] = $basketItem;
            }
            $request->setBasketItems($basketItems);

            $result = CheckoutFormInitialize::create($request, $this->options);

            if ($result->getStatus() !== 'success') {
                Log::error('Iyzico checkout form initialization failed', [
                    'sale_id' => $sale->id,
                    'error' => $result->getErrorMessage() ?? 'Unknown error',
                ]);
                throw new \Exception($result->getErrorMessage() ?? 'Iyzico ödeme formu oluşturulamadı.');
            }

            Log::info('Iyzico checkout form initialized successfully', [
                'sale_id' => $sale->id,
                'token' => $result->getToken(),
                'conversation_id' => $request->getConversationId(),
            ]);

            return [
                'success' => true,
                'token' => $result->getToken(),
                'html_content' => $result->getCheckoutFormContent(),
            ];
        } catch (\Exception $e) {
            Log::error('Iyzico checkout form initialization exception', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cart' => $cart ?? 'N/A',
            ]);
            throw $e;
        }
    }

   public function verifyPayment($token, $conversationId)
{
    try {
        $request = new RetrieveCheckoutFormRequest();
        $request->setLocale(Locale::TR);
        $request->setToken($token);
        $request->setConversationId($conversationId);

        $result = CheckoutForm::retrieve($request, $this->options);

        if ($result->getStatus() !== 'success') {
            throw new \Exception($result->getErrorMessage() ?? 'Ödeme doğrulanamadı.');
        }

        // Correct way to get payment items
        $paymentItems = $result->getPaymentItems() ?: [];

        Log::info('Iyzico payment verification successful', [
            'token' => $token,
            'conversation_id' => $conversationId,
            'payment_id' => $result->getPaymentId(),
            'item_transactions_count' => count($paymentItems),
        ]);

        return [
            'success' => true,
            'payment_id' => $result->getPaymentId(),
            'item_transactions' => array_map(function ($item) {
                return [
                    'item_id' => $item->getItemId(),
                    'payment_transaction_id' => $item->getPaymentTransactionId(),
                    'price' => $item->getPrice(),
                ];
            }, $paymentItems),
        ];
    } catch (\Exception $e) {
        Log::error('Iyzico payment verification failed', [
            'token' => $token,
            'conversationId' => $conversationId,
            'error' => $e->getMessage(),
            'session_id' => session()->getId(),
        ]);
        return [
            'success' => false,
            'message' => $e->getMessage(),
        ];
    }
}
}