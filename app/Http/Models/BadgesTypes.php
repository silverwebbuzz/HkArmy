<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadgesTypes extends Model
{
    use SoftDeletes;
    
    protected $table = 'badges_type';
    protected $html = '';

    public $fillable = [
        'name_en',
        'name_ch',
        'status'
    ];
    public $timestamps = true;

    public function get_badges_type_select_list($badges_type_id = '', $requestType = ''){
        $Model = new static;
		$selected = '';
        $badges_type_name = 'name_'.app()->getLocale();

        // 1 = Skill Sets Badges
        $skillSetBadges = $Model->where('status','active')->where('type_id',1)->get()->toArray();
		if(!empty($skillSetBadges)){
			$this->html .= '<optgroup label="'.__('languages.badges.skill_sets_badges').'">';
			foreach($skillSetBadges as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($badges_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= ">".$value[$badges_type_name]."</option>";
			}
			$this->html .='</optgroup>';
		}

        // 2 = Discovery 
        $discoveryBadges = $Model->where('status','active')->where('type_id',2)->get()->toArray();
		if(!empty($discoveryBadges)){
			$this->html .= '<optgroup label="'.__('languages.badges.discovery').'">';
			foreach($discoveryBadges as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($badges_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= ">".$value[$badges_type_name]."</option>";
			}
			$this->html .='</optgroup>';
		}

        // 3 = Knowledge Award
        $knowledgeAwardBadges = $Model->where('status','active')->where('type_id',3)->get()->toArray();
		if(!empty($knowledgeAwardBadges)){
			$this->html .= '<optgroup label="'.__('languages.badges.knowledge_award').'">';
			foreach($knowledgeAwardBadges as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($badges_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= ">".$value[$badges_type_name]."</option>";
			}
			$this->html .='</optgroup>';
		}
        $this->html .= "<option value='other'";
        if($requestType == 'edit' && $badges_type_id == 0){
            $this->html .='selected';
        }
        $this->html .= ">".__('languages.badges.other')."</option>";
        return $this->html;
    }
}
