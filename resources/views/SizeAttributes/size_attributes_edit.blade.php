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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Size_Attributes.Edit_Size_Attribute') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ route('size-attributes.update',$SizeAttribute->id) }}" method="POST" id="sizeAttributesForm" name="sizeAttributesForm">
						@csrf()
						@method('patch')
							<div class="card-body">
							<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="chineseSizeAttributes">{{ __('languages.Size_Attributes.Chinese_Size_Attribute') }}</label>
										<input type="text" class="form-control" id="chineseSizeAttributes" name="chineseSizeAttributes" value="{{$SizeAttribute->name_ch}}" placeholder="Enter Chainese Size Attribute">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="englishSizeAttributes">{{ __('languages.Size_Attributes.English_Size_Attribute') }}</label>
										<input type="text" class="form-control" id="englishSizeAttributes" name="englishSizeAttributes" value="{{$SizeAttribute->name_en}}" placeholder="Enter English Size Attribute">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.event.Select_status') }}</option>
											<option value="1" @if($SizeAttribute['status'] == '1') selected @endif>{{ __('languages.Active') }}</option>
											<option value="2" @if($SizeAttribute['status'] == '2') selected @endif>{{ __('languages.Inactive') }}</option>
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