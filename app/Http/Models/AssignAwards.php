<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\AwardsBadgesCategories;
use App\Http\Models\User;
class AssignAwards extends Model
{
    use SoftDeletes;

    protected $table = 'awards_assign';
    protected $html = '';

    public $fillable = [
        'user_id',
        'award_id',
        'reference_number',
        'issue_date',
		'status',
		'assigned_date'
    ];
    public $timestamps = true;

    public function user() {
        return $this->hasOne(User::CLASS, 'ID','user_id')->select('ID','English_name','Chinese_name');
    }

    public function award() {
        return $this->hasOne(AwardsBadgesCategories::CLASS, 'id','award_id');
    }
}
