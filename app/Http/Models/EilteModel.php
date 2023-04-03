<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EilteModel extends Model
{
    use SoftDeletes;
    protected $table = 'elite';
	protected $primaryKey = 'id';
	protected $html = '';

	protected $guarded = [];
	
	public function subeilte(){
		return $this->belongsTo('App\Http\Models\SubElite','elite_id','id');
	}

	public function userelite(){
		return $this->belongsTo('App\Http\Models\User','team','id');
	}

	public function subeteam(){
		return $this->belongsTo('App\Http\Models\Subteam','elite_id','id');
	}
}
