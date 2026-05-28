@extends('themes.'.custom_theme('frontend'))

@section('title')
	{{ t('Homepage') }}
@endsection

@section('content')
	<div>
		<link href="https://vjs.zencdn.net/7.11.4/video-js.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/videojs-ima/1.11.0/videojs.ima.css" rel="stylesheet" />
	
		<video id="content_video" class="video-js vjs-default-skin" controls preload="auto" width="640" height="360">
			<source src="https://laravel-12-phoenix.aruna/assets/videos/MG_10s_Left_Hand_Drive.mp4" type="video/mp4" />
		</video>

		<script src="https://vjs.zencdn.net/7.11.4/video.min.js"></script>
		<script src="https://imasdk.googleapis.com/js/sdkloader/ima3.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-ads/6.7.0/videojs-contrib-ads.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/videojs-ima/1.11.0/videojs.ima.min.js"></script>

		<script>
		var player = videojs('content_video');

		var options = 
		{
			id: 'content_video',
			// MASUKKAN LINK IKLAN ANDA DI SINI
			adTagUrl: 'https://gam.aruna-dev.com/api/generate-vast?advertiser_id=5965787889&duration=00:00:15&click_url=https://bmkk.co.id&video_url=https://gam.aruna-dev.com/storage/creatives/videos/xPt53bfQfRbdbTfEUGAgQdn042XcGwB2XzHeAq3o.mp4&sz=1200x720&ciu_szs=640x480%2C728x90&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator='
			// adTagUrl: 'https://pubads.g.doubleclick.net/gampad/ads?iu=/21775744923/external/single_preroll_skippable&sz=640x480&ciu_szs=300x250%2C728x90&gdfp_req=1&output=vast&unviewed_position_start=1&env=vp&impl=s&correlator='
		};

		// Inisialisasi Plugin IMA
		player.ima(options);
		</script>
	</div>
@endsection