<?php

namespace App\Http\Controllers\Api\V1\Manage;

use App\Models\Api\ApiState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class Billing extends Controller
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

    // Todo: issue billing
    public function issue_billing(Request $request)
    {
        // Get Request Data
        $received = $request->all();

        // Log the data
        Log::info($this->api_log . "Request (v1 issue_billing): " . json_encode($received));

        // Decode Data
        $decoded = $received;

        // Issue Billing
        $results = \App\Models\Api\Manage\BillingControl::issue_new_billing($decoded);

    //    dd($results['uid']);

        $decoded = $results;

        // Issue Payment
        // $results = \App\Models\Api\Manage\PaymentControl::prompt_payment($decoded);

        // dd($results);
        // // Check
        // if ($results == 0) {
        //     // Response
        //     return $this->general_response($decoded, [], 'v1/manage/billing');
        // }


        // // Response
        // return $this->general_response($decoded, $results, 'v1/manage/billing');
    }

    // Todo: get billing
    public function get_billing(Request $request)
    {
        // Get Request Data
        $received = $request->all();

        // Log the data
        Log::info($this->api_log . "Request (v1 get_billing): " . json_encode($received));

        // Decode Data
        $decoded = $received;

        // Get Billing
        $results = \App\Models\Api\Manage\BillingControl::get_billing($decoded);

        // Check
        if ($results == 0) {
            // Response
            return $this->general_response($decoded, [], 'v1/manage/billing');
        }

        // Response
        return $this->general_response($decoded, $results, 'v1/manage/billing');
    }
}
