<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
	// protected $table = 'permission';
	protected $table = 'roles';

	protected $fillable = ['created_by','description','role_name','permission'];



	public function getRoles($id = null){
		$roles = new static();
		if($id){
			$result = $roles->select('*')
			->where('id',$id)
			->first()
			->toArray();
		}else{
			$result = $roles->select('*')
			->get()
			->toArray();
		}
		if($result){
			return $result;
		}else{
			return false;
		}
	}

}
