<?php

namespace App\Models\Payment;

use App\Http\Controllers\Api\Payment\MpesaGateway;
use App\Models\MpesaModel;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory;

    // Todo: Table Name
    protected $table = 'billing';

    protected $with = ['this_user', 'this_txns'];

    // ? Mass Assignable
    protected $fillable = ['code', 'type', 'user', 'project', 'total', 'status', 'flag'];

    // Todo: User
    public function this_user()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    // Todo: Txns
    public function this_txns()
    {
        return $this->hasMany(Txn::class, 'billing_id');
    }

    // Todo: Txns
    public function this_mpesa()
    {
        return $this->hasMany(MpesaModel::class, 'billing');
    }

    /**
     * Todo: Make Billing
     *
     * @param string Billing code (default null)
     * @param string Billing Type (default null)
     * @param int user (default null)
     * @param int project (default null)
     * @param float total (default 0)
     * @param int status (default 0)
     *
     */
    public static function makeBilling($code = null, $type = null, $user = null, $total = 0, $status = 0)
    {
        // If no code generate
        $code = (is_null($code)) ? self::generateCode() : $code;

        // Make Billing
        $bill = new self();

        $bill->code = $code;
        $bill->type = $type;
        $bill->user = $user;
        $bill->total = $total;
        $bill->status = $status;

        // Save
        $bill->save();

        // ? If success return billing
        return $bill->id;
    }

    // Todo: Generate Billing Code
    public static function generateCode($prefix = 'TC', $max = 4)
    {
        // Generate Prefix
        $lastId = self::latest('id')->value('id'); // Get the last inserted ID
        $nextId = $lastId + 1; // Add 1 to get the next ID
        $nextId = str_pad($nextId, $max, '0', STR_PAD_LEFT); // Pad the ID with leading zeros and add prefix

        // billing NUmber
        $billingCode = $prefix . $nextId;

        // Check if the invoice number already exists
        while (self::where('code', $billingCode)->exists()) {
            $letter = chr(rand(65, 90)); // Generate a random uppercase letter (ASCII code 65 to 90)
            $numbers = Str::random(5, '123456789'); // Generate a random string of 3 digits
            $uniqueId = $letter . $numbers;

            $billingCode = $prefix . $nextId . $uniqueId;
        }

        // Return billing code
        return $billingCode;
    }
}
