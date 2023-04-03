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
                        <h2 class="mb-4 content-header-title">{{__('languages.import')}} {{__('languages.Member')}}</h2>
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
                    <form class="user-form" method="post" id="importUsers"  action="{{ route('import-users') }}" enctype="multipart/form-data">
                        @csrf()
                        <div class="row select-data align-manage">
                            <div class="form-group col-md-4">
                                <label for="users-list-role">{{ __('languages.upload_csv_file') }}</label>
                                <fieldset class="form-group">
                                    <input type="file" name="user_file" value="">
                                </fieldset>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="form-group mb-50 btn-sec">
                                    <button class="blue-btn btn btn-primary">{{ __('languages.Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                <hr class="blue-line">
                    <div class="form-group col-md-12">
                        <h6>{{__('languages.member_template_csv')}}</h6>
                        <p>
                            <a class="wv-text--link" href="{{asset('uploads/sample_files/member.csv')}}">
                                <i class="fa fa-download" aria-hidden="true"></i>
                                {{ __('languages.download_and_view_member_csv')}}
                            </a>
                            {{ __('languages.sample_member_template')}}
                        </p> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- </div> --}}
@include('layouts.footer') 
@endsection