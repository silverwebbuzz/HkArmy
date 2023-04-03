<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subteam extends Model
{
    use SoftDeletes;
    protected $table = 'subteam';
	protected $primaryKey = 'id';
	protected $html = '';

	protected $guarded = [];

	public function elite(){
		return $this->hasOne('App\Http\Models\EilteModel','id','elite_id');
	}

	public function users(){
		return $this->belongsTo('App\Http\Models\User','elite_team','id');	
	}
}
