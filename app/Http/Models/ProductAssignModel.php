<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAssignModel extends Model
{
    use SoftDeletes;
    protected $table = 'product_assign';
	protected $primaryKey = 'id';
	protected $html = '';

	protected $fillable = [
        'assign_product_order_id','product_id','child_product_id','user_id','remark','cost_type_id','token','money','status'
    ];

	public function users(){
		return $this->hasOne('App\Http\Models\User','ID','user_id');
	}

	public function product(){
		return $this->hasOne('App\Http\Models\ProductModel','id','product_id');		
	}

	public function childProducts(){
		return $this->hasOne('App\Http\Models\ChildProduct','id','child_product_id');
	}

	/**
	 * USE : Get assign model to cost method from assigning users
	 */
	public function ProductCosttypeModel(){
		return $this->hasOne('App\Http\Models\ProductCosttypeModel','id','cost_type_id');
	}
}
