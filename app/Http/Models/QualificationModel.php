<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualificationModel extends Model
{
    use SoftDeletes;
	
	protected $table = 'qualification';
	protected $primaryKey = 'id';
	protected $html = '';

	protected $guarded = []; 

	public function userQualification(){
		return $this->hasOne('App\Http\Models\User','Qualification','id');
	}
}
