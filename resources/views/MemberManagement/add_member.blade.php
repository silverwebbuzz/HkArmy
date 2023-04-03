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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.member.Add_Member') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<div class="card-body">
							<form action="{{ route('users.store') }}" method="post" name="add_member_form" id="add_member_form" enctype="multipart/form-data">
								<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.basic_information') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="memship_number">{{ __('languages.member.Member_Number') }}</label>
										<input type="text" class="form-control" id="memship_number" value="C0{{ $unique_id }}" name="MemberCode" readonly="readonly">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="users-list-role">{{ __('languages.member.elite') }}<span class="required-cls">*</span></label>
										<div class="mb-50">
											<fieldset class="form-group">
												<select class="form-control teamclass" id="team" name="team">
													<option value="">{{ __('languages.member.select') }}</option>
													@if($EilteModel)
													@php
														$elite = 'elite_'.app()->getLocale();
													@endphp
														@foreach($EilteModel as $val)
															<option value="{{ $val['id'] }}">{{ $val[$elite] }}</option>
														@endforeach
													@endif
												</select>
											</fieldset>
										</div>
										<small class="text-danger">{{ $errors->first('team') }}</small>
										<div class="elite-team-cls"></div> <!-- dont't remove class -->
										<div class="mb-50">
											<div class="mb-50 effective-date-cls" >
												<label class="text-bold-600" for="users-list-role">{{ __('languages.member.effective_date') }}<span class="required-cls">*</span></label>
												<input type="text" class="form-control pickadate" placeholder="{{ __('languages.member.effective_date') }}" name="team_effiective_date" id="team_effective_date">
												<small class="text-danger">{{ $errors->first('team_effiective_date') }}</small>
											</div>
										</div>
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Rank_Promotion') }} <span class="required-cls">*</span></h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="rank_effective_date">{{ __('languages.member.effective_date') }}</label>
										<input type="text" class="form-control rank_effective_date" placeholder="{{ __('languages.member.effective_date') }}" id="rank_effective_date" name="rank_effiective_date">
									<small class="text-danger">{{ $errors->first('team_effiective_date') }}</small>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="reference_number">{{ __('languages.member.Reference_number') }}</label>
										<input type="text" class="form-control" id="reference_number" name="Reference_number" placeholder="{{ __('languages.member.Reference_number') }}" value="">
									<small class="text-danger">{{ $errors->first('Reference_number') }}</small>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<div class="rank-team-cls"></div> <!-- dont't remove class -->
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Personal_Information') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chinese_name">{{ __('languages.member.Chinese_name') }}</label>
										<input type="text" class="form-control" id="chinese_name" name="Chinese_name" placeholder="{{ __('languages.member.Chinese_name') }}" value="">
									<small class="text-danger">{{ $errors->first('Chinese_name') }}</small>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="english_name">{{ __('languages.member.English_name') }}</label>
										<input type="text" class="form-control" id="english_name" name
										="English_name" placeholder="{{ __('languages.member.English_name') }}" value="">
									<small class="text-danger">{{ $errors->first('English_name') }}</small>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="dob">{{ __('languages.member.date_of_birth') }}</label>
										<fieldset class="form-group position-relative has-icon-left">
											<input type="text" class="form-control" placeholder="{{ __('languages.member.date_of_birth') }}" name="DOB" id="dob">
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
														<input type="radio" class="custom-control-input" name="Gender" id="customRadio4" value="1">
														<label class="custom-control-label" for="customRadio4">{{ __('languages.member.Male') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="Gender" id="customRadio3" value="2">
														<label class="custom-control-label" for="customRadio3">{{ __('languages.member.Female') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
										<div class="gender-error-cls"></div>
										<small class="text-danger">{{ $errors->first('Gender') }}</small>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="age">{{ __('languages.member.Age') }}</label>
										<input type="text" class="form-control" id="age" name="age" placeholder="{{ __('languages.member.Age') }}" value="" readonly="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="email_address">{{ __('languages.member.Email_address') }}</label>
										<input type="text" class="form-control" id="email_address" name="email" placeholder="{{ __('languages.member.Email_address') }}" value="">
									<small class="text-danger">{{ $errors->first('email') }}</small>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number">{{ __('languages.member.Contact_number') }}</label>
										<input type="text" class="form-control" id="contact_number" name="Contact_number" placeholder="{{ __('languages.member.Contact_number') }}" value="">
									<small class="text-danger">{{ $errors->first('Contact_number') }}</small>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number_2">{{ __('languages.member.Contact_number2') }}</label>
										<input type="text" class="form-control" id="contact_number_2" name="Contact_number_1" placeholder="{{ __('languages.member.Contact_number2') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="contact_number_3">{{ __('languages.member.Contact_number3') }}</label>
										<input type="text" class="form-control" id="contact_number_3" name="Contact_number_2" placeholder="{{ __('languages.member.Contact_number3') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chinese_address">{{ __('languages.member.Chinese_address') }}</label>
										<input type="text" class="form-control" id="chinese_address" name="Chinese_address" placeholder="{{ __('languages.member.Chinese_address') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="English_address">{{ __('languages.member.English_address') }}</label>
										<input type="text" class="form-control" id="English_address" name
										="English_address" placeholder="{{ __('languages.member.English_address') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="nationality">{{ __('languages.member.Nationality') }}</label>
										<input type="text" class="form-control" id="nationality" name="Nationality" placeholder="{{ __('languages.member.Nationality') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="occupation">{{ __('languages.member.Occupation') }}</label>
										<input type="text" class="form-control" id="occupation" name="Occupation" placeholder="{{ __('languages.member.Occupation') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="id_number">{{ __('languages.member.ID_Number') }}</label>
										<input type="text" class="form-control" id="id_number" name="ID_Number" placeholder="{{ __('languages.member.ID_Number') }}" value="">
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
														<option value="{{ $val['id'] }}">{{ $val[$qualification] }}</option>
													@endforeach
												@endif
											</select>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50 notecls" style="display: none;">
										<label class="text-bold-600" for="please_note">{{ __('languages.member.please_note') }}</label>
										<input type="text" class="form-control" id="please_note" name="note" placeholder="{{ __('languages.member.please_note') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="school_name">{{ __('languages.member.school_name') }}</label>
										<input type="text" class="form-control" id="school_name" name
										="School_Name" placeholder="{{ __('languages.member.school_name') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="subject">{{ __('languages.member.subject') }}</label>
										<input type="text" class="form-control" id="subject" name
										="Subject" placeholder="{{ __('languages.member.subject') }}" value="">
									</div>
								</div>
								<div class="header pt-1 pb-1">
									<h4 class="card-title">{{ __('languages.member.Activity_Experience_Skills') }}</h4>
								</div>
								<div class="form-row">
									<div class="form-group member-activity-cls col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.member.Related_Activity_History') }}</label>
										<fieldset class="form-group">
											<span class="multiselect-native-select">
												<div class="btn-group">
													<button type="button" class="multiselect dropdown-toggle btn btn-default" data-toggle="dropdown" title="" aria-expanded="false"><span class="multiselect-selected-text related_value_selected_cls"> {{ __('languages.member.None_selected') }}</span> <b class="caret"></b></button>
													<ul class="multiselect-container dropdown-menu" x-placement="bottom-start" style="">
														@if(!empty($RelatedActivityHistory))
														@php
														$ActivityHistory = 'ActivityHistory_'.app()->getLocale();
														@endphp
														@foreach($RelatedActivityHistory as $val)
															<li class="">
																<a><label class="checkbox" title=''>
																<input type="checkbox" value="{{ $val[$ActivityHistory] }}" name="relatedactivity[]" class="checkboxClass">{{ $val[$ActivityHistory] }}
																</label>
																<input type="text" class="custom-input valid" name="data[{{ $val[$ActivityHistory] }}][]" value="" aria-invalid="false">
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
													   <input type="radio" class="custom-control-input" name="otherexperience" id="other_experience_yes" value="1">
														<label class="custom-control-label" for="other_experience_yes">{{ __('languages.member.Yes') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="otherexperience" id="other_experience_no" value="2">
														<label class="custom-control-label" for="other_experience_no">{{ __('languages.member.No') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
									</div>
									<div class="form-group col-md-3 mb-50" >
										<div class="form-row other-exp-cls" style="display: none">
											<div class="form-group col-md-12 mb-50">
												<label class="text-bold-600" for="other_experience_text"></label>
												<input type="text" class="form-control" id="other_experience_text" name="other_experience_text" placeholder="{{ __('languages.member.Other_experience') }}" value="">
											</div>
										</div>
									</div>
									{{-- <div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="other_experience">{{ __('languages.member.Other_experience') }}</label>
										<input type="text" class="form-control" id="other_experience" placeholder="{{ __('languages.member.Other_experience') }}" name="Other_experience" value="">
									</div> --}}
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
														@php
														$specialty = 'specialty_'.app()->getLocale();
														@endphp
														@foreach($Specialty as $row)
															<li>
																<a><label class="checkbox" title="">
																	<input type="checkbox" value="{{ $row[$specialty] }}" name="specialty[]" class="specialty-clss"> {{ $row[$specialty] }}</label>
																	<input type="text" class="custom-input" name="data[{{ $row[$specialty] }}][]">
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
													   <input type="radio" class="custom-control-input" name="Health_declaration" id="customRadio7" value="1">
														<label class="custom-control-label" for="customRadio7">{{ __('languages.member.Yes') }}</label>
													</div>
												</fieldset>
											</li>
											<li class="d-inline-block my-1 mr-1 mb-1">
												<fieldset>
													<div class="custom-control custom-radio">
														<input type="radio" class="custom-control-input" name="Health_declaration" id="customRadio8" value="2">
														<label class="custom-control-label" for="customRadio8">{{ __('languages.member.No') }}</label>
													</div>
												</fieldset>
											</li>
										</ul>
									</div>
									<div class="form-group col-md-6 mb-50" >
										<div class="form-row health-decl-cls" style="display: none">
											<div class="form-group col-md-12 mb-50">
												<label class="text-bold-600" for="health_declaration_text"></label>
												<input type="text" class="form-control" id="health_declaration_text" name="Health_declaration_text" placeholder="{{ __('languages.member.Health_statement') }}" value="">
											</div>
										</div>
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="emergency_contact_name">{{ __('languages.member.Emergency_Contact_Name') }}</label>
										<input type="text" class="form-control" id="emergency_contact_name" name="Emergency_contact_name" placeholder="{{ __('languages.member.Emergency_Contact_Name') }}" value="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="emergency_number">{{ __('languages.member.Emergency_Number') }} </label>
										<input type="text" class="form-control" id="emergency_number" name="EmergencyContact" placeholder="{{ __('languages.member.Emergency_Number') }}" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.member.Relationship') }} </label>
										<fieldset class="form-group">
											<select class="form-control relarionshipcls" id="relationship" name="Relationship">
												<option value="">{{ __('languages.member.Select_Relationship') }}</option>
												<option value="1"> {{ __('languages.member.Father_Son') }}</option>
												<option value="2">{{ __('languages.member.Mother_Son') }}</option>
												<option value="3">{{ __('languages.member.Father_Daugther') }}</option>
												<option value="4">{{ __('languages.member.Mother_Daugther') }} </option>
												<option value="5">{{ __('languages.member.Brother_sister') }}</option>
												<option value="6">{{ __('languages.member.other') }}</option>
											</select>
										</fieldset>
									</div>
									<div class="form-group col-md-6 mb-50 relationship-cls" style="display: none;">
										<label class="text-bold-600" for="relationship_text"></label>
										<input type="text" class="form-control" id="relationship_text" placeholder="{{ __('languages.member.other_relationship') }}" name="Relationship_text" value="">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Join Date">{{ __('languages.member.join_date') }}</label>
										<fieldset class="form-group position-relative has-icon-left">
											<input type="text" class="form-control" placeholder="{{ __('languages.member.join_date') }}" id="join_date" name="JoinDate">
											<div class="form-control-position">
												<i class='bx bx-calendar'></i>
											</div>
										</fieldset>
									<small class="text-danger">{{ $errors->first('JoinDate') }}</small>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role" class="text-bold-600">{{ __('languages.member.remark') }}</label>
										<fieldset class="form-group">
											<select class="form-control remarkscls" id="remarks" name="Remarks">
												<option value="">{{ __('languages.member.select_remark') }}</option>
													@if($Remarks)
														@php
														$Remark = 'remarks_'.app()->getLocale();
														@endphp
														@foreach($Remarks as $val)
															<option value="{{ $val['id'] }}">{{ $val[$Remark] }}</option>
														@endforeach
													@endif
											</select>
										<!-- <small class="text-danger">{{ $errors->first('Remarks') }}</small> -->
										</fieldset>
										<div class="remarks_html_cls"></div> <!-- Don't remove class -->
									</div>
								</div>
								<!-- <div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="hour_point">{{ __('languages.member.Hour_Point') }}</label>
										@php
											$sitesettings = Helper::getsitesettings();
										@endphp
										<input type="text" class="form-control" id="hour_point" name
										="hour_point" placeholder="{{ __('languages.member.Hour_Point') }}" value="" data-rate = "{{ !empty($sitesettings->HKD) ? $sitesettings->HKD : 10 }}" min="0">
									<small class="text-danger">{{ $errors->first('hour_point') }}</small>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="hour_point_rate">{{ __('languages.member.Hour_Point_Conversion_Rate') }}</label>
										<input type="text" class="form-control" id="hour_point_rate" name
										="hour_point_rate" placeholder="{{ __('languages.member.Hour_Point_Conversion_Rate') }}" value="" readonly>
									</div>
								</div> -->
								<div class="form-row">
									<div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.member.Tokens') }}</label>
										<input type="number" class="form-control" id="member_token" name="member_token" placeholder="{{ __('languages.member.Tokens') }}" value="{{old('member_token')}}">
									</div>
									<!-- <div class="form-group col-lg-6 col-md-12">
										<label>{{ __('languages.member.Money') }}</label>
										<input type="number" class="form-control" id="total_money" name
										="total_money" placeholder="{{ __('languages.member.Money') }}" value="" >
									</div> -->
								</div>

								<div class="form-row attachment_cls">
									<div class="form-group col-lg-6 col-md-12">
										<label for="location">{{ __('languages.member.Attachment') }}</label><span class="allow-extesion-cls">({{ __('languages.member.only_file_size') }})</span><span class="allow-extesion-cls">({{ __('languages.member.only_file_extension') }})</span>
										<fieldset class="form-group">
											<input type="file" name="Attachment[]" class="form-control" id="Attachment" multiple>
										</fieldset>
										<!-- <fieldset class="form-group">
											<label for="Attachment">{{ __('languages.member.Attachment') }}</label>
											<div class="custom-file">
												<input type="file" class="custom-file-input" id="Attachment" name="Attachment">
												<label class="custom-file-label" for="inputGroupFile01"></label>
											</div>
										</fieldset> -->
									</div>
									<div class="form-group col-md-6 mb-50">
										<label>{{ __('languages.Status') }}</label>
										<fieldset class="form-group">
											<select class="form-control" id="Status" name="Status">
												<!-- <option value="">{{ __('languages.member.select') }}</option> -->
												<option value="1">{{ __('languages.Active') }}</option>
												<option value="2">{{ __('languages.Inactive') }}</option>
											</select>
										</fieldset>
									</div>
									
								</div>

								<div class="form-row member-cls">
									<input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="submit">
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
@endsection