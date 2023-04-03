<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberTokenStatus extends Model
{
   	protected $table = 'member_token_status';
	protected $primaryKey = 'id';
	protected $html = '';

	public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

}