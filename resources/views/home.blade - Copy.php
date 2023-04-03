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
		</div>
		<div class="content-body">
			<section id="dashboard-analytics">
				<div class="row">
					<div class="col-md-8 col-sm-12">
						<div class="card">
							<div class="card-content">
								<div class="card-body pb-1">
									<div class="d-flex justify-content-around align-items-center flex-wrap">
										<div class="user-analytics">
											<i class="bx bx-user mr-25 align-middle"></i>
											<span class="align-middle text-muted">{{ __('languages.Home.Members') }}</span>
											<div class="d-flex">
												<div id="user-count-chart" data-users="{{ $users}}"></div>
												<h3 class="mt-1 ml-50">{{ $users }}</h3>
											</div>
										</div>
										<div class="sessions-analytics">
											<i class="bx bxs-book-open align-middle mr-25"></i>
											<span class="align-middle text-muted">{{ __('languages.Home.Attendance') }}</span>
											<div class="d-flex">
												<div id="attendance-count-chart" data-attedance="{{ $Attendance }}"></div>
												<h3 class="mt-1 ml-50">{{ $Attendance }}</h3>
											</div>
										</div>
										<div class="bounce-rate-analytics">
											<i class="bx bx-calendar-event align-middle mr-25"></i>
											<span class="align-middle text-muted">{{ __('languages.Home.Events') }}</span>
											<div class="d-flex">
												<div id="event-count-chart" data-event="{{ $Events }}"></div>
												<h3 class="mt-1 ml-50">{{ $Events }}</h3>
											</div>
										</div>
									</div>
									<div id="user-count-year"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6 col-sm-12 dashboard-referral-impression">
						<div class="row">
							<div class="col-xl-12 col-12">
								<div class="card">
									<div class="card-content">
										<div class="card-body text-center pb-0">
											<h2>${{ number_format($product_amount,2) }}</h2>
											<span class="text-muted">{{ __('languages.Home.Uniform_Selling') }}</span>
											<p></p>
										</div>
									</div>
								</div>
							</div>
							<!-- <div class="col-xl-12 col-12">
								<div class="card">
									<div class="card-content">
										<div class="card-body donut-chart-wrapper">
											<div id="donut-chart" class="d-flex justify-content-center"></div>
											<ul class="list-inline d-flex justify-content-around mb-0">
												<li> <span class="bullet bullet-xs bullet-warning mr-50"></span>Search</li>
												<li> <span class="bullet bullet-xs bullet-info mr-50"></span>Email</li>
												<li> <span class="bullet bullet-xs bullet-primary mr-50"></span>Social</li>
											</ul>
										</div>
									</div>
								</div>
							</div> -->
						</div>
					</div>
					<!-- <div class="col-xl-3 col-md-12 col-sm-12">
						<div class="row">
							<div class="col-xl-12 col-md-6 col-12">
								<div class="card">
									<div class="card-header d-flex justify-content-between pb-xl-0 pt-xl-1">
										<div class="conversion-title">
											<h4 class="card-title">Conversion</h4>
											<p>60%
												<i class="bx bx-trending-up text-success font-size-small align-middle mr-25"></i>
											</p>
										</div>
										<div class="conversion-rate">
											<h2>89k</h2>
										</div>
									</div>
									<div class="card-content">
										<div class="card-body text-center">
											<div id="bar-negative-chart"></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-xl-12 col-md-6 col-12">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body d-flex align-items-center justify-content-between">
												<div class="d-flex align-items-center">
													<div class="avatar bg-rgba-primary m-0 p-25 mr-75 mr-xl-2">
														<div class="avatar-content">
															<i class="bx bx-user text-primary font-medium-2"></i>
														</div>
													</div>
													<div class="total-amount">
														<h5 class="mb-0">$38,566</h5>
														<small class="text-muted">Conversion</small>
													</div>
												</div>
												<div id="primary-line-chart"></div>
											</div>
										</div>
									</div>
									<div class="col-12">
										<div class="card">
											<div class="card-body d-flex align-items-center justify-content-between">
												<div class="d-flex align-items-center">
													<div class="avatar bg-rgba-warning m-0 p-25 mr-75 mr-xl-2">
														<div class="avatar-content">
															<i class="bx bx-dollar text-warning font-medium-2"></i>
														</div>
													</div>
													<div class="total-amount">
														<h5 class="mb-0">$53,659</h5>
														<small class="text-muted">Income</small>
													</div>
												</div>
												<div id="warning-line-chart"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> -->
				</div>
				<!-- <div class="row">
					<div class="col-xl-3 col-md-6 col-12 activity-card">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Activity</h4>
							</div>
							<div class="card-content">
								<div class="card-body pt-1">
									<div class="d-flex activity-content">
										<div class="avatar bg-rgba-primary m-0 mr-75">
											<div class="avatar-content">
												<i class="bx bx-bar-chart-alt-2 text-primary"></i>
											</div>
										</div>
										<div class="activity-progress flex-grow-1">
											<small class="text-muted d-inline-block mb-50">Total Sales</small>
											<small class="float-right">$8,125</small>
											<div class="progress progress-bar-primary progress-sm">
												<div class="progress-bar" role="progressbar" aria-valuenow="50" style="width:50%"></div>
											</div>
										</div>
									</div>
									<div class="d-flex activity-content">
										<div class="avatar bg-rgba-success m-0 mr-75">
											<div class="avatar-content">
												<i class="bx bx-dollar text-success"></i>
											</div>
										</div>
										<div class="activity-progress flex-grow-1">
											<small class="text-muted d-inline-block mb-50">Income Amount</small>
											<small class="float-right">$18,963</small>
											<div class="progress progress-bar-success progress-sm">
												<div class="progress-bar" role="progressbar" aria-valuenow="80" style="width:80%"></div>
											</div>
										</div>
									</div>
									<div class="d-flex activity-content">
										<div class="avatar bg-rgba-warning m-0 mr-75">
											<div class="avatar-content">
												<i class="bx bx-stats text-warning"></i>
											</div>
										</div>
										<div class="activity-progress flex-grow-1">
											<small class="text-muted d-inline-block mb-50">Total Budget</small>
											<small class="float-right">$14,150</small>
											<div class="progress progress-bar-warning progress-sm">
												<div class="progress-bar" role="progressbar" aria-valuenow="60" style="width:60%"></div>
											</div>
										</div>
									</div>
									<div class="d-flex mb-75">
										<div class="avatar bg-rgba-danger m-0 mr-75">
											<div class="avatar-content">
												<i class="bx bx-check text-danger"></i>
											</div>
										</div>
										<div class="activity-progress flex-grow-1">
											<small class="text-muted d-inline-block mb-50">Completed Tasks</small>
											<small class="float-right">106</small>
											<div class="progress progress-bar-danger progress-sm">
												<div class="progress-bar" role="progressbar" aria-valuenow="30" style="width:30%"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6 col-12 profit-report-card">
						<div class="row">
							<div class="col-md-12 col-sm-6">
								<div class="card">
									<div class="card-header d-flex justify-content-between align-items-center">
										<h4 class="card-title">Profit Report</h4>
										<i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
									</div>
									<div class="card-content">
										<div class="card-body pb-0 d-flex justify-content-around">
											<div class="d-inline-flex mr-xl-2">
												<div id="profit-primary-chart"></div>
												<div class="profit-content ml-50 mt-50">
													<h5 class="mb-0">$12k</h5>
													<small class="text-muted">2019</small>
												</div>
											</div>
											<div class="d-inline-flex">
												<div id="profit-info-chart"></div>
												<div class="profit-content ml-50 mt-50">
													<h5 class="mb-0">$64k</h5>
													<small class="text-muted">2019</small>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12 col-sm-6">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Registrations</h4>
									</div>
									<div class="card-content">
										<div class="card-body">
											<div class="d-flex align-items-end justify-content-around">
												<div class="registration-content mr-xl-2">
													<h4 class="mb-0">56.3k</h4>
													<i class="bx bx-trending-up success align-middle"></i>
													<span class="text-success">12.8%</span>
												</div>
												<div id="registration-chart"></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6 col-12 sales-card">
						<div class="card">
							<div class="card-header d-flex justify-content-between align-items-center">
								<div class="card-title-content">
									<h4 class="card-title">Sales</h4>
									<small class="text-muted">Calculated in last 7 days</small>
								</div>
								<i class="bx bx-dots-vertical-rounded font-medium-3 cursor-pointer"></i>
							</div>
							<div class="card-content">
								<div class="card-body">
									<div id="sales-chart" class="mb-2"></div>
									<div class="d-flex justify-content-between my-1">
										<div class="sales-info d-flex align-items-center">
											<i class='bx bx-up-arrow-circle text-primary font-medium-5 mr-50'></i>
											<div class="sales-info-content">
												<h6 class="mb-0">Best Selling</h6>
												<small class="text-muted">Sunday</small>
											</div>
										</div>
										<h6 class="mb-0">28.6k</h6>
									</div>
									<div class="d-flex justify-content-between mt-2">
										<div class="sales-info d-flex align-items-center">
											<i class='bx bx-down-arrow-circle icon-light font-medium-5 mr-50'></i>
											<div class="sales-info-content">
												<h6 class="mb-0">Lowest Selling</h6>
												<small class="text-muted">Thursday</small>
											</div>
										</div>
										<h6 class="mb-0">986k</h6>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-3 col-md-6 col-12 growth-card">
						<div class="card">
							<div class="card-body text-center">
								<div class="dropdown">
									<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButtonSec" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									2019
									</button>
									<div class="dropdown-menu" aria-labelledby="dropdownMenuButtonSec">
										<a class="dropdown-item" href="#">2019</a>
										<a class="dropdown-item" href="#">2018</a>
										<a class="dropdown-item" href="#">2017</a>
									</div>
								</div>
								<div id="growth-Chart"></div>
								<h6 class="mb-0"> 62% Company Growth in 2019</h6>
							</div>
						</div>
					</div>
				</div> -->
			</section>
		</div>
	</div>
</div>
<!-- END: Content-->
<!-- footer content -->
@include('layouts.footer')
<!-- /footer content -->
@endsection
<script type="text/javascript">
	var userarr = [];
	<?php foreach ($allyearcountusers as $key => $value) { ?>
		userarr.push('<?php echo $value; ?>');
	<?php } ?>
</script>