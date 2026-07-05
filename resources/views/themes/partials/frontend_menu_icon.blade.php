@if ($iconUrl !== '')
	<img src="{{ $iconUrl }}" class="{{ $className }}" alt="">
@elseif ($iconHtml !== '')
	<span class="{{ $className }}">{!! $iconHtml !!}</span>
@endif
