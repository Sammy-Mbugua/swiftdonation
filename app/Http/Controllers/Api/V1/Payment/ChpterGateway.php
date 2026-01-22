<?php

namespace App\Http\Controllers\Api\V1\Payment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Payment\Chpter;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;


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
        Log::info('CHPTER Callback: 10 ' . json_encode($data));

        //Tempo
        // $data = json_encode([
        //     'Message' => "Success",
        //     'Success' => true,
        //     'Status' => 1.00,
        //     'Amount' => "50.00",
        //     'chpter_transaction_ref' => "feab-4949-94a7-d617630ae49d7430068",
        //     'transaction_code' => "SC553HIQ0B",
        // ]);


        // Decode Data
        $response = json_decode($data, true);
        Log::info('CHPTER Callback: 11 true ' . json_encode($response));
        // $response['payment_method'] = 'mpesa';
        $response['Amount'] = (int) $response['Amount'];

        // Check if Response is empty
        if (empty($response)) {
            // Log Response
            Log::error('CHPTER Callback Response: Empty Response');
            // Return
            return 'Empty Response';
        }
        
        // get billing id
        $billing_id = \App\Models\Cash\Chpter::where('reference', $response['chpter_transaction_ref'])->pluck('billing_id')->first();

        Log::info('CHPTER Callback: 12 harr billing id ' . json_encode($billing_id));
           

        // Get Billing
        $getBilling = \App\Models\Cash\Billing::with(['user_info'])->where('id', $billing_id)->first();

        // Check Flow
        if (!$getBilling) {
            print_r('Billing does not exist');
            // Log the data
            Log::info('CHPTER Billing Invalid: ' . 'Billing does not exist');
            // Return
            return;
        }

        // Change To Float
        $getBilling->total = (int)$getBilling->total;
        Log::info('CHPTER Callback: 13 true see the total amount for the billing ' . json_encode($getBilling->total));
        // Force - Amount For Demo
        $response['Amount'] = $getBilling->total;

        // If Cancelled
        if ($response['Success'] == False) {
            // Log Response
            Log::error('CHPTER Payment Action: Cancelled' . json_encode($response));
            // Return
            return 'Cancelled';
        }

        // check if order is already confirmed
        if ($getBilling->total == $response['Amount'] && $getBilling->status  == 1) {
            print_r('Billing already confirmed');
            return;
        }

        // Check if Paid amount is correct
        if ($response['Amount'] != $getBilling->total) {
            // Log Response
            Log::error("CHPTER Callback Response: Low amount paid for ($getBilling->total) " . json_encode($response));
            // Return
            return "Amount Doesn't Match";
        }

        // ? Check if billing_id has been used
        $chpter = \App\Models\Cash\Chpter::where('billing_id', $getBilling->id)->first();
        // ? if not used - create new else update
        if ($chpter == null) {
            $chpter = new \App\Models\Cash\Chpter;
            $chpter->billing = $getBilling->id;
            $chpter->type = 'subscription';
        }

        // Save Chpter
        $chpter->payment_message = $response['Message'];

        $chpter->payment_amount = $response['Amount'];
        $chpter->billing_amount = $getBilling->total;

        // Pay Mode
        $chpter->payment_method = $getBilling->billing_meta->where('key', 'mode')->first()?->value;

        $chpter->payment_status = 'Payment complete.'; //$response['payment_status'];
        // $chpter->reference = $response['transaction_reference'];
        $chpter->transaction = $response['transaction_code'];
        $chpter->paid_at = date('Y-m-d H:i:s');
        $chpter->paid = ($response['Success'] === True) ? 1 : 0;

        // Save
        $chpter->save();

        // Check if payment matches with expected amount
        $isConfirmed = $this->confirmPayment($getBilling, $response['Amount']);
        if (!$isConfirmed) {
            print_r('Payment could not be connfirmed');
            // Log the data
            Log::info('CHPTER Payment Invalid: ' . 'Payment could not be connfirmed');
            // Return
            return;
        }

         // update referral
         $referral = \App\Models\Referral::where('user', $getBilling['user'])->first();
         if ($referral) {
             $referral->billing = $getBilling['id'];
             $referral->status = 1;
             $referral->save();
         }


        // Txn - CR
        \App\Models\Cash\Txn::cash_txn(billing_id: $getBilling->id, amount_cr: $chpter->payment_amount);

        // Todo: Confirm
        $getBilling->status = 1;
        $getBilling->save();

        // Activate Subscription
        $this->activateSubscription($getBilling);

        // print payment success responce
        Log::info('CHPTER Payment Billing: ' . $getBilling->id);
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

        // Apply TXN
        $_bill_verify = \App\Models\Cash\Billing::where('uid', $this_billing->uid)->where('status', 0)->first();
        if (!$_bill_verify) {
            return false;
        }

        // Amount
        if ($_bill_verify->total != $paid_amount) {
            return false;
        }

        // flag
        if ($_bill_verify->flag != 1) {
            return false;
        }

        // Return
        return true;
    }

    /**
     * Todo: Activate Subscription
     */
    private function activateSubscription($billing)
    {
        // Todo: Activate Subscription
        $subscription = \App\Models\Cash\Subscription::with(['package_info'])->where('billing', $billing->id)->first();
        if ($subscription) {
            // Get Package Days
            $_days = $subscription->package_info->duration;
            $_post_grace_days = $subscription->package_info->post_grace;

            // Today using Carbon
            $start_date = Carbon::now();
            $end_date = $start_date->copy()->addDays($_days);
            $post_grace_end_date = (!is_null($_post_grace_days) && !empty($_post_grace_days)) ? $start_date->copy()->addDays($_post_grace_days) : null;

            // Save
            $subscription->start_at = $start_date;
            $subscription->end_at = $end_date;
            $subscription->post_grace_at = $post_grace_end_date;

            $subscription->flag = 1;
            $subscription->save();

            // Todo: Send Notification
            $this->sendNotification($billing, $_days, $end_date);
        }
    }

    /**
     * Todo: Send Notification
     */
    private function sendNotification($billing, $pkg_days, $pkg_expires)
    {
        // User Details
        $_name = $billing->user_info->name;
        $_email = $billing->user_info->email;
        $_phone = $billing->user_info->phone;

        // $pkg_expires change to 14th Feb, YYYY
        $pkg_expires = $pkg_expires->format('d M, Y');

        // message data
        $_message_data = [
            'link' => url('/'),
            'title' => 'Visit TutorLink',
            'pkg_days' => $pkg_days . ' days',
            'pkg_expires' => $pkg_expires
        ];

        // Todo: Via SMS /Email
        $_send_via = (!is_null($_email) && !empty($_email)) ? 'email' : 'phone';
        $_notify_message = $this->subscription_message($_name, $_message_data, $_send_via);

        // Todo: send email & phone number verification
        // Dispatch email job to the queue
        // if ($_notify_message['email']) {
        //     dispatch(new \App\Jobs\Vrm\SendMail($_email, $_notify_message['email'], "SUBSCRIPTION ACTIVATED"));
        // }

        // Dispatch SMS job to the queue
        // if ($_notify_message['sms']) {
        //     $_phoneNo = \App\Services\AfricasTalkingService::formatPhoneNumber($_phone);
        //     dispatch(new \App\Jobs\Vrm\SendSms(
        //         $_phoneNo,
        //         $_notify_message['sms']
        //     ));
        // }
    }


    // TODO: **************************** MESSAGES & MAIL ****************************

    /**
     * Todo: Subscription Message
     */
    private function subscription_message(string $name,  array $code_link, string $generate = 'both'): array
    {

        // Return
        $results = ['email' => false, 'sms' => false];

        // If is Both or Email
        if (in_array(strtolower(trim($generate)), ['both', 'email'])) {
            // Logo - Add Link to logo
            $_logo = null;

            // Links & Button
            $_btn['link'] = (array_key_exists('link', $code_link)) ? $code_link['link'] : null;
            $_btn['title'] = (array_key_exists('title', $code_link)) ? $code_link['title'] : null;

            $_btn_extra = "
            <p>or - copy/click the link below</p>
            <a href='{$_btn['link']}'> {$_btn['link']}</a>
            ";

            $_pkg_expires = $code_link['pkg_expires'];
            $_pkg_days = $code_link['pkg_days'];

            // **** PREPAIRE EMAIL ****

            // Title & Sub Title
            $_title = "Subscription Success";
            $_subtitle = "You have successfully subscribed to $_pkg_days subscription package. Expires on $_pkg_expires.";

            // App Name
            $_app_name = config('services.app_name');
            // App Name
            $_support_mail = config('services.app_mail.support');

            // Body
            $_body = "
            <p>Hello <strong>$name</strong>,</p>

            <p>Thank you for subscribing to $_app_name!</p>

            <p>For more information, please visit our website: <a href='https://$_app_name.com'>$_app_name.com</a></p>
            ";

            // Outro
            $_outro = "
           <p>If you have any questions or need assistance, feel free to contact our support team at <strong>$_support_mail</strong>.</p>
           <p>Looking forward to seeing you share your knowledge!</p>

           <br />

           <p>Best Regards,</p>
           <p>$_app_name Team</p>
            ";

            // Found - results
            $results['email'] = [
                'logo' => $_logo,
                'title' => $_title,
                'subtitle' => $_subtitle,
                'body' => $_body,
                'outro' => $_outro,
                'btn' => $_btn,
                'btn_extra' => $_btn_extra
            ];
        }

        // If is Both or Phone
        if (in_array(strtolower(trim($generate)), ['both', 'phone'])) {
            // **** PREPAIRE SMS ****
            $_sms = "$_app_name: You have successfully subscribed to $_pkg_days subscription package. Expires on $_pkg_expires.";

            // Found - results
            $results['sms'] = $_sms;
        }

        // Return Data
        return $results;
    }
}
