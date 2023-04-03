<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderModel extends Model
{
    use SoftDeletes;
    protected $table = 'order';
	protected $primaryKey = 'id';
	protected $html = '';

	public function orderItems(){
		return $this->hasMany('App\Http\Models\OrderItems','order_id','id');
	}

	public function getUsers(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

	public function serachOrderItems(){
		return $this->belongsTo('App\Http\Models\OrderItems','order_id','id');
	}
}
