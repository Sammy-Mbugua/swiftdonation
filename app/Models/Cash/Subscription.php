<?php

namespace App\Models\Cash;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    /**
     * Todo: Package
     */
    public function package_info()
    {
        return $this->hasOne(Package::class, 'id', 'package');
    }

    /**
     * Todo: Users
     */
    public function user_info()
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    /**
     * Todo: Billing
     */
    public function billing_info()
    {
        return $this->hasOne(Billing::class, 'id', 'billing');
    }
}
