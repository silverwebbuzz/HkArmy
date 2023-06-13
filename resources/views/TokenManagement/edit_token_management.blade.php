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
						<h3 class="content-header-title float-left pr-1 mb-0">{{ __('languages.Token Management.Update Token') }}</h3>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body new-user">
			<section class="users-edit">
				<div class="card">
					<div class="card-content">
						<form action="{{ url('token-management/update',$tokenData['id']) }}" method="POST" id="tokenUpdate" name="tokenUpdate">
						<input type="hidden" name="_token"  id="csrf-token" value="{{ csrf_token() }}">
							
							@if(app()->getLocale() == 'ch')
							@php
							$user_name = $tokenData->users['Chinese_name'];
							@endphp
							@else
							@php
							$user_name = $tokenData->users['English_name'];
							@endphp
							@endif

							<div class="card-body">
								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="MemberName">{{ __('languages.member.Member_Name') }}</label>
										<input type="text" class="form-control" id="MemberName" name="user_id" placeholder="{{ __('languages.member.Member_Name') }}" value="{{ $user_name }}" readonly="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="MemberName">{{ __('languages.event.Event Name') }}</label>
										<input type="text" class="form-control" id="EventName" name="event_id" placeholder="{{ __('languages.event.Event Name') }}" value="{{ $tokenData->event['event_name'] }}" readonly="">
									</div>
								</div>
								

								<div class="form-row">
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Token">{{ __('languages.member.Tokens') }}</label>
										<input type="text" class="form-control" id="Token" name="token" placeholder="{{ __('languages.member.Tokens') }}" value="{{ $tokenData->generate_token }}">
										<span class="text-danger">{{ $errors->first('token') }}</span>
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="MemberName">{{__('languages.used_token')}}</label>
										<input type="text" class="form-control" id="remark" name="used_token" placeholder="{{__('languages.used_token')}}" value="{{ $tokenData->used_token }}">
										<span class="text-danger">{{ $errors->first('used_token') }}</span>
									</div>
								</div>


								<div class="form-row">
								<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="MemberName">{{ __('languages.Token Management.Remaining Token') }}</label>
										<input type="hidden" class="form-control" id="old_remaining_token" name="old_remaining_token" placeholder="{{ __('languages.Token Management.Remaining Token') }}" value="{{ $tokenData->remaining_token }}">
										<input type="number" class="form-control" id="remaining_token" name="remaining_token" placeholder="" value="{{ ($tokenData->generate_token) - ($tokenData->used_token) }}" readonly="">
									</div>
									<div class="form-group col-md-6 mb-50">
										<label class="text-bold-600" for="Token">{{ __('languages.Token Management.Expire Date') }}</label>
										<fieldset class="form-group position-relative has-icon-left">
											<input type="text" class="form-control" placeholder="{{ __('languages.Token Management.Expire Date') }}" name="expired_at" id="expired_at" value="{{ date('m/d/Y',strtotime($tokenData['expire_date'])) }}">
											<div class="form-control-position">
												<i class='bx bx-calendar'></i>
											</div>
										</fieldset>
										<span class="text-danger">{{ $errors->first('expired_date') }}</span>
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