<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table = 'roles';

	protected $fillable = ['role_name','description','permission'];

	public function getRoles($id = null){
		$roles = new static();
		if($id){
			$result = $roles->select('*')
			->where('id',$id)
			->first()
			->orderBy('id','desc')
			->toArray();
		}else{
			$result = $roles->select('*')->orderBy('id','desc')->get()->toArray();
		}
		if($result){
			return $result;
		}else{
			return false;
		}
    }

    /**
	** USE : GET API Role
	**/
	public function getAPIRole(){
		$roles = new static();
		$result = $roles->select('id','role_name','description','permission')
		->where('role_name','!=','admin')
		->get()
		->toArray();
		return $result;
	}

}
