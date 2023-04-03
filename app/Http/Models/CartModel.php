<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartModel extends Model
{
    use SoftDeletes;
    protected $table = 'cart';
	protected $primaryKey = 'id';
	protected $html = '';

	public function getProduct(){
		return $this->hasOne('App\Http\Models\ProductModel','id','product_id');
	}
}
