@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$items = is_array($node['tabItems'] ?? null) && count($node['tabItems']) ? $node['tabItems'] : [];
	$activeId = (string) ($settings['activeTabId'] ?? ($items[0]['id'] ?? ''));
	if (!collect($items)->contains(fn ($item) => (string) ($item['id'] ?? '') === $activeId)) $activeId = (string) ($items[0]['id'] ?? '');
	$direction = in_array($settings['direction'] ?? 'row', ['row','row-reverse','column','column-reverse'], true) ? $settings['direction'] : 'row';
	$justify = in_array($settings['justify'] ?? 'flex-start', ['flex-start','center','flex-end','stretch'], true) ? $settings['justify'] : 'flex-start';
	$align = in_array($settings['alignTitle'] ?? 'center', ['left','center','right'], true) ? $settings['alignTitle'] : 'center';
	$breakpoint = in_array($settings['breakpoint'] ?? 'mobile', ['mobile','tablet','none'], true) ? $settings['breakpoint'] : 'mobile';
	$widthUnit = ($settings['tabWidthUnit'] ?? 'px') === '%' ? '%' : 'px';
	$width = is_numeric($settings['tabWidth'] ?? null) && (float) $settings['tabWidth'] > 0 ? ((float) $settings['tabWidth'] + 0) . $widthUnit : '';
	$nodeId = trim((string) ($node['id'] ?? ''));
	$domId = $nodeId !== '' ? 'pb-node-' . preg_replace('/[^A-Za-z0-9_\-]/', '-', $nodeId) : 'pb-tabs-' . substr(md5(json_encode($node)), 0, 8);
	$customClass = trim(preg_replace('/\s+/', ' ', preg_replace('/[^A-Za-z0-9_\-\s]/', ' ', (string) ($settings['cssClass'] ?? ''))));
	$className = trim('el-widget-tabs el-widget-tabs--' . $direction . ' el-widget-tabs--breakpoint-' . $breakpoint . (!empty($settings['horizontalScroll']) ? ' el-widget-tabs--scroll' : '') . ' ' . $customClass);
@endphp
<div id="{{ $domId }}" class="{{ $className }}" data-tabs-widget>
	<div class="el-widget-tabs__nav el-tabs-nav" role="tablist" style="display:flex;flex-direction:{{ $direction }};justify-content:{{ $justify }};@if($width !== '')--pb-tabs-width:{{ $width }};@endif">
		@foreach($items as $index => $item)
			@php $itemId=(string)($item['id']??'tab-'.$index);$selected=$itemId===$activeId;$tabId=$domId.'-tab-'.preg_replace('/[^A-Za-z0-9_\-]/','-',$itemId);$panelId=$domId.'-panel-'.preg_replace('/[^A-Za-z0-9_\-]/','-',$itemId); @endphp
			<button id="{{ $tabId }}" class="el-tabs-nav-item{{ $selected?' is-active':'' }}" role="tab" aria-selected="{{ $selected?'true':'false' }}" aria-controls="{{ $panelId }}" data-tab-id="{{ $itemId }}" data-tab-target="{{ $itemId }}" style="text-align:{{ $align }}">
				@if(!empty($item[$selected?'activeIconClass':'iconClass']))<i class="{{ $item[$selected?'activeIconClass':'iconClass'] }}" aria-hidden="true"></i>@endif
				<span>{{ $item['title'] ?? 'Tab '.($index+1) }}</span>
			</button>
		@endforeach
	</div>
	<div class="el-tabs-content">
		@foreach($items as $index => $item)
			@php $itemId=(string)($item['id']??'tab-'.$index);$selected=$itemId===$activeId;$tabId=$domId.'-tab-'.preg_replace('/[^A-Za-z0-9_\-]/','-',$itemId);$panelId=$domId.'-panel-'.preg_replace('/[^A-Za-z0-9_\-]/','-',$itemId); @endphp
			<div id="{{ $panelId }}" class="el-tabs-panel{{ $selected?' is-active':'' }}" role="tabpanel" aria-labelledby="{{ $tabId }}" data-tab-panel="{{ $itemId }}" @if(!$selected) hidden @endif>
				@foreach(($item['children'] ?? []) as $child) @include('pagebuilder_elementor.partials.render_node',['node'=>$child]) @endforeach
			</div>
		@endforeach
	</div>
</div>
<script>(function(){const root=document.getElementById(@json($domId));if(!root||root.dataset.tabsBound==='1')return;root.dataset.tabsBound='1';root.addEventListener('click',function(event){const button=event.target.closest('[data-tab-target]');if(!button||!root.contains(button))return;const target=button.getAttribute('data-tab-target');root.querySelectorAll('[data-tab-target]').forEach(el=>{const active=el.getAttribute('data-tab-target')===target;el.classList.toggle('is-active',active);el.setAttribute('aria-selected',active?'true':'false');});root.querySelectorAll('[data-tab-panel]').forEach(el=>{const active=el.getAttribute('data-tab-panel')===target;el.classList.toggle('is-active',active);el.hidden=!active;});});})();</script>
