<!-- BEGIN: Header-->
<div class="header-navbar-shadow"></div>
<nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
	<div class="navbar-wrapper">
		<div class="navbar-container content">
			<div class="navbar-collapse" id="navbar-mobile">
				<div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
					<ul class="nav navbar-nav">
						<li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon bx bx-menu"></i></a></li>
					</ul>
				</div>
				<ul class="nav navbar-nav float-right">
					<li class="dropdown dropdown-language nav-item">
						@if(app()->getLocale() == 'ch')
							<a class="dropdown-toggle nav-link" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="selected-language">中文 </span></a>
						@else
							<a class="dropdown-toggle nav-link" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="selected-language">English </span></a>
						@endif
							<div class="dropdown-menu" aria-labelledby="dropdown-flag">
								<a class="dropdown-item" href="{{ url('locale/ch') }}"> 中文 </a>
								<a class="dropdown-item" href="{{ url('locale/en') }}"> English</a>
							</div>
						</li>
					<li class="dropdown dropdown-user nav-item">
						<a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
							@if(Session::get('user')['username'])
								<div class="user-nav d-sm-flex d-none"><span class="user-name">{{ Session::get('user')['username'] }}</span><span class="user-status text-muted">{{ __('languages.sidebar.Available') }}</span></div>
							@else
								<div class="user-nav d-sm-flex d-none"><span class="user-name">{{ Session::get('user')['Chinese_name'] }} & {{ Session::get('user')['English_name'] }}</span><span class=" user-status text-muted">{{ __('languages.sidebar.Available') }}</span></div>
							@endif
							@if(!empty(Session::get('user')['image']) && Session::get('user')['image'])
								<span><img class="round" src="{{ asset(Session::get('user')['image']) }}" alt="avatar" height="40" width="40"></span>
							@else
								<span><img class="round" src="{{ asset('app-assets/images/portrait/small/avatar-s-11.jpg') }}" alt="avatar" height="40" width="40"></span>
							@endif
						</a>
						<div class="dropdown-menu dropdown-menu-right pb-0">
							<a class="dropdown-item" href="{{ url('profile') }}"><i class="bx bx-user mr-50"></i>{{ __('languages.sidebar.Edit_Profile') }}</a>
							<a href="{{ url('changepassword') }}" class="dropdown-item" href="javascript:void(0);"><i class="bx bx-user mr-50"></i>{{ __('languages.ChangePassword.ChangePassword') }}</a>
							<a class="dropdown-item" href="{{ route('logout') }}"><i class="bx bx-power-off mr-50"></i> {{ __('languages.sidebar.Logout') }}</a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>
<!-- END: Header-->