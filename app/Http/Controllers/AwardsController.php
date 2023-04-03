<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Helpers\Helper;
use App\Http\Models\Awards;
use App\Http\Models\AwardsCategories;
use App\Http\Models\AwardsBadgesCategories;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\AssignAwards;
use DateTime;
use App\Http\Models\User;
class AwardsController extends Controller 
{
    public function index(){
        if(in_array('awards_management_read', Helper::module_permission(Session::get('user')['role_id']))){
            $AwardsData = [];
            $AwardsData = Awards::with('awardscategories')->orderBy('id', 'DESC')->get();
            return view('Awards.awards_list',compact('AwardsData'));
        }else{
            return redirect('/');
        }
    }

    public function create(){
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_awards_categories = $AwardsBadgesCategories->get_awards_categories('award');
        return view('Awards.awards_add',compact('get_awards_categories'));
    }

    public function store(Request $request){
        if(in_array('awards_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $rules = [
                'name_en' => 'required',
                'name_ch' => 'required',
                'award_categories_id' => 'required',
                'award_year' => 'required',
                'status' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field is required.'
            ];
        
            $this->validate($request, $rules, $customMessages);

            $postData = [
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'award_categories_id' => $request->award_categories_id,
                'other_awards_type_en' => $request->other_awards_type_en ?? null,
                'other_awards_type_ch' => $request->other_awards_type_ch ?? null,
                'award_year' => $request->award_year,
                'reference_number' => $request->reference_number ?? null,
                'status' => $request->status ?? 'active'
            ];
            $result = Awards::create($postData);
            if($result){
                return redirect('awards')->with('success_msg', 'Awards added successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function edit($id){
        $Awards = Awards::find($id);
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_awards_categories = $AwardsBadgesCategories->get_awards_categories('award',$Awards->award_categories_id);
        return view('Awards.awards_edit',compact('Awards','get_awards_categories'));
    }

    public function update(Request $request, $id){
        if(in_array('awards_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $rules = [
                'name_en' => 'required',
                'name_ch' => 'required',
                'award_categories_id' => 'required',
                'award_year' => 'required',
                'status' => 'required'
            ];
            $customMessages = [
                'required' => 'The :attribute field is required.'
            ];
        
            $this->validate($request, $rules, $customMessages);

            $other_awards_type_en = null;
            $other_awards_type_ch = null;
            if(!Helper::CheckCategoriesIsOther($request->award_categories_id)){
                $other_awards_type_en = $request->other_awards_type_en;
                $other_awards_type_ch = $request->other_awards_type_ch;
            }
            $postData = [
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'award_categories_id' => $request->award_categories_id,
                'other_awards_type_en' => $other_awards_type_en,
                'other_awards_type_ch' => $other_awards_type_ch,
                'award_year' => $request->award_year,
                'reference_number' => $request->reference_number ?? null,
                'status' => $request->status ?? 'active'
            ];
            $result = Awards::find($id)->update($postData);
            if($result){
                return redirect('awards')->with('success_msg', 'Awards updated successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function destroy($id){
        if(in_array('awards_management_delete', Helper::module_permission(Session::get('user')['role_id']))){
            $result = Awards::find($id)->delete();
            if($result){
                $message = 'Award deleted successfully..';
                $status = true;
            }else{
                $message = 'Please try again';
                $status = false;
            }
            return response()->json(['status' => $status,'message' => $message]);
        }else{
            return redirect('/');
        }
    }

    public function getAllAwardlist(Request $request){
        $html = '';
        $AwardList = Awards::select('id','name_en','name_ch')->get();
        $html .= '<div class="award_main_cls">';
        $html .= '<div class="award_select_cls"><fieldset class="form-group">';
        $html .= '<select class="form-control award_id" id="award_id" name="award_id">';
        if(isset($AwardList) && !empty($AwardList)){
			// Product dropdown
            $html .= '<option value="">' . __('languages.awards.select_award') . '</option>';
            foreach ($AwardList as $award) {
                $html .= '<option value="' . $award->id . '">'.$award->{'name_'.app()->getLocale()}.'</option>';
            }
        }else{
            $html .= '<option value="">No Any Awards Available</option>';
        }
        $html .= '</select>';
        $html .='</fieldset>'.
                '<div class="form-row events-id-cls1">'.
                    '<input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_award">'.
                '</div></div></div>';
        return response()->json(['status' => 1, 'html' => $html]);
    }

    /**
     * USE : Get Awards categories list
     */
    public function getAwardCategoriesList(Request $request){
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_awards_categories = $AwardsBadgesCategories->get_awards_categories('award');

        $html = '';
        $html .= '<div class="award_main_cls">';
            $html .= '<div class="award_select_cls">';
                    $html .= '<div class="award-selection col-md-3 col-lg-3 col-sm-6"><select class="form-control award_id" id="award_id" name="award_id">';
                    if(isset($get_awards_categories) && !empty($get_awards_categories)){
                        $html .= $get_awards_categories;
                    }else{
                        $html .= '<option value="">'.__('languages.no_any_awards_available').'</option>';
                    }
                    $html .= '</select>';
                    $html .= '<span class="award_select_error error"></span></div>';
                $html .= '<div class="col-md-3 col-lg-3 col-sm-6 reference-number-cls"><input type="text" class="form-control" name="refrence_number" value="" id="reference_number" placeholder="'.__('languages.enter_reference_number').'"><span class="reference_number_error error"></span></div>'.
                '<div class="col-md-3 col-lg-3 col-sm-6 issue-date-cls"><input type="date" class="form-control" id="issue_date" name="issue_date" spellcheck="false" data-ms-editor="true"><span class="issue_date_error error"></span></div>'.
                '<div class="events-id-cls1">'.
                    '<input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_award">'.
                '</div></div></div>';
        return response()->json(['status' => 1, 'html' => $html]);
    }

    // Assign Awards to users
    public function awardAssignUser(Request $request){
        $users = $request->user_id;
        if (!empty($users)) {
            foreach ($users as $key => $user_id) {
                //if(AssignAwards::where('user_id',$user_id)->where('award_id',$request->award_id)->doesntExist()){
                    AssignAwards::create([
                        'user_id' => $user_id,
                        'award_id' => $request->award_id,
                        'reference_number' => $request->reference_number,
                        'issue_date' => date('Y-m-d',strtotime($request->issue_date)),
                        'assigned_date' => date('Y-m-d')
                    ]);
                //}
            }
            return response()->json(['status' => true, 'message' => __('languages.award_assign.award_assigned_successfully')]);
        }else{
            return response()->json(['status' => false, 'message' => __('languages.award_assign.please_select_atleast_one_member')]);
        }
    }

    /**
     * USE : Display all assigned awaeds member list
     */
    public function awardAssignedMemberList(Request $request){
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_awards_categories = $AwardsBadgesCategories->get_awards_categories('award',($request->award_categories) ? $request->award_categories : '');
        $membersList = User::where('Role_ID',2)->get();
        $Model = AssignAwards::with('user')->with('award');
        if(isset($request->filter)){
            // Filter by issue date
            if(isset($request->award_issue_date) && !empty($request->award_issue_date)){
                $explodeDate = array_map('trim',explode('-',$request->award_issue_date));

                if(!empty($explodeDate[0]) && !empty($explodeDate[1])){
                    $fromDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[0])));
                    $toDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[1])));
                    $Model->whereBetween('issue_date', [$fromDate, $toDate]);
                }
            }
            // Search by award categories
            if(isset($request->award_categories) && !empty($request->award_categories)){
                $Model->where('award_id',$request->award_categories);
            }
            // Search by reference number
            if(isset($request->search_text) && !empty($request->search_text)){
                $Model->where('reference_number','like','%'.$request->search_text.'%');
            }
            // Filter by member
            if(isset($request->member_id) && !empty($request->member_id) && $request->member_id != 'all'){
                $Model->where('user_id',$request->member_id);
            }
        }
        $AwardsMemberList = $Model->orderBy('id','desc')->get();
        return view('Awards.assigned_awards_member_list',compact('AwardsMemberList','get_awards_categories','membersList'));
    }

    public function convertDateFormate($date){
        $date = str_replace('/', '-', date('d/m/Y',strtotime($date)));
        return date('Y-m-d', strtotime($date));
    }
}