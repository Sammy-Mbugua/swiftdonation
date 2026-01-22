<?php

namespace App\Models\Cash;

use Illuminate\Database\Eloquent\Model;

class BillingMeta extends Model
{
    // Todo: Table Name
    protected $table = 'billing_meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'billing',
        'key',
        'value',
        'flag',
    ];

    /**
     * Todo: Billing
     * One or more meta values can be related to a single billing
     */
    public function billing_info()
    {
        return $this->belongsTo(Billing::class, 'id', 'billing');
    }
}
