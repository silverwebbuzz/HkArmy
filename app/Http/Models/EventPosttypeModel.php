<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventPosttypeModel extends Model
{
    use SoftDeletes;
    protected $table = 'event_post_type';
	protected $primaryKey = 'id';
	protected $html = '';
	public $timestamps = true;
	protected $guarded = [];

}
