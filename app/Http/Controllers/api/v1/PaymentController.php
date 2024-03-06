<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCylinder;
use App\Models\CylinderSize;
use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "order_id" => "required",
            ], [
                "order_id.required" => "No order id supplied",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "Initiating payment failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $token = CustomerController::extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            $paymentDetails = CustomerCylinder::select("tblcylinder.weight_id")
                ->join("tblcylinder", "tblcylinder.cylcode", "tblcustomer_cylinder.cylcode")
                ->where('tblcustomer_cylinder.order_id', $request->order_id)->get()->toArray();
            $weightAmt = [];
            // return $paymentDetails;
            if (count($paymentDetails) === 0) {
                return response()->json([
                    "status" => false,
                    "message" => "No order available",
                ]);
            }
            foreach ($paymentDetails as $payment) {
                $cylinderSize = CylinderSize::where('id', $payment['weight_id'])->first();
                if ($cylinderSize) {
                    $quantity = CylinderSize::where('id', $payment['weight_id'])->count();
                    // CustomerCylinder::where('weight_id', $cylinderSize->id)->where('order_id', $request->order_id)->count();
                    // return $quantity;
                    $amountForPayment = (int)$quantity * (int)$cylinderSize->amount;
                    $weightAmt[] = $amountForPayment;
                } else {
                    return response()->json([
                        "status" => false,
                        "message" => "No amount available for this package",
                    ]);
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

            $transactionId = mt_rand(100000000000, 999999999999);
            $username = env("API_USER");
            $key = env("API_KEY");
            $url = env("APP_URL");

            $credentials = base64_encode($username . ':' . $key);
            $payload = json_encode([
                "merchant_id" => "TTM-00008908",
                "transaction_id" => $transactionId,
                "desc" => "Payment Using Checkout Page",
                "amount" => $amount,
                "redirect_url" => $url,
                "email" => $user->phone . '@topoil.com',
            ]);

            $curl = curl_init("https://checkout.theteller.net/initiate");
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
                return response()->json([
                    "status" => false,
                    "message" => $err,
                ]);
            }

            Payment::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "transaction_id" => $transactionId,
                "amount_paid" => $amt,
                "order_id" => $request->order_id,
                "status" => Payment::PENDING,
                "payment_mode" => "online",
            ]);

            return response()->json([
                "status" => true,
                "message" => "Request successful",
                "data" => json_decode($response, true)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "errLine" => $e->getLine(),
            ]);
        }
    }

    public function verifyPayment(Request $request, $transactionId)
    {
        try {
            $token = CustomerController::extractToken($request);

            if (!empty($token)) {
                $user = User::where('remember_token', $token)->first();
                if (empty($user)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Unauthorized - Token not provided or invalid'
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized - Token not provided or invalid'
                ], 401);
            }

            DB::beginTransaction();
            $payment  = Payment::find($transactionId);
            $payment->update(['status' => Payment::SUCCESS]);
            if (!$payment) {
                CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                // Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);
                return response()->json(['status' => false, 'message' => 'Invalid transaction id'], 200);
            }
            CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::SUCCESS]);
            Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Payment successful",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "errLine" => $e->getLine(),
            ]);
        }
    }
}
