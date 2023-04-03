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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Categories.Edit_Categories') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ route('categories.update',$Categories->id) }}" method="POST" id="categoriesForm" name="categoriesForm">
						@csrf()
						@method('patch')
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chinesecategories">{{ __('languages.Categories.Chinese_Categories') }}</label>
										<input type="text" class="form-control" id="chinesecategories" name="chinesecategories_name" placeholder="{{ __('languages.Size_Attributes.Ch_Place_Holder') }}" value="{{$Categories->name_ch}}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="englishcategories">{{ __('languages.Categories.English_Categories') }}</label>
										<input type="text" class="form-control" id="englishcategories" name="englishcategories_name" placeholder="{{ __('languages.Size_Attributes.En_Place_Holder') }}" value="{{$Categories->name_en}}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.event.Select_status') }}</option>
											<option value="1" @if($Categories['status'] == '1') selected @endif>{{ __('languages.Active') }}</option>
											<option value="2" @if($Categories['status'] == '2') selected @endif>{{ __('languages.Inactive') }}</option>
										</select>
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