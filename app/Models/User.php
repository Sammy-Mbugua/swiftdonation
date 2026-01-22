<?php

namespace App\Models;
use Illuminate\Support\Str;
use App\Models\Referral;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
    ];

    // protected $with = ['referral'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Todo:: method to get usermetas
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usermetas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Vrm\UserMeta::class, 'user', 'id');
    }

    public function referral(){
        return $this->hasMany(Referral::class, 'referred_by', 'id');
    }
    /**
     * Todo:: method to save user Metadata
     * @param integer $userId
     * @param array $data
     * @return bool
     */
    public static function save_usermeta(int $userId, array $data): bool
    {
        /**
         * Loop through the data
         * key should be the meta_key
         * value should be the meta_value
         */
        foreach ($data as $key => $value) {
            if (!is_null($key)) {
                // Save
                \App\Models\Vrm\UserMeta::create([
                    'user' => $userId,
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }

        return true;
    }

    /**
     * Todo:: method to update user Metadata
     * @param integer $userId
     * @param array $data
     * @return bool
     */
    public static function update_usermeta(int $userId, array $data): bool
    {
        /**
         * Loop through the data
         * key should be the meta_key
         * value should be the meta_value
         */
        foreach ($data as $key => $value) {

            \App\Models\Vrm\UserMeta::where('user', $userId)
                ->where('key', $key)
                ->update([
                    'value' => $value,
                ]);
        }

        return true;
    }

    /**
     * Todo:: method retrive usermeta
     * ? Pass the usermeta as a Collection
     * ? Loop and get the metakey, set is as parent array key
     * ? If specific key is being asked return that only
     *
     * @param any $usermeta
     * @param string|null $key
     *
     */
    public static function get_usermeta($usermeta, string $key)
    {
        // Check if is laravel Collection
        if (!is_a($usermeta, 'Illuminate\Database\Eloquent\Collection')) {
            return $usermeta;
        }

        // Loop
        $meta = [];
        foreach ($usermeta as $index => $this_meta) {
            $meta[$this_meta->meta_key] = $this_meta->meta_value;
        }

        // If key is not null
        if (!is_null($key) && !array_key_exists($key, $meta)) return null;

        // Return
        return is_null($key) ? (object) $meta : $meta[$key];
    } 

    // Todo:: method to get calculate user balance
    public function calculateBalance(){
        $bills = \App\Models\Referral::whereHas('this_billing', function($query) { $query->where('status',1)->where('flag',1);})->where('referred_by', $this->id)->get();
        $total_earned = $bills->sum(function($bill) {
            return intval(optional($bill->this_billing)->total);
        });

        $successWithdraw = \App\Models\Cash\Txn::whereHas('billing_info', function($query) {$query->where('status',1)->where('flag',1)->where('type','withdraw')->where('user', $this->id);})->sum('dr');
        $transactionCost = \App\Models\Cash\Txn::whereHas('billing_info', function($query) {$query->where('status',1)->where('flag',1)->where('type','withdraw')->where('user', $this->id);})->where('type', 'transaction')->sum('cr');
        return $total_earned - $successWithdraw ;
    }

    protected static function boot()
    {
        parent::boot();

        // static::creating(function ($user) {
        //     do {
        //         $code = Str::upper(Str::random(10)); // Generates a unique 10-character code
        //     } while (User::where('referral_code', $code)->exists());

        //     $user->referral_code = $code;
        // });
        // static::creating(function ($user) {
        //     $user->referral_code = strtoupper(Str::random(6) . time()); // Appends a timestamp to ensure uniqueness
        // });

    }

}
