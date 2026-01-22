<?php

namespace App\Models\Api\Manage;

use Illuminate\Database\Eloquent\Model;

class BillingControl extends Model
{
    //

    /**
     * Todo: issue new billing
     */
    public static function issue_new_billing($billing_info): array
    {
        // Load Package
        $_package = \App\Models\Cash\Package::where('id', $billing_info['package'])?->first();
        if ($_package == null) {
            return [];
        }

        // Unique ID
        $unique_id = uniqid();

        // New Billing
        $_billing = new \App\Models\Cash\Billing;
        $_billing->uid = $unique_id;
        $_billing->user = (int) $billing_info['user'];
        $_billing->currency = $_package->currency;
        $_billing->total = $_package->price;
        $_billing->save();

        // Subscription
        $_subscriptionId = self::issue_new_subscription($_package, $_billing);
        $_billing->type_id = $_subscriptionId;
        // Update
        $_billing->save();

        // Return
        return ['id' => $_billing->id, 'uid' => $_billing->uid];
    }

    /**
     * Todo: Issue new subscription
     */
    public static function issue_new_subscription($_package, $_billing)
    {
        // Subscription
        $_subscription = new \App\Models\Cash\Subscription;
        $_subscription->package = $_package->id;
        $_subscription->user = $_billing->user;
        $_subscription->billing = $_billing->id;
        $_subscription->price = $_package->price;
        $_subscription->currency = $_package->currency;
        $_subscription->flag = 0;
        $_subscription->save();

        // Cash
        \App\Models\Cash\Txn::cash_txn($_billing->id, amount_dr: $_package->price, note: 'attempt-subscription');

        // Return
        return $_subscription->id;
    }

    /**
     * Todo: get billing
     */
    public static function get_billing($billing_info): array
    {
        // Load Billing
        $_billing = \App\Models\Cash\Billing::where('id', $billing_info['id'])?->first();
        if ($_billing == null) {
            return [];
        }

        // Return
        return $_billing->toArray();
    }
}
