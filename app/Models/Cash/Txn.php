<?php

namespace App\Models\Cash;

use Illuminate\Database\Eloquent\Model;

class Txn extends Model
{
    protected $table = 'txns';
    protected $fillable = ['billing', 'gatway', 'cr', 'dr', 'type', 'reserse', 'ref', 'note', 'flag'];

    /**
     * Todo: Billing
     */
    public function billing_info()
    {
        return $this->hasOne(Billing::class, 'id', 'billing');
    }

    // Todo: CASH TXN
    public static function cash_txn($billing_id, $amount_cr = 0, $amount_dr = 0, $type = 'subscription', $note = 'tutor-subscribed')
    {
        // Txn
        $txn = new self;
        $txn->billing = $billing_id;
        $txn->gateway = 'chpter';

        $txn->cr = $amount_cr;
        $txn->dr = $amount_dr;
        $txn->type = $type;

        $txn->ref = null;
        $txn->note = $note;

        $txn->flag = 1;

        $txn->save();
    }
}
