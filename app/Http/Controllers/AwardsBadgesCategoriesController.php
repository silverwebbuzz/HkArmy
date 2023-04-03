<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Helpers\Helper;
use App\Http\Models\AwardsBadgesCategories;
use Illuminate\Support\Facades\Validator;

class AwardsBadgesCategoriesController extends Controller {

    public function index(){
        if(in_array('award_badge_categories_management_read', Helper::module_permission(Session::get('user')['role_id']))){
            $CategoriesList = [];
            $CategoriesList = AwardsBadgesCategories::orderBy('id', 'DESC')->get();
            return view('award_badge_categories.award_badge_categories_list',compact('CategoriesList'));
        }else{
            return redirect('/');
        }
    }

    public function create(){
        return view('award_badge_categories.award_badge_categories_add');
    }

    public function store(Request $request){
        if(in_array('award_badge_categories_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $rules = [
                'name_en' => 'required',
                'name_ch' => 'required',
                'categories_type' => 'required',
            ];
            $customMessages = [
                'required' => 'The :attribute field is required.'
            ];
        
            $this->validate($request, $rules, $customMessages);

            if($request->categories_type == 'award'){
                $parentCategoriesId = ($request->parent_categories_id) ? $request->parent_categories_id : 0;
                $is_mentor_team_categories = null;
            }
            if($request->categories_type == 'badge'){
                if($request->categories_type == 'badge' && $request->is_team_member_mentor == 'yes'){
                    $parentCategoriesId = null;
                }else{
                    $parentCategoriesId = ($request->parent_categories_id) ? $request->parent_categories_id : 0;
                }
                $is_mentor_team_categories = $request->is_team_member_mentor;
            }

            $postData = [
                'categories_type' => $request->categories_type,
                'parent_categories_id' => $parentCategoriesId,
                'is_mentor_team_categories' => $is_mentor_team_categories,
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'status' => $request->status ?? 'active'
            ];
            
            if($request->categories_type == 'award'){
                $exists = AwardsBadgesCategories::where(['parent_categories_id' => $request->parent_categories_id,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                        ->first();   
            }

            if($request->categories_type == 'badge'){
                if($request->is_team_member_mentor == 'yes'){
                    $exists = AwardsBadgesCategories::where(['is_mentor_team_categories' => $request->is_team_member_mentor,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                        ->first(); 
                }else{
                    $exists = AwardsBadgesCategories::where(['is_mentor_team_categories' => $request->is_team_member_mentor,
                                                        'parent_categories_id' => $request->parent_categories_id,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                        ->first(); 
                }
            }

            if($exists){
                return back()->with('error_msg', __('languages.awards_badges_categories.categories_name_already_exists'));
            }

            $result = AwardsBadgesCategories::create($postData);
            if($result){
                return redirect('awards-badges-categories')->with('success_msg', __('languages.awards_badges_categories.award_badges_categories_added_successfully'));
            }else{
                return back()->with('error_msg', __('languages.awards_badges_categories.problem_was_error_accured'));
            }
        }else{
            return redirect('/');
        }
    }

    public function edit(Request $request, $id){
        $CategoriesData = AwardsBadgesCategories::find($id);
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $categories_list = $AwardsBadgesCategories->get_category_select_list($request, $CategoriesData);
        return view('award_badge_categories.award_badge_categories_edit',compact('CategoriesData','categories_list'));
    }

    public function update(Request $request, $id){
        if(in_array('award_badge_categories_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $rules = [
                'name_en' => 'required',
                'name_ch' => 'required',
                'categories_type' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field is required.'
            ];
        
            $this->validate($request, $rules, $customMessages);

            if($request->categories_type == 'award'){
                $exists = AwardsBadgesCategories::where(['parent_categories_id' => $request->parent_categories_id,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                    ->whereNotIn('id',$id)
                                                    ->first();   
            }

            if($request->categories_type == 'badge'){
                if($request->is_team_member_mentor == 'yes'){
                    $exists = AwardsBadgesCategories::where(['is_mentor_team_categories' => $request->is_team_member_mentor,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                        ->whereNotIn('id',$id)
                                                        ->first(); 
                }else{
                    $exists = AwardsBadgesCategories::where(['is_mentor_team_categories' => $request->is_team_member_mentor,
                                                        'parent_categories_id' => $request->parent_categories_id,
                                                        'name_en' => $request->name_en,
                                                        'name_ch' => $request->name_ch,
                                                        'status' => $request->status])
                                                        ->whereNotIn('id',$id)
                                                        ->first(); 
                }
            }

            if($exists){ // If record is exists thrn redirect to back page with error
                return back()->with('error_msg', __('languages.awards_badges_categories.categories_name_already_exists'));
            }

            if($request->categories_type == 'award'){
                $parentCategoriesId = ($request->parent_categories_id) ? $request->parent_categories_id : 0;
                $is_mentor_team_categories = null;
            }
            if($request->categories_type == 'badge'){
                if($request->categories_type == 'badge' && $request->is_team_member_mentor == 'yes'){
                    $parentCategoriesId = null;
                }else{
                    $parentCategoriesId = ($request->parent_categories_id) ? $request->parent_categories_id : 0;
                }
                $is_mentor_team_categories = $request->is_team_member_mentor;
            }

            $postData = [
                'categories_type' => $request->categories_type,
                'parent_categories_id' => $parentCategoriesId,
                'is_mentor_team_categories' => $is_mentor_team_categories,
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'status' => $request->status ?? 'active'
            ];
            $result = AwardsBadgesCategories::find($id)->update($postData);
            if($result){
                return redirect('awards-badges-categories')->with('success_msg', 'Awards & Badges categories updated successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function destroy($id){
        if(in_array('award_badge_categories_management_delete', Helper::module_permission(Session::get('user')['role_id']))){
            $checkChildCategoryData = AwardsBadgesCategories::where('parent_categories_id',$id)->get();
            if($checkChildCategoryData->isEmpty()){
                $result = AwardsBadgesCategories::find($id)->delete();
                if($result){
                    $message = 'Award & Badge Categories deleted successfully..';
                    $status = true;
                }else{
                    $message = 'Please try again';
                    $status = false;
                }
            }else{
                $message = 'You can not delete direct parent categories. Please first delete child category after that delete main category';
                $status = false;
            }

            return response()->json(['status' => $status,'message' => $message]);
        }else{
            return redirect('/');
        }
    }

    public function getAwardsBadgeCategoriesOptions(Request $request){
        $status = true;
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $categories_list = $AwardsBadgesCategories->get_category_select_list($request);
        return response()->json(['status' => $status, 'categories_list' => $categories_list]);
    }
}