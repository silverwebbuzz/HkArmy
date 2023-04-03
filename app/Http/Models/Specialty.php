<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialty extends Model
{
    use SoftDeletes;
	
	protected $table = 'specialty';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = []; 
}
