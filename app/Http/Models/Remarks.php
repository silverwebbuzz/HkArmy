<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remarks extends Model
{
	use SoftDeletes;
	protected $table = 'remarks';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = []; 

	public function UserRemarks(){
		return $this->hasOne('App\Http\Models\User','Remarks','id');
	}
}
