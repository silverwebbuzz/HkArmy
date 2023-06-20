@extends('layouts.app')
@php
	$permissions = [];
	$role_id = Session::get('user')['role_id'];
	if($role_id){
		$module_permission = Helper::getPermissions($role_id);
		if($module_permission && !empty($module_permission['permission'])){
			$permissions = $module_permission['permission'];
		}
	}else{
		$permissions = [];
	}
@endphp
@section('content')
<!-- top navigation -->
@include('layouts.header')
<!-- /top navigation -->
@include('layouts.sidebar')

<div class="app-content content">
<div class="content-overlay"></div>
<div class="content-wrapper">
	<div class="content-header row">
		<div class="content-header-left col-12 mb-2 mt-1">
			<div class="row">
				<div class="col-12">
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.member.MemberList') }}</h3>
				</div>
			</div>
		</div>
	</div>
	<div class="content-body">
		<!-- users list start -->
		<section class="users-list-wrapper">
			<div class="users-list-filter px-1">
				@if(session()->has('success_msg'))
				<div class="alert alert-success">
					{{ session()->get('success_msg') }}
				</div>
				@endif
				@if(session()->has('error_msg'))
				<div class="alert alert-danger">
					{{ session()->get('error_msg') }}
				</div>
				@endif
                <form name="filterDateForm" id="filterDateForm">
				<input type="hidden" name="export_filter" value="">
				
				<div class="row border rounded py-2 mb-2">
					<div class="col-lg-2 col-md-2">
						
						<div class="button-group border rounded field_list_cls">
							<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span>{{ __('languages.member.Field') }} <span class="caret"></span></button>
							<ul class="dropdown-menu">
								<li>
									<input type="checkbox" id="all" name="customfilter[]" data-column="" class="checkbox-input filter-serach-cls-all" value="all">
									<label for="all">{{ __('languages.all') }}</label>
								</li>
								<li>
									<input type="checkbox" id="membercode" name="customfilter[]" data-column="2" class="checkbox-input filter-serach-cls" value="2" checked="checked">
									<label for="membercode">{{ __('languages.member.Member_Number') }}</label>
								</li>
								<li>
									<input type="checkbox" id="team" name="customfilter[]" data-column="3" class="checkbox-input filter-serach-cls" value="3" checked="checked">
									<label for="team">{{ __('languages.member.team') }}</label>
								</li>
								<li>
									<input type="checkbox" id="subteam" name="customfilter[]" data-column="4" class="checkbox-input filter-serach-cls" value="4" checked="checked">
									<label for="subteam">{{ __('languages.member.subteam') }}</label>
								</li>
								<li>
									<input type="checkbox" id="speical_ins" name="customfilter[]" data-column="5" class="checkbox-input filter-serach-cls" value="5">
									<label for="speical_ins">{{ __('languages.member.Special_instructor') }}</label>
								</li>
								<li>
									<input type="checkbox" id="speical_ins_text" name="customfilter[]" data-column="6" class="checkbox-input filter-serach-cls" value="6">
									<label for="speical_ins_text">{{ __('languages.member.Special_instructor_text') }}</label>
								</li>
								<li>
									<input type="checkbox" id="team_effective_date" name="customfilter[]" data-column="7" class="checkbox-input filter-serach-cls" value="7">
									<label for="team_effective_date">{{ __('languages.member.Team_effective_date') }}</label>
								</li>
								<li>
									<input type="checkbox" id="rank" name="customfilter[]" data-column="8" class="checkbox-input filter-serach-cls" value="8" checked="checked">
									<label for="rank">{{ __('languages.member.Rank') }}</label>
								</li>
								<li>
									<input type="checkbox" id="rank_effective_date" name="customfilter[]" data-column="9" class="checkbox-input filter-serach-cls" value="9">
									<label for="rank_effective_date">{{ __('languages.member.Rank_effective_date') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Reference_number" name="customfilter[]" data-column="10" class="checkbox-input filter-serach-cls" value="10">
									<label for="Reference_number">{{ __('languages.member.Reference_number') }}</label>
								</li>
								<li>
									<input type="checkbox" id="chinesename" name="customfilter[]" data-column="11" class="checkbox-input filter-serach-cls" value="11" checked="checked">
									<label for="chinesename">{{ __('languages.member.Chinese_name') }}</label>
								</li>
								<li>
									<input type="checkbox" id="englishname" name="customfilter[]" data-column="12" class="checkbox-input filter-serach-cls" value="12" checked="checked">
									<label for="englishname">{{ __('languages.member.English_name') }}</label>
								</li>
								<li>
									<input type="checkbox" id="ID_Number" name="customfilter[]" data-column="13" class="checkbox-input filter-serach-cls" value="13">
									<label for="ID_Number">{{ __('languages.member.ID_Number') }}</label>
								</li>
								<li>
									<input type="checkbox" id="gender" name="customfilter[]" data-column="14" class="checkbox-input filter-serach-cls" value="14" checked="checked">
									<label for="gender">{{ __('languages.member.Gender') }}</label>
								</li>
								<li>
									<input type="checkbox" id="age_filter" name="customfilter[]" data-column="15" class="checkbox-input filter-serach-cls" value="15" checked="checked">
									<label for="age">{{ __('languages.member.Age') }}</label>
								</li>
								<li>
									<input type="checkbox" id="dateofbirth" name="customfilter[]" data-column="16" class="checkbox-input filter-serach-cls" value="16">
									<label for="dateofbirth">{{ __('languages.member.date_of_birth') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Nationality" name="customfilter[]" data-column="17" class="checkbox-input filter-serach-cls" value="17">
									<label for="Nationality">{{ __('languages.member.Nationality') }}</label>
								</li>
								<li>
									<input type="checkbox" id="emailaddress" name="customfilter[]" data-column="18" class="checkbox-input filter-serach-cls" value="18" checked="checked">
									<label for="emailaddress">{{ __('languages.member.Email_address') }}</label>
								</li>
								<li>
									<input type="checkbox" id="contactnumber" name="customfilter[]" data-column="19" class="checkbox-input filter-serach-cls" value="19">
									<label for="contactnumber">{{ __('languages.member.Contact_number') }}</label>
								</li>
								<li>
									<input type="checkbox" id="contactnumber2" name="customfilter[]" data-column="20" class="checkbox-input filter-serach-cls" value="20">
									<label for="contactnumber2">{{ __('languages.member.Contact_number2') }}</label>
								</li>
								<li>
									<input type="checkbox" id="contactnumber3" name="customfilter[]" data-column="21" class="checkbox-input filter-serach-cls" value="21">
									<label for="contactnumber3">{{ __('languages.member.Contact_number3') }}</label>
								</li>
								<li>
									<input type="checkbox" id="chineseaddress" name="customfilter[]" data-column="22" class="checkbox-input filter-serach-cls" value="22">
									<label for="emailaddress">{{ __('languages.member.Chinese_address') }}</label>
								</li>
								<li>
									<input type="checkbox" id="englishaddress" name="customfilter[]" data-column="23" class="checkbox-input filter-serach-cls" value="23">
									<label for="emailaddress">{{ __('languages.member.English_address') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Occupation" name="customfilter[]" data-column="24" class="checkbox-input filter-serach-cls" value="24">
									<label for="Occupation">{{ __('languages.member.Occupation') }}</label>
								</li>
								<li>
									<input type="checkbox" id="education" name="customfilter[]" data-column="25" class="checkbox-input filter-serach-cls" value="25">
									<label for="education">{{ __('languages.member.Highest_Education') }}</label>
								</li>
								<li>
									<input type="checkbox" id="school_name" name="customfilter[]" data-column="26" class="checkbox-input filter-serach-cls" value="26">
									<label for="school_name">{{ __('languages.member.school_name') }}</label>
								</li>
								<li>
									<input type="checkbox" id="subject" name="customfilter[]" data-column="27" class="checkbox-input filter-serach-cls" value="27">
									<label for="subject">{{ __('languages.member.subject') }}</label>
								</li>
								<li>
									<input type="checkbox" id="activity" name="customfilter[]" data-column="28" class="checkbox-input filter-serach-cls" value="28">
									<label for="activity">{{ __('languages.member.Related_activity_experience') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Other_experience" name="customfilter[]" data-column="29" class="checkbox-input filter-serach-cls" value="29">
									<label for="Other_experience">{{ __('languages.member.Other_experience') }}</label>
								</li>
								<li>
									<input type="checkbox" id="health" name="customfilter[]" data-column="30" class="checkbox-input filter-serach-cls" value="30" checked="checked">
									<label for="health">{{ __('languages.member.Health_declaration') }}</label>
								</li>
								<li>
									<input type="checkbox" id="health_text" name="customfilter[]" data-column="31" class="checkbox-input filter-serach-cls" value="31" checked="checked">
									<label for="health">{{ __('languages.member.Health_declaration_text') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Emergency_Contact_Name" name="customfilter[]" data-column="32" class="checkbox-input filter-serach-cls" value="32">
									<label for="Emergency_Contact_Name">{{ __('languages.member.Emergency_Contact_Name') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Emergency_Number" name="customfilter[]" data-column="33" class="checkbox-input filter-serach-cls" value="33">
									<label for="Emergency_Number">{{ __('languages.member.Emergency_Number') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Relationship" name="customfilter[]" data-column="34" class="checkbox-input filter-serach-cls" value="34">
									<label for="Relationship">{{ __('languages.member.Relationship') }}</label>
								</li>
								<li>
									<input type="checkbox" id="joindate" name="customfilter[]" data-column="35" class="checkbox-input filter-serach-cls" value="35" checked="checked">
									<label for="joindate">{{ __('languages.member.Enqueue_date') }}</label>
								</li>
								<li>
									<input type="checkbox" id="remarks" name="customfilter[]" data-column="36" class="checkbox-input filter-serach-cls" value="36">
									<label for="remarks">{{ __('languages.member.remark') }}</label>
								</li>
								<li>
									<input type="checkbox" id="remarks_desc" name="customfilter[]" data-column="37" class="checkbox-input filter-serach-cls" value="37">
									<label for="remarks_desc">{{ __('languages.member.Remark_desc') }}</label>
								</li>
								<li>
									<input type="checkbox" id="remarks_date" name="customfilter[]" data-column="38" class="checkbox-input filter-serach-cls" value="38">
									<label for="remarks_date">{{ __('languages.member.Remark_date') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Member Status" name="customfilter[]" data-column="39" class="checkbox-input filter-serach-cls" value="39" checked="checked">
									<label for="Member Status">{{ __('languages.Status') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Last Activity" name="customfilter[]" data-column="40" class="checkbox-input filter-serach-cls" value="40">
									<label for="Last Activity">{{ __('languages.export_member.last_activity') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Hour Point" name="customfilter[]" data-column="41" class="checkbox-input filter-serach-cls" value="41">
									<label for="Hour Point">{{ __('languages.total_hours') }}</label>
								</li>
								<li>
									<input type="checkbox" id="Tokens" name="customfilter[]" data-column="42" class="checkbox-input filter-serach-cls" value="42">
									<label for="Tokens">{{ __('languages.member.Tokens') }}</label>
								</li>
								<li>
									<input type="checkbox" id="role" name="customfilter[]" data-column="25" class="checkbox-input filter-serach-cls" value="25">
									<label for="activity">{{ __('languages.member.Role') }}</label>
								</li>
								<li>
									<input type="checkbox" id="specialty" name="customfilter[]" data-column="43" class="checkbox-input filter-serach-cls" value="43">
									<label for="specialty">{{ __('languages.member.Specialty') }}</label>
								</li>
								<li>
									<label for="clear"></label>
									<a href="javascript:void(0);" class="clearsorting">{{ __('languages.Clear') }}</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="float-right align-items-center col-md-2">
						<label>{{ __('languages.member.team') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filterelite" name="filterelite">
								<option value="">{{ __('languages.member.select') }}</option>
								@if($Teams)
									@foreach($Teams as $team)
									<option value="{{ $team['id'] }}" @if(!empty($_GET['filterelite']) && $_GET['filterelite'] == $team['id']) selected @endif>{{ $team['elite_'.app()->getLocale()] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center col-md-2">
						<label>{{ __('languages.member.subteam') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filtersubteam" name="filtersubteam">
								<option value="">{{ __('languages.member.select') }}</option>
								@if($subteams)
									@foreach($subteams as $subteam)
									<option value="{{ $subteam['id'] }}" @if(!empty($_GET['filtersubteam']) && $_GET['filtersubteam'] == $subteam['id']) selected @endif>{{ $subteam['subteam_'.app()->getLocale()] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center col-md-3">
						<label>{{ __('languages.member.Rank') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filterrank" name="filterrank">
								<option value="">{{ __('languages.member.Select_rank') }}</option>
								@if($Ranks)
									@foreach($Ranks as $rank)
									<option value="{{ $rank['id'] }}"  @if(!empty($_GET['filterrank']) && $_GET['filterrank'] == $rank['id']) selected @endif>{{ $rank['subelite_'.app()->getLocale()] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group align-items-center col-md-3 filterrealtedactivity-cls">
						<label>{{ __('languages.member.Related_Activity_History') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filterrealtedactivity" name="filterrealtedactivity[]" multiple="multiple">
								@if(!empty($RelatedActivityHistory))
									@php
										$ActivityHistory = 'ActivityHistory_'.app()->getLocale();
									@endphp
									@foreach($RelatedActivityHistory as $val)
										<option value="{{ $val[$ActivityHistory] }}" {{ (!empty(request()->get('filterrealtedactivity')) && in_array($val[$ActivityHistory],request()->get('filterrealtedactivity')) ) ? 'selected' : ''}}> {{ $val[$ActivityHistory] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group align-items-center col-md-3 filterrealtedactivity-cls">
						<label>{{ __('languages.member.Specialty') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filterspecialty" name="filterspecialty[]" multiple="multiple">
								@if(!empty($Specialty))
									@php
										$specialty = 'specialty_'.app()->getLocale();
									@endphp
									@foreach($Specialty as $row)
										<option value="{{ $row[$specialty] }}" {{ (!empty(request()->get('filterspecialty')) && in_array($row[$specialty],request()->get('filterspecialty')) ) ? 'selected' : ''}}>{{ $row[$specialty] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group col-md-2">
						<label class="text-bold-600" for="Qualification">{{ __('languages.member.Qualification') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="filterqualification" name="filterqualification">
								<option value="">{{ __('languages.member.Select_Qualification') }}</option>
								@if($Qualification)
									@foreach($Qualification as $val)
									<option value="{{ $val['id'] }}" {{(request()->get('filterqualification') == $val['id']) ? 'Selected' : '' }}>{{ $val['qualification_'.app()->getLocale()] }}</option>
									@endforeach
								@endif
							</select>
						</fieldset>
					</div>
					<div class="form-group col-md-2">
						<label>{{ __('languages.member.join_date') }}</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control filterjoindate" id="filterjoindate" name="filterjoindate" placeholder="{{ __('languages.Select_date') }}" autocomplete="off" value="{{ request()->get('filterjoindate') }}">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="form-group col-md-2">
						<label>{{ __('languages.member.Hour_Point') }}</label>
						<input type="text" class="form-control filterhourpoint" id="filterhourpoint" name="filterhourpoint" placeholder="{{ __('languages.member.Hour_Point') }}" autocomplete="off" value={{ request()->get('filterhourpoint') }}>
					</div>
					<div class="form-group col-md-2">
						<label for="users-list-role">{{ __('languages.event.Tokens') }}</label>
						<fieldset class="form-group">
							<select class="form-control" id="costTypetoken" name="token">
								<option value="">{{ __('languages.event.Select_token') }} </option>
								<option value="1-25" @if(!empty($_GET['token']) && $_GET['token'] == '1-25') selected @endif>1-25</option>
								<option value="26-50" @if(!empty($_GET['token']) && $_GET['token'] == '26-50') selected @endif>26-50</option>
								<option value="51-100" @if(!empty($_GET['token']) && $_GET['token'] == '51-100') selected @endif>51-100</option>
								<option value="101-150" @if(!empty($_GET['token']) && $_GET['token'] == '101-150') selected @endif>101-150</option>
							</select>
						</fieldset>
					</div>
					<div class="form-group col-md-2">
						<label>{{ __('languages.member.date_of_birth') }}</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control filterdateofBirth" id="filterdateofBirth" name="dateofbirth" placeholder="{{ __('languages.Select_date') }}" autocomplete="off" value={{ request()->get('dateofbirth') }}>
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="form-group col-md-2">
						<label>{{ __('languages.member.Age') }}</label>
						<input type="text" class="form-control age" id="age" name="age" placeholder="{{ __('languages.member.Age') }}" autocomplete="off" list="ageList">
						<datalist id="ageList">
							<option value="15-25" {{ (request()->get('age') == '15-25') ? 'selected' : '' }}>15-25</option>
							<option value="25-35" {{ (request()->get('age') == '25-35') ? 'selected' : '' }}>25-35</option>
							<option value="35-45" {{ (request()->get('age') == '35-45') ? 'selected' : '' }}>35-45</option>
							<option value="45-55" {{ (request()->get('age') == '45-55') ? 'selected' : '' }}>45-55</option>
							<option value="55-65" {{ (request()->get('age') == '55-65') ? 'selected' : '' }}>55-65</option>
							<option value="65-75" {{ (request()->get('age') == '65-75') ? 'selected' : '' }}>65-75</option>
						</datalist>
						<span>{{ __('languages.member.note_please_enter_age_in_15_25_format') }}</span>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<label>{{ __('languages.member.Gender') }}</label>
							<select class="form-control" id="filter_gender" name="filter_gender">
								<option value="">{{ __('languages.UserManagement.gender') }} </option>
								<option value="1" {{( request()->get('filter_gender') == 1) ? 'selected' : ''}}>{{__('languages.member.Male')}}</option>
                                <option value="2" {{(request()->get('filter_gender')==2) ? 'selected' : ''}}>{{__('languages.member.Female')}}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<fieldset class="form-group">
							<label>{{ __('languages.Status') }}</label>
							<select class="form-control" id="user_status" name="user_status">
								<option value="">{{ __('languages.Status') }}</option>
								<option value="1" {{( request()->get('user_status') == 1) ? 'selected' : ''}}>{{__('languages.Active')}}</option>
                                <option value="2" {{( request()->get('user_status') == 2) ? 'selected' : ''}}>{{__('languages.Inactive')}}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1 col-lg-4 col-md-4 col-sm-4">
						<label>{{ __('languages.Search') }}</label>
						<fieldset class="form-group">
							<input type="text" class="form-control" id="search_text" name="search_text" placeholder="{{ __('languages.search_by')}} {{__('languages.UserManagement.email')}},{{__('languages.UserManagement.user_name')}},{{__('languages.UserManagement.contact_no') }}" autocomplete="off" value="{{request()->get('search_text')}}">
						</fieldset>
					</div>
					
					<div class="float-right align-items-center ml-1">
						<input type="submit" style="margin-top:25px" class="btn btn-primary glow submit" value="{{__('languages.Submit')}} " name="submit">
					</div>
					<div class="float-right align-items-center ml-1" style="margin-top:25px">
						<a href="{{ route('members') }}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
					</div>
					{{-- @if(in_array('event_management_create', Helper::module_permission(Session::get('user')['role_id']))) --}}
					<div class="float-right align-items-center ml-1" style="margin-top:25px">
						<a href="{{ route('users.create') }}" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.member.Add_Member') }}</a>
					</div>
					{{-- @endif --}}
				</div>
                </form>
			</div>
		
			{{-- Import Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center import-export-btn ml-1">
					<div class="multiple_user_status">
						<label>{{__('languages.update_multiple_user_status')}}</label>
						<select class="form-control" id="multiple_status_member" name="multiple_status_member">
							<option value="">{{__('languages.select_status')}}</option>
							<option value="1">{{__('languages.Active')}}</option>
							<option value="2">{{__('languages.Inactive')}}</option>
						</select>
					</div>
					<div class="multiple_status_member_section serach-member-btn">
						<a href="javascript:void(0);" class="btn btn-primary mb-0 member-export-csv" onclick="exportCSV()"> {{ __('languages.export_member_qrcode') }}</a>
						<!-- <a href="javascript:void(0);" class="btn btn-primary btn-block glow mb-0 export-qrcodes-btn">{{ __('languages.export_qrcode')}}</a> -->
						<a href="{{route('import-users')}}" class="btn btn-primary btn-block glow mb-0"> {{ __('languages.import') }} {{ __('languages.Member') }}</a>
						<a href="{{asset('uploads\sample_files\member.csv')}}">
							<button class="btn"><i class="bx bxs-download"></i>{{__('languages.download_sample_file')}}</button>
						</a>
					</div>
				</div>
			</div>
			{{-- Import Button End --}}
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body member-table-cls">
								<div class="dt-buttons d-flex">
								</div>
								</br>
								<div class="table-responsive event-search-list-cls">
									<table class="table member-list-table">
										<thead>
											<tr>
												<th class="member-ids-cls">
													<input type="checkbox" name="member_ids[]" class="dt-checkboxes-select-all" value="all">
												</th>
												{{-- @sortablelink('name',__('languages.grade')) --}}
												<th style="display:none;">@sortablelink('ID',__('languages.member.ID'))</th>
												<th> @sortablelink('MemberCode',__('languages.member.Member_Number'))</th>
												<th>@sortablelink('team',__('languages.member.team'))</th>
												<th>@sortablelink('elite_team',__('languages.member.subteam'))</th> 
												<th>@sortablelink('Specialty_Instructor',__('languages.member.Special_instructor'))</th>
												<th>@sortablelink('Specialty_Instructor_text',__('languages.member.Special_instructor_text')) </th>
												<th>@sortablelink('team_effiective_date',__('languages.member.Team_effective_date')) </th>
												<th>@sortablelink('rank_team',__('languages.member.Rank'))</th>
												<th>@sortablelink('rank_effiective_date',__('languages.member.Rank_effective_date')) </th>
												<th>@sortablelink('Reference_number',__('languages.member.Reference_number')) </th> 
												<th>@sortablelink('Chinese_name',__('languages.member.Chinese_name'))</th>
												<th>@sortablelink('English_name',__('languages.member.English_name'))</th>
												<th>@sortablelink('ID_Number',__('languages.member.ID_Number')) </th>
												<th>@sortablelink('Gender',__('languages.member.Gender'))</th>
												<th>@sortablelink('age',__('languages.member.Age')) </th>
												<th>@sortablelink('DOB',__('languages.member.date_of_birth')) </th>
												<th>@sortablelink('Nationality',__('languages.member.Nationality')) </th> 
												<th>@sortablelink('email',__('languages.member.Email_address')) </th>
												<th>@sortablelink('Contact_number',__('languages.member.Contact_number')) </th>
												<th>@sortablelink('Contact_number_1',__('languages.member.Contact_number2')) </th>
												<th>@sortablelink('Contact_number_2',__('languages.member.Contact_number3')) </th>
												<th>@sortablelink('Chinese_address',__('languages.member.Chinese_address')) </th>
												<th>@sortablelink('English_address',__('languages.member.English_address')) </th>
												<th>@sortablelink('Occupation',__('languages.member.Occupation')) </th> 
												<th>@sortablelink('Qualification',__('languages.member.Highest_Education')) </th> 
												<th>@sortablelink('School_Name',__('languages.member.school_name')) </th>
												<th>@sortablelink('Subject',__('languages.member.subject')) </th>
												<th>@sortablelink('Related_Activity_History',__('languages.member.Related_activity_experience'))</th>
												<th>@sortablelink('Other_experience',__('languages.member.Other_experience'))</th>
												<th>@sortablelink('Health_declaration',__('languages.member.Health_declaration'))</th>
												<th>@sortablelink('Health_declaration_text',__('languages.member.Health_declaration_text'))</th>
												<th>@sortablelink('Emergency_contact_name',__('languages.member.Emergency_Contact_Name')) </th>
												<th>@sortablelink('EmergencyContact',__('languages.member.Emergency_Number')) </th>
												<th>@sortablelink('Relationship',__('languages.member.Relationship')) </th>
												<th>@sortablelink('JoinDate',__('languages.member.join_date'))</th>
												<th>@sortablelink('Remarks',__('languages.member.remark')) </th>
												<th>@sortablelink('Remarks_desc',__('languages.member.Remark_desc')) </th>
												<th>@sortablelink('remark_date',__('languages.member.Remark_date')) </th>
												<th>@sortablelink('Status',__('languages.Status'))</th>
												<th>@sortablelink('lastactivity',__('languages.member.Last_Activity'))</th>
												<th>@sortablelink('hour_point',__('languages.total_hours')) </th> 
												<th>@sortablelink('member_token',__('languages.member.Tokens'))</th>
												<th>@sortablelink('Specialty',__('languages.member.Specialty'))</th>
												<!-- @if (in_array('members_write', $permissions)) -->
													<th>{{__('languages.member.generate_qr_code')}}</th>
												<!-- @endif -->
												<th>{{__('languages.member.Role')}}</th> 
												<th class="action-option">{{__('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if($userData)
												@foreach($userData as $val)
												@php
												$related_activity = unserialize($val['Related_Activity_History']);
												if(!empty($related_activity)){
													$related_activity_key = array_keys($related_activity);
													$related_activity_text = str_replace("_"," ",$related_activity_key);
												}
												$Specialty = unserialize($val['Specialty']);
												if(!empty($Specialty)){
													$Specialty_key = array_keys($Specialty);
													$Specialty_text = str_replace("_"," ",$Specialty_key);
												}
												@endphp 
													<tr id="{{ $val['ID'] }}" class="user-id-cls">
														<td class="member-ids-cls">
															<input type="checkbox" name="member_ids[]" class="dt-checkboxes" value="{{$val['ID']}}">
														</td>														
														@if (in_array('members_write', $permissions))
															<td><a href="{{ route('users.edit',$val['ID']) }}">C{{ $val['MemberCode'] }}</a></td>
														@else
															<td>C{{ $val['MemberCode'] }}</td>
														@endif

														@if(!empty($val['elite']))
															<td>{{ $val['elite']['elite_'.app()->getLocale()] }}</td>
														@else
															<td></td>
														@endif

														@if(!empty($val['subteam']))
															<td>{{ $val['subteam']['subteam_'.app()->getLocale()] }}</td>
														@else
															<td></td>
														@endif 

														@if($val['Specialty_Instructor'] == "1")
															<td>{{ __('languages.Yes') }}</td>
														@else
															<td>{{ __('languages.No') }}</td>
														@endif 

														<td>{{ $val['Specialty_Instructor_text'] }}</td>
														<td>{{ date('d/m/Y',strtotime($val['team_effiective_date'])) }}</td>

														@if(!empty($val['rank']))
															<td>{{ $val['rank']['subelite_'.app()->getLocale()] }}</td>
														@else
															<td></td>
														@endif 

														<td>{{$val['rank_effiective_date']}}</td>
														<td>{{ $val['Reference_number'] }}</td> 
														<td>{{ $val['Chinese_name'] ?? $val['English_name'] }}</td>
														<td>{{ $val['English_name'] }}</td>
														<td>{{ $val['ID_Number'] }}</td>

														@if($val['Gender'] == '1')
															<td>{{ __('languages.member.Male') }}</td>
														@else
															<td>{{ __('languages.member.Female') }}</td>
														@endif

														<td>{{ $val['age'] }}</td>
														<td>{{$val['DOB']}}</td>
														<td>{{ $val['Nationality'] }}</td> 
														<td>{{ $val['email'] }}</td>
														<td>{{ $val['Contact_number'] }}</td>
														<td>{{ $val['Contact_number_1'] }}</td>
														<td>{{ $val['Contact_number_2'] }}</td>
														<td>{{ $val['Chinese_address'] }}</td>
														<td>{{ $val['English_address'] }}</td>
														<td>{{ $val['Occupation'] }}</td>

														@if(!empty($val['qualification']['qualification_'.app()->getLocale()]))
															<td>{{ $val['qualification']['qualification_'.app()->getLocale()] }}</td>
														@else
															<td></td>
														@endif 

														<td>{{ $val['School_Name'] }}</td>
														<td>{{ $val['Subject'] }}</td>

														@if(!empty($val['Related_Activity_History']))
															<td>{{ implode(',',$related_activity_text) }}</td>
														@else
															<td></td>
														@endif

														<td>{{ $val['Other_experience'] }}</td>

														@if($val['Health_declaration'] == '1')
															<td>{{ __('languages.Yes') }}</td>
														@else
															<td>{{ __('languages.No') }}</td>
														@endif

														<td>{{ $val['Health_declaration_text'] }}</td>
														<td>{{ $val['Emergency_contact_name'] }}</td>
														<td>{{ $val['EmergencyContact'] }}</td>
														@if($val['Relationship'] == 1)
															<td>{{ __('languages.member.Father_Son') }}</td>
														@elseif($val['Relationship'] == 2)
															<td>{{ __('languages.member.Mother_Son') }}</td>
														@elseif($val['Relationship'] == 3)
															<td>{{ __('languages.member.Father_Daugther') }}</td>
														@elseif($val['Relationship'] == 4)
															<td>{{ __('languages.member.Mother_Daugther') }}</td>
														@elseif($val['Relationship'] == 5)
															<td>{{ __('languages.member.Brother_sister') }}</td>
														@elseif($val['Relationship'] == 6)
															<td>{{ __('languages.member.other') }}</td>
														@else
															<td></td>
														@endif

														<td>{{$val['JoinDate']}}</td>
														@if(!empty($val['remarks']['remarks_'.app()->getLocale()]))
															<td>{{ $val['remarks']['remarks_'.app()->getLocale()] }}</td>
														@else
															<td></td>
														@endif

														<td>{{ $val['Remarks_desc'] }}</td>
														<td>{{ date('d/m/Y',strtotime($val['remark_date'])) }}</td> 

														<td>
															<fieldset class="">
																<select class="form-control" id="status_member" name="status_member" data-id="{{ $val['ID'] }}">
																	<option value="1"  @if($val['Status']== '1') selected @endif >{{ __('languages.Active') }}</option>
																	<option value="2" @if($val['Status']== '2') selected @endif>{{ __('languages.Inactive') }}</option>
																</select>
															</fieldset>
														</td>
														
														<td>{{ date('d/m/Y',strtotime($val['lastactivity'] )) }}</td> 
														<td>
															@php
															$hour = Helper::getMemberHours($val['ID']);
															@endphp
	
															@if(!empty($hour))
																<span title="{{__('languages.activity_hours')}} : {{$hour['activityHour']}}&#13;{{__('languages.training_hours')}} : {{$hour['trainingHour']}}&#13;{{__('languages.service_hours')}} : {{$hour['serviceHour']}}">{{$hour['totalHour']}}</span>
															@endif
														</td> 
														
														@php
														$memberToken = ($val['member_token'] + (Helper::countUsersExistingTokens($val['ID']))) ?? '---';
														@endphp
														<!-- <td>{{ $val['member_token'] ?? '-' }}</td> -->
														<td>{{ $memberToken ?? '-' }}</td>
														
														<td>
															@if(!empty($val['Specialty']))
																{{ implode(',',$Specialty_text) }}
															@else
																-----
															@endif
														</td>

														<!-- @if (in_array('members_write', $permissions)) -->
															<td><button type="button" class="qrBTN btn btn-primary glow">{{ __('languages.member.qr_code') }}</button></td>
														<!-- @endif -->
														
														@if($val['Role_ID'] == "2")
															<td>{{ __('languages.Member') }}</td>
														@else
															<td></td>
														@endif
														 
														
														<td class="action-option">
															@if (in_array('members_write', $permissions))
																<a href="{{ route('users.edit',$val['ID']) }}"><i class="bx bx-edit-alt"></i></a>
															@endif
															@if (in_array('members_write', $permissions))
																<a href="{{ route('users.show',$val['ID']) }}"><i class="bx bx-show-alt"></i></a>
															@endif
															@if (in_array('members_delete', $permissions))
																<a href="javascript:void(0);" data-id="{{ $val['ID'] }}" class="deleteMember"><i class="bx bx-trash-alt"></i></a>
															@endif
														</td>
													</tr>
												@endforeach
												@endif
											</tbody>
									</table>
									<div class="row">
										<div class="col-md-11 col-lg-11">{{__('languages.showing')}} {{($userData->firstItem()) ? $userData->firstItem() : 0}} {{__('languages.to')}} {{!empty($userData->lastItem()) ? $userData->lastItem() : 0}}
											{{__('languages.of')}}  {{$userData->total()}} {{__('languages.entries')}}
										</div>
										<div calss="col-md-1 col-lg-1">
											<form>
												<select id="pagination">
													<option value="10" @if(app('request')->input('items') == 10) selected @endif >10</option>
													<option value="20" @if(app('request')->input('items') == 20) selected @endif >20</option>
													<option value="25" @if(app('request')->input('items') == 25) selected @endif >25</option>
													<option value="30" @if(app('request')->input('items') == 30) selected @endif >30</option>
													<option value="40" @if(app('request')->input('items') == 40) selected @endif >40</option>
													<option value="50" @if(app('request')->input('items') == 50) selected @endif >50</option>
													<option value="{{$userData->total()}}" @if(app('request')->input('items') == $userData->total()) selected @endif >{{__('languages.all')}}</option>
												</select> 
											</form>
										</div>
									</div>
									{{$userData->appends($_GET)->links()}}
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

<!-- footer content -->

<!-- Start Change password Popup -->
<div class="modal" id="changeUserPwd" tabindex="-1" aria-labelledby="changeUserPwd" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog modal-lg" style="max-width: 50%;">
		<div class="modal-content">
			<form id="changepasswordUserFrom">	
				@csrf()
				<input type="hidden" value="" name="userId" id="changePasswordUserId">
				<div class="modal-header">
					<h4 class="modal-title w-100">{{__('languages.change_password')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">
					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<label class="text-bold-600" for="newPassword">{{__('languages.new_password')}}</label>
							<input type="password" class="form-control" name="newPassword" id="newPassword" placeholder="{{__('languages.new_password')}}" value="" maxlength="8">
							@if($errors->has('newPassword'))<span class="validation_error">{{ $errors->first('newPassword') }}</span>@endif
						</div>
					</div>
					<div class="form-row">
						<div class="col-lg-12 col-md-12">
							<label class="text-bold-600" for="confirmPassword">{{__('languages.confirm_password')}}</label>
							<input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="{{__('languages.confirm_password')}}" value="" maxlength="8">
							@if($errors->has('confirmPassword'))<span class="validation_error">{{ $errors->first('confirmPassword') }}</span>@endif
						</div>
					</div>
				</div>
				<div class="modal-footer btn-sec">
					<button type="button" class="btn btn-default close-userChangePassword-popup" data-dismiss="modal">{{__('languages.close')}}</button>
					<button type="submit" class="blue-btn btn btn-primary submit-change-password-form">{{__('languages.Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- End Change password Popup -->
@include('layouts.footer')
@include('NewMemberManagement.member_list_js')
@endsection
