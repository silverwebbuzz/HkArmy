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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.badges.add_badges') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ route('badges.store') }}" method="POST" id="addBadgesForm" name="addBadgesForm" enctype='multipart/form-data'>
						<input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="select_team_mentor">{{ __('languages.awards.current_team_member') }}</label>
										<select class="form-control" id="current_team_member" name="current_team_member">
											<option value="">{{ __('languages.awards.select_current_team_member') }}</option>
											<option value="mentor_team">{{ __('languages.awards.mentor_team') }}</option>
											<option value="not_mentor_team">{{ __('languages.awards.not_mentor_team') }}</option>
										</select>
										@if($errors->has('current_team_member'))
    										<span class="validation_error">{{ $errors->first('current_team_member') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.badges.badges_type') }}</label>
										<select class="form-control" id="badges_type_id" name="badges_type_id">
											<option value="">{{ __('languages.badges.Select_badges_type') }}</option>
										</select>
										@if($errors->has('badges_type_id'))
    										<span class="validation_error">{{ $errors->first('badges_type_id') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="badges_name_en">{{ __('languages.badges.badges_name_en') }}</label>
										<input type="text" class="form-control" id="badges_name_en" name="name_en" placeholder="{{ __('languages.badges.badges_name_en') }}" value="{{old('name_en')}}">
										@if($errors->has('name_en'))
    										<span class="validation_error">{{ $errors->first('name_en') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="badges_name_ch">{{ __('languages.badges.badges_name_ch') }}</label>
										<input type="text" class="form-control" id="badges_name_ch" name="name_ch" placeholder="{{ __('languages.badges.badges_name_ch') }}" value="{{old('name_ch')}}">
										@if($errors->has('name_ch'))
    										<span class="validation_error">{{ $errors->first('name_ch') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<!-- <div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.badges.badges_type') }}</label>
										<select class="form-control" id="badges_type_id" name="badges_type_id">
											<option value="">{{ __('languages.badges.Select_badges_type') }}</option>
											@if(!empty($get_badges_type_list))
												@php echo $get_badges_type_list; @endphp
											@endif
										</select>
										@if($errors->has('badges_type_id'))
    										<span class="validation_error">{{ $errors->first('badges_type_id') }}</span>
										@endif
									</div> -->
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.badges.upload_badges_image') }}</label>
										<input type="file" class="form-control" id="badges_image" name="badges_image" value="">
										<p>{{__('languages.badges.image_note')}}</p>
										<img id="preview-badges-image" src="" height="200" width="300">
									</div>
								</div>
								<div class="form-row" id="other-badges-type-section" style="display:none;">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="other_badges_type_en">{{__('languages.badges.other_badges_type_english')}}</label>
										<input type="text" class="form-control" id="other_badges_type_en" name="other_badges_type_en" placeholder="{{__('languages.badges.other_badges_type_english')}}" value="{{old('other_badges_type_en')}}">
										@if($errors->has('other_badges_type_en'))
    										<span class="validation_error">{{ $errors->first('other_badges_type_en') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="other_badges_type_ch">{{__('languages.badges.other_badges_type_chinese')}}</label>
										<input type="text" class="form-control" id="other_badges_type_ch" name="other_badges_type_ch" placeholder="{{__('languages.badges.other_badges_type_chinese')}}" value="{{old('other_badges_type_ch')}}">
										@if($errors->has('other_badges_type_ch'))
    										<span class="validation_error">{{ $errors->first('other_badges_type_ch') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
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