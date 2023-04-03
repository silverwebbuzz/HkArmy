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
                            @endif

                            <?php
                            if(!empty($_GET['event_id'])){
                                $id = Helper::decodekey($_GET['event_id']); 

                            }

                            ?>

                            <div class="col-12 col-sm-6 col-lg-2 search-filter-cls">
                                <div class="published_event_cls">
                                    <label for="users-list-role">{{ __('languages.Attendance.Select_Event') }}</label>
                                    <fieldset class="form-group">
                                        <select class="form-control attendance_event_id" id="event_id" name="event_id">
                                            <option value="">{{ __('languages.Attendance.Select_Event_Name') }}</option>
                                            @if($events)
                                            @foreach($events as $event)
                                            <option value="{{ $event['event_id'] }}"  data-event-schedule="{{ $event['id'] }}" data-event-type="{{ $event['events']['event_type'] }}" <?php if(!empty($_GET['event_id']) && $id == $event['event_id']) { echo 'selected'; }?>>{{ $event['events']['event_name'] }} - {{ $event['events']['occurs'] }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-center">
                                <fieldset>
                                    <div class="checkbox">
                                        <input type="checkbox" class="checkbox-input useCoin" id="checkbox1" name="useCoin" value="1">
                                        <label for="checkbox1">Use @if (Session::get('user') ['role_id'] != '1') my @endif coin</label>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block glow mb-0" id="recordAttend">{{ __('languages.Attendance.Record_Attendance') }}</button>
                            </div>
                            <!-- <div class="col-12 col-sm-6 col-lg-3 d-flex align-items-center">
                                <button type="button" class="btn btn-primary btn-block glow mb-0" id="eventLogout">{{ __('languages.Attendance.Member_Logout') }}</button>
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
                                        <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">QR With Attendance</a></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">MemberShip Number</a></h4>
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
                                        <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">QR With Attendance</a></h4>
                                    </div>
                                    <div class="col-md-6">
                                        <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">MemberShip Number</a></h4>
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
                                            <h4><a href="javascript:void(0);" class="btn btn-primary qrattendance-cls">QR With Attendance</a></h4>
                                        </div>
                                        <div class="col-md-6">
                                            <h4><a href="javascript:void(0);" class="btn btn-primary membership-cls">MemberShip Number</a></h4>
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

                   <div class="users-list-table">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive event-search-list-cls">
                                    <table id="attendanceTable" class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('languages.Attendance.Member_Code') }}</th>
                                                <th>{{ __('languages.Attendance.Member_Name') }}</th>
                                                <th>{{ __('languages.Attendance.Event_Name') }}</th>
                                                <th>{{ __('languages.Attendance.Event_Type') }}</th>
                                                <th>{{ __('languages.Attendance.Date') }}</th>
                                                <th>{{ __('languages.Attendance.In_Time') }}</th>
                                                <th>{{ __('languages.Attendance.Out_Time') }}</th>
                                                <th>{{ __('languages.Attendance.Hours') }}</th>
                                                <th>{{ __('languages.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id ="tbodyid">
                                            @if(!empty($Attendance))
                                            @foreach($Attendance as $row)
                                            @if(!empty($row['users']))
                                            <tr>
                                                @if(in_array('members_write', Helper::module_permission(Session::get('user')['role_id'])))
                                                <td><a href="{{ route('users.edit',$row['user_id']) }}">C{{ $row['member_code'] }}</a></td>
                                                @else
                                                <td>C{{ $row['member_code'] }}</td>
                                                @endif
                                                @if($row['users']['UserName'])
                                                <td>{{ $row['users']['UserName'] }}</td>
                                                @else
                                                <td>{{ $row['users']['Chinese_name'] }} & {{ $row['users']['English_name'] }}</td>
                                                @endif
                                                <td>{{ $row['event']['event_name'] ?? '' }}</td>
                                                <td>{{ $row['event_type']['event_type_name_en'] }}</td>
                                                <td>{{ date('d/m/Y',strtotime($row['date'])) }}</td>
                                                <td>{{ $row['in_time'] }}</td>
                                                <td>{{ $row['out_time'] }}</td>
                                                <td>{{ $row['hours'] }}</td>
                                                <td><!-- <a href="{{ route('attendanceManagement.edit',$row['id']) }}"><i class="bx bx-edit-alt"></i></a> -->

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
                <h4 class="modal-title" id="myModalLabel4">Edit Attendance</h4>
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

<!-- /footer content -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.0.3/html5-qrcode.min.js"></script>
<!-- <script src="{{ asset('assets/codeQR.min.js') }}"></script> -->

<script>
    function docReady(fn) {
        // see if DOM is already available
        if (document.readyState === "complete"
            || document.readyState === "interactive") {
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
                                type: "GET",
                                url: BASE_URL + "/getEventAttenderList/" + event_id + '/' + date,
                                data: {},
                                success : function(response) {
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
        }
    }

    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);
});
</script>
@endsection