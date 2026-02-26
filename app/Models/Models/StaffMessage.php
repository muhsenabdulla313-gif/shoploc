<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Staff;

class StaffMessage extends Model
{
    protected $fillable = [
        'from_admin_id',
        'to_staff_id',
        'message',
        'subject',
        'is_read',
        'read_at',
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_admin_id');
    }
    
    public function recipient()
    {
        return $this->belongsTo(Staff::class, 'to_staff_id');
    }
}