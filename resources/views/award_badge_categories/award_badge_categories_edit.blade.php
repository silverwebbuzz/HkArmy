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
				<div class="row">
					<div class="col-12">
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.awards_badges_categories.add_award_badge_categories') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">						
						<form action="{{ route('awards-badges-categories.update',$CategoriesData->id) }}" method="POST" id="AwardBadgeCategoriesForm" name="AwardBadgeCategoriesForm" enctype='multipart/form-data'>
						@csrf()
						@method('patch')
						<input type="hidden" name="_token" id="csrf-token" value="{{ csrf_token() }}">
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.awards_badges_categories.categories_types') }}</label>
										<select class="form-control" id="categories_type" name="categories_type">
											<option value="" selected>{{ __('languages.awards_badges_categories.select_categories') }}</option>
											<option value="award" @if($CategoriesData->categories_type == 'award') selected @endif>{{ __('languages.awards_badges_categories.award') }}</option>
											<option value="badge" @if($CategoriesData->categories_type == 'badge') selected @endif>{{ __('languages.awards_badges_categories.badge') }}</option>
										</select>
										@if($errors->has('categories_type'))
    										<span class="validation_error">{{ $errors->first('categories_type') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50" id="parent-categories-section" <?php if($CategoriesData->categories_type == 'badge' && $CategoriesData->is_mentor_team_categories == 'no'){ echo 'style="display:block;"'; }else{echo 'style="display:none;"';} ?>>
										<label for="users-list-role">{{ __('languages.awards_badges_categories.parent_categories') }}</label>
										<select class="form-control" id="parent_categories_id" name="parent_categories_id">
											{{!!$categories_list!!}}
										</select>
										@if($errors->has('parent_categories_id'))
    										<span class="validation_error">{{ $errors->first('parent_categories_id') }}</span>
										@endif
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-6 mb-50" id="is_current_team_member_option" <?php if($CategoriesData->categories_type == 'badge'){ echo 'style="display:block;"'; }else{echo 'style="display:none;"';} ?>>
										<label for="users-list-role">{{ __('languages.awards_badges_categories.current_team_membor_mentor') }}</label>
										<select class="form-control" id="is_team_member_mentor" name="is_team_member_mentor">
											<option value="" selected>{{ __('languages.awards_badges_categories.select_categories') }}</option>
											<option value="yes" @if($CategoriesData->is_mentor_team_categories === 'yes') selected @endif>{{ __('languages.awards_badges_categories.yes') }}</option>
											<option value="no" @if($CategoriesData->is_mentor_team_categories === 'no') selected @endif>{{ __('languages.awards_badges_categories.no') }}</option>
										</select>
										@if($errors->has('is_team_member_mentor'))
    										<span class="validation_error">{{ $errors->first('is_team_member_mentor') }}</span>
										@endif
									</div>
								</div>

								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="name_en">{{ __('languages.awards_badges_categories.categories_name_en') }}</label>
										<input type="text" class="form-control" id="name_en" name="name_en" placeholder="{{ __('languages.awards_badges_categories.categories_name_en') }}" value="{{$CategoriesData->name_en}}">
										@if($errors->has('name_en'))
    										<span class="validation_error">{{ $errors->first('name_en') }}</span>
										@endif
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="award_name_ch">{{ __('languages.awards_badges_categories.categories_name_ch') }}</label>
										<input type="text" class="form-control" id="name_ch" name="name_ch" placeholder="{{ __('languages.awards_badges_categories.categories_name_ch') }}" value="{{$CategoriesData->name_ch}}">
										@if($errors->has('name_ch'))
    										<span class="validation_error">{{ $errors->first('name_ch') }}</span>
										@endif
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.badges.select_status') }}</option>
											<option value="active" @if($CategoriesData->status == 'active') selected @endif>{{ __('languages.Active') }}</option>
											<option value="inactive" @if($CategoriesData->status == 'inactive') selected @endif>{{ __('languages.Inactive') }}</option>
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