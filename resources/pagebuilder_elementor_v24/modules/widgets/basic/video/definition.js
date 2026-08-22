(function (registry) {
	'use strict';

	if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');

	const ratios = new Set(['16/9', '4/3', '1/1', '3/2', '21/9', '9/16', '4/5']);
	const defaults = () => ({
		sourceType: 'youtube',
		youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
		youtubeEmbed: 'https://www.youtube.com/embed/dQw4w9WgXcQ',
		vimeoUrl: 'https://vimeo.com/235215203',
		dailymotionUrl: 'https://www.dailymotion.com/video/x84sh87',
		fileUrl: '', externalUrl: false, startTime: '', endTime: '', autoplay: false, mute: false, loop: false,
		playerControls: true, captions: false, privacyMode: false, lazyLoad: false,
		suggestedVideos: 'current_channel', introTitle: true, introPortrait: true, introByline: true,
		controlsColor: '', videoInfo: true, logo: true, downloadButton: true, preload: 'metadata',
		poster: '', imageOverlay: false, overlayImage: '', ratio: '16/9', cssClass: '',
	});
	const sourceType = (value) => {
		const raw = String(value || '').trim().toLowerCase();
		if (raw === 'file') return 'self_hosted';
		return ['youtube', 'vimeo', 'dailymotion', 'self_hosted', 'videopress'].includes(raw) ? raw : 'youtube';
	};
	const positiveInteger = (value) => {
		if (value === '' || value === null || value === undefined) return '';
		const number = Number(value);
		return Number.isFinite(number) && number >= 0 ? Math.round(number) : '';
	};
	const ratio = (value) => ratios.has(String(value || '').trim()) ? String(value).trim() : '16/9';
	const youtubeEmbed = (url) => {
		const value = String(url || '');
		if (!value || value.includes('embed/')) return value;
		const match = value.match(/youtu\.be\/([^?&]+)/) || value.match(/[?&]v=([^&]+)/);
		return match ? 'https://www.youtube.com/embed/' + match[1] : value;
	};

	registry.register({type: 'video',defaults,normalize(node) {
			const normalized = node && typeof node === 'object' ? node : {};
			const settings = normalized.settings = { ...defaults(), ...(normalized.settings || {}) };
			settings.sourceType = sourceType(settings.sourceType);
			settings.youtubeEmbed = youtubeEmbed(settings.youtubeUrl);
			settings.ratio = ratio(settings.ratio);
			if (settings.ratioTablet !== '' && settings.ratioTablet != null) settings.ratioTablet = ratio(settings.ratioTablet);
			if (settings.ratioMobile !== '' && settings.ratioMobile != null) settings.ratioMobile = ratio(settings.ratioMobile);
			settings.externalUrl = !!settings.externalUrl;
			settings.startTime = positiveInteger(settings.startTime);
			settings.endTime = positiveInteger(settings.endTime);
			['autoplay', 'mute', 'loop', 'captions', 'privacyMode', 'lazyLoad', 'imageOverlay'].forEach((key) => { settings[key] = !!settings[key]; });
			['playerControls', 'introTitle', 'introPortrait', 'introByline', 'videoInfo', 'logo', 'downloadButton'].forEach((key) => { settings[key] = settings[key] !== false; });
			settings.suggestedVideos = settings.suggestedVideos === 'any_video' ? 'any_video' : 'current_channel';
			settings.preload = ['metadata', 'auto', 'none'].includes(String(settings.preload || '').toLowerCase()) ? String(settings.preload).toLowerCase() : 'metadata';
			['controlsColor', 'poster', 'overlayImage', 'cssClass'].forEach((key) => { settings[key] = String(settings[key] || '').trim(); });
			return normalized;
		}});
})(window.PageBuilderElementorV24Widgets);
