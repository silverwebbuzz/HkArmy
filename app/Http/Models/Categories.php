<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;
    
    protected $table = 'categories';
    public $fillable = [
        'name_en',
        'name_ch',
        'status'
    ];
    public $timestamps = true;
}
