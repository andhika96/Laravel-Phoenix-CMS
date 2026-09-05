@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Dashboard') }}
@endsection

@section('content')
	<div id="ph-app-echarts" class="ph-dashboard-page">
		<div class="mb-4 ph-dashboard-summary">
			<h4 class="ph-dashboard-title"><i class="fad fa-tachometer-alt fa-fw me-1" aria-hidden="true"></i><span>{{ t('Dashboard') }}</span></h4>
			<div class="ph-dashboard-summary-copy">
				<span class="ph-dashboard-summary-copy-default">Here’s what’s going on at your business right now</span>
				<span class="ph-dashboard-summary-copy-lucent" aria-hidden="true">{{ t('Ringkasan bisnis Anda') }}</span>
			</div>
		</div>

		<div>
			<!-- Section 1 -->
			<div class="row gx-3 mb-4">
				<div class="col-lg-6 ph-dashboard-stats">
					<div class="row g-3">
						<div class="col-lg-6">
							<div class="ph-content p-4 rounded ph-dashboard-stat">
								<span class="ph-dashboard-stat-icon" aria-hidden="true"><i class="fad fa-users"></i></span>
								<h6 class="text-muted ph-dashboard-stat-label">Customers</h6>
								<h4 class="fw-bold my-3 ph-dashboard-stat-value">36,123</h4>
								<p class="mb-0 text-muted ph-dashboard-stat-trend">
									<span class="text-success me-2"><i class="fad fa-arrow-alt-up fa-fw"></i> 5,27%</span>
									<span>Since last month</span>
								</p>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="ph-content p-4 rounded ph-dashboard-stat">
								<span class="ph-dashboard-stat-icon" aria-hidden="true"><i class="fad fa-shopping-cart"></i></span>
								<h6 class="text-muted ph-dashboard-stat-label">Orders</h6>
								<h4 class="fw-bold my-3 ph-dashboard-stat-value">5,212</h4>
								<p class="mb-0 text-muted ph-dashboard-stat-trend">
									<span class="text-danger me-2"><i class="fad fa-arrow-alt-down fa-fw"></i> 1,27%</span>
									<span>Since last month</span>
								</p>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="ph-content p-4 rounded ph-dashboard-stat">
								<span class="ph-dashboard-stat-icon" aria-hidden="true"><i class="fad fa-wallet"></i></span>
								<h6 class="text-muted ph-dashboard-stat-label">Revenue</h6>
								<h4 class="fw-bold my-3 ph-dashboard-stat-value">Rp. 500,341</h4>
								<p class="mb-0 text-muted ph-dashboard-stat-trend">
									<span class="text-danger me-2"><i class="fad fa-arrow-alt-down fa-fw"></i> 6,00%</span>
									<span>Since last month</span>
								</p>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="ph-content p-4 rounded ph-dashboard-stat">
								<span class="ph-dashboard-stat-icon" aria-hidden="true"><i class="fad fa-chart-line"></i></span>
								<h6 class="text-muted ph-dashboard-stat-label">Growth</h6>
								<h4 class="fw-bold my-3 ph-dashboard-stat-value">+ 30.56%</h4>
								<p class="mb-0 text-muted ph-dashboard-stat-trend">
									<span class="text-success me-2"><i class="fad fa-arrow-alt-up fa-fw"></i> 5,27%</span>
									<span>Since last month</span>
								</p>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6 ph-dashboard-projection">
					<div class="ph-content p-4 rounded position-relative">
						<h6 class="text-muted ph-dashboard-projection-title">Projections Vs Actuals</h6>
						<div id="echartSeriesSimpleBar_ProjectionActual" class="ph-dashboard-projection-chart" style="height: 100%;"></div>
					</div>
				</div>
			</div>

			<!-- Section 2 -->
			<div class="ph-content p-4 mb-4 rounded">
				<h6 class="text-muted">Revenue per Month</h6>
				<div id="echartLineBarGradient_StatsRevenue" style="width: 100%;min-height: 350px;"></div>
			</div>

			<!-- Section 3 -->
			<div class="row g-3 mb-4">
				<div class="col-lg-4">
					<div class="ph-content p-4 rounded">
						<h6 class="text-muted">Total Orders</h6>
						<div class="mb-2 fw-semibold">Last 7 days</div>

						<div id="echartSimpleBarWithBackground_TotalLastOrder" style="width: 100%;min-height: 320px;"></div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="ph-content p-4 rounded">
						<h6 class="text-muted">Total Sales</h6>
						<div class="mb-2 fw-semibold">Last 7 days</div>

						<div id="echartDoughnutWithBorderRadius_TotalSales" style="width: 100%;min-height: 320px;"></div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="ph-content p-4 rounded">
						<h6 class="text-muted">User Access by Location</h6>
						<div class="mb-2 fw-semibold">Last 7 days</div>

						<div id="vectorMap_UserAccessByLocation" style="width: 100%;min-height: 320px"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
	<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

	<script src="{{ url('assets/plugins/echarts/5.5.1/dist/echarts.min.js') }}"></script>
	<script src="{{ url('assets/js/vue3/dashboard/vueV3-dashboard-2026.js?v=').time() }}"></script>
@endpushonce
