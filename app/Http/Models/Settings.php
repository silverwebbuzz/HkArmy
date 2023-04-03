<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
	protected $primaryKey = 'id';
	protected $html = '';
}
