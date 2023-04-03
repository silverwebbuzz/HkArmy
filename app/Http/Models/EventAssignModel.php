<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventAssignModel extends Model
{
    use SoftDeletes;
    protected $table = 'event_assign';
	protected $primaryKey = 'id';
	protected $html = '';

	/*public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

	public function product(){
		return $this->hasOne('App\Http\Models\ProductModel','id','product_id');		
	}*/

	public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

}
