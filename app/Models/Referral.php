<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referral';

    protected $fillable = [
        'user', 'referred_by', 'status', 'flag',
    ];
    
    protected $with = ['this_refer', 'this_billing'];

    public function this_refer()
    {
        return $this->hasOne(User::class, 'id', 'user'); // corrected
    }

    public function this_billing()
{
    return $this->hasOne(\App\Models\Cash\Billing::class, 'id', 'billing');
}

}
