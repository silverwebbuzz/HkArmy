<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\SizeAttributes;
use App\Http\Models\ProductModel;

class ChildProduct extends Model
{   
    protected $table = 'child_products';
    public $fillable = [
        'main_product_id',
        'product_suffix',
        'product_suffix_name',
        'status'
    ];
    public $timestamps = true;

    public function SizeAttributes(){
        return $this->belongsTo(SizeAttributes::class, 'size_attributes_id', 'id');
    }

    public function Product() {
        return $this->hasOne(ProductModel::CLASS, 'id','main_product_id');
    }
}
