<?php

namespace App\Models\Cash;

use App\Models\Vrm\Hierarchy;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    protected $table = 'packages';

    protected $fillable = [
        'title',
        'name',
        'code',

        'price',
        'discount',
        'currency',

        'duration',

        'post_grace',
        'description',

        'thumbnail',
        'flag',
    ];

    /**
     * Todo: Currency
     */
    public function currency_info()
    {
        return $this->hasOne(Hierarchy::class, 'id', 'currency');
    }
}
