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
					<h3 class="content-header-title float-left pr-1 mb-0">{{__('languages.sidebar.Audit_log')}}</h3>
				</div>
			</div>
		</div>
	</div>
	<div class="content-body">
		<!-- users list start -->
		<section class="users-list-wrapper">
			<div class="users-list-filter px-1">
				<div class="row border rounded py-2 mb-2">
					<div class="float-right align-items-center ml-1">
						<label>{{__('languages.formname')}}</label>
						<fieldset class="form-group position-relative has-icon-left">
							<select class="form-control" id="logFormname" name="logFormname">
								<option value="">{{__('languages.member.select')}}</option>
								<option value="Member">{{__('languages.Member')}}</option>
								<option value="Event">{{__('languages.Event')}}</option>
								<option value="Attendance">{{__('languages.Attendance.Attendance')}}</option>
							</select>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<label>{{__('languages.event.Date')}}</label>
						<fieldset class="form-group position-relative has-icon-left">
							<input type="text" class="form-control pickadate" id="logdate" name="logdate" placeholder="{{ __('languages.Select_date') }}" autocomplete="off">
							<div class="form-control-position">
								<i class="bx bx-calendar-check"></i>
							</div>
						</fieldset>
					</div>
					<div class="float-right align-items-center ml-1">
						<label></label>
						<button type="reset" class="btn btn-primary btn-block glow log-list-clear mb-0">{{__('languages.Clear')}}</button>
					</div>
				</div>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center ml-1">
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-audit-log mb-0"> {{ __('languages.export') }} {{ __('languages.sidebar.Audit_log') }}</a>
				</div>
			</div>
			{{-- Export Button End --}}
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="logtable" class="table">
										<thead>
											<tr>
												<th>
													<input type="checkbox" name="allAuditIDs[]" class="select-all-Audit-chkbox" value="all">
												</th>
												<th>{{__('languages.Sr_No')}}</th>
												<th>{{__('languages.formname')}}</th>
												<th>{{__('languages.member.ID')}}</th>
												<th>{{__('languages.event.Date')}}</th>
												<th>{{__('languages.updated_by')}}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if($auditlog)
											@foreach($auditlog as $val)
												<tr>
													<td>
														<input type="checkbox" name="allAuditIDs[]" class="select-Audit-chkbox" value="{{$val['id']}}">
													</td>
													<td>{{$val['id']}}</td>
													<td>{{ $val['page']}}</td>
													<td>{{ $val['Log_id']}}</td>
													<td>{{ date('d/m/Y',strtotime($val['date'])) }}</td>
													<td>
														@php
															if(app()->getLocale() == "en"){
																if(!empty($val['users']['English_name'])){
																	echo $val['users']['English_name'] ?? '';
																}else{ 
																	echo $val['users']['UserName'] ?? '';
																}
															}

															if(app()->getLocale() == "ch"){
																if(!empty($val['users']['Chinese_name'])){
																	echo $val['users']['Chinese_name'] ?? '';
																}else{ 
																	echo $val['users']['UserName'] ?? '';
																}
															}
														@endphp
													</td>
													<td><a href="{{ url('audit-log/show',$val['id']) }}"><i class="bx bx-show-alt"></i></a>
														<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deletelog"><i class="bx bx-trash-alt"></i></a>
													</td>
												</tr>
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
<script>
	AuditIds = [];
	$(document).on("click", ".select-all-Audit-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#logtable")
			.DataTable()
			.table("#logtable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Audit-chkbox").prop('checked', true);
				var auditids = row.closest('tr').find(".select-Audit-chkbox").val();
				if (AuditIds.indexOf(auditids) !== -1) {
					// Current value is exists in array
				} else {
					AuditIds.push(auditids);
				}
			});
		} else {
			$("#logtable")
			.DataTable()
			.table("#logtable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Audit-chkbox").prop('checked', false);
			});
			AuditIds = [];
		}
	});

	$(document).on("click", ".select-Audit-chkbox", function (){
		if($('.select-Audit-chkbox').length === $('.select-Audit-chkbox:checked').length){
			$(".select-all-Audit-chkbox").prop('checked',true);
		}else{
			$(".select-all-Audit-chkbox").prop('checked',false);
		}
		auditids = $(this).val();
		if ($(this).is(":checked")) {
			if (AuditIds.indexOf(auditids) !== -1) {
				// Current value is exists in array
			} else {
				AuditIds.push(auditids);
			}
		} else {
			AuditIds = $.grep(AuditIds, function(value) {
				return value != auditids;
			});
		}
	});
</script>
<!-- /footer content -->
@endsection