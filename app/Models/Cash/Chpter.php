<?php

namespace App\Models\Cash;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class Chpter extends Model
{
    protected $table = 'chptertxns';

    /**
     * Todo: Fillable
     */
    protected $fillable = [
        'billing_id',
        'reference',
        'type',
        'payment_method',
        'payment_message',
        'payment_amount',
        'billing_amount',
        'payment_status',
        'transaction',
        'paid',
        'paid_at',
        'paid_at',
        'flag',
    ];

    // Todo: orders
    public function billing_info()
    {
        return $this->hasOne(Billing::class, 'id', 'billing');
    }

    /**
     * Todo: Chpter Send Curl Request
     * ? Pass Data Array customer_details
     * ? Pass Data Array amount_details
     * ? Pass Data Array redirect_urls
     *
     * ? If Success Return Response -> [status = true, 'redirect_url' => $response['redirect_url']]
     * ? If Failed Return Response -> [status = false, 'message' => $response['message']]
     *
     * @param array $customer_details
     * @param array $amount_details
     * @param array $redirect_urls
     *
     * @return array
     */
    public static function chpterRequest(array $customer_details, array $amount_details, array $redirect_urls, string $payment_method = 'mpesa'): array
    {
        // Merge Data
        $data = array_merge($customer_details, $amount_details, $redirect_urls);
        // Log Chapter
        Log::info('Chpter Payment Request: ' . json_encode($data));

        //The response is in json
        //$response = $chpter->hostedRedirectPayment($customer_details, $amount_details, $redirect_urls);
        if ($payment_method == 'card') {
            $curlopt_url = 'https://api.chpter.co/v1/initiate/card-payment';
        } else {
            $curlopt_url = 'https://api.chpter.co/v1/initiate/mpesa-payment';
        }

        // ? Load .env variable
        $chpter_api_key = config('services.chpter.chpter_token');

        /**
         * Todo: Via Curl
         */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $curlopt_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data), //Encode Data
            CURLOPT_HTTPHEADER => array(
                "Api-Key: $chpter_api_key",
                "Content-Type: application/json"
            ),
        ));

        /**
         * Todo: Response
         */
        $response = curl_exec($curl);
        curl_close($curl);

        // Log Chapter Response
        Log::info('Chpter Redirect API Response ' . $response);

        // Decode Response
        $response = json_decode($response, true);

        // Check Response
        if ($response['status'] == 200) {
            // Log Chapter
            Log::info('Chpter Success: ' . json_encode($response));

            if ($payment_method == 'card') {
                // Return
                return ['status' => true, 'redirect_url' => $response['checkout_url'], 'chpter_transaction_ref' => $response['chpter_transaction_ref']];
            }

            // Return
            return ['status' => true, 'redirect_url' => '', 'chpter_transaction_ref' => $response['chpter_transaction_ref']];
        } else {
            // Log Chapter
            Log::info('Chpter Failed: ' . $response['message']);

            // Return
            return ['status' => false, 'errors' => $response['message']];
        }
    }

    /**
     * Todo: Chpter Payout Destination
     * ? Pass Data Array payout_details
     *
     * @param array $payout_details
     * @param string $payment_method
     *
     *
     * @return void
     */
    public static function chpterPayoutDestination(array $payout_details, string $payment_method): void
    {
        if ($payment_method == 'bank') {
            $curlopt_url = 'https://api.chpter.co/v1/payout/domestic-bank-destination';
        } else {
            $curlopt_url = 'https://api.chpter.co/v1/payout/mobile-destination';
        }

        // ? Load .env variable
        $chpter_api_key = config('services.chpter.chpter_token');

        /**
         * Todo: Via Curl
         */
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $curlopt_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payout_details), //Encode Data
            CURLOPT_HTTPHEADER => array(
                "Api-Key: $chpter_api_key",
                "Content-Type: application/json"
            ),
        ));

        /**
         * Todo: Response
         */
        $response = curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Todo: chpterPayoutSend
     */
    public static function chpterPayoutSend($payout_details, $type = 'mpesa')
    {


        // ? Load .env variable
        $chpter_api_key = config('services.chpter.chpter_token');

        /**
         * Todo: Via Curl
         */
        $curl = curl_init();

        // Via Bank
        if ($type == 'bank') {
            $curlopt_url = 'https://api.chpter.co/v1/payout/domestic-bank';

            curl_setopt_array($curl, array(
                CURLOPT_URL => $curlopt_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payout_details), //Encode Data
                CURLOPT_HTTPHEADER => array(
                    "Api-Key: $chpter_api_key",
                    "Content-Type: application/json"
                ),
            ));
        } else {

            $curlopt_url = 'https://api.chpter.co/v1/payout/mobile-wallet';

            // Via M-Pesa
            curl_setopt_array($curl, array(
                CURLOPT_URL => $curlopt_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($payout_details), //Encode Data
                CURLOPT_HTTPHEADER => array(
                    "Api-Key: $chpter_api_key",
                    "Content-Type: application/json"
                ),
            ));
        }


        /**
         * Todo: Response
         */
        $response = curl_exec($curl);
        curl_close($curl);

        // ? Decode
        $results = json_decode($response, true);
        return $results;
    }
}
