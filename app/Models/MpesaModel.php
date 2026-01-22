<?php

namespace App\Models;

use App\Models\Payment\Billing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaModel extends Model
{
    use HasFactory;

    // ? Table Name
    protected $table = 'mpesatxns';

    protected $baseUrl;
    protected $consumerKey;
    protected $consumerSecret;
    protected $shortcode;
    protected $party_b;
    protected $passkey;
    protected $transactionType;
    protected $callback_url;
    protected $confirmation_url;
    protected $validation_url;
    protected $env;



       // Todo: Txns
       public function this_billing()
       {
           return $this->hasOne(Billing::class, 'id', 'billing');
       }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->baseUrl = config('mpesa.base_url');
        $this->consumerKey = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode = config('mpesa.shortcode');
        $this->party_b = config('mpesa.party_b');
        $this->passkey = config('mpesa.passkey');
        $this->transactionType = config('mpesa.transaction_type');
        $this->callback_url = config('mpesa.callback_url');
        $this->confirmation_url = config('mpesa.confirmation_url');
        $this->validation_url = config('mpesa.validation_url');
        $this->env = config('mpesa.env');
    }

    public function getAccessToken()
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");
        return $response->json()['access_token'];
    }

    public function registerC2BUrls()
    {
        $url = $this->baseUrl . '/mpesa/c2b/v1/registerurl';

        $payload = [
            'ShortCode' => $this->shortcode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $this->confirmation_url,
            'ValidationURL' => $this->validation_url,
        ];

        $response = Http::withToken($this->getAccessToken())->post($url, $payload);

        return $response->json();
    }

    public function lipaNaMpesa($amount, $phoneNumber, $accountReference = "SWIFT DONATION", $transactionDesc="TEST PAYMENT")
    {
        $url = $this->baseUrl . '/mpesa/stkpush/v1/processrequest';
        $timestamp = date('YmdHis');
        // $timestamp = date("YmdHis", time());
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->transactionType,
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $this->party_b,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $this->callback_url === "" ? route('mpesa.callback') : $this->callback_url,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];
        // log the data
        // // Log::info('Mpesa Request: ' . json_encode($payload));
        $response = Http::withToken($this->getAccessToken())->post($url, $payload);
        // log the response
        Log::info('Mpesa Response: ' . json_encode($response->json()));
        return $response->json();
    }

    public function checkTransactionStatus($checkoutRequestId)
    {
        $url = $this->baseUrl . '/mpesa/stkpushquery/v1/query';
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $response = Http::withToken($this->getAccessToken())->post($url, $payload);

        if($response->json()){
            $push = self::where('checkout_request_id',$checkoutRequestId)->where('type','push')->first();
            if($push){
                $res = $response->json();
                if(array_key_exists('ResponseCode',$res)){
                    if($res['ResponseCode'] == 0){
                        $transaction = [
                            'merchant_request_id' => $push->merchant_request_id,
                            'checkout_request_id' => $push->checkout_request_id,
                            'transaction_desc' => $res['ResponseDescription'],
                            'customer_message' => $res['ResultDesc'],
                            'response_code' => $res['ResultCode'],
                            'amount' => $push->amount,
                            'phone_number' => $push->phone_number,
                            'type' => 'response',
                            'order' => $push->order,
                        ];

                        self::create($transaction);
                    }
                }

            }
        }
        return $response->json();
    }

    /**
     * Generate the encrypted password for M-Pesa
     *
     * @return string
     */
    public function generateMpesaPassword()
    {
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        return $password;
    }
}
