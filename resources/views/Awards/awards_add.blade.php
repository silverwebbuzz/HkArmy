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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.awards.add_awards') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ route('awards.store') }}" method="POST" id="addAwardsForm" name="addAwardsForm" enctype='multipart/form-data'>
						<input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="award_name_en">{{ __('languages.awards.award_name_en') }}</label>
										<input type="text" class="form-control" id="award_name_en" name="name_en" placeholder="{{ __('languages.awards.award_name_en') }}" value="{{old('name_en')}}">
										@if($errors->has('name_en'))
    										<span class="validation_error">{{ $errors->first('name_en') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="award_name_ch">{{ __('languages.awards.award_name_ch') }}</label>
										<input type="text" class="form-control" id="award_name_ch" name="name_ch" placeholder="{{ __('languages.awards.award_name_ch') }}" value="{{old('name_ch')}}">
										@if($errors->has('name_ch'))
    										<span class="validation_error">{{ $errors->first('name_ch') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.awards.award_categories') }}</label>
										<select class="form-control" id="award_categories_id" name="award_categories_id">
											@if(!empty($get_awards_categories))
												@php echo $get_awards_categories; @endphp
											@endif
										</select>
										@if($errors->has('award_categories_id'))
    										<span class="validation_error">{{ $errors->first('award_categories_id') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="awards-year">{{ __('languages.awards.award_year') }}</label>
										<select class="form-control" id="award_year" name="award_year">
											<option value="">{{ __('languages.awards.select_award_year') }}</option>
											<?php
											for($i = 2000 ; $i <= 2050; $i++){
												echo "<option value=".$i.">$i</option>";
											}
											?>
										</select>
										@if($errors->has('award_year'))
    										<span class="validation_error">{{ $errors->first('award_year') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row" id="other-awards-type-section" style="display:none;">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="other_awards_type_en">{{__('languages.awards.other_award_type_english')}}</label>
										<input type="text" class="form-control" id="other_awards_type_en" name="other_awards_type_en" placeholder="{{__('languages.awards.other_award_type_english')}}" value="{{old('other_awards_type_en')}}">
										@if($errors->has('other_awards_type_en'))
    										<span class="validation_error">{{ $errors->first('other_awards_type_en') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="other_awards_type_ch">{{__('languages.awards.other_award_type_chinese')}}</label>
										<input type="text" class="form-control" id="other_awards_type_ch" name="other_awards_type_ch" placeholder="{{__('languages.awards.other_award_type_chinese')}}" value="{{old('other_awards_type_ch')}}">
										@if($errors->has('other_awards_type_ch'))
    										<span class="validation_error">{{ $errors->first('other_awards_type_ch') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="reference_number">{{__('languages.awards.reference_number')}}</label>
										<input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="{{__('languages.awards.reference_number')}}" value="{{old('reference_number')}}">
										@if($errors->has('reference_number'))
    										<span class="validation_error">{{ $errors->first('reference_number') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.badges.select_status') }}</option>
											<option value="active" selected>{{ __('languages.Active') }}</option>
											<option value="inactive">{{ __('languages.Inactive') }}</option>
										</select>
										@if($errors->has('status'))
    										<span class="validation_error">{{ $errors->first('status') }}</span>
										@endif
									</div>
								</div>
								<input type="submit" class="btn btn-primary glow" value="{{ __('languages.Submit') }}" name="submit">
							</div>
						</form>
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