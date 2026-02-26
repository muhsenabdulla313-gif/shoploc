<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralTracking extends Model
{
    protected $table = 'referral_tracking'; // Specify the exact table name
    
    protected $fillable = [
        'staff_id',
        'referral_code',
        'referral_type',
        'amount',
        'referred_user_email',
        'used_at',
    ];

    public function staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'staff_id');
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'referred_user_email', 'email');
    }
}
