<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Txn extends Model
{
    use HasFactory;

    // Todo: Table Name
    protected $table = 'txn';

    // protected $with = ['this_billing'];

    // ? Mass Assignable
    protected $fillable = ['billing_id', 'cr_amount', 'dr_amount', 'type', 'reserse', 'note', 'flag'];

    // Todo: Billing
    public function this_billing()
    {
        return $this->hasOne(Billing::class, 'id', 'billing_id', 'cr_amount', 'dr_amount',);
    }

    /**
     * Todo: Make TXN
     *
     * @param int Billing ID
     * @param float cr_amt (default 0)
     * @param float dr_amt (default 0)
     * @param string type (default null)
     * @param int reserse (default 0)
     *
     */
    public static function makeTxn(int $billing, float $cr_amt = 0, float $dr_amt = 0, $ref = null, string $type = null, int $reserse = 0)
    {
        // Txn
        self::txn_cr($billing, $cr_amt, $ref, $type, $reserse);
        self::txn_dr($billing, $dr_amt, $ref, $type, $reserse);
    }

    // Todo: Credit TXN
    public static function txn_cr(int $billing, float $amount = 0, $ref = null, string $type = null, int $reserse = 0, $note = null)
    {
        // Txn
        $txn = new self();

        $txn->billing_id = $billing;
        $txn->cr_amount = $amount;
        $txn->dr_amount = 0;
        $txn->reference = $ref;
        $txn->type = $type;
        $txn->reserse = $reserse;
        $txn->note = $note;

        // Save
        $txn->save();
    }

    // Todo: Debit Txn
    public static function txn_dr(int $billing, float $amount = 0, $ref = null, string $type = null,  int $reserse = 0, $note = null)
    {
        // Txn
        $txn = new self();

        $txn->billing_id = $billing;
        $txn->cr_amount = 0;
        $txn->dr_amount = $amount;
        $txn->reference = $ref;
        $txn->type = $type;
        $txn->reserse = $reserse;
        $txn->note = $note;

        // Save
        $txn->save();
    }
}
