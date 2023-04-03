<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Events extends Model
{
    use SoftDeletes;
	
	protected $table = 'events';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = [];

	public function attendance(){
		return $this->belongsTo('App\Http\Models\Attendance','event_id','id');
	}

	public function attend_event(){
		return $this->belongsTo('App\Http\Models\Attendance','event_id','id');
	}

	public function eventType(){
		return $this->hasOne('App\Http\Models\EventType','id','event_type');
	}
	public function attendanceevents(){
		return $this->belongsTo('App\Http\Models\EventSchedule','event_id','id');
	}
	
	public function memberUsedToken(){
		return $this->belongsTo('App\Http\Models\MemberUsedToken','event_id','id');
	}

	public function eventCostType(){
		return $this->hasMany('App\Http\Models\EventPosttypeModel','event_id','id');
	}
	public function eventschedule(){
		return $this->hasMany('App\Http\Models\EventSchedule','event_id','id');
	}
}
