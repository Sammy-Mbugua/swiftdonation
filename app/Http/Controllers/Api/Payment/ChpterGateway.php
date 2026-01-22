<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\Chpter;
use App\Models\Money\Txn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ChpterGateway extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //

    }

    /**
     * Method is public and accessible via the web
     * Todo: This method is the callback for.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        // Get Request Data
        $data = $request->getContent();

        // Log the data
        Log::info('CHPTER Callback1: ' . json_encode($data));

        // Tempo
        $data = json_encode([
            'Message' => "Success",
            'Success' => true,
            'Status' => 200,
            'Amount' => "1.000",
            'transaction_reference' => "DR_7MGQJLW",
            'transaction_code' => "TAS2VRTZXM",
            "chpter_transaction_ref" => "0831-42c3-b374-3654788f3f9d76005481"
        ]);

        // Decode Data
        $response = json_decode($data, true);
        // $response['payment_method'] = 'mpesa';
        $response['Amount'] = (int) $response['Amount'];

        // Check if Response is empty
        if (empty($response)) {
            // Log Response
            Log::error('CHPTER Callback Response: Empty Response');
            // Return
            return 'Empty Response';
        }

        // Billing Id
        $order_Id = null;

        // ? Check if billing_id has been used
        $chpter = \App\Models\Cash\Chpter::where('reference', $response['chpter_transaction_ref'])->first();
        log::info('CHPTER Callback : ' . json_encode($chpter));
        // Check Flow
        if (!$chpter) {
            print_r('Billing does not exist 1');
            // Log the data
            Log::info('CHPTER Billing Invalid: ' . 'Billing does not exist 1');
            // Return
            return;
        }


        // Get Billing id
        $_ref = \App\Models\Cash\Chpter::where('reference', $response['chpter_transaction_ref'])->first();

        $billing = \App\Models\Cash\Billing::where('id', $_ref->billing_id)->first();


        // check if order is already confirmed
        if ($billing->status  == 1) {
            print 'Billing already confirmed';
            return;
        }

        // Check if Paid amount is correct
        if ($response['Amount'] != $billing->amount) {
            // Log Response
            Log::error("CHPTER Callback Response: Low amounnt paid for ($billing->amount) " . json_encode($response));
            // Return
            return "Amount Doesn't Match";
        }

        // Save Chpter
        $chpter->payment_message = $response['Message'];
        $chpter->payment_amount = $response['Amount'];
        $chpter->billing_amount = $billing->amount;
        $chpter->payment_status = 'Payment complete.'; //$response['payment_status'];
        // $chpter->reference = $response['transaction_reference'];
        $chpter->transaction = $response['transaction_code'];
        $chpter->paid_at = date('Y-m-d H:i:s');
        $chpter->paid = ($response['Success'] === True) ? 1 : 0;

        // Save
        $chpter->save();

        // Todo: Confirm
        $billing->status = 1;
        $billing->save();
        $amount = $response['Amount'];
        // charge fee of 5% on amount
        $fee = $amount * config('services.withdraw_fee')??0;
        $amount = $amount - $fee;

        $account = 'CRT-'. $billing->donationinfo->user;
        // ? Withdraw
        Txn::create([
            'billing' => $billing->id,
            'cr_amount' => 0.00,
            'dr_amount' => $response['Amount'],
            'currency' => 'KES',
            'type' => 'deposit',
            'account' => 'CASH',
            'user_id' => $billing->donationinfo->user,
            'flag' => 1,
        ]);

        // ? Withdraw
        Txn::create([
            'billing' => $billing->id,
            'cr_amount' => 0.00,
            'dr_amount' => $fee,
            'currency' => 'KES',
            'type' => 'income',
            'account' => $account,
            'user_id' => $billing->donationinfo->user,
            'flag' => 1,
        ]);

        // ? Withdraw
        Txn::create([
            'billing' => $billing->id,
            'dr_amount' => 0.00,
            'cr_amount' => $response['Amount']-$fee,
            'currency' => 'KES',
            'type' => 'creator-reward',
            'account' => $account,
            'user_id' => $billing->donationinfo->user,
            'flag' => 1,
        ]);

        // print payment success responce
        Log::info('CHPTER Payment Billing: ' . $billing->cobe);
        print 'OK';
    }

}
