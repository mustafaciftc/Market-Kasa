<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\IyzicoService;
use App\Models\Sale;
use App\Models\PaymentDetail;
use Illuminate\Support\Facades\Log;

class ProcessIyzicoPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sale;
    protected $cart;
    protected $userData;
    protected $shippingAddress;

    public function __construct(Sale $sale, array $cart, array $userData, string $shippingAddress)
    {
        $this->sale = $sale;
        $this->cart = $cart;
        $this->userData = $userData;
        $this->shippingAddress = $shippingAddress;
    }

    public function handle(IyzicoService $iyzicoService)
    {
        try {
            $startTime = microtime(true);
            Log::info('Starting ProcessIyzicoPaymentJob', [
                'sale_id' => $this->sale->id,
            ]);

            $paymentResult = $iyzicoService->initializeCheckoutForm(
                $this->sale,
                $this->cart,
                $this->userData,
                $this->shippingAddress
            );

            Log::info('Iyzico payment initialization completed in job', [
                'sale_id' => $this->sale->id,
                'duration' => microtime(true) - $startTime,
                'payment_result' => $paymentResult,
            ]);

            PaymentDetail::updateOrCreate(
                ['sale_id' => $this->sale->id],
                [
                    'details' => json_encode([
                        'status' => $paymentResult['success'] ? 'pending' : 'failed',
                        'method' => 'credit_card',
                        'form_content' => $paymentResult['success'] ? $paymentResult['form_content'] : null,
                        'token' => $paymentResult['success'] ? $paymentResult['token'] : null,
                        'error' => !$paymentResult['success'] ? $paymentResult['message'] : null,
                        'processed_at' => now(),
                    ]),
                ]
            );

            if (!$paymentResult['success']) {
                Log::error('Iyzico payment failed in job', [
                    'sale_id' => $this->sale->id,
                    'message' => $paymentResult['message'],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('ProcessIyzicoPaymentJob failed', [
                'sale_id' => $this->sale->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'duration' => microtime(true) - $startTime,
            ]);

            PaymentDetail::updateOrCreate(
                ['sale_id' => $this->sale->id],
                [
                    'details' => json_encode([
                        'status' => 'failed',
                        'method' => 'credit_card',
                        'error' => 'Ödeme işlemi sırasında hata: ' . $e->getMessage(),
                        'processed_at' => now(),
                    ]),
                ]
            );
        }
    }
}