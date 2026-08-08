<span class="el-widget-accordion__icon" aria-hidden="true">
	@if(($expandIcon['source'] ?? 'none') === 'svg')
		<img class="el-widget-accordion__icon-expand" src="{{ $expandIcon['value'] }}" alt="">
	@elseif(($expandIcon['source'] ?? 'none') === 'library')
		<i class="el-widget-accordion__icon-expand {{ $expandIcon['value'] }}"></i>
	@endif
	@if(($collapseIcon['source'] ?? 'none') === 'svg')
		<img class="el-widget-accordion__icon-collapse" src="{{ $collapseIcon['value'] }}" alt="">
	@elseif(($collapseIcon['source'] ?? 'none') === 'library')
		<i class="el-widget-accordion__icon-collapse {{ $collapseIcon['value'] }}"></i>
	@endif
</span>
