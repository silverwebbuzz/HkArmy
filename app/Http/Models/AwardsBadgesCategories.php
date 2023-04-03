<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AwardsBadgesCategories extends Model
{
    use SoftDeletes;
    
    protected $table = 'awards_badges_categories';
    protected $categoriesOptionHtml = '';

    public $fillable = [
        'categories_type',
        'parent_categories_id',
        'is_mentor_team_categories',
        'name_en',
		'name_ch',
		'status'
    ];

    public $timestamps = true;

    public function get_category_select_list($request, $editData = '', $catid = 0, $space = '', $repeat = 0){
        if($repeat == 0){
            $this->categoriesOptionHtml .= "<option value=''>Please select parent categories</option>";
            ++$repeat;
        }

        $Model = new static;
        if(isset($editData) && !empty($editData)){
            $result = $Model->select('*')->where('categories_type', $editData->categories_type)->where('parent_categories_id',$catid)->get();
        }else{
            $result = $Model->select('*')->where('categories_type', $request->categories_type)->where('parent_categories_id',$catid)->get();
        }
        
        $countRows = $result->count();

        if($catid === 0){
            $space = '';
        }else{
            $space .= "----";
        }

        if(isset($result) && !empty($result)){
            foreach($result as $row){
                if(isset($editData) && !empty($editData) && $row['id'] == $editData->parent_categories_id){
                    $this->categoriesOptionHtml .= "<option value='".$row['id']."' selected>".$space.$row['name_en']."</option>";
                }else{
                    $this->categoriesOptionHtml .= "<option value='".$row['id']."'>".$space.$row['name_en']."</option>";
                }
                
                $this->get_category_select_list($request, $editData, $row['id'], $space, $repeat);
            }
        }
        return $this->categoriesOptionHtml;
    }

    public function get_awards_categories($categoriesType='', $selectedCategoriesId = '', $catid = 0, $space = '', $repeat = 0){
        if($repeat == 0){
            $this->categoriesOptionHtml .= "<option value=''>".__('languages.select_categories')."</option>";
            ++$repeat;
        }
        $Model = new static;
        // if(isset($editData) && !empty($editData)){
        //     $result = $Model->select('*')->where('categories_type', $editData->categories_type)->where('parent_categories_id',$catid)->get();
        // }else{
            $result = $Model->select('*')->where('categories_type', $categoriesType)->where('parent_categories_id',$catid)->get();
        //}
        
        $countRows = $result->count();
        if($catid === 0){
            $space = '';
        }else{
            $space .= "----";
        }

        if(isset($result) && !empty($result)){
            foreach($result as $row){
                if(isset($selectedCategoriesId) && !empty($selectedCategoriesId) && $row['id'] == $selectedCategoriesId){                    
                    if($row['parent_categories_id'] == 0){
                        $this->categoriesOptionHtml .= '<optgroup label="'.$row['name_'.app()->getLocale()].'"></optgroup>';
                    }else{
                        $this->categoriesOptionHtml .= "<option value='".$row['id']."' data-catval='".$row['name_en']."' selected>".$space.$row['name_'.app()->getLocale()]."</option>";
                    }
                }else{
                    if($row['parent_categories_id'] == 0){
                        $this->categoriesOptionHtml .= '<optgroup label="'.$row['name_'.app()->getLocale()].'"></optgroup>';
                    }else{
                        $this->categoriesOptionHtml .= "<option value='".$row['id']."' data-catval='".$row['name_en']."'>".$space.$row['name_'.app()->getLocale()]."</option>";
                    }
                }
                $this->get_awards_categories($categoriesType, $selectedCategoriesId, $row['id'], $space, $repeat);
            }
        }
        return $this->categoriesOptionHtml;
    }

    // Get mentor categories
    public function get_badge_mentor_categories($selectedeCategoriesId = ''){
        $Model = new static;
        $result = $Model->select('*')->where('categories_type','badge')->where('is_mentor_team_categories','yes')->get();
        if(isset($result) && !empty($result)){
            $this->categoriesOptionHtml .= "<option value=''>".__('languages.select_categories')."</option>";
            foreach($result as $row){
                if(isset($selectedeCategoriesId) && $selectedeCategoriesId == $row['id']){
                    $this->categoriesOptionHtml .= "<option value='".$row['id']."' data-catval='".$row['name_en']."' selected>".$row['name_'.app()->getLocale()]."</option>";
                }else{
                    $this->categoriesOptionHtml .= "<option value='".$row['id']."' data-catval='".$row['name_en']."'>".$row['name_'.app()->getLocale()]."</option>";
                }
            }
        }
        return $this->categoriesOptionHtml;
    }

    public function getParentCategoriesName($id){
        $Model = new static;
        $result = $Model->find($id);
        return $result;
    }
}
