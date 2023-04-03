<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTokenManage extends Model
{
    use SoftDeletes;

	protected $table = 'event_token_manage';
	protected $primaryKey = 'id';
    public $fillable = ['user_id','event_id','generate_token','used_token','remaining_token','status','expire_date'];
    public $timestamps = true;

    public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}


	public function event(){
		return $this->hasOne('App\Http\Models\Events','id','event_id');
	}

	public function EventSchedule(){
		return $this->hasOne('App\Http\Models\EventSchedule','id','event_id');
	}

	
}