<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class User extends Model
{
    use SoftDeletes,Sortable;

	protected $guarded = [];  
	
	protected $table = 'users';
	protected $primaryKey = 'ID';
	protected $html = '';

	public $sortable = [
						'ID', 'UserName', 'email', 'password', 'Chinese_name', 'English_name', 'Contact_number', 'Contact_number_1', 'Contact_number_2', 
						'Gender', 'DOB', 'age', 'QrCode', 'HkidNumber', 'Address', 'MemberCode', 'team_effiective_date', 'team', 'elite_team', 
						'Specialty_Instructor', 'Specialty_Instructor_text', 'elite_text', 'district_text', 'rank_effiective_date', 
						'Reference_number', 'rank_team', 'rank_team_mentor', 'rank_elite_text', 'rank_district_text', 
						'Chinese_address', 'English_address', 'image', 'Nationality', 'Occupation', 'ID_Number', 'Qualification',
						 'note', 'School_Name', 'Subject', 'Related_Activity_History', 'Related_Activity_History_text', 
						 'is_other_experience', 'Other_experience', 'Specialty', 'Specialty_text', 'Health_declaration', 
						 'Health_declaration_text', 'Emergency_contact_name', 'EmergencyContact', 'Relationship', 
						 'Relationship_text', 'JoinDate', 'service_package_hour__id', 'Remarks', 'Remarks_desc', 
						 'remark_date', 'Remarks_Accident', 'Accident_date', 'discipline_issues_date', 'Appraisal_date', 
						 'others_date', 'Remarks_Discipline_Issues', 'Remarks_Appraisal', 'Remarks_Others', 'email_verified', 
						 'Role_ID', 'Attachment', 'hour_point', 'hour_point_rate', 'total_money', 'total_tokens', 'member_token', 
						 'Status', 'lastactivity'
						];

	public function attendance(){
		return $this->belongsTo('App\Http\Models\Attendance','user_id','ID');
	}

	public function elite(){
		return $this->hasOne('App\Http\Models\EilteModel','id','team');
	}

	public function Qualification(){
		return $this->hasOne('App\Http\Models\QualificationModel','id','Qualification');
	}

	public function Remarks(){
		return $this->hasOne('App\Http\Models\Remarks','id','Remarks');
	}

	public function orderUsers(){
		return $this->belongsTo('App\Http\Models\OrderModel','user_id','ID');
	}
	public function searchorderUsers(){
		return $this->belongsTo('App\Http\Models\OrderItems','user_id','ID');
	}
	public function rank(){
		return $this->hasOne('App\Http\Models\SubElite','id','rank_team');
	}
	public function subteam(){
		return $this->hasOne('App\Http\Models\Subteam','id','elite_team');
	}

	public function memberUsedToken(){
		return $this->belongsTo('App\Http\Models\MemberUsedToken','user_id','ID');
	}

	public function MemberTokenStatus(){
		return $this->hasOne('App\Http\Models\MemberTokenStatus','user_id','ID');
    }

    public function MemberToken(){
		return $this->hasOne('App\Http\Models\MemberToken','user_id','ID');
    }

    public function ProductAssignModel(){
		return $this->belongsTo('App\Http\Models\ProductAssignModel','user_id','ID');
	}

	public function EventAssignModel(){
		return $this->belongsTo('App\Http\Models\EventAssignModel','user_id','ID');
	}

	public function Role(){
		return $this->hasOne('App\Http\Models\RolePermission','id','Role_ID');
	}
}
