@php
	$settings = is_array($node['settings'] ?? null) ? $node['settings'] : [];
	$nodeId = trim((string) ($node['id'] ?? ''));
	$advanced = app(\App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver::class)->resolve($settings, $nodeId !== '' ? $nodeId : 'video', request());
	$nodeDomId = $advanced['id'];
	$customClass = trim(preg_replace('/\s+/', ' ', preg_replace('/[^A-Za-z0-9_\-\s]/', ' ', (string) ($settings['cssClass'] ?? ''))));
	$sourceTypeRaw = strtolower(trim((string) ($settings['sourceType'] ?? 'youtube')));
	$sourceType = $sourceTypeRaw === 'file' ? 'self_hosted' : $sourceTypeRaw;
	if (!in_array($sourceType, ['youtube', 'vimeo', 'dailymotion', 'self_hosted', 'videopress'], true)) $sourceType = 'youtube';
	$allowedRatios = ['16/9', '4/3', '1/1', '3/2', '21/9', '9/16', '4/5'];
	$paddingPercent = function ($ratio) use ($allowedRatios) {
		$safe = trim((string) ($ratio ?? '16/9'));
		if (!in_array($safe, $allowedRatios, true)) $safe = '16/9';
		[$width, $height] = array_pad(explode('/', str_replace(' ', '', $safe)), 2, null);
		$width = (float) ($width ?: 16);
		$height = (float) ($height ?: 9);
		return ($width > 0 ? round(($height / $width) * 100, 4) : 56.25) . '%';
	};
	$positiveInt = function ($value) {
		if ($value === '' || $value === null || !is_numeric($value)) return null;
		$number = (int) round((float) $value);
		return $number < 0 ? null : $number;
	};
	$sanitizeHex = function ($value) {
		$raw = preg_replace('/[^0-9a-f]/i', '', (string) $value);
		return strlen($raw) === 3 || strlen($raw) === 6 ? strtolower($raw) : '';
	};
	$extractYoutubeId = function ($url) {
		$value = trim((string) $url);
		if (preg_match('/embed\/([^?&\/]+)/', $value, $match)) return $match[1];
		if (preg_match('/youtu\.be\/([^?&\/]+)/', $value, $match)) return $match[1];
		if (preg_match('/[?&]v=([^&]+)/', $value, $match)) return $match[1];
		return '';
	};
	$extractVimeoId = function ($url) {
		$value = trim((string) $url);
		if (preg_match('/(?:video\/|vimeo\.com\/)(\d+)/', $value, $match)) return $match[1];
		$digits = preg_replace('/\D+/', '', $value);
		return $digits ?: '';
	};
	$extractDailymotionId = function ($url) {
		$value = trim((string) $url);
		if (preg_match('/video\/([^_?&\/]+)/', $value, $match)) return $match[1];
		if (preg_match('/dai\.ly\/([^_?&\/]+)/', $value, $match)) return $match[1];
		return $value;
	};
	$timeFragment = function ($start, $end) {
		$parts = array_values(array_filter([$start, $end], fn ($value) => $value !== null));
		return $parts ? '#t=' . implode(',', $parts) : '';
	};
	$startTime = $positiveInt($settings['startTime'] ?? null);
	$endTime = $positiveInt($settings['endTime'] ?? null);
	$autoplay = !empty($settings['autoplay']);
	$mute = !empty($settings['mute']);
	$loop = !empty($settings['loop']);
	$playerControls = array_key_exists('playerControls', $settings) ? (bool) $settings['playerControls'] : true;
	$captions = !empty($settings['captions']);
	$privacyMode = !empty($settings['privacyMode']);
	$lazyLoad = !empty($settings['lazyLoad']);
	$suggestedVideos = ($settings['suggestedVideos'] ?? 'current_channel') === 'any_video' ? 'any_video' : 'current_channel';
	$introTitle = array_key_exists('introTitle', $settings) ? (bool) $settings['introTitle'] : true;
	$introPortrait = array_key_exists('introPortrait', $settings) ? (bool) $settings['introPortrait'] : true;
	$introByline = array_key_exists('introByline', $settings) ? (bool) $settings['introByline'] : true;
	$controlsColor = $sanitizeHex($settings['controlsColor'] ?? '');
	$videoInfo = array_key_exists('videoInfo', $settings) ? (bool) $settings['videoInfo'] : true;
	$logo = array_key_exists('logo', $settings) ? (bool) $settings['logo'] : true;
	$downloadButton = array_key_exists('downloadButton', $settings) ? (bool) $settings['downloadButton'] : true;
	$preload = strtolower(trim((string) ($settings['preload'] ?? 'metadata')));
	if (!in_array($preload, ['metadata', 'auto', 'none'], true)) $preload = 'metadata';
	$poster = trim((string) ($settings['poster'] ?? ''));
	$overlayImage = trim((string) ($settings['overlayImage'] ?? ''));
	$overlayEnabled = !empty($settings['imageOverlay']) && $overlayImage !== '';
	$effectiveAutoplay = $autoplay || $overlayEnabled;
	$isIframeSource = in_array($sourceType, ['youtube', 'vimeo', 'dailymotion'], true);
	$mediaSrc = '';
	if ($sourceType === 'youtube') {
		$youtubeId = $extractYoutubeId($settings['youtubeUrl'] ?? ($settings['youtubeEmbed'] ?? ''));
		if ($youtubeId !== '') {
			$query = [];
			if ($effectiveAutoplay) $query['autoplay'] = 1;
			if ($mute) $query['mute'] = 1;
			if ($loop) { $query['loop'] = 1; $query['playlist'] = $youtubeId; }
			if (!$playerControls) $query['controls'] = 0;
			if ($captions) $query['cc_load_policy'] = 1;
			if ($suggestedVideos === 'current_channel') $query['rel'] = 0;
			if ($startTime !== null) $query['start'] = $startTime;
			if ($endTime !== null) $query['end'] = $endTime;
			$base = $privacyMode ? 'https://www.youtube-nocookie.com/embed/' . $youtubeId : 'https://www.youtube.com/embed/' . $youtubeId;
			$mediaSrc = $query ? $base . '?' . http_build_query($query) : $base;
		}
	} elseif ($sourceType === 'vimeo') {
		$vimeoId = $extractVimeoId($settings['vimeoUrl'] ?? '');
		if ($vimeoId !== '') {
			$query = [];
			if ($effectiveAutoplay) $query['autoplay'] = 1;
			if ($mute) $query['muted'] = 1;
			if ($loop) $query['loop'] = 1;
			if ($privacyMode) $query['dnt'] = 1;
			if (!$introTitle) $query['title'] = 0;
			if (!$introPortrait) $query['portrait'] = 0;
			if (!$introByline) $query['byline'] = 0;
			if ($controlsColor !== '') $query['color'] = $controlsColor;
			$mediaSrc = 'https://player.vimeo.com/video/' . $vimeoId . ($query ? '?' . http_build_query($query) : '') . ($startTime !== null ? '#t=' . $startTime . 's' : '');
		}
	} elseif ($sourceType === 'dailymotion') {
		$dailymotionId = $extractDailymotionId($settings['dailymotionUrl'] ?? '');
		if ($dailymotionId !== '') {
			$query = [];
			if ($effectiveAutoplay) $query['autoplay'] = 1;
			if ($mute) $query['mute'] = 1;
			if (!$playerControls) $query['controls'] = 0;
			if (!$videoInfo) $query['ui-start-screen-info'] = 0;
			if (!$logo) $query['logo'] = 0;
			if ($controlsColor !== '') $query['ui-highlight'] = $controlsColor;
			if ($startTime !== null) $query['start'] = $startTime;
			$mediaSrc = 'https://www.dailymotion.com/embed/video/' . $dailymotionId . ($query ? '?' . http_build_query($query) : '');
		}
	} else {
		$fileUrl = trim((string) ($settings['fileUrl'] ?? ''));
		if ($fileUrl !== '') $mediaSrc = $fileUrl . $timeFragment($startTime, $endTime);
	}
	$mediaHtml = '';
	if ($mediaSrc !== '') {
		if ($isIframeSource) {
			$mediaHtml = '<iframe src="' . e($mediaSrc) . '" title="Video" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen' . ($lazyLoad ? ' loading="lazy"' : '') . '></iframe>';
		} else {
			$attributes = ['src="' . e($mediaSrc) . '"', 'playsinline'];
			if ($playerControls) $attributes[] = 'controls';
			if ($effectiveAutoplay) $attributes[] = 'autoplay';
			if ($mute) $attributes[] = 'muted';
			if ($loop) $attributes[] = 'loop';
			if ($sourceType === 'self_hosted') $attributes[] = 'preload="' . e($preload) . '"';
			if ($poster !== '') $attributes[] = 'poster="' . e($poster) . '"';
			if ($sourceType === 'self_hosted' && !$downloadButton) $attributes[] = 'controlslist="nodownload"';
			$mediaHtml = '<video ' . implode(' ', $attributes) . '></video>';
		}
	}
	$className = implode(' ', array_values(array_unique(array_merge(['el-widget-video'], $advanced['classes']))));
	$styleBlocks = [];
	if (($settings['ratioTablet'] ?? '') !== '' && $nodeDomId !== '') $styleBlocks[] = '@media (max-width: 1024px){#' . $nodeDomId . ' > .el-widget-video-wrapper{padding-bottom:' . $paddingPercent($settings['ratioTablet']) . '}}';
	if (($settings['ratioMobile'] ?? '') !== '' && $nodeDomId !== '') $styleBlocks[] = '@media (max-width: 767px){#' . $nodeDomId . ' > .el-widget-video-wrapper{padding-bottom:' . $paddingPercent($settings['ratioMobile']) . '}}';
@endphp
<div id="{{ $nodeDomId }}" class="{{ $className }}" data-pb-motion="{{ $advanced['motion'] }}" data-entrance-delay="{{ $advanced['entranceDelay'] }}" data-entrance-duration="{{ $advanced['entranceDuration'] }}" @foreach($advanced['attributes'] as $attributeName=>$attributeValue) {{ $attributeName }}="{{ e($attributeValue) }}" @endforeach>
	<div class="el-widget-video-wrapper" style="padding-bottom:{{ $paddingPercent($settings['ratio'] ?? '16/9') }}">
		@if($overlayEnabled && $mediaHtml !== '')
			<button type="button" class="el-video-overlay" style="background-image:url('{{ e($overlayImage) }}')" data-video-html="{{ e($mediaHtml) }}"><span class="el-video-overlay-play" aria-hidden="true"></span></button>
		@elseif($mediaHtml !== '')
			{!! $mediaHtml !!}
		@endif
	</div>
</div>
<style>{!! $advanced['css'] !!}{!! implode("\n", $styleBlocks) !!}</style>
@if($overlayEnabled && $mediaHtml !== '' && $nodeDomId !== '')
	<script>(function(){const root=document.getElementById(@json($nodeDomId));if(!root)return;const overlay=root.querySelector('.el-video-overlay[data-video-html]');if(!overlay||overlay.dataset.bound==='1')return;overlay.dataset.bound='1';overlay.addEventListener('click',function(){const wrapper=overlay.parentElement;const html=overlay.getAttribute('data-video-html')||'';if(!wrapper||!html)return;overlay.remove();wrapper.insertAdjacentHTML('beforeend',html);});})();</script>
@endif
