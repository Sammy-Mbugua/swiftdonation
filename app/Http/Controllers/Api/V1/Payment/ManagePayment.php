<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Models\Api\ApiState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ManagePayment extends Controller
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
     * Todo: v2 general response
     *
     * Pass api response and data
     */
    private function general_response($decoded, $results, $route)
    {

        // Default
        $response_tree = [];

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

    // Todo: issue payment
    public function make_payment(Request $request)
    {
        // Get Request Data
        $received = $request->all();

        // Log the data
        Log::info($this->api_log . "Request (v1 make_payment): " . json_encode($received));

        // Decode Data
        $decoded = $received;

        // Issue Payment
        $results = \App\Models\Api\Manage\PaymentControl::prompt_payment($decoded);

        // Check
        if ($results['status'] == false) {
            // Response
            return $this->general_response($decoded, [], 'v1/manage/payment');
        }

        // Response
        return $this->general_response($decoded, $results, 'v1/manage/payment');
    }

    // Todo: issue payment retry
    public function make_payment_retry(Request $request)
    {
        // Get Request Data
        $received = $request->all();

        // Log the data
        Log::info($this->api_log . "Request (v1 make_payment_retry): " . json_encode($received));

        // Decode Data
        $decoded = $received;

        // Issue Payment
        $results = \App\Models\Api\Manage\PaymentControl::prompt_retry_payment($decoded['uid']);

        // Check
        if ($results['status'] == false) {
            // Response
            return $this->general_response($decoded, [], 'v1/manage/payment/pay-retry');
        }

        // Response
        return $this->general_response($decoded, $results, 'v1/manage/payment/pay-retry');
    }
}
