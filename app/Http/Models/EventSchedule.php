<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class EventSchedule extends Model
{
    protected $table = 'event_schedule';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = [];

	public function events(){
		return $this->hasOne('App\Http\Models\Events','id','event_id');
	}
}
