<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\ProductModel;
use App\Http\Models\ProductAssignModel;
use App\Http\Models\ProductCosttypeModel;
use App\Http\Models\ChildProduct;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignProductOrder extends Model
{
    use SoftDeletes;
    protected $table = 'assign_product_order';
	protected $primaryKey = 'id';

    protected $fillable = [
        'order_id','product_id','product_cost_type_id','child_product_id','order_date','created_by_user_id','status'
    ];
	
	// public function order(){
	// 	return $this->belongsTo('App\Http\Models\OrderModel','id','order_id');
	// }

	// public function orderdata(){
	// 	return $this->hasMany('App\Http\Models\OrderModel','id','order_id');
	// }

	public function product(){
		return $this->hasOne(ProductModel::class,'id','product_id');
	}

	public function ProductCostType(){
		return $this->hasOne(ProductCosttypeModel::class,'id','product_cost_type_id');
	}

	public function ProductAssignMembers(){
		return $this->hasMany(ProductAssignModel::class,'assign_product_order_id','id');
	}
	public function ChildProducts(){
		return $this->hasOne(childProduct::class,'id','child_product_id');
	}
}