<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubElite extends Model
{
    use SoftDeletes;
    protected $table = 'subelite';
	protected $primaryKey = 'id';
	protected $html = '';
	
	protected $guarded = [];

	public function elite(){
		return $this->hasOne('App\Http\Models\EilteModel','id','elite_id');
	}

	public function users(){
		return $this->belongsTo('App\Http\Models\User','rank_team','id');
	}
}
