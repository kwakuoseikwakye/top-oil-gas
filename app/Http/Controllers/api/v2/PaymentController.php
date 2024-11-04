<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    const PAYMENT_SERVICES = 'payswitch';
    protected $paymentService;
    protected $request;

    public function __construct(PaymentService $paymentService, Request $request)
    {
        $this->paymentService = $paymentService;
        $this->request = $request;
    }
    public function initiatePayment()
    {
        $user = $this->request->user();

        switch (self::PAYMENT_SERVICES) {
            case 'payswitch':
                $payment = $this->paymentService->payswitchCheckoutSession($this->request->all(), $user);
                break;

            default:
                # code...
                break;
        }

        return $payment;
    }

    public function verifyPayment($transactionId)
    {
        switch (self::PAYMENT_SERVICES) {
            case 'payswitch':
                $payment = $this->paymentService->payswitchVerifyPayment($this->request->all(), $transactionId);
                break;

            default:
                # code...
                break;
        }

        return $payment;
    }

}
