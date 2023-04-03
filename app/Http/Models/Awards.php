<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\AwardsCategories;

class Awards extends Model
{
    use SoftDeletes;
    
    protected $table = 'awards';
    protected $html = '';

    public $fillable = [
        'award_categories_id',
        'name_en',
		'name_ch',
		'other_awards_type_en',
		'other_awards_type_ch',
		'award_year',
        'reference_number',
		'status'
    ];
    public $timestamps = true;

	public function awardscategories() {
        return $this->hasOne(AwardsBadgesCategories::CLASS, 'id','award_categories_id');
    }
}
