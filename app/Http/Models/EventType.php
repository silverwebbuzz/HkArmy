<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventType extends Model
{
    use SoftDeletes;
    protected $table = 'event_type';
	protected $primaryKey = 'id';
	protected $html = '';
	protected $guarded = [];

	public function events(){
		return $this->belongsTo('App\Http\Models\Events','event_type','id');
	}
	public function attendance(){
		return $this->belongsTo('App\Http\Models\Attendance','event_type','id');
	}

	public function get_event_type_select_list($event_type_id = ''){
		$Model = new static;
		$selected = '';
		$EventType = 'event_type_name_'.app()->getLocale();
		$mainEvent_type = $Model->where('status','1')->where('type_id',0)->get()->toArray();
		if(!empty($mainEvent_type)){
			foreach($mainEvent_type as $row){
				$this->html .= "<option value='".$row['id']."'";
				if($event_type_id == $row['id']){
					$this->html .='selected';
				}
				$this->html .=" data-eventtypeparentid='".$row['type_id']."'>".$row[$EventType]."</option>";
			}
		}
		$TrainingEvent = $Model->where('status','1')->where('type_id',1)->get()->toArray();
		if(!empty($TrainingEvent)){
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			foreach($TrainingEvent as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($event_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$value['type_id']."'>".$value[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}

		$ActivityEvent = $Model->where('status','1')->where('type_id',2)->get()->toArray();
		if(!empty($ActivityEvent)){
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			foreach($ActivityEvent as $val){
				$this->html .= "<option value='".$val['id']."'";
				if($event_type_id == $val['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$val['type_id']."'>".$val[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}

		$service_events = $Model->where('status','1')->where('type_id',3)->get();
		if($service_events){
			$catrow = $service_events->toArray();
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			foreach($catrow as $row){
				$this->html .= "<option value='".$row['id']."'";
				if($event_type_id == $row['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$row['type_id']."'>".$row[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}
		return $this->html;
	}

	public function get_event_type_select_list_filter($event_type_id = ''){
		$Model = new static;
		$selected = '';
		$EventType = 'event_type_name_'.app()->getLocale();
		$mainEvent_type = $Model->where('status','1')->where('type_id',0)->get()->toArray();
		if(!empty($mainEvent_type)){
			foreach($mainEvent_type as $row){
				$this->html .= "<option value='".$row['id']."'";
				if($event_type_id == $row['id']){
					$this->html .='selected';
				}
				$this->html .=" data-eventtypeparentid='".$row['type_id']."'>".$row[$EventType]."</option>";
			}
		}
		$TrainingEvent = $Model->where('status','1')->where('type_id',1)->get()->toArray();
		if(!empty($TrainingEvent)){
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			foreach($TrainingEvent as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($event_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$value['type_id']."'>".$value[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}

		$ActivityEvent = $Model->where('status','1')->where('type_id',2)->get()->toArray();
		if(!empty($ActivityEvent)){
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			foreach($ActivityEvent as $val){
				$this->html .= "<option value='".$val['id']."'";
				if($event_type_id == $val['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$val['type_id']."'>".$val[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}

		$service_events = $Model->where('status','1')->where('type_id',3)->get();
		if($service_events){
			$catrow = $service_events->toArray();
			$this->html .= '<optgroup label="'.__('languages.event.Service').'">';
			$this->html .= "<option value='all_service'>".__('languages.event.all_services')."</option>";
			foreach($catrow as $row){
				$this->html .= "<option value='".$row['id']."'";
				if($event_type_id == $row['id']){
					$this->html .='selected';
				}
				$this->html .= " data-eventtypeparentid='".$row['type_id']."'>".$row[$EventType]."</option>";
			}
			$this->html .='</optgroup>';
		}
		return $this->html;
	}
}
