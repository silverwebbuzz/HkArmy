<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'module';
	protected $primaryKey = 'id';
	protected $html = '';
}
