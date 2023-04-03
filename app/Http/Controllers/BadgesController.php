<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Helpers\Helper;
use App\Http\Models\Badges;
use App\Http\Models\BadgesTypes;
use App\Http\Models\AwardsBadgesCategories;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\BadgeAssign;
use App\Http\Models\User;

class BadgesController extends Controller
{
    public function index(){
        if(in_array('badges_management_read', Helper::module_permission(Session::get('user')['role_id']))){
            $Badges = [];
            $Badges = Badges::with('badgecategories')->orderBy('id', 'DESC')->get();            
            return view('Badges.badges_list',compact('Badges'));
        }else{
            return redirect('/');
        }
    }

    public function create(){
        return view('Badges.badges_add');
    }

    public function store(Request $request){
        if(in_array('badges_management_create', Helper::module_permission(Session::get('user')['role_id']))){
            $rules = [
                'name_en' => 'required',
                'name_ch' => 'required',
                'badges_type_id' => 'required',
                'current_team_member' => 'required',
                'badges_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ];
            $customMessages = [
                'required' => 'The :attribute field is required.'
            ];
        
            $this->validate($request, $rules, $customMessages);

            // Upload Badges Image
            $public_path = 'assets/badges_images';
            $badgesImagePath = '';
            if ($request->hasfile('badges_image')) {
                $image = $request->file('badges_image');
                $name = time() . $image->getClientOriginalName();
                $image->move(public_path($public_path), $name);
                $badgesImagePath = $public_path . '/' . $name;
            }

            $postData = [
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'badges_type_id' => $request->badges_type_id,
                'current_team_member' => $request->current_team_member,
                'badges_image' => ($badgesImagePath) ? $badgesImagePath : null,
                'other_badges_type_en' => ($request->badges_type_id == 0) ? $request->other_badges_type_en :null,
                'other_badges_type_ch' => ($request->badges_type_id == 0) ? $request->other_badges_type_ch : null,
                'status' => $request->status ?? 'active'
            ];
            $result = Badges::create($postData);
            if($result){
                return redirect('badges')->with('success_msg', 'Badges added successfully.');
            }else{
                return back()->with('error_msg', 'Problem was error accured.. Please try again..');
            }
        }else{
            return redirect('/');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id){
        if(in_array('badges_management_write', Helper::module_permission(Session::get('user')['role_id']))){
            $Badges = Badges::find($id);
            $AwardsBadgesCategories = new AwardsBadgesCategories;
            if(isset($Badges) && $Badges->current_team_member == 'mentor_team'){
                $get_badge_categories = $AwardsBadgesCategories->get_badge_mentor_categories($Badges->badges_type_id);
            }else{
                $get_badge_categories = $AwardsBadgesCategories->get_awards_categories('badge',$Badges->badges_type_id);
            }            
            return view('Badges.badges_edit',compact('Badges','get_badge_categories'));
        }else{
            return redirect('/');
        }
        
    }

    public function update(Request $request, $id){
        $rules = [
            'name_en' => 'required',
            'name_ch' => 'required',
            'badges_type_id' => 'required',
            'current_team_member' => 'required'
        ];
    
        $customMessages = [
            'required' => 'The :attribute field is required.'
        ];
    
        $this->validate($request, $rules, $customMessages);

        // Upload Badges Image
        $public_path = 'assets/badges_images';
        $badgesImagePath = '';
        if ($request->hasfile('badges_image')) {
            $image = $request->file('badges_image');
            $name = time() . $image->getClientOriginalName();
            $image->move(public_path($public_path), $name);
            $badgesImagePath = $public_path . '/' . $name;
            $postData = [
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'badges_type_id' => $request->badges_type_id,
                'current_team_member' => $request->current_team_member,
                'badges_image' => ($badgesImagePath) ? $badgesImagePath : null,
                'other_badges_type_en' => ($request->badges_type_id == 0) ? $request->other_badges_type_en :null,
                'other_badges_type_ch' => ($request->badges_type_id == 0) ? $request->other_badges_type_ch : null,
                'status' => $request->status ?? 'active'
            ];
        }else{
            $postData = [
                'name_en' => $request->name_en,
                'name_ch' => $request->name_ch,
                'badges_type_id' => $request->badges_type_id,
                'current_team_member' => $request->current_team_member,
                'other_badges_type_en' => ($request->badges_type_id == 0) ? $request->other_badges_type_en : null,
                'other_badges_type_ch' => ($request->badges_type_id == 0) ? $request->other_badges_type_ch : null,
                'status' => $request->status ?? 'active'
            ];
        }
        
        $result = Badges::find($id)->update($postData);
        if($result){
            return redirect('badges')->with('success_msg', 'Badges updated successfully.');
        }else{
            return back()->with('error_msg', 'Problem was error accured.. Please try again..');
        }
    }

    public function destroy($id){
        $result = Badges::find($id)->delete();
        if($result){
            $message = 'Badges deleted successfully..';
            $status = true;
        }else{
            $message = 'Please try again';
            $status = false;
        }
        return response()->json(['status' => $status,'message' => $message]);
    }

    public function getBadgeCategoriesByTeamMember(Request $request){
        $status = true;
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_badge_categories = '';
        if($request->mentor_type == 'mentor_team'){
            $get_badge_categories = $AwardsBadgesCategories->get_badge_mentor_categories();
        }else{
            $get_badge_categories = $AwardsBadgesCategories->get_awards_categories('badge');
        }
        return response()->json(['status' => $status, 'get_badge_categories' => $get_badge_categories]);
    }

    public function getAllBadgelist(Request $request){
        $html = '';
        $badgeList = Badges::select('id','name_en','name_ch')->get();
        $html .= '<div class="badges_main_cls">';
        $html .= '<div class="badges_select_cls"><fieldset class="form-group">';
        $html .= '<select class="form-control badge_id" id="badge_id" name="badge_id">';
        if(isset($badgeList) && !empty($badgeList)){
			// Product dropdown
            $html .= '<option value="">' . __('languages.badge_assign.select_badge') . '</option>';
            foreach ($badgeList as $badge) {
                $html .= '<option value="' . $badge->id . '">'.$badge->{'name_'.app()->getLocale()}.'</option>';
            }
        }else{
            $html .= '<option value="">No Any Badge Available</option>';
        }
        $html .= '</select>';
        $html .='</fieldset>'.
                '<div class="form-row events-id-cls1">'.
                    '<input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_badge">'.
                '</div></div></div>';
        return response()->json(['status' => 1, 'html' => $html]);
    }

    public function getBadgesCategoriesList(Request $request){
        $html = '';
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_badge_categories = '';
        if($request->mentor_type == 'mentor_team'){
            $get_badge_categories = $AwardsBadgesCategories->get_badge_mentor_categories();
        }else{
            $get_badge_categories = $AwardsBadgesCategories->get_awards_categories('badge');
        }
        
        $html .= '<div class="col-md-3 col-lg-3 col-sm-6">
                    <select class="form-control" id="badges_select_team_mentor" name="current_team_member">
                        <option value="">'.__('languages.member_type').'</option>';
                        if($request->mentor_type == 'mentor_team'){
                            $html .= '<option value="mentor_team" selected>'.__('languages.mentor_team').'</option>';
                        }else{
                            $html .= '<option value="mentor_team">'.__('languages.mentor_team').'</option>';
                        }
                        if($request->mentor_type == 'not_mentor_team'){
                            $html .= '<option value="not_mentor_team" selected>'.__('languages.not_mentor_team').'</option>';
                        }else{
                            $html .= '<option value="not_mentor_team">'.__('languages.not_mentor_team').'</option>';
                        }
            $html .='</select>
                    <span class="error badges_select_team_mentor"></span>
                </div>';
            $html .= '<div class="">';
                $html .= '<select class="form-control badge_id" id="badge_id" name="badge_id">';
                            if(isset($get_badge_categories) && !empty($get_badge_categories)){
                                $html .= $get_badge_categories;
                            }
                $html .= '</select>';
                $html .= '<span class="badges_select_error error"></span>';
            $html .= '</div>';
            $html .= '<div class="col-md-3 col-lg-3 col-sm-6 reference-number-cls">';
                $html .= '<input type="text" class="form-control" name="refrence_number" value="" id="reference_number" placeholder="'.__('languages.enter_reference_number').'">';
                    $html .= '<span class="reference_number_error error"></span>';
            $html .= '</div>';
            $html .= '<div class="col-md-3 col-lg-3 col-sm-6 issue-date-cls">';
                $html .= '<input type="date" class="form-control" id="issue_date" name="issue_date" spellcheck="false" data-ms-editor="true">';
                    $html .= '<span class="issue_date_error error"></span>';
            $html .= '</div>';
        $html .='<div class="events-id-cls1">';
            $html .= '<input type="button" class="btn btn-primary glow submit assign-user-cls" value="' . __('languages.event.Assign_user') . '" name="submit" data-type="assign_badge">';
        $html .= '</div>';
        return response()->json(['status' => 1, 'html' => $html]);
    }

    // Assign Badge to users
    public function badgeAssignUser(Request $request){
        $users = $request->user_id;
        if (!empty($users)) {
            if($request->member_type == 'not_mentor_team'){
                $isStatus = true;
                foreach ($users as $key => $user_id) {
                    $userdata = User::find($user_id);
                    if($userdata->team == '3' || $userdata->team == '4'){
                    }else{
                        $isStatus = false;
                        break;
                    }
                }
                if($isStatus == false){
                    return response()->json(['status' => false, 'message' => __('languages.please_select_only_non_mentor_member')]);
                    exit;
                }
            }

            if($request->member_type == 'mentor_team'){
                $isStatus = true;
                foreach ($users as $key => $user_id) {
                    if(User::where('ID',$request->user_id)->where('team',2)->exists()){
                        
                    }else{
                        $isStatus = false;
                        break;
                    }
                }
                if($isStatus == false){
                    return response()->json(['status' => false, 'message' => __('languages.please_select_only_mentor_member')]);
                    exit;
                }
            }

            foreach ($users as $key => $user_id) {
                if(BadgeAssign::where('user_id',$user_id)->where('badge_id',$request->badge_id)->doesntExist()){
                    BadgeAssign::create([
                        'user_id' => $user_id,
                        'badge_id' => $request->badge_id,
                        'reference_number' => $request->reference_number,
                        'issue_date' => date('Y-m-d',strtotime($request->issue_date)),
                        'assigned_date' => date('Y-m-d')
                    ]);
                }
            }
            return response()->json(['status' => true, 'message' => __('languages.badge_assign.badge_assigned_successfully')]);
        }else{
            return response()->json(['status' => false, 'message' => __('languages.badge_assign.please_select_atleast_one_member')]);
        }
    }

    /**
     * USE : Display all assigned badge member list
     */
    public function badgeAssignedMemberList(Request $request){
        //$BadgeMemberList = BadgeAssign::with('user')->with('badge')->get();
        $Model = BadgeAssign::with('user')->with('badge');
        if(isset($request->filter)){
            // Filter by issue date
            if(isset($request->badges_issue_date) && !empty($request->badges_issue_date)){
                $explodeDate = array_map('trim',explode('-',$request->badges_issue_date));
                if(!empty($explodeDate[0]) && !empty($explodeDate[1])){
                    $fromDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[0])));
                    $toDate = date('Y-m-d',strtotime(Helper::DateConvert('/','-',$explodeDate[1])));
                    $Model->whereBetween('issue_date', [$fromDate, $toDate]);
                }
            }

            // Search by award categories
            if(isset($request->badges_categories) && !empty($request->badges_categories)){
                $Model->where('badge_id',$request->badges_categories);
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
        $BadgeMemberList = $Model->orderBy('id','desc')->get();

        $membersList = User::where('Role_ID',2)->get();
        // Get categories Options
        $AwardsBadgesCategories = new AwardsBadgesCategories;
        $get_badge_categories = '';
        if(isset($request->member_type) && $request->member_type == 'mentor_team'){
            $get_badge_categories = $AwardsBadgesCategories->get_badge_mentor_categories(($request->badges_categories) ? $request->badges_categories : '');
        }else{
            $get_badge_categories = $AwardsBadgesCategories->get_awards_categories('badge', ($request->badges_categories) ? $request->badges_categories : '');
        }
        return view('Badges.assigned_badge_member_list',compact('BadgeMemberList','membersList','get_badge_categories'));
    }

    public function convertDateFormate($date){
        $date = str_replace('/', '-', date('d/m/Y',strtotime($date)));
        return date('Y-m-d', strtotime($date));
    }
}
