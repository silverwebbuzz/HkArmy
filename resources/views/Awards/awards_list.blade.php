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
					<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.awards.awards') }}</h3>
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
						<a href="{{ route('awards.create') }}" type="reset" class="btn btn-primary btn-block glow users-list-clear mb-0">
							<i class="bx bx-user-plus"></i>{{ __('languages.awards.add_awards') }}
						</a>
					</div>
				</div>
			</div>
				<div class="users-list-table">
					<div class="card">
						<div class="card-content">
							<div class="card-body">
								<div class="table-responsive">
									<table id="qualificationstable" class="table">
										<thead>
											<tr>
												<th>{{ __('SR.NO') }}</th>
												<th>{{ __('languages.awards.name') }}</th>
												<th>{{ __('languages.awards.award_categories') }}</th>
												<th>{{ __('languages.awards.other_categories_name') }}</th>
												<th>{{ __('languages.awards.award_year') }}</th>
												<th>{{ __('languages.awards.reference_number') }}</th>
												<th>{{ __('languages.Status') }}</th>
												<th>{{ __('languages.Action') }}</th>
											</tr>
										</thead>
										<tbody>
											@if(!empty($AwardsData))
											@php
												$awardCategoriesName = 'name_'.app()->getLocale();
												$awardsName = 'name_'.app()->getLocale();
												$OtherAwardType = 'other_awards_type_'.app()->getLocale();
											@endphp
												@foreach($AwardsData as $val)
													<tr>
														<td>{{ $loop->iteration}}</td>
														<td>{{ $val->$awardsName}}</td>
														@if($val->award_categories_id == 0)
														<td>{{ __('languages.awards.other') }}</td>
														@else
														<td>{{ $val->awardscategories[$awardCategoriesName] ?? '---'}}</td>
														@endif
														<td>{{$val->$OtherAwardType ?? '---'}}</td>
														<td>{{$val->award_year ?? ''}}</td>
														<td>{{$val->reference_number ?? ''}}</td>
														@if($val['status'] == "active")
															<td><span class="badge badge-light-success">{{ __('languages.Active') }}</span></td>
														@else
															<td><span class="badge badge-light-danger">{{ __('languages.Inactive') }}</span></td>
														@endif
														<td>
															<a href="{{ route('awards.edit',$val['id']) }}"><i class="bx bx-edit-alt"></i></a>
															<a href="javascript:void(0);" data-id="{{ $val['id'] }}" class="deleteAwards"><i class="bx bx-trash-alt"></i> </a>
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
<!-- /footer content -->
@endsection