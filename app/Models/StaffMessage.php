<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;

class StaffMessage extends Model
{
    protected $fillable = [
        'staff_id',
        'recipient_type',
        'subject',
        'message',
        'read_at',
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
    ];
    
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}