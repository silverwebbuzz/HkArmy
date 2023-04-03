<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryLog extends Model
{
    use SoftDeletes;
	protected $table = 'history_team_rank_log';
	protected $primaryKey = 'id';
	protected $html = '';
}
