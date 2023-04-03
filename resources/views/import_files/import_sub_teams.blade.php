@extends('layouts.app')

@section('content')
<!-- top navigation -->
@include('layouts.header')
<!-- /top navigation -->
@include('layouts.sidebar')
<div class="app-content content">
    <div class="sm-right-detail-sec import-pages-header pl-5 pr-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="sec-title">
                        <h2 class="mb-4">{{__('Import Sub Team')}}</h2>
                    </div>
                </div>
            </div>
            
            <div class="sm-add-user-sec card">
                <div class="select-option-sec pb-5 card-body">
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
                    <form class="user-form" method="post" id="importSubTeam"  action="{{ route('import-sub-teams') }}" enctype="multipart/form-data">
                        @csrf()
                        <div class="form-row select-data align-manage">
                            
                            <div class="form-group col-md-4">
                                <label for="users-list-role">{{ __('Upload CSV File') }}</label>
                                <fieldset class="form-group">
                                    <input type="file" name="user_file" value="">
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="form-group mb-50 btn-sec">
                                    <button class="blue-btn btn btn-primary">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                <hr class="blue-line">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- </div> --}}
@include('layouts.footer') 
@endsection