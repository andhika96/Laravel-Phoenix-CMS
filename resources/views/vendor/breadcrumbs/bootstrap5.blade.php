@unless ($breadcrumbs->isEmpty())
<!-- 	<style>
	.breadcrumb-item a 
	{
		text-decoration: none;
	}

	.breadcrumb-item + .breadcrumb-item::before 
	{
		font-family: 'Font Awesome 5 Pro';
		font-display: block;
		font-weight: 400;
		content: '\f054';
	}
	</style> -->

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			@foreach ($breadcrumbs as $breadcrumb)

				@if ($breadcrumb->url && !$loop->last)
					<li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
				@else
					<li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</li>
				@endif

			@endforeach
		</ol>
	</nav>
@endunless
