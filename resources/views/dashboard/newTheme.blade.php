<!doctype html>
<html lang="en" data-bs-theme="dark">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Bootstrap demo</title>
		
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
	
		<!-- Custom CSS -->
		<link href="{{ asset('assets/beta_css/new.css?v=').time() }}" rel="stylesheet">
	</head>

	<body>	
		<div class="container py-4">
			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Default Button Area</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-secondary-larapx">Secondary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-secondary-larapx active">(Active) Secondary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-secondary-larapx disabled">(Disabled) Secondary Phoenix Button</a>
					</li>
				</ul>
			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Outline Button Area</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-secondary-larapx">Secondary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-secondary-larapx active">(Active) Secondary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-outline-secondary-larapx disabled">(Disabled) Secondary Phoenix Button</a>
					</li>
				</ul>
			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Subtle Button Area</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-secondary-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-secondary-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-secondary-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>
			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Subtle 2 Button Area</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-secondary-larapx">Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-secondary-larapx active">(Active) Primary Phoenix Button</a>
					</li>

					<li class="list-group-item border-0">
						<a href="#!" class="btn btn-subtle-2-secondary-larapx disabled">(Disabled) Primary Phoenix Button</a>
					</li>
				</ul>
			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Default Background Area</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<div class="bg-larapx-primary text-white p-4">.bg-larapx-primary</div>
					</li>

					<li class="list-group-item border-0">
						<div class="bg-larapx-primary-subtle p-4" style="color: var(--ph-primary);">.bg-larapx-primary-subtle</div>
					</li>

					<li class="list-group-item border-0">
						<div class="bg-larapx-primary-subtle-2 p-4" style="color: var(--ph-primary);">.bg-larapx-primary-subtle-2</div>
					</li>
				</ul>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<div class="bg-larapx-secondary text-white p-4">.bg-larapx-secondary</div>
					</li>

					<li class="list-group-item border-0">
						<div class="bg-larapx-secondary-subtle text-larapx-secondary-subtle p-4">.bg-larapx-secondary-subtle</div>
					</li>

					<li class="list-group-item border-0">
						<div class="bg-larapx-secondary-subtle-2 text-larapx-secondary-subtle p-4">.bg-larapx-secondary-subtle-2</div>
					</li>
				</ul>
			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Toast</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<div class="ph-callout ph-callout-success shadow p-4">.ph-callout-success</div>
					</li>

					<li class="list-group-item border-0">
						<div class="ph-callout ph-callout-danger shadow p-4">.ph-callout-danger</div>
					</li>

					<li class="list-group-item border-0">
						<div class="ph-callout ph-callout-warning shadow p-4">.ph-callout-warning</div>
					</li>

					<li class="list-group-item border-0">
						<div class="ph-callout ph-callout-info shadow p-4">.ph-callout-info</div>
					</li>
				</ul>

			</div>

			<div class="pb-4 mb-5 border-bottom">
				<div class="h5 pb-3 mb-3 border-bottom">Link</div>

				<ul class="list-group list-group-horizontal">
					<li class="list-group-item border-0">
						<a href="#!">Testing Link</a>
					</li>
				</ul>

			</div>
		</div>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
	</body>
</html>