<?php

namespace App\Models\Cash;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billings';

    protected $with = ['billing_meta'];

    /**
     * Todo: Users
     */
    public function user_info()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public function txns_info(){
        return $this->hasMany(Txn::class, 'billing', 'id');
    }

    /**
     * Todo: Billing Meta
     * One or more meta values can be related to a single billing
     */
    public function billing_meta()
    {
        return $this->hasMany(BillingMeta::class, 'billing');
    }

    public function referrer()
    {
        return $this->belongsTo(\App\Models\Referral::class, 'id', 'billing');
    }
}
