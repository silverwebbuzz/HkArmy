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
                        <h3 class="content-header-title float-left pr-1 mb-0">
                            {{ __('languages.Attendance.Attendance') }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
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
                    <form id="attendEvent" name="attendEvent">
                       <!--  <input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}"> -->
                       
                        <div class="row border rounded py-2 mb-2">
                            @if(empty($_GET['event_id']))
                            <div class="float-right align-items-center ml-1">
                                <label for="users-list-role">{{ __('languages.Select_date') }}</label>
                                <fieldset class="form-group position-relative has-icon-left">
                                    <input type="text" class="form-control filter_date_attendance" id="filter_date_attendance" name="filter_date_attendance" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
                                    <div class="form-control-position">
                                        <i class="bx bx-calendar-check"></i>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls">
                                <div class="published_event_cls">
                                    <label for="users-list-role">{{ __('languages.Attendance.Select_Event') }}</label>
                                    <fieldset class="form-group">
                                        <select class="form-control attendance_event_id" id="event_id" name="event_id">
                                            <option value="">{{ __('languages.Attendance.Select_Event_Name') }}</option>
                                            @if($events)
                                            @foreach($events as $event)
                                            <option value="{{ $event['event_id'] }}"  data-event-schedule="{{ $event['id'] }}" data-event-type="{{ isset($event['events']) ? $event['events']['event_type'] : '' }}" <?php if(!empty($_GET['event_id']) && $id == $event['event_id']) { echo 'selected'; }?>>{{ isset($event['events']) ? $event['events']['event_name'] : '' }} - {{ isset($event['events']) ? $event['events']['occurs'] : '' }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls1">
                                <div class="published_event_cls1">
                                    <label for="users-list-role1">{{ __('languages.Attendance.Event_Type') }}</label>
                                    <fieldset class="form-group">
                                        <select class="form-control" id="filter_event_type1" name="filter_event_type1">
                                            <option value="">{{ __('languages.event.Select_event_type') }} </option>
                                            @if(!empty($get_event_type_list))
                                            @php
                                            echo $get_event_type_list;
                                            @endphp
                                            @endif
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            
                            
                            @endif
                            <?php
                            if(!empty($_GET['event_id'])){
                                $id = Helper::decodekey($_GET['event_id']); 

                            } ?>
                            {{-- <div class="col-12 col-sm-6 col-lg-2 search-filter-cls">
                                <div class="published_event_cls">
                                    <label for="users-list-role">{{ __('languages.Attendance.Select_Event') }}</label>
                                    <fieldset class="form-group">
                                        <select class="form-control attendance_event_id" id="event_id" name="event_id">
                                            <option value="">{{ __('languages.Attendance.Select_Event_Name') }}</option>
                                            @if($events)
                                            @foreach($events as $event)
                                            <option value="{{ $event['event_id'] }}"  data-event-schedule="{{ $event['id'] }}" data-event-type="{{ isset($event['events']) ? $event['events']['event_type'] : '' }}" <?php if(!empty($_GET['event_id']) && $id == $event['event_id']) { echo 'selected'; }?>>{{ isset($event['events']) ? $event['events']['event_name'] : '' }} - {{ isset($event['events']) ? $event['events']['occurs'] : '' }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </fieldset>
                                </div>
                            </div> --}}

                            
                            

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls1">
                                <div class="published_event_cls1">
                                    <label for="users-list-role1">{{ __('languages.Status') }}</label>
                                    <fieldset class="form-group">
                                    <select class="form-control" id="event_status" name="event_status">
                                        <option value="">{{ __('languages.Status') }} </option>
                                        <!-- <option value="0">{{ __('languages.event.Draft') }}</option> -->
                                        <option value="1">{{ __('languages.event.Published') }}</option>
                                        <option value="2">{{ __('languages.event.Unpublished') }}</option>
                                        <!-- <option value="3">{{ __('languages.event.ready_for_close') }}</option> -->
                                        <option value="4">{{ __('languages.event.close') }}</option>
                                    </select>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls1">
                                <div class="published_event_cls1">
                                    <label for="users-list-role1">{{ __('languages.search_event_name_event_code') }}</label>
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" id="search_text" name="search_text" placeholder="{{ __('languages.search_event_name_event_code') }}" autocomplete="off">
                                    </fieldset>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls1">
                                <div class="published_event_cls1">
                                    <label for="users-list-role1">{{ __('languages.search_member_name_code') }}</label>
                                    <fieldset class="form-group">
                                        <input type="text" class="form-control" id="search_member_text" name="search_member_text" placeholder="{{ __('languages.search_member_name_code') }}" autocomplete="off">
                                    </fieldset>
                                </div>
                            </div>

                            <!-- <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-center">
                                <fieldset>
                                    <div class="checkbox">
                                        <input type="checkbox" class="checkbox-input useCoin" id="checkbox1" name="useCoin" value="1">
                                        <label for="checkbox1">Use @if (Session::get('user') ['role_id'] != '1') my @endif coin</label>
                                    </div>
                                </fieldset>
                            </div> -->
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                               <label for="clear"></label>
								<a href="{{route('attendanceManagement.index')}}" class="btn btn-primary btn-block glow mb-0 clearsorting">{{ __('languages.Clear') }}</a>
                            </div>
                           
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block glow mb-0" id="recordAttend">{{ __('languages.Attendance.Record_Attendance') }}</button>
                            </div>
                            <!-- <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block glow mb-0" id="eventLogout">{{ __('languages.Attendance.Member_Logout') }}</button>
                            </div> -->
                            <!-- <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                               <input type="submit" class="btn btn-primary glow submit serach-events-cls1" value="{{ __('languages.Submit') }} " name="submit">
                            </div> -->
                        </div>
                    </form>
                </div>
                <!-- QR Code Attendance POPUP -->
                <div class="modal fade" id="eventAttend" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('languages.Attendance.Record_Attendance') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">{{__('languages.qr_with_attendance')}}</a></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">{{__('languages.membership_number')}}</a></h4>
                                    </div>
                                </div>
                                <input type="hidden" name="currentevent" id="currentevent" value="" class="currentevent">
                                <input type="hidden" name="type" class="attendances_type" id="attendances_type" value="">
                                <input type="hidden" name="scheduleID" class="scheduleID" id="scheduleID" value="">
                                <!-- QR CODE SCAN START -->
                                <form class="membershipnumberForm1" name="membershipnumberForm">
                                    <div  class="membership-code-cls" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-group col-md-12 mb-50">
                                                <label class="text-bold-600" for="MemberCode">{{ __('languages.member.Member_Number') }}</label>
                                                <input type="text" class="form-control MemberCode" id="MemberCode" value="" name="MemberCode">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-12 mb-50">
                                                <input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="submit">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div id="mainbody1" class="qr-attend-login-cls">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
                                                <!-- <div id="reader" style="display: inline-block;"></div>
                                                <div class="empty"></div>
                                                <div id="scanned-result"></div> -->
                                                <!-- <div id="qr-reader" style="display: inline-block;">    
                                                </div>
                                                <div id="qr-reader-results"></div> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- QR CODE SCAN END -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- QR Code Attendance End-->
                <!-- QR Code LOGOUT POPUP -->
                <div class="modal fade" id="eventLogoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('languages.Attendance.Member_Logout') }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">{{__('languages.qr_with_attendance')}}</a></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">{{__('languages.membership_number')}}</a></h4>
                                    </div>
                                </div>
                                <form class="membershipnumberForm1" name="membershipnumberForm">
                                    <div  class="membership-code-logout-cls" style="display: none;">
                                        <div class="form-row">
                                            <div class="form-group col-md-12 mb-50">
                                                <label class="text-bold-600" for="MemberCode">{{ __('languages.member.Member_Number') }}</label>
                                                <input type="text" class="form-control" id="MemberCodelogout" value="" name="MemberCode">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-12 mb-50">
                                                <input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="submit">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <!-- QR CODE SCAN START -->
                                <div id="mainbody" class="qr-attend-logout-cls">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
                                                <!-- <div id="qrlogout" style="display: inline-block;"></div>
                                                    <div class="empty"></div> -->
                                                    <div id="qrlogout" style="display: inline-block;">

                                                    </div>
                                                    <div id="qr-reader-results">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- QR CODE SCAN END -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- QR Code Attendance LOGOUT-->

                    <!-- Start : QR Code Toggle start -->
                    <div class="card product-add-more-card qr-code-scan-toggle" style="display:none;">
                        <div class="card-content">
                            <div class="card-body1">
                                <a href="#" class="cancel-inv-qr" style="
                                float: right;
                                display: block;
                                width: 100%;
                                text-align: right;
                                ">×</a>

                                <div class="main_qr_header">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">{{__('languages.qr_with_attendance')}}</a></h4>
                                        </div>
                                        <div class="col-md-6">
                                            <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">{{__('languages.membership_number')}}</a></h4>
                                        </div>
                                    </div>
                                    <input type="hidden" name="currentevent" id="currentevent" value="" class="currentevent">
                                    <input type="hidden" name="type" class="attendances_type" id="attendances_type" value="">
                                    <input type="hidden" name="scheduleID" class="scheduleID" id="scheduleID" value="">
                                    <!-- QR CODE SCAN START -->
                                    <form class="membershipnumberForm" name="membershipnumberForm">
                                        <div  class="membership-code-cls" style="display: none;">
                                            <div class="form-row">
                                                <div class="form-group col-md-12 mb-50">
                                                    <label class="text-bold-600" for="MemberCode">{{ __('languages.member.Member_Number') }}</label>
                                                    <input type="text" class="form-control MemberCode1" id="MemberCode1" value="" name="MemberCode">
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-12 mb-50">
                                                    <input type="submit" class="btn btn-primary glow submit" value="{{ __('languages.Submit') }}" name="submit">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div  class="qr-attend-login-cls">
                                        <div class="container1">
                                            <div class="row">
                                                <div class="col-md-12" style="text-align: center;margin-bottom: 20px;">
                                                   <div id="qr-reader" style="display: inline-block;">    
                                                   </div>
                                                   <div id="qr-reader-results"></div>
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                                   <!-- QR CODE SCAN END -->
                               </div>
                           </div>
                       </div>
                   </div>
                   <!-- End : QR Code Toggle end -->
                   {{-- Export Button Start --}}
                   <div class="row mb-2">
                        <div class="float-right align-items-center ml-1">
                            <a href="javascript:void(0);" class="btn btn-primary btn-block glow export_attendance mb-0"> {{ __('languages.export') }} {{__('languages.Attendance.attendances')}}</a>
                        </div>
                    </div>
                    {{-- Export Button End --}}
                   <div class="users-list-table">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive event-search-list-cls">
                                    <table id="attendanceTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>
													<input type="checkbox" name="attendanceIds[]" class="select-all-attendance-chkbox" value="all">
												</th>
                                                <th>{{ __('languages.Attendance.Member_Code') }}</th>
                                                {{-- <th>{{ __('languages.Attendance.Member_Name') }}</th> --}}
                                                <th>{{ __('languages.member.English_name') }}</th>
                                                <th>{{ __('languages.member.Chinese_name') }}</th>
                                                <th>{{ __('languages.Attendance.Event_Name') }}</th>
                                                <th>{{ __('languages.Attendance.Event_Type') }}</th>
                                                <th>{{ __('languages.Attendance.Date') }}</th>
                                                <th>{{ __('languages.Attendance.In_Time') }}</th>
                                                <th>{{ __('languages.Attendance.Out_Time') }}</th>
                                                <th>{{__('languages.Attendance.total_event_hour')}}</th>
                                                <th>{{__('languages.Attendance.in_time_deducted_hour')}}</th>
                                                <th>{{__('languages.Attendance.out_time_deducted_hour')}}</th>
                                                <th>{{__('languages.Attendance.total_deducted_hour')}}</th>
                                                {{-- <th>{{ __('languages.Attendance.Hours') }}</th> --}}
                                                <th>{{ __('languages.training_hours') }}</th>
                                                <th>{{ __('languages.activity_hours') }}</th>
                                                <th>{{ __('languages.service_hours') }}</th>
                                                <th>{{ __('languages.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id ="tbodyid">
                                            @if(!empty($Attendance))
                                            @foreach($Attendance as $row)
                                            @if(!empty($row['users']))
                                            <tr>
                                                <td>
													<input type="checkbox" name="attendanceIds[]" class="select-attendance-chkbox" value="{{$row['id']}}">
												</td>
                                                @if(in_array('members_write', Helper::module_permission(Session::get('user')['role_id'])))
                                                <td><a href="{{ route('users.edit',$row['user_id']) }}">C{{ $row['member_code'] }}</a></td>
                                                @else
                                                <td>C{{ $row['member_code'] }}</td>
                                                @endif
                                                {{-- @if($row['users']['UserName'])
                                                <td>{{ $row['users']['UserName'] }}</td>
                                                @else
                                                <td>{{ $row['users']['Chinese_name'] }} & {{ $row['users']['English_name'] }}</td>
                                                @endif --}}
                                                <td>{{ $row['users']['English_name'] ?? ''}}</td>
                                                <td>{{ $row['users']['Chinese_name'] ?? ''}}</td>
                                                <td>{{ $row['event']['event_name'] ?? '' }}</td>
                                                <td>{{ $row['event_type']['event_type_name_en'] }}</td>
                                                <td>{{ date('d/m/Y',strtotime(str_replace(',','',$row['date']))) }}</td>
                                                <td>{{ date('H:i:s a', strtotime($row['in_time'])) ?? '---' }}</td>
                                                @if(!empty($row['out_time']) && $row['out_time'] != '-')
                                                <td>{{ date('H:i:s a', strtotime($row['out_time'])) ?? '---' }}</td>
                                                @else
                                                <td>---</td>
                                                @endif
                                                <td>{{ $row['total_event_hours'] ?? '---' }}</td>
                                                <td>{{ $row['in_time_deducted_hour'] ?? '---' }}</td>
                                                <td>{{ $row['out_time_deducted_hour'] ?? '---' }}</td>
                                                <td>{{ $row['total_deducted_hour'] ?? '---' }}</td>
                                                {{-- <td>{{ $row['hours'] ?? '---' }}</td> --}}
                                                <td>{{ ($row['training_hour'] =="00:00") ? '---' : $row['training_hour'] }}</td>
                                                <td>{{ ($row['activity_hour']=="00:00") ? '---' : $row['activity_hour'] }}</td>
                                                <td>{{ ($row['service_hour'] =="00:00") ? '---' : $row['service_hour'] }}</td>
                                                <td>
                                                    <!-- <a href="{{ route('attendanceManagement.edit',$row['id']) }}"><i class="bx bx-edit-alt"></i></a> -->
                                                    <a href="javascript:void(0);" data-id="{{ $row['id'] }}" class="editAttendance"><i class="bx bx-edit-alt"></i></a>
                                                    <a href="javascript:void(0);" data-id="{{ $row['id'] }}" class="deleteAttendance"><i class="bx bx-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endif
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
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
@include('layouts.footer')

<div class="modal fade text-left edit_attendance_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel4">{{__('languages.edit_attendance')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <div class="modal-body">

                <div class="editAttendaceForm">

                </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary glow position-relative updateAttendance" name="submit" id="submit" value="Submit">
            </form>

            <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                <i class="bx bx-x d-block d-sm-none"></i>
                <span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
            </button>
        </div>
    </div>
</div>
</div>

<!-- Start Export Fields Popup Modal -->
<div class="modal fade" id="exportAttendanceListSelectField" tabindex="-1" role="dialog" aria-labelledby="exportAttendanceListSelectField" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">{{__('languages.export_fields.select_export_fields')}}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="all-attendancelist-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="member_code" checked>
						<span>{{ __('languages.Attendance.Member_Code') }}</span>
					</div>
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.Attendance.Member_Name') }}</span>
					</div> --}}
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="english_name" checked>
						<span>{{ __('languages.member.English_name') }}</span>
					</div>
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="chinese_name" checked>
						<span>{{ __('languages.member.Chinese_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="event_name" checked>
						<span>{{ __('languages.Attendance.Event_Name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="event_type" checked>
						<span>{{ __('languages.Attendance.Event_Type') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="date" checked>
						<span>{{ __('languages.Attendance.Date') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="intime" checked>
						<span>{{ __('languages.Attendance.In_Time') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="outtime" checked>
						<span>{{ __('languages.Attendance.Out_Time') }}</span>
					</div>
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="total_event_hours" checked>
						<span>{{__('languages.Attendance.total_event_hour')}}</span>
					</div>
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="in_time_deducted_hour" checked>
						<span>{{__('languages.Attendance.in_time_deducted_hour')}}</span>
					</div>
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="out_time_deducted_hour" checked>
						<span>{{__('languages.Attendance.out_time_deducted_hour')}}</span>
					</div>
                    <div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="total_deducted_hour" checked>
						<span>{{__('languages.Attendance.total_deducted_hour')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportFields[]" class="attendancelist-field-checkbox" value="hours" checked>
						<span>{{ __('languages.Attendance.Hours') }}</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportAttendanceList()">{{ __('languages.export') }} {{__('languages.Attendance.Attendance')}}</button>
			</div>
		</div>
	</div>
</div>
<!-- End Export Fields Popup Modal -->

<!-- /footer content -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.0.3/html5-qrcode.min.js"></script>
<!-- <script src="{{ asset('assets/codeQR.min.js') }}"></script> -->

<script>
    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete" || document.readyState === "interactive") {
            // call on next available tick
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

docReady(function () {
    var resultContainer = document.getElementById('qr-reader-results');
    var lastResult, countResults = 0;
    function onScanSuccess(decodedText, decodedResult) {        
        if (decodedText !== lastResult) {
            ++countResults;
            lastResult = decodedText;

            var parts = decodedText.split("/");
            var user_id = parts[0];
            var email = parts[1];
            var event_id = $(".currentevent").val();
            var type = $(".attendances_type").val();

            $(".scheduleID").val(
                $(".attendance_event_id option:selected").attr(
                    "data-event-schedule"
                )
            );

            if ($('input.useCoin').is(':checked')) {
                    //user want to used coin
                    var usedCoin = '1';
            }else{
                    //user does not want to used coin
                    var usedCoin = '0';
            }
            $("#cover-spin").show();
            $.ajax({
                    type: "POST",
                    url : BASE_URL+"/recordAttendance",
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        'user_id' : user_id,
                        'event_id' : event_id,
                        'type' : type,
                        'scheduleID': $(".scheduleID").val(),
                        'usedCoin' : usedCoin,
                    },
                    success : function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if(data.status){
                            toastr.success(data.message);
                            var date = $("#filter_date_attendance").val();
                             if(date != ''){
                                var date = date.replace(/(\d\d)\/(\d\d)\/(\d{4})/, "$3-$1-$2");
                            }else{
                                date = 'null';
                            }

                            $.ajax({
                                type: "POST",
                                url:BASE_URL +"/attendance-event-list-search-date",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr("content"),
                                    filter_date_attendance_event: $("#filter_date_attendance").val(),
                                    event_id: event_id,
                                    type: $("#filter_event_type1").val(),
                                },
                                success: function (response) {
                                    $("#cover-spin").hide();
                                    $(".event-search-list-cls").html(response);
                                    $("#search-eventtable").dataTable();
                                    $("#attendanceTable").hide();
                                }
                            });
                        }else{
                            toastr.error(data.message);
                        }
                    }
                });
            //$('#qr-reader__dashboard_section_swaplink').trigger('click');
            setTimeout(function() {
                document.getElementById("qr-reader__dashboard_section_swaplink").click()
            },2000);
            //document.getElementById("qr-reader__dashboard_section_swaplink").click();
        }
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
});

function formatDate (input) {
  var datePart = input.match(/\d+/g),
  year = datePart[0], // get only two digits
  month = datePart[1], day = datePart[2];

  return day+'/'+month+'/'+year;
}
</script>

<script>
var ExportFieldColumnList = ['member_code','member_name','event_name','event_type','date','intime','outtime','total_event_hours','in_time_deducted_hour','out_time_deducted_hour','total_deducted_hour','hours'];
var EventAttendanceIds = [];
$(function () {
    
	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-attendance-chkbox", function (){
		if ($(this).is(":checked")) {
			$(".select-attendance-chkbox").each(function() {
				$(this).prop('checked', true);
				attendanceid = $(this).val();
				if (EventAttendanceIds.indexOf(attendanceid) !== -1) {
					// Current value is exists in array
				} else {
					EventAttendanceIds.push(attendanceid);
				}
			});
		} else {
			$(".select-attendance-chkbox").each(function() {
				$(this).prop('checked', false);
			});
			EventAttendanceIds = [];
		}
	});

    $(document).on("click", ".select-attendance-chkbox", function (){
		if($('.select-attendance-chkbox').length === $('.select-attendance-chkbox:checked').length){
			$(".select-all-attendance-chkbox").prop('checked',true);
		}else{
			$(".select-all-attendance-chkbox").prop('checked',false);
		}
		attendanceid = $(this).val();
		if ($(this).is(":checked")) {
			if (EventAttendanceIds.indexOf(attendanceid) !== -1) {
				// Current value is exists in array
			} else {
				EventAttendanceIds.push(attendanceid);
			}
		} else {
			EventAttendanceIds = $.grep(EventAttendanceIds, function(value) {
				return value != attendanceid;
			});
		}
	});

    $(document).on("click", ".export_attendance", function () {
        $('#exportAttendanceListSelectField').modal('show');
    });

    $(document).on("click", ".all-attendancelist-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".attendancelist-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var ColumnName = $(this).val();
				if (ExportFieldColumnList.indexOf(ColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportFieldColumnList.push(ColumnName);
				}
			});
		} else {
			$(".attendancelist-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportFieldColumnList = [];
		}
        // console.log('ExportFieldColumnList',ExportFieldColumnList);
	});

	$(document).on("click", ".attendancelist-field-checkbox", function (){
		if($('.attendancelist-field-checkbox').length === $('.attendancelist-field-checkbox:checked').length){
			$(".all-attendancelist-field-checkbox").prop('checked',true);
		}else{
			$(".all-attendancelist-field-checkbox").prop('checked',false);
		}
		var ColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportFieldColumnList.indexOf(ColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportFieldColumnList.push(ColumnName);
			}
		} else {
			ExportFieldColumnList = $.grep(ExportFieldColumnList, function(value) {
				return value != ColumnName;
			});
		}
	});

    $("#filter_date_attendance").trigger('change');
});

function exportAttendanceList(){
    if($('.attendancelist-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#attendanceTable,#search-eventtable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
        $.ajax({
            type: "GET",
            url: BASE_URL + "/export/attendance-list",
            data: {
                'event_id' : $('#event_id').val(),
                'date' : formatDate($('#filter_date_attendance').val()),
                'type' : $('#filter_event_type1').val(),
                'event_status' : $('#search_text').val(),
                'search_text' : $('#search_text').val(),
                'search_member_text' : $('#search_member_text').val(),
                'columnList' : ExportFieldColumnList,
                'AttendanceIds' : EventAttendanceIds
            },
            contentType: 'application/json; charset=utf-8',
            success: function (data) {
                var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
                if (!isHTML(data)) {
                    var downloadLink = document.createElement("a");
                    var fileData = ["\ufeff" + data];

                    var blobObject = new Blob(fileData, {
                        type: "text/csv;charset=utf-8;",
                    });

                    var url = URL.createObjectURL(blobObject);
                    downloadLink.href = url;
                    downloadLink.download = "AttendanceList.csv";

                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }
            },
        });
    }
}
</script>
@endsection