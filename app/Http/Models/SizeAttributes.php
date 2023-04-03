<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SizeAttributes extends Model
{
    use SoftDeletes;
    
    protected $table = 'size_attribute';
    public $fillable = [
        'name_en',
        'name_ch',
        'status'
    ];
    public $timestamps = true;
}
