<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'village',
        'district',
        'pincode',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'password',
        'uniqueCode',
        'referral_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'password' => 'hashed',
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($staff) {
            $staff->referral_code = static::generateUniqueReferralCode();
        });
    }
    
    private static function generateUniqueReferralCode()
    {
        do {
            $referralCode = 'STAFF' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        } while (static::where('referral_code', $referralCode)->exists());
        
        return $referralCode;
    }
    
    /**
     * Get the referrals for the staff member.
     */
    public function referrals()
    {
        return $this->hasMany(\App\Models\ReferralTracking::class, 'staff_id');
    }
    
    /**
     * Get the orders referred by the staff member.
     */
    public function referredOrders()
    {
        return $this->hasMany(\App\Models\Order::class, 'staff_id');
    }
}
