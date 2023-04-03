@extends('layouts.app')

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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.member.Edit_Member') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<form action="{{ route('users.update',$edit_user['ID']) }}" method="post" name="edit_member_form" id="edit_member_form" enctype="multipart/form-data">
								{{ method_field('PUT') }}
								<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
								<span class="user_id" data-id="{{ $edit_user['ID'] }}"></span>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.basic_information') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="memship_number">{{ __('languages.member.Member_Number') }}</label>
										<input type="text" class="form-control" id="memship_number" value="C{{ $edit_user['MemberCode'] }}" name="MemberCode" readonly="readonly">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role" class="text-bold-600">{{ __('languages.member.team') }}</label>
										<div class="mb-50 ">
											<fieldset class="form-group">
												<select class="form-control teamclass" id="team" name="team">
													<option value="">{{ __('languages.member.select') }}</option>
													@if($EilteModel)
													@php
														$elite = 'elite_'.app()->getLocale();
													@endphp
														@foreach($EilteModel as $val)
															<!-- <option value="{{ $val['id'] }}" @if($edit_user['team'] == $val['id']) selected @endif>{{ $val[$elite] }}</option> -->
															<option value="{{ $val['id'] }}">{{ $val[$elite] }}</option>
														@endforeach
													@endif
												</select>
											</fieldset>
										</div>
										<div class="elite-team-cls"></div> <!-- dont't remove class -->
										<div class="mb-50">
											<div class="mb-50 effective-date-cls">
												<label class="text-bold-600" for="users-list-role">{{ __('languages.member.effective_date') }}</label>
												<input type="text" class="form-control pickadate" placeholder="{{ __('languages.member.effective_date') }}" name="team_effiective_date" id="team_effective_date" value="">
											</div>
										</div>
										<div class="mb-50 elite_team-class">
											<!-- <fieldset class="form-group">
												<select class="form-control" id="elite_team" name="elite_team">
													<option value="">{{ __('languages.member.select') }}</option>
													@if($SubElite)
													@php
														$subelite = 'subelite_'.app()->getLocale();
													@endphp
														@foreach($SubElite as $val)
															<option value="{{ $val['id'] }}" @if($edit_user['elite_team'] == $val['id']) selected @endif>{{ $val[$subelite] }}</option>
														@endforeach
													@endif
												</select>
											</fieldset> -->
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="header pt-1 pb-1 col-md-6">
									</div>
									<div class="header pt-1 pb-1 col-md-6 history-team-clss">
										@if(!empty($audit_log))
										<label class="text-bold-600">{{ __('languages.member.history_of_team') }}</label>
										<table>
											<tbody>
												@foreach($audit_log as $val)
													@if(!empty($val->teameilte_log))
														@php
															$history_team = json_decode($val->teameilte_log);
															$elite = '';
															$subteam = '';
															if (property_exists($history_team,"team")){
																if(!empty($history_team)){
																	$elite = Helper::geteliteData($history_team->team);
																}
															}
															if (property_exists($history_team,"elite_team")){
																if(!empty($history_team)){
																	$subteam = Helper::getSubteamData($history_team->elite_team);
																}
															}
														@endphp
														@if(!empty($val->team_status) && $val->team_status == "1")
															<tr>
																<td>{{ $elite }} : {{ $subteam }} - {{ $history_team->team_effiective_date }}</td>
																<td><a href="javascript:void(0);" data-id="{{ $val->id }}" class="history-team-rank-cls" data-log="team"><i class="bx bx-trash-alt"></i></a></td>
															</tr>
														@endif
													@endif
												@endforeach
											</tbody>
										</table>
										@endif
									</div>
								</div>
								<div class="header pt-1 pb-1 col-md-6">
									<h4 class="card-title">{{ __('languages.member.Rank_Promotion') }} </h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="rank_effective_date">{{ __('languages.member.effective_date') }}</label>
										<input type="text" class="form-control rank_effective_date" placeholder="{{ __('languages.member.effective_date') }}" id="rank_effective_date" name="rank_effiective_date" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="reference_number">{{ __('languages.member.Reference_number') }}</label>
										<input type="text" class="form-control" id="reference_number" name="Reference_number" placeholder="{{ __('languages.member.Reference_number') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<div class="rank-team-cls"></div> <!-- dont't remove class -->
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
									@if(!empty($audit_log))
									<label class="text-bold-600">{{ __('languages.member.history_of_rank') }}</label>
										<table>
											<tbody>
												@foreach($audit_log as $val)
													@if(!empty($val->rank_log))
														@php
															$history_rank = json_decode($val->rank_log);
															$rank_team = '';
															if (property_exists($history_rank,"rank_team")){
																if($history_rank->rank_team != ''){
																	$rank_team = Helper::getSubeliteData($history_rank->rank_team);
																}
															}
														@endphp
														@if(!empty($val->rank_status) && $val->rank_status == "1")
															<tr>
																<td>{{ $rank_team}} - {{ date('d F,Y',strtotime($history_rank->rank_effiective_date)) }}, {{ $history_rank->Reference_number }}</td>
																<td><a href="javascript:void(0);" data-id="{{ $val->id }}" class="history-team-rank-cls" data-log="rank"><i class="bx bx-trash-alt"></i></a></td>
															</tr>
														@endif
													@endif
												@endforeach
											</tbody>
										</table>
										@endif
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Personal_Information') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chinese_name">{{ __('languages.member.Chinese_name') }}</label>
										<input type="text" class="form-control" id="chinese_name" name="Chinese_name" placeholder="{{ __('languages.member.Chinese_name') }}" value="{{ $edit_user['Chinese_name'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="english_name">{{ __('languages.member.English_name') }}</label>
										<input type="text" class="form-control" id="english_name" name
										="English_name" placeholder="{{ __('languages.member.English_name') }}" value="{{ $edit_user['English_name'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="dob">{{ __('languages.member.date_of_birth') }}</label>
										<fieldset class="form-group position-relative has-icon-left">
											<input type="text" class="form-control" placeholder="{{ __('languages.member.date_of_birth') }}" name="DOB" id="dob" value="{{ date('d/m/Y',strtotime($edit_user['DOB'])) }}">
											<div class="form-control-position">
												<i class='bx bx-calendar'></i>
											</div>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="exampleInputUsername1">{{ __('languages.member.Gender') }}</label>
										<ul class="list-unstyled mb-0">
											<li class="d-inline-block mt-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="Gender" id="customRadio4" value="1" @if($edit_user['Gender'] == "1") checked="" @endif>
														<label class="custom-control-label" for="customRadio4">{{ __('languages.member.Male') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="Gender" id="customRadio3" value="2"  @if($edit_user['Gender'] == "2") checked @endif>
														<label class="custom-control-label" for="customRadio3">{{ __('languages.member.Female') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
										<div class="gender-error-cls"></div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="age">{{ __('languages.member.Age') }}</label>
										<input type="text" class="form-control" id="age" name="age" placeholder="{{ __('languages.member.Age') }}" value="" readonly="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="email_address">{{ __('languages.member.Email_address') }}</label>
										<input type="text" class="form-control" id="email_address" name="email" placeholder="{{ __('languages.member.Email_address') }}" value="{{ $edit_user['email'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number">{{ __('languages.member.Contact_number') }}</label>
										<input type="text" class="form-control" id="edit_contact_number" name="Contact_number" placeholder="Enter {{ __('languages.member.Contact_number') }}" value="{{ $edit_user['Contact_number'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number_2">{{ __('languages.member.Contact_number2') }}</label>
										<input type="text" class="form-control" id="contact_number_2" name="Contact_number_1" placeholder="{{ __('languages.member.Contact_number2') }}" value="{{ $edit_user['Contact_number_1'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number_3">{{ __('languages.member.Contact_number3') }}</label>
										<input type="text" class="form-control" id="contact_number_3" name="Contact_number_2" placeholder="{{ __('languages.member.Contact_number3') }}" value="{{ $edit_user['Contact_number_2'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chinese_address">{{ __('languages.member.Chinese_address') }}</label>
										<input type="text" class="form-control" id="chinese_address" name="Chinese_address" placeholder="{{ __('languages.member.Chinese_address') }}" value="{{ $edit_user['Chinese_address'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="English_address">{{ __('languages.member.English_address') }}</label>
										<input type="text" class="form-control" id="English_address" name
										="English_address" placeholder="{{ __('languages.member.English_address') }}" value="{{ $edit_user['English_address'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="nationality">{{ __('languages.member.Nationality') }}</label>
										<input type="text" class="form-control" id="nationality" name="Nationality" placeholder="" value="{{ $edit_user['Nationality'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="occupation">{{ __('languages.member.Occupation') }}</label>
										<input type="text" class="form-control" id="occupation" name="Occupation" placeholder="" value="{{ $edit_user['Occupation'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="id_number">{{ __('languages.member.ID_Number') }}</label>
										<input type="text" class="form-control" id="id_number" name="ID_Number" placeholder="" value="{{ $edit_user['ID_Number'] }}">
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Highest_Education') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Qualification">{{ __('languages.member.Qualification') }}</label>
										<fieldset class="form-group">
											<select class="form-control highereducation-cls" id="qualification" name="Qualification">
												<option value="">{{ __('languages.member.Select_Qualification') }}</option>
												@if($Qualification)
													@php
														$qualification = 'qualification_'.app()->getLocale();
													@endphp
													@foreach($Qualification as $val)
														<option value="{{ $val['id'] }}"  @if($edit_user['Qualification']==$val['id']) selected="" @endif>{{ $val[$qualification] }}</option>
													@endforeach
												@endif
											</select>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50 notecls" @if($edit_user['Qualification']=='10') style="display: block;" @else  style="display: none;" @endif>
										<label class="text-bold-600" for="please_note">{{ __('languages.member.please_note') }}</label>
										<input type="text" class="form-control" id="please_note" name="note" placeholder="please note" value="{{ $edit_user['note'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="school_name">{{ __('languages.member.school_name') }}</label>
										<input type="text" class="form-control" id="school_name" name
										="School_Name" placeholder="" value="{{ $edit_user['School_Name'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="subject">{{ __('languages.member.subject') }}</label>
										<input type="text" class="form-control" id="subject" name
										="Subject" placeholder="" value="{{ $edit_user['Subject'] }}">
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Activity_Experience_Skills') }}</h4>
								</div>
								@php
									$ActivityHistory = 'ActivityHistory_'.app()->getLocale();
									$related_activity = unserialize($edit_user['Related_Activity_History']);
									$Specialty_data = unserialize($edit_user['Specialty']);
									$specialty = 'specialty_'.app()->getLocale();
								@endphp
								<div class="form-row">
									<div class="form-group member-activity-cls col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.member.Related_Activity_History') }}</label>
										<fieldset class="form-group">
											<span class="multiselect-native-select">
												<div class="btn-group">
													<button type="button" class="multiselect dropdown-toggle btn btn-default" data-toggle="dropdown" title=" Hong Kong University Student Military Life Experience Camp,  Youth 
													Military Summer Camp,  Youth Moral Education Training Course" aria-expanded="false">
														<span class="multiselect-selected-text related_value_selected_cls">{{ __('languages.member.None_selected') }}</span>
														<b class="caret"></b>
													</button>
													<ul class="multiselect-container dropdown-menu" x-placement="bottom-start" style="">
														@if(!empty($RelatedActivityHistory))
															@foreach($RelatedActivityHistory as $val)
																<li class="">
																	<a><label class="checkbox" title='tets13'>
																	<input type="checkbox" value="{{ $val[$ActivityHistory] }}" name="relatedactivity[]" class="checkboxClass" @if (!empty($related_activity) && array_key_exists($val[$ActivityHistory],$related_activity)) checked @endif>{{ $val[$ActivityHistory] }}
																	</label>
																	<input type="text" class="custom-input valid" name="data[{{ $val[$ActivityHistory] }}][]" value=" @if ( !empty($related_activity) && array_key_exists($val[$ActivityHistory],$related_activity)) {{ $related_activity[$val[$ActivityHistory]] }} @endif" aria-invalid="false">
																	</a>
																</li>
															@endforeach
														@endif
													</ul>
												</div>
											</span>
										</fieldset>
									</div>
									<div class="form-group col-md-3 mb-50">
										<label class="text-bold-600" for="otherexperience">{{ __('languages.member.Other_experience') }}</label>
										<ul class="list-unstyled mb-0">
											<li class="d-inline-block mt-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="otherexperience" id="other_experience_yes" value="1" @if($edit_user['is_other_experience'] == "1") checked="" @endif>
														<label class="custom-control-label" for="other_experience_yes">{{ __('languages.member.Yes') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="otherexperience" id="other_experience_no" value="2" @if($edit_user['is_other_experience'] == "2") checked="" @endif>
														<label class="custom-control-label" for="other_experience_no">{{ __('languages.member.No') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
									</div>
									<div class="form-group col-md-3 mb-50">
										<div class="form other-exp-cls" @if($edit_user['is_other_experience'] == "1") style="display: block;" @else style="display: none;" @endif>
											<div class="form-group col-md-12 mb-50">
												<label class="text-bold-600" for="other_experience_text"></label>
												<input type="text" class="form-control" id="other_experience_text" name="other_experience_text" placeholder="{{ __('languages.member.Other_experience') }}" value="{{ $edit_user['Other_experience'] }}">
											</div>
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group member-activity-cls col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.member.Specialty') }}</label>
										<fieldset class="form-group">
											<span class="multiselect-native-select">
												<div class="btn-group">
													<button type="button" class="multiselect dropdown-toggle btn btn-default" data-toggle="dropdown" title="None selected"><span class="multiselect-selected-text specialty_value_selected_cls">{{ __('languages.member.None_selected') }}</span> <b class="caret"></b></button>
													<ul class="multiselect-container dropdown-menu">
														@if(!empty($Specialty))
															@foreach($Specialty as $row)
																<li>
																	<a><label class="checkbox" title="">
																		<input type="checkbox" value="{{ $row[$specialty] }}" name="specialty[]" class="specialty-clss" @if ( !empty($Specialty_data) && array_key_exists($row[$specialty],$Specialty_data)) checked @endif> {{ $row[$specialty] }}</label>
																		<input type="text" class="custom-input" name="data[{{ $row[$specialty] }}][]" value="@if (!empty($Specialty_data) && array_key_exists($row[$specialty],$Specialty_data)) {{ $Specialty_data[$row[$specialty]] }} @endif">
																	</a>
																</li>
															@endforeach
														@endif
													</ul>
												</div>
											</span>
										</fieldset>
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.other') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Health declaration">{{ __('languages.member.Health_declaration') }}</label>
										<ul class="list-unstyled mb-0">
											<li class="d-inline-block mt-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
													   <input type="radio" class="custom-control-input" name="Health_declaration" id="customRadio7" value="1" @if($edit_user['Health_declaration'] == "1") checked="" @endif>
														<label class="custom-control-label" for="customRadio7">{{ __('languages.member.Yes') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="Health_declaration" id="customRadio8" value="2" @if($edit_user['Health_declaration'] == "2") checked="" @endif>
														<label class="custom-control-label" for="customRadio8">{{ __('languages.member.No') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
									</div>
									<div class="form-group col-md-6 mb-50">
										<div class="form-row health-decl-cls" @if($edit_user['Health_declaration'] == "1") style="display: block;" @else style="display: none;" @endif>
											<div class="form-group col-md-12 mb-50">
												<label class="text-bold-600" for="health_declaration_text"></label>
												<input type="text" class="form-control" id="Health_declaration_text" name="Health_declaration_text" placeholder="{{ __('languages.member.Health_statement') }}" value="{{ $edit_user['Health_declaration_text'] }}">
											</div>
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="emergency_contact_name">{{ __('languages.member.Emergency_Contact_Name') }}</label>
										<input type="text" class="form-control" id="emergency_contact_name" name="Emergency_contact_name" placeholder="" value="{{ $edit_user['Emergency_contact_name'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="emergency_number">{{ __('languages.member.Emergency_Number') }} </label>
										<input type="text" class="form-control" id="emergency_number" name="EmergencyContact" placeholder="" value="{{ $edit_user['EmergencyContact'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.member.Relationship') }} </label>
										<fieldset class="form-group">
											<select class="form-control relarionshipcls" id="relationship" name="Relationship">
												<option value="">{{ __('languages.member.Select_Relationship') }}</option>
												<option value="1" @if($edit_user['Relationship']=='1') selected="" @endif> {{ __('languages.member.Father_Son') }}</option>
												<option value="2" @if($edit_user['Relationship']=='2') selected="" @endif>{{ __('languages.member.Mother_Son') }}</option>
												<option value="3" @if($edit_user['Relationship']=='3') selected="" @endif>{{ __('languages.member.Father_Daugther') }}</option>
												<option value="4" @if($edit_user['Relationship']=='4') selected="" @endif>{{ __('languages.member.Mother_Daugther') }}</option>
												<option value="5"@if($edit_user['Relationship']=='5') selected="" @endif> {{ __('languages.member.Brother_sister') }}</option>
												<option value="6" @if($edit_user['Relationship']=='6') selected="" @endif>{{ __('languages.member.other') }}</option>
											</select>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50 relationship-cls" @if($edit_user['Relationship']=='6') style="display: block;" @else style="display: none;" @endif>
										<label class="text-bold-600" for="relationship_text"></label>
										<input type="text" class="form-control" id="relationship_text" placeholder="{{ __('languages.member.other_relationship') }}" name="Relationship_text" value="{{ $edit_user['Relationship_text'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Join Date">{{ __('languages.member.join_date') }}</label>
										<fieldset class="form-group position-relative has-icon-left">
											<input type="text" class="form-control" placeholder="{{ __('languages.member.join_date') }}" id="join_date" name="JoinDate" value="{{ $edit_user['JoinDate'] }}">
											<div class="form-control-position">
												<i class='bx bx-calendar'></i>
											</div>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role" class="text-bold-600">{{ __('languages.member.remark') }}  <!-- <span class="required-cls">*</span> --></label>
										<fieldset class="form-group">
											<select class="form-control remarkscls remarkedit-cls" id="remarks" name="Remarks">
												<option value="">{{ __('languages.member.select_remark') }}</option>
												@if($Remarks)
													@php
													$Remark = 'remarks_'.app()->getLocale();
													@endphp
													@foreach($Remarks as $val)
														<!-- <option value="{{ $val['id'] }}" @if($edit_user['Remarks']==$val['id']) selected="" @endif data-user-id="{{ $edit_user['ID'] }}">{{ $val[$Remark] }}</option> -->
														<option value="{{ $val['id'] }}" data-user-id="{{ $edit_user['ID'] }}">{{ $val[$Remark] }}</option>
													@endforeach
												@endif
											</select>
										</fieldset>
										<div class="remarks_html_cls"></div> <!-- don't remove this clasas -->
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
									</div>
									<div class="form-group col-md-6 mb-50">
										@if(!empty($audit_log))
										<label class="text-bold-600">{{ __('languages.member.history_of_remark') }}</label>
											<table>
												<tbody>
													@foreach($audit_log as $val)
														@if(!empty($val->remark_log))
															@php
																$history_remark = json_decode($val->remark_log);
																$remarks = '';
																if(!empty($history_remark)){
																	$remarks = Helper::getremarksData($history_remark->remark);
																}
															@endphp
															@if(!empty($val->remark_status) && $val->remark_status == "1")
																<tr>
																	<td>{{ $remarks }} : {{ $history_remark->Remarks_desc }} - {{ $history_remark->remark_date }}</td>
																	<td><a href="javascript:void(0);" data-id="{{ $val->id }}" class="history-team-rank-cls" data-log="remarks"><i class="bx bx-trash-alt"></i></a></td>
																</tr>
															@endif
														@endif
													@endforeach
												</tbody>
											</table>
										@endif
									</div>
								</div>
								<!-- <div class="form-row">
									@php
										$sitesettings = Helper::getsitesettings();
									@endphp
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="hour_point">{{ __('languages.member.Hour_Point') }}</label>
										<input type="text" class="form-control" id="hour_point" name
										="hour_point" placeholder="{{ __('languages.member.Hour_Point') }}" value="{{ $edit_user['hour_point'] }}" data-rate = "{{ !empty($sitesettings->HKD) ? $sitesettings->HKD : 10 }}" min="0">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="hour_point_rate">{{ __('languages.member.Hour_Point_Conversion_Rate') }}</label>
										<input type="text" class="form-control" id="hour_point_rate" name
										="hour_point_rate" placeholder="{{ __('languages.member.Hour_Point_Conversion_Rate') }}" value="{{ $edit_user['hour_point_rate'] }}" readonly>
									</div>
								</div> -->
								
								<div class="form-row">
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.member.Tokens') }}</label>
										<input type="number" class="form-control" id="member_token" name="member_token" placeholder="{{ __('languages.member.Tokens') }}" value="{{$MemberToken}}" readonly>
									</div>
									<!-- <div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.member.Money') }}</label>
										<input type="number" class="form-control" id="total_money" name
										="total_money" placeholder="{{ __('languages.member.Money') }}" value="{{ $edit_user['total_money'] }}" >
									</div> -->
								</div>
								<div class="form-row">
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.activity_hours') }}</label>
										<input type="number" class="form-control" id="activity_hours" name="activity_hours" placeholder="{{ __('languages.activity_hours') }}" value="{{$activityHour}}" readonly>
									</div>
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.service_hours') }}</label>
										<input type="number" class="form-control" id="service_hours" name="service_hours" placeholder="{{ __('languages.service_hours') }}" value="{{$serviceHour}}" readonly>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.training_hours') }}</label>
										<input type="number" class="form-control" id="training_hours" name="training_hours" placeholder="{{ __('languages.training_hours') }}" value="{{$trainingHour}}" readonly>
									</div>
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.total_hours') }}</label>
										<input type="number" class="form-control" id="total_hours" name="total_hours" placeholder="{{ __('languages.total_hours') }}" value="{{$totalHour}}" readonly>
									</div>
								</div>
								
								<div class="form-row attachment_cls">
									<div class="form-group col-lg-6 col-md-12">
										<label for="location">{{ __('languages.member.Attachment') }}</label><span class="allow-extesion-cls">({{ __('languages.member.only_file_size') }})</span><span class="allow-extesion-cls">({{ __('languages.member.only_file_extension') }})</span>
										<fieldset class="form-group">
											<input type="file" name="Attachment[]" class="form-control" id="Attachment" multiple>
											<label id="Attachment-error" class="error" for="Attachment"></label>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{ __('languages.Status') }}</label>
										<fieldset class="form-group">
											<select class="form-control" id="Status" name="Status">
												<!-- <option value="">{{ __('languages.member.select') }}</option> -->
												<option value="1" @if($edit_user['Status'] == 1) selected @endif>{{ __('languages.Active') }}</option>
												<option value="2" @if($edit_user['Status'] == 2) selected @endif>{{ __('languages.Inactive') }}</option>
											</select>
										</fieldset>
									</div>
									
								</div>

								<div class="form-row">
									<div class="form-group col-lg-12 col-md-12 attchement_find_images">
										@if(!empty($edit_user['Attachment']))
											@php
												$images = explode(',',$edit_user['Attachment']);
												$i = 0;
											@endphp
											@foreach($images as $val)
											@if(!empty($val))
											<span class="pip">
												@php
													$image_explode = explode("/",$val);
													$image_name = !empty($image_explode[2]) ? $image_explode[2] : '';
												@endphp
												<input type="hidden" class="attchement_doc_cls" value="{{ $val }}" data-images = "{{ $val }}">
												<div class="multiple-image-cls">
													<a href="javascript:void(0);">
														<img src="" class="icon_{{ $i }}" width="150" height="150">
														<!-- <span class="image-name-cls">{{ preg_replace('/[0-9]+/', '', $image_name) }}</span> -->
														<span class="image-name-cls">{{ $image_name }}</span>
														<span class="remove deleteImage" id="{{ $edit_user['ID'] }}" imageName="{{ $image_name }}">{{ __('languages.Remove') }}</span>
													</a>
												</div>
												@php
													$i++;
												@endphp
											</span>
											@endif
											@endforeach
										@endif
									</div>
								</div>
								<div class="form-row member-cls">
									<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="submit">
									<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Save') }}" name="save">
									<a href="{{ route('users.index') }}" class="btn btn-primary glow submit">{{ __('languages.Cancel') }}</a>
								</div>
							</form>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
<div class="modal fade error-modal" id="warning" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Warning</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<h6 id='msg'>Contact number already exists.</h6>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
<script type="text/javascript">
var fileTypes = ['pdf', 'docx', 'rtf', 'jpg', 'jpeg', 'png', 'txt']; //acceptable file types
$(document).ready(function(){
	var test = ASSET_URL;
    ASSET_URL = test.replace("index.php/public", "");

$( ".attchement_doc_cls" ).each(function( index,value) {
var split_images = (this.value).split(".");
var extension = split_images[1].toLowerCase();
var thisclass = $(this).attr('class');
if (extension == 'pdf') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/pdf.svg');
}
else if (extension == 'docx' || extension == 'docm' || extension == 'dotm' || extension == 'dot' ||extension == 'doc' ||extension == 'dotx') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/docfile.svg');
} else if (extension == 'txt') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/txtfile.svg');
} else if (extension == 'png') {
$('.icon_'+index).attr('src',ASSET_URL+'/'+this.value);
} else if (extension == 'jpg' || extension == 'jpeg') {
$('.icon_'+index).attr('src',ASSET_URL+'/'+this.value);
} else if (extension == 'csv') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/csvfile.svg');
} else if (extension == 'xls' || extension == 'xlsm') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/xlsfile.svg');
}else if (extension == 'xlsx') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/xlsx.svg');
}else if (extension == 'gif') {
$('.icon_'+index).attr('src',ASSET_URL+'/app-assets/images/gif-file.svg');
} else {
$(input).closest('.uploadDoc').find(".docErr").slideUp('slow');
}
});
});
</script>
@endsection