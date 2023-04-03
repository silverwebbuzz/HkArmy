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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.awards_badges_categories.award_badges_categories') }}</h3>
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
				<div class="row border rounded py-2 mb-2">
					<div class="float-right align-items-center ml-1">
						<a href="{{ route('awards-badges-categories.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">
							<i class="bx bx-user-plus"></i>{{ __('languages.awards_badges_categories.award_badges_categories') }}
						</a>
					</div>
				</div>
			</div>
			{{-- Export Button Start --}}
			<div class="row mb-2">
				<div class="float-right align-items-center ml-1">
					<a href="javascript:void(0);" class="btn btn-primary btn-block glow export-award_badges_categories mb-0"> {{ __('languages.export') }} {{ __('languages.awards_badges_categories.award_badges_categories') }}</a>
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
													<input type="checkbox" name="allAwardIDs[]" class="select-all-Award-chkbox" value="all">
												</th>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.awards_badges_categories.categories_type') }}</th>
												<th>{{ __('languages.awards_badges_categories.is_mentor_categories') }}</th>
												<th>{{ __('languages.awards_badges_categories.parent_categories') }}</th>
												<th>{{ __('languages.awards_badges_categories.categories_name') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($CategoriesList))
												@php
												$categoriesName = 'name_'.app()->getLocale();												
												@endphp
												@foreach($CategoriesList as $val)
													<tr>
														<td>
															<input type="checkbox" name="AwardIDs[]" class="select-Award-chkbox" value="{{$val['id']}}">
														</td>
														<td>{{$val['id']}}</td>
														<td>
															@if($val->categories_type == 'award')
																{{ __('languages.awards_badges_categories.award') }}	
															@else
																{{ __('languages.awards_badges_categories.badge') }}
															@endif
														</td>
														<td>
															@if($val->categories_type == 'badge' && $val->is_mentor_team_categories == 'yes')
																{{ __('languages.awards_badges_categories.yes') }}
															@elseif($val->categories_type == 'badge' && $val->is_mentor_team_categories == 'no')
																{{ __('languages.awards_badges_categories.no') }}
															@endif
														</td>
														@php
														$AwardsBadgesCategories = new \App\Http\Models\AwardsBadgesCategories;
														$ParentCategoriesData = $AwardsBadgesCategories->getParentCategoriesName($val->parent_categories_id);
														if(isset($ParentCategoriesData) && !empty($ParentCategoriesData)){
															$ParentCategoriesName = $ParentCategoriesData['name_'.app()->getLocale()];
														}else{
															$ParentCategoriesName = '----';
														}
														@endphp
														<td>{{$ParentCategoriesName}}</td>
														<td>{{ $val->$categoriesName}}</td>
														@if($val['status'] == "active")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('awards-badges-categories.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deleteAwardBadgeCategories"><i class="bx bx-trash-alt"></i> </a>
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
	AwardIds = [];
	$(document).on("click", ".select-all-Award-chkbox", function (){
		if ($(this).is(":checked")) {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Award-chkbox").prop('checked', true);
				var awardid = row.closest('tr').find(".select-Award-chkbox").val();
				if (AwardIds.indexOf(awardid) !== -1) {
					// Current value is exists in array
				} else {
					AwardIds.push(awardid);
				}
			});
		} else {
			$("#qualificationstable")
			.DataTable()
			.table("#qualificationstable")
			.rows()
			.every(function (index, element) {
				var row = $(this.node());
				row.closest('tr').find(".select-Award-chkbox").prop('checked', false);
			});
			AwardIds = [];
		}
	});

	$(document).on("click", ".select-Award-chkbox", function (){
		if($('.select-Award-chkbox').length === $('.select-Award-chkbox:checked').length){
			$(".select-all-Award-chkbox").prop('checked',true);
		}else{
			$(".select-all-Award-chkbox").prop('checked',false);
		}
		awardid = $(this).val();
		if ($(this).is(":checked")) {
			if (AwardIds.indexOf(awardid) !== -1) {
				// Current value is exists in array
			} else {
				AwardIds.push(awardid);
			}
		} else {
			AwardIds = $.grep(AwardIds, function(value) {
				return value != awardid;
			});
		}
	});
</script>
<!-- /footer content -->
@endsection