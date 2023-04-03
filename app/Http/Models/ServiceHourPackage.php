<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceHourPackage extends Model
{
	use SoftDeletes;
	
	protected $table = 'service_hour_package';
	protected $primaryKey = 'id';
	protected $html = '';
}
