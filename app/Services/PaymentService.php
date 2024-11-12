<?php

namespace App\Services;

use App\Models\CylinderWeights;
use App\Models\Dispatch;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentService
{
      public function payswitchCheckoutSession(array $request, $user)
      {
            try {
                  $validator = Validator::make($request, [
                        "order_number" => "required|exists:orders,order_number",
                  ], [
                        "order_number.required" => "No order number supplied",
                  ]);

                  if ($validator->fails()) {
                        return apiErrorResponse("Initiating payment failed. " . join(". ", $validator->errors()->all()), 422);
                  }

                  $orderNumber = $request['order_number'];

                  $orders = Orders::where('order_number', $orderNumber)->get()->toArray();

                  $weightAmt = [];

                  foreach ($orders as $payment) {

                        $cylinderSize = CylinderWeights::where('id', $payment['weight_id'])->first();

                        if ($cylinderSize) {
                              $quantity = Orders::where('order_number', $orderNumber)->sum('quantity');
                              $amountForPayment = (int)$quantity * (int)$cylinderSize->amount;
                              $weightAmt[] = $amountForPayment;
                        } else {
                              return apiErrorResponse("No amount available for this package");
                        }
                  }

                  // return $weightAmt;
                  $amt =  array_sum($weightAmt);

                  $amount = match (true) {
                        is_numeric($amt) && ($number = (int)($amt * 100)) >= 0 && $number <= 999999999999 =>
                        str_pad($number, 12, '0', STR_PAD_LEFT),
                        is_string($amt) && strlen($amt) === 12 && ctype_digit($amt) =>
                        $amt,
                        default => '',
                  };

                  $transactionId = random_int(100000000000, 999999999999);
                  $username = env("API_USER");
                  $key = env("API_KEY");
                  $url = env("APP_URL");

                  Payment::create([
                        "transaction_id" => $transactionId,
                        "amount_paid" => $amt,
                        "order_number" => $orderNumber,
                        "customer_id" => $user->customer_id,
                        "status" => Payment::PENDING,
                        "payment_mode" => "online",
                  ]);

                  $credentials = base64_encode($username . ':' . $key);
                  $payload = json_encode([
                        "merchant_id" => "TTM-00008908",
                        "transaction_id" => $transactionId,
                        "desc" => "Payment Using Checkout Page",
                        "amount" => $amount,
                        "redirect_url" => $url,
                        "email" => $user->phone . '@topoil.com',
                  ]);

                  $curl = curl_init("https://checkout-test.theteller.net/initiate");
                  curl_setopt_array($curl, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_HTTPHEADER => [
                              "Authorization: Basic " . $credentials,
                              "Cache-Control: no-cache",
                              "Content-Type: application/json",
                        ],
                        CURLOPT_POSTFIELDS => $payload,
                  ]);

                  $response = curl_exec($curl);
                  $err = curl_error($curl);

                  curl_close($curl);

                  if ($err) {
                        return apiErrorResponse($err);
                  }

                  $url = json_decode($response, true);

                  return apiSuccessResponse('Payment initiated', 200, $url);
            } catch (\Exception $e) {
                  return apiErrorResponse('Internal error occured', 500, $e);
            }
      }

      public function payswitchVerifyPayment(array $request, $transactionId)
      {
            try {

                  
                  $payment  = Payment::where('transaction_id', $transactionId)->first();
                  
                  
                  if (empty($payment)) {
                        Orders::where('order_number', $payment->order_number)->update(['status' => Orders::CANCELLED]);
                        
                        return apiErrorResponse('Invalid transaction id');
                  }
                  
                  DB::beginTransaction();
                  
                  Payment::where('transaction_id', $transactionId)->update(['status' => Payment::SUCCESS]);
                  Orders::where('order_number', $payment->order_number)->update(['status' => Orders::SUCCESS]);
                  Dispatch::where('order_number', $payment->order_number)->update(['status' => Dispatch::PENDING_ASSIGNMENT]);

                  DB::commit();

                  return apiSuccessResponse("Payment verified successfully");
            } catch (\Exception $e) {
                  DB::rollBack();
                  return apiErrorResponse('Internal error occured', 500, $e);
            }
      }
}
