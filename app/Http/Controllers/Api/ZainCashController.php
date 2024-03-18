<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\InitialPaymentRequest;
use App\Models\InsuranceDriver;
use App\Models\InsurancePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Waad\ZainCash\Facades\ZainCash;

class ZainCashController extends Controller
{
    /**
     * Create Request Transaction
     *
     * @param InitialPaymentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialTransaction(InitialPaymentRequest $request)
    {
        $amount = 1010;
        $zainCashPayment = ZainCash::setAmount($amount)
            ->setServiceType('Book')
            ->setOrderId(Str::random(36))
            ->setIsTest(true)
            ->setIsReturnArray(true);

        $transactionId = $zainCashPayment->createTransaction();
        $transactionId = $transactionId['id'];

        $zainCashPayment = ZainCash::make()
            ->setTransactionID($transactionId)
            ->setIsReturnArray(true);

        // Check transaction details
        $checkTransaction = $zainCashPayment->checkTransaction();

        // Process transaction
        $processingTransaction = $zainCashPayment->processingTransaction($request->phone_number, $request->pin);

        // Check if the driver already has insurance
        $driverId = auth()->user()->id;
        $insurance = InsurancePayment::where('driver_id', $driverId)->first();

        if ($insurance) {
            // Update existing insurance record
            $insurance->update([
                'from' => Carbon::now()->format('Y-m-d'),
                'to' => Carbon::now()->addYear()->format('Y-m-d'),
            ]);
        } else {
            // Create new insurance record
            InsurancePayment::create([
                'transaction_id' => $processingTransaction['transactionid'],
                'driver_id' => $driverId,
                'from' => Carbon::now()->format('Y-m-d'),
                'to' => Carbon::now()->addYear()->format('Y-m-d'),
                'type' => 'zain_cash',
                'amount' => $amount,
            ]);
        }


        return response()->json([
            'checkTransaction' => $checkTransaction,
            'processingTransaction' => $processingTransaction,
        ]);
    }


    /**
     * Pay Transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payTransaction(Request $request)
    {
        // Validate request data
        $request->validate([
            'transaction_id' => 'required|string',
            'phone_number' => 'required|string',
            'pin' => 'required|string',
            'otp' => 'required|string',
        ]);

        $transactionId = $request->input('transaction_id');
        $phoneNumber = $request->input('phone_number');
        $pin = $request->input('pin');
        $otp = $request->input('otp');

        // Pay transaction
        $zainCashPayment = ZainCash::make()
            ->setTransactionID($transactionId)
            ->setIsReturnArray(true);

        $payDetails = $zainCashPayment->payTransaction($phoneNumber, $pin, $otp);

        if ($payDetails['success'] == 1) {
            $driverId = auth()->user()->id;
            $insurancePayment = InsurancePayment::where('driver_id', $driverId)->first();
            if ($insurancePayment->status == 0) {
                $insurancePayment->update([
                  'status' => 1,
                ]);
            }
        }

        return response()->json([
            'pay' => $payDetails,
        ]);
    }
}
