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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Specialty.Edit_Specialty') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ url('specialty',$Specialty['id']) }}" method="POST" id="specialtyForm" name="specialtyForm">
						<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
						{{ method_field('PUT') }}
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="ChineseSpecialty">{{ __('languages.Specialty.Chinese_Specialty') }}</label>
										<input type="text" class="form-control" id="chinesespecialty" name="chinesespecialty" placeholder="" value="{{ $Specialty['specialty_ch'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="englishqualification">{{ __('languages.Specialty.English_Specialty') }}</label>
										<input type="text" class="form-control" id="englishspecialty" name="englishspecialty" placeholder="" value="{{ $Specialty['specialty_en'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.event.Select_status') }}</option>
											<option value="1" @if($Specialty['status'] == '1') selected @endif>{{ __('languages.Active') }}</option>
											<option value="2" @if($Specialty['status'] == '2') selected @endif>{{ __('languages.Inactive') }}</option>
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