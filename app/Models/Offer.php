<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = ['title', 'image', 'alt_text', 'active', 'start_date', 'end_date'];
    
    protected $casts = [
        'active' => 'boolean',
    ];
}
