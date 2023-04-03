<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\AwardsCategories;
use App\Http\Models\AwardsBadgesCategories;
use App\Http\Models\User;
class BadgeAssign extends Model
{
    use SoftDeletes;
    
    protected $table = 'badge_assign';
    protected $html = '';

    public $fillable = [
        'user_id',
        'badge_id',
        'reference_number',
        'issue_date',
		'status',
		'assigned_date'
    ];
    public $timestamps = true;

    public function user() {
        return $this->hasOne(User::CLASS, 'ID','user_id')->select('ID','English_name','Chinese_name');
    }

    public function badge() {
        return $this->hasOne(AwardsBadgesCategories::CLASS, 'id','badge_id');
    }
}
