<template>
	<div :class="['el-widget-video', customClass]">
		<div class="el-widget-video-wrapper" :style="wrapperStyle">
			<button v-if="showOverlay" type="button" class="el-video-overlay" :style="overlayStyle" @click="dismissOverlay">
				<span class="el-video-overlay-play" aria-hidden="true"></span>
			</button>
			<iframe
				v-else-if="isIframeSource"
				:src="iframeUrl"
				title="Video"
				:loading="iframeLoading"
				:allow="iframeAllow"
				allowfullscreen
			></iframe>
			<video v-else v-bind="videoAttributes"></video>
		</div>
	</div>
</template>

<script>
export default {
	name: 'BasicVideo',
	props: {
		item: {
			type: Object,
			required: true,
		},
		responsiveDevice: {
			type: String,
			default: 'desktop',
		},
	},
	data() {
		return {
			overlayDismissed: false,
		};
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		customClass() {
			const value = String(this.settings.cssClass ?? '').trim();
			if (!value) return '';
			return value
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
		},
		currentSourceType() {
			return this.normalizeSourceType(this.settings.sourceType);
		},
		isIframeSource() {
			const source = this.currentSourceType;
			return source === 'youtube' || source === 'vimeo' || source === 'dailymotion';
		},
		wrapperStyle() {
			return {
				paddingBottom: this.paddingBottomPercent,
			};
		},
		paddingBottomPercent() {
			const ratio = this.responsiveValue('ratio', '16/9');
			const parts = String(ratio || '16/9').split('/').map(Number);
			if (parts.length !== 2 || !parts[0] || !parts[1]) {
				return '56.25%';
			}
			return ((parts[1] / parts[0]) * 100).toFixed(4).replace(/\.?0+$/, '') + '%';
		},
		overlayEnabled() {
			return !!this.settings.imageOverlay && !!String(this.settings.overlayImage || '').trim();
		},
		showOverlay() {
			return this.overlayEnabled && !this.overlayDismissed;
		},
		overlayStyle() {
			const image = String(this.settings.overlayImage || '').trim();
			return image ? { backgroundImage: 'url(' + image + ')' } : {};
		},
		iframeLoading() {
			return this.settings.lazyLoad ? 'lazy' : null;
		},
		iframeAllow() {
			return 'autoplay; fullscreen; picture-in-picture';
		},
		effectiveAutoplay() {
			return !!this.settings.autoplay || this.overlayDismissed;
		},
		iframeUrl() {
			const source = this.currentSourceType;
			if (source === 'youtube') {
				return this.youtubeUrl();
			}
			if (source === 'vimeo') {
				return this.vimeoUrl();
			}
			if (source === 'dailymotion') {
				return this.dailymotionUrl();
			}
			return '';
		},
		videoUrl() {
			const baseUrl = String(this.settings.fileUrl || '').trim();
			if (!baseUrl) return '';
			const fragment = this.timeFragment(this.settings.startTime, this.settings.endTime);
			return fragment ? baseUrl + fragment : baseUrl;
		},
		videoAttributes() {
			const attrs = {
				src: this.videoUrl,
				playsinline: '',
			};
			if (this.settings.playerControls !== false) attrs.controls = '';
			if (this.effectiveAutoplay) attrs.autoplay = '';
			if (this.settings.mute) attrs.muted = '';
			if (this.settings.loop) attrs.loop = '';
			if (this.currentSourceType === 'self_hosted' && this.settings.preload) attrs.preload = this.settings.preload;
			if (this.settings.poster) attrs.poster = this.settings.poster;
			if (this.currentSourceType === 'self_hosted' && this.settings.downloadButton === false) attrs.controlslist = 'nodownload';
			return attrs;
		},
	},
	watch: {
		item: {
			deep: true,
			handler() {
				this.overlayDismissed = false;
			},
		},
	},
	methods: {
		responsiveValue(base, fallback = '') {
			const settings = this.item.settings || {};
			const device = String(this.responsiveDevice || 'desktop').toLowerCase();
			const key = device === 'tablet' ? base + 'Tablet' : (device === 'mobile' ? base + 'Mobile' : base);
			const value = settings[key];
			if (device === 'mobile' && (value === '' || value === null || value === undefined)) {
				const tabletValue = settings[base + 'Tablet'];
				if (tabletValue !== '' && tabletValue !== null && tabletValue !== undefined) return tabletValue;
			}
			if (device !== 'desktop' && (value === '' || value === null || value === undefined)) {
				const desktopValue = settings[base];
				return (desktopValue === '' || desktopValue === null || desktopValue === undefined) ? fallback : desktopValue;
			}
			return (value === '' || value === null || value === undefined) ? fallback : value;
		},
		normalizeSourceType(value) {
			const source = String(value || '').trim().toLowerCase();
			if (source === 'file') return 'self_hosted';
			if (source === 'youtube' || source === 'vimeo' || source === 'dailymotion' || source === 'self_hosted' || source === 'videopress') {
				return source;
			}
			return 'youtube';
		},
		positiveTime(value) {
			if (value === '' || value === null || value === undefined) return null;
			const num = Number(value);
			if (!Number.isFinite(num) || num < 0) return null;
			return Math.round(num);
		},
		extractYoutubeId(url) {
			const value = String(url || '').trim();
			if (!value) return '';
			const embedMatch = value.match(/embed\/([^?&/]+)/);
			if (embedMatch) return embedMatch[1];
			const shortMatch = value.match(/youtu\.be\/([^?&/]+)/);
			if (shortMatch) return shortMatch[1];
			const watchMatch = value.match(/[?&]v=([^&]+)/);
			if (watchMatch) return watchMatch[1];
			return '';
		},
		extractVimeoId(url) {
			const value = String(url || '').trim();
			if (!value) return '';
			const matches = value.match(/(?:video\/|vimeo\.com\/)(\d+)/);
			return matches ? matches[1] : value.replace(/\D+/g, '');
		},
		extractDailymotionId(url) {
			const value = String(url || '').trim();
			if (!value) return '';
			const matches = value.match(/video\/([^_?&/]+)/);
			if (matches) return matches[1];
			const shortMatch = value.match(/dai\.ly\/([^_?&/]+)/);
			return shortMatch ? shortMatch[1] : value;
		},
		cleanColor(value) {
			const match = String(value || '').trim().match(/^#?([0-9a-f]{3}|[0-9a-f]{6})$/i);
			return match ? match[1].toLowerCase() : '';
		},
		timeFragment(start, end) {
			const startTime = this.positiveTime(start);
			const endTime = this.positiveTime(end);
			if (startTime === null && endTime === null) return '';
			const parts = [];
			if (startTime !== null) parts.push(startTime);
			if (endTime !== null) parts.push(endTime);
			return '#t=' + parts.join(',');
		},
		dismissOverlay() {
			this.overlayDismissed = true;
		},
		youtubeUrl() {
			const videoId = this.extractYoutubeId(this.settings.youtubeUrl || this.settings.youtubeEmbed || '');
			if (!videoId) return this.settings.youtubeEmbed || '';
			const params = new URLSearchParams();
			if (this.effectiveAutoplay) params.set('autoplay', '1');
			if (this.settings.mute) params.set('mute', '1');
			if (this.settings.loop) {
				params.set('loop', '1');
				params.set('playlist', videoId);
			}
			if (this.settings.playerControls === false) params.set('controls', '0');
			if (this.settings.captions) params.set('cc_load_policy', '1');
			if ((this.settings.suggestedVideos || 'current_channel') === 'current_channel') params.set('rel', '0');
			const startTime = this.positiveTime(this.settings.startTime);
			const endTime = this.positiveTime(this.settings.endTime);
			if (startTime !== null) params.set('start', String(startTime));
			if (endTime !== null) params.set('end', String(endTime));
			const base = this.settings.privacyMode
				? 'https://www.youtube-nocookie.com/embed/' + videoId
				: 'https://www.youtube.com/embed/' + videoId;
			const query = params.toString();
			return query ? base + '?' + query : base;
		},
		vimeoUrl() {
			const videoId = this.extractVimeoId(this.settings.vimeoUrl || '');
			if (!videoId) return '';
			const params = new URLSearchParams();
			if (this.effectiveAutoplay) params.set('autoplay', '1');
			if (this.settings.mute) params.set('muted', '1');
			if (this.settings.loop) params.set('loop', '1');
			if (this.settings.privacyMode) params.set('dnt', '1');
			if (this.settings.introTitle === false) params.set('title', '0');
			if (this.settings.introPortrait === false) params.set('portrait', '0');
			if (this.settings.introByline === false) params.set('byline', '0');
			const color = this.cleanColor(this.settings.controlsColor);
			if (color) params.set('color', color);
			const base = 'https://player.vimeo.com/video/' + videoId;
			const query = params.toString();
			const fragmentStart = this.positiveTime(this.settings.startTime);
			const fragment = fragmentStart !== null ? '#t=' + fragmentStart + 's' : '';
			return (query ? base + '?' + query : base) + fragment;
		},
		dailymotionUrl() {
			const videoId = this.extractDailymotionId(this.settings.dailymotionUrl || '');
			if (!videoId) return '';
			const params = new URLSearchParams();
			if (this.effectiveAutoplay) params.set('autoplay', '1');
			if (this.settings.mute) params.set('mute', '1');
			if (this.settings.playerControls === false) params.set('controls', '0');
			if (this.settings.videoInfo === false) params.set('ui-start-screen-info', '0');
			if (this.settings.logo === false) params.set('logo', '0');
			const color = this.cleanColor(this.settings.controlsColor);
			if (color) params.set('ui-highlight', color);
			const startTime = this.positiveTime(this.settings.startTime);
			if (startTime !== null) params.set('start', String(startTime));
			const base = 'https://www.dailymotion.com/embed/video/' + videoId;
			const query = params.toString();
			return query ? base + '?' + query : base;
		},
	},
};
</script>
