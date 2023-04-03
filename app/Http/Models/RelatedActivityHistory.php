<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedActivityHistory extends Model
{
    use SoftDeletes;
	
	protected $table = 'relatedactivityhistory';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = []; 
}
