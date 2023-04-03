<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberUsedToken extends Model
{
   	
	protected $table = 'member_used_token';
	protected $primaryKey = 'id';
	protected $html = '';



	public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

	public function event(){
		return $this->hasOne('App\Http\Models\Events','id','event_id');		
	}

}