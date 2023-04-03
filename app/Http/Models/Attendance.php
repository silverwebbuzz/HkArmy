<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;
	
	protected $table = 'attendance';
	protected $primaryKey = 'id';
	protected $html = '';


	public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

	public function event(){
		return $this->hasOne('App\Http\Models\Events','id','event_id');		
	}
	
	public function event_reports(){
		return $this->hasOne('App\Http\Models\Events','id','event_id');		
	}

	public function eventType(){
		return $this->hasOne('App\Http\Models\EventType','id','event_type');		
	}
}
