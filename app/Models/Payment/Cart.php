<?php

namespace App\Models\Payment;

use App\Models\User;
use App\Models\Portal\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    // Todo: Table Name
    protected $table = 'carts';

    protected $with = ['this_user'];

    // ? Mass Assignable
    protected $fillable = ['cuid', 'user', 'document', 'cost', 'fee', 'total', 'quantity', 'paid', 'flag'];

    // Todo: User
    public function this_user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    // Todo: Document
    public function this_document()
    {
        return $this->hasOne(Document::class, 'id', 'document');
    }

    // Todo: Add to Cart
    public static function addCart(string $cartId, int $docId, int $userId = null)
    {
        // Check Cart Unique Id
        $this_cart = self::where('cuid', $cartId)->where('paid', 0)->where('document', $docId)->first();

        // If doesnt exist
        if (!$this_cart) {
            // Check Document
            $_doc = \App\Models\Portal\Document::where('id', $docId)->where('approved', 1)->where('suspend', 0)->where('flag', 1)->first();
            if ($_doc) {
                // Add to cart
                $my_cart = new self();

                $my_cart->cuid = $cartId;
                $my_cart->user = $userId;
                $my_cart->document = $_doc->id;

                $cost = floatval($_doc->price) - floatval($_doc->discount);
                $fee = ceil($cost * floatVal(config('services.document.rate')));
                $total_cost = ceil(floatVal($fee) + floatVal($cost));

                $my_cart->cost = $cost;
                $my_cart->fee = $fee;
                $my_cart->total = $total_cost;

                $my_cart->quantity = 1;
                $my_cart->save();

                // Return
                return true;
            }
        }

        // Return
        return false;
    }

    // Todo: Removed to Cart
    public static function removeCart(string $cartId, int $docId, int $userId = null)
    {
        // Check Cart Unique Id
        $this_cart = self::where('cuid', $cartId)->where('paid', 0)->where('document', $docId)->first();

        // If does exist
        if ($this_cart) {
            $this_cart->delete();

            // Return
            return true;
        }
        // Return
        return false;
    }

    // Todo: Clear whole Cart
    public static function clearCart(string $cartId, int $userId = null)
    {
        // This cart
        $my_cart = self::where('cuid', $cartId)->where('paid', 0)->get();
        foreach ($my_cart as $i => $row) {
            self::removeCart($cartId, $row->document, $userId);
        }

        // Return
        return true;
    }

    // Todo: Bill Cart
    public static function billCart(string $cartId, int $userId = null)
    {
        // Check if Bill was created
        $this_billing = Billing::where('cuid', $cartId)->where('flag', 1)->first();
        if ($this_billing) {
            // Delete TXN
            $this_txn = \App\Models\Payment\Txn::where('billing_id', $this_billing->id);
            $this_txn->delete();
            // Delete Billing
            $this_billing->delete();
        }

        // New Billing
        $this_cart = self::where('cuid', $cartId)->where('paid', 0)->get();

        // Loop and Update
        foreach ($this_cart as $i => $_cart) {
            if (is_null($_cart->user)) {
                $_cart->user = $userId;
                $_cart->save();
            }
        }

        // Sum all Items Total
        $cost = $this_cart->sum('total');
        $fee =  ceil(floatVal(config('services.document.fee')));

        // Billing
        $billing_code = Billing::generateCode('DP');

        $this_billing = new Billing();
        $this_billing->code = $billing_code;
        $this_billing->type = 'document';
        $this_billing->user = $userId;
        $this_billing->cuid = $cartId;
        $this_billing->total = $cost + $fee;
        $this_billing->status = 0;

        // Save
        $this_billing->save();
        // Update Code
        $this_billing->code = $this_billing->code . '-' . $this_billing->id;
        $this_billing->save();

        // TODO: MAKE TXN CR
        // \App\Models\Payment\Txn::txn_cr(billing: $this_billing->id, amount: $this_billing->total, type: 'document-invoicing');

        // Return
        return (object) [
            'id' => $this_billing->id,
            'code' => $this_billing->code,
            'cost' => $cost,
            'fee' => $fee,
            'total' => $this_billing->total,
        ];
    }
}
