<?php

namespace App\Models\Api\Manage;

use Illuminate\Database\Eloquent\Model;

class PaymentControl extends Model
{
    // Todo: prompt payment
    public static function prompt_payment(array $data): array
    {

        // Prepare Payment
        $full_name = $data['name'];
        $email = (!is_null($data['email']) && !empty($data['email'])) ? $data['email'] : config('services.default_email');
        $phone = \App\Services\AfricasTalkingService::formatPhoneNumber($data["phone"]);

        $location = (!is_null($data['location']) && !empty($data['location'])) ? $data['location'] : config('services.default_location');
        $currency = config("services.currency.name");

        // Billing Verification
        $_billing = \App\Models\Cash\Billing::where('uid', $data['billing'])->where('status', 0)->where('flag', 1)?->first();
        if ($_billing == null) {
            return [
                'status' => false,
                'mode' => $data['mode'],
            ];
        }

        // Add / Update Billing Meta
        self::add_update_billing_meta($data, $_billing->id);

        // Amount
        $amount = floatval($_billing->total);

        // TODO: SEND REQUEST TO CHPTER
        $chpter_data = self::prepare_chpter_payment_data($full_name, $email, $phone, $location, $currency, $amount);

        // ? chapter_customer
        $chpter_customer = $chpter_data['customer'];

        // TODO: CHPTER PAYMENT MODE
        $chpter_mode_urls = self::chpter_payment_mode($data['mode'], $_billing->uid);

        // TODO: PROCESS PRE-DATA
        // Merge Arrays $chpter_amount and $chpter_product
        $chpter_process = array_merge($chpter_data['amount'], $chpter_data['products']);

        // Chpter Request
        $results = self::chpter_make_request($chpter_customer, $chpter_process, $chpter_mode_urls, $data['mode']);

        // Return Results
        return $results;
    }

    // Todo: prompt retry payment
    public static function prompt_retry_payment(string $uid): array
    {

        // Billing Verification
        $_billing = \App\Models\Cash\Billing::where('uid', $uid)->where('status', 0)->where('flag', 1)?->first();
        if ($_billing == null) {
            return [
                'status' => false,
                'mode' => null,
            ];
        }

        // Get Meta
        $meta = $_billing->billing_meta;
        if ($meta == null) {
            return [
                'status' => false,
                'mode' => null,
            ];
        }

        // Prepare Meta Data
        $payment_data = [];
        foreach ($_billing->billing_meta as $m) {
            $payment_data[$m->key] = $m->value;
        }

        // If is empty
        if (empty($payment_data)) {
            return [
                'status' => false,
                'mode' => null,
            ];
        }

        // Make Payment
        return self::prompt_payment($payment_data);
    }

    // Todo: prepare chpter payment data
    public static function prepare_chpter_payment_data(string $_name, string $_email, string $_phone, string $_location, string $_currency, float $_amount): array
    {
        // 1: Customer
        $chpter_customer = [
            "customer_details" => [
                "full_name" => "$_name",
                "location" => "$_location",
                "phone_number" => "$_phone",
                "email" => "$_email",
            ]
        ];

        // 2: Product
        $chpter_product = [
            "products" => []
        ];

        // 3: Payable
        $chpter_amount = [
            "amount" => [
                "currency" => $_currency,
                "delivery_fee" => 0.00,
                "discount_fee" => 0.00,
                "total" => $_amount,
            ]
        ];

        return [
            "customer" => $chpter_customer,
            "products" => $chpter_product,
            "amount" => $chpter_amount
        ];
    }

    // Todo: chpter payment mode
    public static function chpter_payment_mode(string $_payment_mode, string $_billing_code): array
    {

        // Us Chpter
        if ($_payment_mode == 'card') {
            $chpter_redirect_urls =  [
                "callback_details" => [
                    "transaction_reference" => "$_billing_code",
                    "success_url" => (blank(config('services.chpter.success_back_url'))) ? url('payment-success-redirect') : config('services.chpter.success_back_url'),
                    "failed_url" => (blank(config('services.chpter.failed_back_url'))) ? url('payment-failed-redirect') : config('services.chpter.failed_back_url'),
                    "callback_url" => (blank(config('services.chpter.callback_back_url'))) ? url('api/chpter-callback') : config('services.chpter.callback_back_url'),
                ]
            ];
        } else {
            $chpter_redirect_urls =  [
                "callback_details" => [
                    "notify_customer" => true,
                    "transaction_reference" => "$_billing_code",
                    "callback_url" => (blank(config('services.chpter.callback_back_url'))) ? url('api/chpter-callback') : config('services.chpter.callback_back_url'),
                ]
            ];
        }

        // Return
        return $chpter_redirect_urls;
    }

    // Todo: chpter callback
    public static function chpter_make_request(array $_customer, array $_process, array $_mode_urls, string $_mode): array
    {
        // TODO: SEND TO CHPTER
        $chpter_response =  \App\Models\Cash\Chpter::chpterRequest($_customer, $_process, $_mode_urls, $_mode);

        // TODO: Default Results
        $results = [
            'status' => false,
            'mode' => $_mode,
        ];

        // TODO: Check Response
        if ($chpter_response['status']) {
            if ($_mode == 'card') {
                $results = [
                    'status' => true,
                    'mode' => $_mode,
                    'redirect_url' => $chpter_response['redirect_url'],
                ];
            } else {
                $results = [
                    'status' => true,
                    'mode' => $_mode,
                    'ref' => $chpter_response['chpter_transaction_ref'],
                ];
            }
        }

        // Return
        return $results;
    }

    // Todo: add or update billing meta
    public static function add_update_billing_meta(array $data, int $billing_id): void
    {
        // Submitted Payment Data
        $payment_data = [
            'mode' => $data['mode'],
            'billing' => $data['billing'],
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'location' => $data['location'],
        ];

        // Add / Update Meta 
        foreach ($payment_data as $key => $value) {
            self::upsertBillingMeta($billing_id, $key, $value);
        }
    }

    /**
     * Create or update a billing meta entry
     * 
     * @param int $billing_id
     * @param string $key
     * @param mixed $value
     * @return \App\Models\Cash\BillingMeta
     */
    private static function upsertBillingMeta($billing_id, $key, $value)
    {
        return \App\Models\Cash\BillingMeta::updateOrCreate(
            [
                'billing' => $billing_id,
                'key' => $key,
            ],
            [
                'value' => $value,
                'flag' => 1
            ]
        );
    }
}
