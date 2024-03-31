<?php

namespace App\Http\Controllers;

use App\Arkesel\Arkesel;
use App\Http\Controllers\api\v1\CustomerController;
use App\Http\Resources\PaymentResource;
use App\Models\CustomerCylinder;
use App\Models\CylinderSize;
use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payment = Payment::select(
            'tblpayment.order_id',
            'tblpayment.payment_mode',
            'tblpayment.transaction_id',
            'tblpayment.amount_paid',
            'tblpayment.status',
            'tblcustomer.fname',
            'tblcustomer.lname'
        )
            ->join('tblcustomer_cylinder', 'tblpayment.order_id', 'tblcustomer_cylinder.order_id')
            ->join('tblcustomer', 'tblcustomer.custno', 'tblpayment.custno')
            ->orderByDesc('tblpayment.createdate')
            ->get();

        return response()->json([
            "data" => PaymentResource::collection($payment)
        ]);
    }

    public static function generatePaymentLink($orderid)
    {
        try {
            $paymentDetails = CustomerCylinder::select("weight_id")->where('order_id', $orderid)->get()->toArray();
            $weightAmt = [];
            // return $paymentDetails;
            if (count($paymentDetails) === 0) {
                return response()->json([
                    "ok" => false,
                    "msg" => "No order available for payment",
                ]);
            }
            foreach ($paymentDetails as $payment) {
                $cylinderSize = CylinderSize::where('id', $payment['weight_id'])->first();
                if ($cylinderSize) {
                    $quantity = CylinderSize::where('id', $payment['weight_id'])->count();
                    // CustomerCylinder::where('weight_id', $cylinderSize->id)->where('order_id', $orderid)->count();
                    // return $quantity;
                    $amountForPayment = (int)$quantity * (int)$cylinderSize->amount;
                    $weightAmt[] = $amountForPayment;
                } else {
                    return response()->json([
                        "ok" => false,
                        "msg" => "No amount available for this package",
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

            $transactionId = '';
            for ($i = 0; $i < 12; $i++) {
                $transactionId .= random_int(0, 9);
            }
            $username = env("API_USER");
            $key = env("API_KEY");
            $url = env("APP_URL") . "/verify-payment";

            $customer = CustomerCylinder::where('order_id', $orderid)->first();
            $credentials = base64_encode($username . ':' . $key);
            $payload = json_encode([
                "merchant_id" => "TTM-00008908",
                "transaction_id" => $transactionId,
                "desc" => "Payment Using Checkout Page",
                "amount" => $amount,
                "redirect_url" => $url,
                "email" => $customer->custno . '@topoil.com',
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
                return response()->json([
                    "status" => false,
                    "message" => $err,
                ]);
            }

            Payment::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "transaction_id" => $transactionId,
                "amount_paid" => $amt,
                "order_id" => $orderid,
                "custno" => $customer->custno,
                "status" => Payment::PENDING,
                "payment_mode" => "online",
            ]);

            return json_decode($response, true);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "errLine" => $e->getLine(),
            ]);
        }
    }

    public function verifyPayments(Request $request)
    {
        try {
            $transactionId = $request->query('transaction_id');
            if (empty($transactionId)) {
                return redirect(route("/cancel-payment"));
            }
            DB::beginTransaction();
            $payment  = Payment::where('transaction_id', $transactionId)->first();
            Payment::where('transaction_id', $transactionId)->update(['status' => Payment::SUCCESS]);
            if (!$payment) {
                // CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                return response()->json(['ok' => false, 'msg' => 'Invalid transaction id'], 200);
            }
            CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::PENDING_ASSIGNMENT]);
            Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);

            DB::commit();
            return response()->json([
                "ok" => true,
                "msg" => "Payment successful",
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

    public function verifyPayment(Request $request, $transactionId)
    {
        try {
            $transactionId = $request->query('transaction_id');
            $statusCode = $request->query('code');
            if (empty($transactionId)) {
                return redirect(route("/cancel-payment"));
            }


            DB::beginTransaction();
            $payment  = Payment::where('transaction_id', $transactionId)->first();

            if (empty($payment)) {
                return response()->json(['status' => false, 'message' => 'Transaction not found'], 404);
            }

            if ($statusCode == "000") {
                Payment::where('transaction_id', $transactionId)->update(['status' => Payment::SUCCESS]);
                if (empty($payment)) {
                    // CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                    Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::CANCELLED, 'modifydate' => date('Y-m-d H:i:s')]);
                    return response()->json(['status' => false, 'message' => 'Payment failed'], 402);
                }
                CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::PENDING_ASSIGNMENT]);
                Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);
            } else {
                // CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::CANCELLED, 'modifydate' => date('Y-m-d H:i:s')]);
                return response()->json(['status' => false, 'message' => 'Payment failed'], 402);
            }


            DB::commit();

            return redirect(route("/success-payment"));
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

    public function sendPaymentLink($customer, $orderid)
    {
        $checkPayment = Payment::where('order_id', $orderid)->where('status', Payment::SUCCESS)->first();
        if (!empty($checkPayment)) {
            return response()->json(["ok" => false, "msg" => "Customer already made payment"]);
        }

        $user = User::where('userid', $customer)->first();
        $payLink = PaymentController::generatePaymentLink($orderid);
        if ($payLink['code'] === 200) {
            $msg = <<<MSG
            Dear customer,
            Your order for a cylinder with order ID {$orderid} is successful.
            Kindly click on the payment link below to complete your order. 
            {$payLink['checkout_url']}
            MSG;

            $sms = new Arkesel('TOP-OIL', env('ARKESEL_SMS_API_KEY'));
            $sms->send($user->phone, $msg);
        }

        return response()->json([
            "ok" => true,
        ]);
    }

    public function collectCashPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "order_id" => "required",
                "customer" => "required",
                "amount" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "ok" => false,
                    "msg" => "Collecting payment failed. " . join(". ", $validator->errors()->all()),
                ], 422);
            }

            $checkPayment = Payment::where('order_id', $request->order_id)->where('status', Payment::SUCCESS)->first();
            if (!empty($checkPayment)) {
                return response()->json(["ok" => false, "msg" => "Customer already made payment"]);
            }

            $paymentDetails = CustomerCylinder::select("weight_id")->where('order_id', $request->order_id)->get()->toArray();
            $weightAmt = [];
            // return $paymentDetails;
            if (count($paymentDetails) === 0) {
                return response()->json([
                    "ok" => false,
                    "msg" => "No order available for payment",
                ]);
            }
            foreach ($paymentDetails as $payment) {
                $cylinderSize = CylinderSize::where('id', $payment['weight_id'])->first();
                if ($cylinderSize) {
                    $quantity = CylinderSize::where('id', $payment['weight_id'])->count();
                    $amountForPayment = (int)$quantity * (int)$cylinderSize->amount;
                    $weightAmt[] = $amountForPayment;
                } else {
                    return response()->json([
                        "ok" => false,
                        "msg" => "No amount available for this package",
                    ]);
                }
            }

            // return $weightAmt;
            $amount =  array_sum($weightAmt);



            if ($amount !== (int)$request->amount) {
                return response()->json([
                    "ok" => false,
                    "msg" => 'Amount to be paid is GHS ' . $amount . '.00'
                ]);
            }

            $transactionId = '';
            for ($i = 0; $i < 12; $i++) {
                $transactionId .= random_int(0, 9);
            }

            Payment::insert([
                "transid" => strtoupper(bin2hex(random_bytes(4))),
                "transaction_id" => $transactionId,
                "amount_paid" => $amount,
                "order_id" => $request->order_id,
                "custno" => $request->customer,
                "status" => Payment::SUCCESS,
                "payment_mode" => "offline",
                "createuser" => $request->createuser,
            ]);

            CustomerCylinder::where('order_id', $request->order_id)->update(['status' => CustomerCylinder::PENDING_ASSIGNMENT]);
            Dispatch::where('order_id', $request->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);

            return response()->json([
                "ok" => true,
            ]);
        } catch (\Throwable $e) {
            Log::error("An error occured during cylinder assignment", [
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ]);
            return response()->json([
                "status" => false,
                "message" => "Request failed. An internal error occured",
                "errMsg" => $e->getMessage(),
                "trace" => $e->getTrace(),
            ], 500);
        }
    }

    public function verifyCustomerPaymentLink(Request $request)
    {
        try {
            $transactionId = $request->query('transaction_id');
            $statusCode = $request->query('code');
            if (empty($transactionId)) {
                return redirect(route("/cancel-payment"));
            }


            DB::beginTransaction();
            $payment  = Payment::where('transaction_id', $transactionId)->first();

            if (empty($payment)) {
                return redirect(route("/cancel-payment"));
            }

            if ($statusCode == "000") {
                Payment::where('transaction_id', $transactionId)->update(['status' => Payment::SUCCESS]);
                if (empty($payment)) {
                    // CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                    Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::CANCELLED, 'modifydate' => date('Y-m-d H:i:s')]);
                    return redirect(route("/cancel-payment"));
                }
                CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::PENDING_ASSIGNMENT]);
                Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::EN_ROUTE, 'modifydate' => date('Y-m-d H:i:s')]);
                return redirect()->route('payment.successful')->with('success', 'Payment was successful!');
 
            } else { 
                // CustomerCylinder::where('order_id', $payment->order_id)->update(['status' => CustomerCylinder::CANCELLED]);
                Dispatch::where('order_id', $payment->order_id)->update(['status' => Dispatch::CANCELLED, 'modifydate' => date('Y-m-d H:i:s')]);
                return redirect(route("/cancel-payment"));
            }


            DB::commit();
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

    public function successful()
    {
        return Inertia::render('PaymentSuccessful');
    }

    public function reports($customer, $cylinder, $dateFrom, $dateTo)
    {
        $payment = DB::table("tblpayment")->select(
            "tblpayment.*",
            "tblcustomer.fname",
            "tblcustomer.mname",
            "tblcustomer.lname",
        )
            ->join("tblcustomer", "tblcustomer.custno", "tblpayment.custno")
            ->when($customer !== 'all', function ($q)  use ($customer) {
                return $q->where('tblcustomer.custno', $customer);
            })
            ->when($cylinder !== 'all', function ($q)  use ($cylinder) {
                return $q->where('tblpayment.cylcode', $cylinder);
            })
            ->where("tblcustomer.deleted", "0")
            ->whereBetween('tblpayment.createdate', [$dateFrom, $dateTo])
            ->get();

        return response()->json([
            "data" => PaymentResource::collection($payment)
        ]);
    }
}
