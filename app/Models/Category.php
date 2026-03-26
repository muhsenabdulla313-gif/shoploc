<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{

protected $fillable = [
'name',
'slug',
'parent_id',
'status' 
];

public function children()
{
return $this->hasMany(Category::class,'parent_id');
}

public function parent()
{
return $this->belongsTo(Category::class,'parent_id');
}

}
