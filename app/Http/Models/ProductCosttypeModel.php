<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCosttypeModel extends Model
{
    use SoftDeletes;
    protected $table = 'product_cost_type';
	protected $primaryKey = 'id';
	public $fillable = [
        'product_id',
        'cost_type',
        'cost_value'
    ];
	protected $html = '';
	public $timestamps = true;

}
