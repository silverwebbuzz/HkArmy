<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Models\User;

class AuditLog extends Model
{
    use SoftDeletes;
	
	protected $table = 'audit_log';
	protected $primaryKey = 'id';
	protected $html = '';

	public function users(){
		return $this->hasOne(User::class,'ID','user_id');
	}
}
