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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Rank.Edit_Rank') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ url('rank',$SubElite['id']) }}" method="POST" id="subeliteForm" name="subeliteForm">
							{{ method_field('PUT') }}
						<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="ChineseSubElite">{{ __('languages.Team.Team') }}</label>
										<select class="form-control" id="elite" name="elite">
											<option value="">{{ __('languages.SubElite.Select_elite') }}</option>
											@if($Eiltes)
												@php
													$eilte = 'elite_'.app()->getLocale();
												@endphp
												@foreach($Eiltes as $row)
													<option value="{{ $row['id'] }}" @if($SubElite['elite_id'] == $row['id']) selected @endif>{{ $row[$eilte] }}</option>
												@endforeach
											@endif
										</select>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="ChineseSubElite">{{ __('languages.Rank.Chinese_Rank') }}</label>
										<input type="text" class="form-control" id="chinesesubelite" name="chinesesubelite" placeholder="" value="{{ $SubElite['subelite_ch'] }}">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="englishqualification">{{ __('languages.Rank.English_Rank') }}</label>
										<input type="text" class="form-control" id="englishsubelite" name="englishsubelite" placeholder="" value="{{ $SubElite['subelite_en'] }}">
									</div>
								</div>
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label for="users-list-role">{{ __('languages.Status') }}</label>
										<select class="form-control" id="status" name="status">
											<option value="">{{ __('languages.event.Select_status') }}</option>
											<option value="1" @if($SubElite['status'] == '1') selected @endif>{{ __('languages.Active') }}</option>
											<option value="2" @if($SubElite['status'] == '2') selected @endif>{{ __('languages.Inactive') }}</option>
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