<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItems extends Model
{
    use SoftDeletes;
    protected $table = 'order_items';
	protected $primaryKey = 'id';
	protected $html = '';

	public function order(){
		return $this->belongsTo('App\Http\Models\OrderModel','id','order_id');
	}

	public function orderdata(){
		return $this->hasMany('App\Http\Models\OrderModel','id','order_id');
	}

	public function getUsers(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');	
	}
}
