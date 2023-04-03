<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\AwardsBadgesCategories;

class Badges extends Model
{
    use SoftDeletes;
    
    protected $table = 'badges';

    public $fillable = [
        'badges_type_id',
        'current_team_member',
        'name_en',
        'name_ch',
        'badges_image',
        'other_badges_type_en',
        'other_badges_type_ch',
        'status'
    ];
    public $timestamps = true;

    public function badgecategories() {
        return $this->hasOne(AwardsBadgesCategories::CLASS, 'id','badges_type_id');
    }
}
