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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.sidebar.Transaction History') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- users list start -->
			<section class="users-list-wrapper">
				<div class="users-list-filter px-1">
<!-- <div class="row border rounded py-2 mb-2">
<div class="float-right align-items-center ml-1">
<a href="{{ route('team.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0"><i class="bx bx-user-plus"></i> {{ __('languages.Team.Add_Team') }}</a>
</div>
</div> -->
</div>
{{-- Export Button Start --}}
<div class="row mb-2">
	<div class="float-right align-items-center ml-1">
		<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-product-transaction_history mb-0"> {{__('languages.export_transaction_history')}}</a>
	</div>
</div>
{{-- Export Button End --}}
<div class="users-list-table">
	<div class="card">
		<div class="card-content">
			<div class="card-body">
				<div class="table-responsive">

					<table id="qualificationstable" class="table">
						<thead>
							<tr>
								<th>
									<input type="checkbox" name="allproductTransactionIDs[]" class="select-all-product-transaction-chkbox" value="all">
								</th>
								<th>{{ __('languages.Sr_No') }}</th>
								<th>{{__('languages.member.Member_Number')}}</th>
								{{-- <th>{{ __('languages.member.Member_Name') }}</th> --}}
								<th>{{__('languages.member.English_name')}}</th>
								<th>{{__('languages.member.Chinese_name')}}</th>
								<th>{{ __('languages.Product.Product_name') }}</th>
								<th>{{__('languages.product_suffix_code')}} & {{__('languages.name')}}</th>
								<th>{{ __('languages.event.Date') }}</th>
								<th>{{ __('languages.Remarks.Remarks') }}</th>
								<th>{{ __('languages.member.Tokens') }}</th>
								<th>{{ __('languages.member.Money') }}</th>
								<th>{{ __('languages.Status') }}</th>
								<!-- <th>{{ __('languages.Action') }}</th> -->
							</tr>
						</thead>
						<tbody>
							@if(!empty($result))
							@foreach($result as $val)
							<?php //echo '<pre>';print_r($val['child_products']['product_suffix']);die; ?>

							{{-- @if(app()->getLocale() == 'ch')
							@php
							$user_name = $val['users']['Chinese_name'];
							@endphp
							@else
							@php
							$user_name = $val['users']['English_name'];
							@endphp
							@endif --}}
							<tr>
								<td>
									<input type="checkbox" name="productTransactionIDs[]" class="select-product-transaction-chkbox" value="{{$val['id']}}">
								</td>
								<td>{{$val['id']}}</td>
								<td>C{{ $val['users']['MemberCode'] }}</td>
								<td>{{ $val['users']['English_name'] ?? '' }}</td>
								<td>{{ $val['users']['Chinese_name'] ?? '' }}</td>
								<td>@if(!empty($val['product']['product_name'])){{ $val['product']['product_name'] }}@else - @endif</td>
								<td>
								<!-- @if(!empty($val['child_products']))
								<ul>
									<li><?php echo $val['child_products']['product_suffix'].' + '.$val['child_products']['product_suffix_name']; ?></li>
								</ul>
								@endif -->
								@if(!empty($val['child_product_id']))
									@if(isset($val['product']['combo_product_ids']) && !empty($val['product']['combo_product_ids']))
									{!!Helper::get_transaction_history_child_product($val['id'])!!}
									@else
									{{$val['child_products']['product_suffix'].' + '.$val['child_products']['product_suffix_name']}}
									@endif
								@endif
								</td>
								<td>{{ Helper::dateConvertDDMMYYY('-','/',$val['created_at'])}}</td>
								<td>
									<span  id="TremarkEdit<?php echo $val['id']; ?>" style="display:none;"><input type="text" class="Tremarkinput" data-id="<?php echo $val['id']; ?>" value="<?php echo $val['remark']; ?>" />
									</span>
									<span  class="TremarkClick" data-id="<?php echo $val['id']; ?>" id="TremarkShow<?php echo $val['id']; ?>">
									<?php 
									if(isset($val['remark'])){
										echo $val['remark'];
									}else{
										echo '-';
									}
									?>
									</span>
								</td>
								<td>{{ $val['token'] }}</td>
								<td>{{ $val['money'] }}</td>
								<td>
									<select class="form-control product_assign_status">
										<option value="">{{ __('languages.event.Select_status') }}</option>
										<option value="0" data-id="{{ $val['id'] }}" @if($val['status'] == '0') selected @endif>{{__('languages.Not_Confirm')}}</option>
										<option value="1" data-id="{{ $val['id'] }}" @if($val['status'] == '1') selected @endif>{{__('languages.Confirm')}}</option>
									</select>
								</td>
								<!-- <td><a href="javascript:void(0);" data-id="{{ $val['id'] }}" data-remark="{{ $val['remark'] }}" data-toggle="modal" data-backdrop="false" data-target="#backdrop" class="product-edit-remark"><i class="bx bx-edit-alt"></i></a>
								</td> -->
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
<!-- /footer content -->

<!--Disabled Backdrop Modal -->
<div class="modal fade text-left addmember_modal" id="backdrop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel4">Add / Edit Remark</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="bx bx-x"></i>
				</button>
			</div>
			<div class="modal-body">
				<p>
					<div class="form-row addmember_modal_form">
						<div class="form-group">
							<input type="hidden" name="productAssignId" value="">
							<label for="users-list-role" style="margin-right: 15px;">{{ __('languages.Remarks.Remarks') }}</label>
							<input type="text" class="form-control" id="remarks" name="remarks" value=""  placeholder="Event Remarks">
						</div>
					</div>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light-secondary" data-dismiss="modal">
					<i class="bx bx-x d-block d-sm-none"></i>
					<span class="d-none d-sm-block">{{ __('languages.Cancel') }}</span>
				</button>
				<button type="button" class="btn btn-primary ml-1 addProductRemark">
					<i class="bx bx-check d-block d-sm-none"></i>
					<span class="d-none d-sm-block">{{ __('languages.Save') }}</span>
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exportProductHistorySelectField" tabindex="-1" role="dialog" aria-labelledby="exportProductHistorySelectField" data-backdrop="static" aria-hidden="true">
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
						<input type="checkbox" name="exportProductHistoryFields[]" class="all-product-history-field-checkbox" value="all" checked>
						<span>{{__('languages.export_fields.all_fields')}}</span>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="member_number" checked>
						<span>{{__('languages.member.Member_Number')}}</span>
					</div>
					{{-- <div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="member_name" checked>
						<span>{{ __('languages.member.Member_Name') }}</span>
					</div> --}}
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="english_name" checked>
						<span>{{ __('languages.member.English_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="chinese_name" checked>
						<span>{{ __('languages.member.Chinese_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="product_name" checked>
						<span>{{ __('languages.Product.Product_name') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="product_suffix_code_and_name" checked>
						<span>{{__('languages.product_suffix_code')}} & {{__('languages.name')}}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="date" checked>
						<span>{{ __('languages.event.Date') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="remarks" checked>
						<span>{{ __('languages.Remarks.Remarks') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="tokens" checked>
						<span>{{ __('languages.member.Tokens') }}</span>
					</div>
					<div class="col-md-6">
						<input type="checkbox" name="exportProductHistoryFields[]" class="product-history-field-checkbox" value="money" checked>
						<span>{{ __('languages.member.Money') }}</span>
					</div>
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('languages.export_fields.close')}}</button>
				<button type="button" class="btn btn-primary" onClick="exportProductHistory()">{{__('languages.export_fields.events.export_events')}}</button>
			</div>
		</div>
	</div>
</div>

<script>
var ExportProductHistoryFieldColumnList = ['member_number','english_name','chinese_name','product_name','product_suffix_code_and_name','date','remarks','tokens','money'];
var productTransactionIds = [];
$(function () {

	// On click on checkbox eventlist 
	$(document).on("click", ".select-all-product-transaction-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-product-transaction-chkbox").prop('checked', true);
				var transactionid = row.closest('tr').find(".select-product-transaction-chkbox").val();
				if (productTransactionIds.indexOf(transactionid) !== -1) {
					// Current value is exists in array
				} else {
					productTransactionIds.push(transactionid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-product-transaction-chkbox").prop('checked', false);
			});
			productTransactionIds = [];
		}
	});

	
	$(document).on("click", ".select-product-transaction-chkbox", function (){
		if($('.select-product-transaction-chkbox').length === $('.select-product-transaction-chkbox:checked').length){
			$(".select-all-product-transaction-chkbox").prop('checked',true);
		}else{
			$(".select-all-product-transaction-chkbox").prop('checked',false);
		}
		transactionid = $(this).val();
		if ($(this).is(":checked")) {
			if (productTransactionIds.indexOf(transactionid) !== -1) {
				// Current value is exists in array
			} else {
				productTransactionIds.push(transactionid);
			}
		} else {
			productTransactionIds = $.grep(productTransactionIds, function(value) {
				return value != transactionid;
			});
		}
	});

	$(document).on("click", ".export-product-transaction_history", function () {
		$("#exportProductHistorySelectField").modal('show');
	});

	$(document).on("click", ".all-product-history-field-checkbox", function (){
		if ($(this).is(":checked")) {
			$(".product-history-field-checkbox").each(function () {
				$(this).prop('checked', true);
				var productHistoryColumnName = $(this).val();
				if (ExportProductHistoryFieldColumnList.indexOf(productHistoryColumnName) !== -1) {
					// Current value is exists in array
				} else {
					ExportProductHistoryFieldColumnList.push(productHistoryColumnName);
				}
			});
		} else {
			$(".product-history-field-checkbox").each(function () {
				$(this).prop('checked',false);
			});
			ExportProductHistoryFieldColumnList = [];
		}
	});

	$(document).on("click", ".product-history-field-checkbox", function (){
		if($('.product-history-field-checkbox').length === $('.product-history-field-checkbox:checked').length){
			$(".all-product-history-field-checkbox").prop('checked',true);
		}else{
			$(".all-product-history-field-checkbox").prop('checked',false);
		}
		var productHistoryColumnName = $(this).val();
		if ($(this).is(":checked")) {
			if (ExportProductHistoryFieldColumnList.indexOf(productHistoryColumnName) !== -1) {
				// Current value is exists in array
			} else {
				ExportProductHistoryFieldColumnList.push(productHistoryColumnName);
			}
		} else {
			ExportProductHistoryFieldColumnList = $.grep(ExportProductHistoryFieldColumnList, function(value) {
				return value != productHistoryColumnName;
			});
		}
	});
});

function exportProductHistory(){
	if($('.product-history-field-checkbox:checked').length === 0){
        toastr.error('Please select atleast one column for export csv');
    }else if(! $("#qualificationstable").DataTable().data().count()){
		toastr.error('No data available in table');
	}else{
		$.ajax({
			type: "GET",
			url: BASE_URL + "/export/product-history",
			data: {
				'columnList' : ExportProductHistoryFieldColumnList,
				'productTransactionIds' : productTransactionIds
			},
			contentType: 'application/json; charset=utf-8',
			success: function (data) {
				//return false;
				var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
				if (!isHTML(data)) {
					var downloadLink = document.createElement("a");
					var fileData = ["\ufeff" + data];

					var blobObject = new Blob(fileData, {
						type: "text/csv;charset=utf-8;",
					});

					var url = URL.createObjectURL(blobObject);
					downloadLink.href = url;
					downloadLink.download = "ProductHistory.csv";
					exportHistroy('ProductHistory', url, fileData);

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