<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardsCategories extends Model
{
    use SoftDeletes;
    
    protected $table = 'awards_categories';
    protected $html = '';

    public $fillable = [
        'name_en',
        'name_ch',
        'type_id',
		'status'
    ];
    public $timestamps = true;

    public function get_awards_categories($awards_type_id = '', $requestType = ''){
        $Model = new static;
		$selected = '';
        $badges_type_name = 'name_'.app()->getLocale();

        // 1 = Skill Sets Badges
        $AwardsCategories = $Model->where('status','active')->where('type_id',1)->get()->toArray();
		if(!empty($AwardsCategories)){
			$this->html .= '<optgroup label="'.__('languages.awards.internal_awards_of_hong_kong_army_cadet_headquarter').'">';
			foreach($AwardsCategories as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($awards_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= ">".$value[$badges_type_name]."</option>";
			}
			$this->html .='</optgroup>';
		}

        // 2 = Discovery 
        $AwardsCategories = $Model->where('status','active')->where('type_id',2)->get()->toArray();
		if(!empty($AwardsCategories)){
			$this->html .= '<optgroup label="'.__('languages.awards.awards_received_on_behalf_of_hk_army_cadet').'">';
			foreach($AwardsCategories as $value){
				$this->html .= "<option value='".$value['id']."'";
				if($awards_type_id == $value['id']){
					$this->html .='selected';
				}
				$this->html .= ">".$value[$badges_type_name]."</option>";
			}
			$this->html .='</optgroup>';
		}

		// Other Options
        $this->html .= "<option value='other'";
        if($requestType == 'edit' && $awards_type_id == 0){
            $this->html .='selected';
        }
        $this->html .= ">".__('languages.awards.other')."</option>";
        return $this->html;
    }
}
