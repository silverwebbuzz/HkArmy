<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModel extends Model
{
    use SoftDeletes;
    protected $table = 'product';
	protected $primaryKey = 'id';
	protected $html = '';

	public function cartproduct(){
		return $this->belongsTo('App\Http\Models\CartModel','product_id','id');
	}

	public function ProductAssignModel(){
		return $this->belongsTo('App\Http\Models\ProductAssignModel','product_id','id');
	}

	public function childProducts(){
		return $this->hasMany('App\Http\Models\ChildProduct','main_product_id','id');
	}

	public function productCostType(){
		return $this->hasMany('App\Http\Models\ProductCosttypeModel','product_id','id');
	}
	
}
