<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\MpesaModel;
use App\Models\Process\Txn;
use Illuminate\Http\Request;
use App\Models\Api\ApiState;
use App\Models\Process\Account;
use App\Models\Process\Billing;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class MpesaGateway extends Controller
{
    //? API Defaults
    private $api_log = "Api: ";
    private $api_debug = False;
    private $api_response = 503;
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
     * Todo: v1 general response
     *
     * Pass api response and data
     */
    private function general_response($decoded, $results, $route)
    {

        // check if $results is empty
        if (count($results) == 0) {
            // Response
            $this->api_response = 204;
        } else {
            // Response
            $this->api_response = 200;

            // Response
            $response_tree['response'] = $results;
        }

        /**
         ** RESPONSE - RETURN
         */
        // Status - Precondition Failed
        $_http_response = ApiState::response_code($this->api_response);

        // Default Return
        $response['sent'] = $decoded;
        $response['response'] =  array_key_exists('response', $response_tree) ? $response_tree['response'] : false;
        $response['message'] = $_http_response['value'];

        // Debug
        if ($this->api_debug) {

            $code_feedback = [
                "route" => $route,
                "controller" => class_basename(__CLASS__) . ".php",
            ];

            $response["debug"] = $code_feedback;
        }

        // Set header Json
        return response()->json($response, $this->api_response);
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
        Log::info('CHPTER Callback: ' . json_encode($data));

        // Tempo
        // $data = json_encode([
        //     'Message' => "Success",
        //     'Success' => true,
        //     'Status' => 500,
        //     'Amount' => "30000.00",
        //     'transaction_reference' => "RoPl6AWe1SAMH7MmFsOP",
        //     'transaction_code' => "SC553HIQ0B",
        // ]);
        $data = '
        {
            "Body":{
                "stkCallback":{
                    "MerchantRequestID":"c20c-4eb8-8161-40af4f84ee39117244",
                    "CheckoutRequestID":"ws_CO_04042025145940398717873111",
                    "ResultCode":0,
                    "ResultDesc":"The service request is processed successfully.",
                    "CallbackMetadata":{
                        "Item":[
                            {"Name":"Amount","Value":1},
                            {"Name":"MpesaReceiptNumber","Value":"RK33BXFPEF"},
                            {"Name":"Balance"},
                            {"Name":"TransactionDate","Value":20241103172655},
                            {"Name":"PhoneNumber","Value":254726688832}
                        ]
                    }
                }
            }
        }';

        // Decode Data
        $response = json_decode($data, true);

        // $response['payment_method'] = 'mpesa';

        // Check if Response is empty
        if (empty($response)) {
            // Log Response
            Log::error('CHPTER Callback Response: Empty Response');
            // Return
            return 'Empty Response';
        }
        $response = $response['Body']['stkCallback'];
        // change array in callbackmetadata to key value array
        // eg CallbackMetadata[Amount] = 1
        $items = [];
        foreach ($response['CallbackMetadata']['Item'] as $key => $value) {
            // dd($value);
            $items[$value['Name']] = $value['Value'] ?? null;
        }
        $response['CallbackMetadata'] = $items;

        // dd($response);


        $payment_amount = (int) $response['CallbackMetadata']['Amount'];


        // Data
        $merchant_request_id = trim($response['MerchantRequestID']);
        $checkout_request_id = trim($response['CheckoutRequestID']);
        $result_code = $response['ResultCode'];
        if ($result_code != "0") {
            print_r('Failed Payment');
            // Log the data
            Log::info('Mpesa Callback Failed: ' . 'Failed Callback response');
            // Return
            return;
        }

        // Billing Id
        $order_Id = null;

        // ? Check if billing_id has been used
        $mpesatxns = MpesaModel::where('CheckoutRequestID', $checkout_request_id)->first();
        // dd($mpesatxns);
        $received = $mpesatxns->billing;
      
        // Decode Data
        $decoded['id'] = $received;
        // dd($decoded);

         // Get Billing
         $results = \App\Models\Api\Manage\BillingControl::get_billing($decoded);


        // Get Billing
        // $getBilling = Billing::with(['orderinfo'])->where('id', $mpesatxns->billing)->first();
        $getBilling = \App\Models\Api\Manage\BillingControl::get_billing($decoded);

        // dd($getBilling);

        // Check Flow
        if (!$getBilling) {
            print_r('Billing does not exist');
            // Log the data
            Log::info('CHPTER Billing Invalid: ' . 'Billing does not exist');
            // Return
            return;
        }

        // Change To Float
        $getBilling['total'] = (int)$getBilling['total'];
        // Force - total For Demo
        $response['total'] = $getBilling['total'];

        // check if order is already confirmed
        if ($getBilling['total'] == $response['total'] && $getBilling['status']  == 1) {
            print 'Billing already confirmed';
            return;
        }

        // Check if Paid total is correct
        if ($response['total'] != $getBilling['total']) {
            // Log Response
            // Log::error("CHPTER Callback Response: Low amounnt paid for ($getBilling['total']) " . json_encode($response));
            // Return
            return "Amount Doesn't Match";
        }

        // dd($mpesatxns);
        // Save Chpter
        $mpesatxns->payment_message = 'Success';
        $mpesatxns->payment_amount = $response['total'];
        $mpesatxns->billing_amount = $getBilling['total'];
        $mpesatxns->payment_status = 'Payment complete.'; //$response['payment_status'];
        // $chpter->reference = $response['transaction_reference'];
        $mpesatxns->transaction = $response['CallbackMetadata']['MpesaReceiptNumber'];
        $mpesatxns->paid_at = date('Y-m-d H:i:s');
        $mpesatxns->paid = 1;

        // Save
        $mpesatxns->save();


        // update billing

        $_billing = \App\Models\Cash\Billing::where('id', $getBilling['id'])->first(); ;
        $_billing->status = 1;
        $_billing->save();

        // update referral
        $referral = \App\Models\Referral::where('user', $getBilling['user'])->first();
        if ($referral) {
            $referral->billing = $getBilling['id'];
            $referral->status = 1;
            $referral->save();
        }


        // Check if payment matches with expected amount
        $isConfirmed = $this->confirmPayment($getBilling, $response['total']);
        if (!$isConfirmed) {
            print_r('Payment could not be connfirmed');
            // Log the data
            Log::info('CHPTER Payment Invalid: ' . 'Payment could not be connfirmed');
            // Return
            return;
        }

        // TXN
        // Txn::payViaAcentria($getBilling);

        // Txn::payViaInsurer($getBilling);



        // Todo: Confirm
        // $getBilling->status = 1;
        // $getBilling->paid_at = date('Y-m-d H:i:s');
        // $getBilling->method = 'chpter';
        // $getBilling->save();

        // Email
        // $this->sendDepositEmail($getBilling->orderinfo);

        // print payment success responce
        // Log::info('CHPTER Payment Billing: ' . $getBilling->id);
        print 'OK';
    }

    /**
     * Todo: Confirm Payment
     * ? Pass Billing Id
     * ? Pass Paid Amount
     * ? Pass Order Id
     *
     * @param $this_billing
     * @param $paid_amount
     */
    private function confirmPayment($this_billing, $paid_amount)
    {
        return true;

        // Apply TXN
        $_cash = Billing::where('id', $this_billing->id)->first();
        if (!$_cash) {
            return false;
        }

        // Return
        return (intval($_cash->amount) == intval($paid_amount)) ? true : false;
    }



    /**
     * Todo: Send Order Email
     * ? Send Deposit Email
     * ? Pass Company Id
     *
     * @param  $orderInfo -> Get From Billing->orderinfo
     *
     * @return void
     */
    public function sendDepositEmail($order_info)
    {

        // Send Email
        $response = (object) ['status' => true]; //Auto::sendCurlRequest($mail_response, $defaults['api_url'] . '/api-mail/Order');
        if ($response->status) {

            $email_address = strtolower($order_info->this_customer->email);
            // Log Response
            Log::info("Company Email Sent Successfully: Order ($email_address) " . json_encode($response));

            $send_data['name'] = ucwords(strtolower($order_info->this_customer->name));

            $send_data['message'] = "
                    <p>We have received payment for your cover</p>
                ";

            // Mail
            $subject = "ACENTRIA COVER PAYMENT #$order_info->request";
            Mail::to($email_address)->send(new \App\Mail\Payment\ChpterMail("$subject", $send_data));

            // Return
            return 'Email Send';
        } else {
            // Log Response
            Log::error("Company Email Failed: " . json_encode($response));

            // Return
            return 'Email Failed';
        }
    }
}
