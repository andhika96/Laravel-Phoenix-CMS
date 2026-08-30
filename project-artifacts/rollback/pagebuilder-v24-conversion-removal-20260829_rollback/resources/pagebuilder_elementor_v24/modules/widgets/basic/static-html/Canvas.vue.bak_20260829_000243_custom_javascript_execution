<template>
	<div class="pb-static-html-preview pb-static-html-preview--canvas" data-pb-static-html="true">
		<div class="pb-static-html-preview__notice"><i class="fas fa-shield-alt" aria-hidden="true"></i><span>Exact Visual — isolated source preview</span></div>
		<iframe ref="frame" :srcdoc="srcdoc" :title="title" sandbox="allow-scripts" :style="frameStyle" @load="requestHeight"></iframe>
	</div>
</template>

<script>
export default {
	name: 'StaticHtmlPreviewCanvas',
	props: { item: { type: Object, required: true } },
	data() { return { runtimeHeight: 0 }; },
	computed: {
		settings() { return this.item.settings || {}; },
		srcdoc() { return String(this.settings.srcdoc || ''); },
		title() { return String(this.settings.title || 'Imported page exact preview'); },
		configuredHeight() {
			const value = String(this.settings.height || '1200px').trim();
			return /^\d{2,5}px$/.test(value) ? value : '1200px';
		},
		frameStyle() { return { height: this.runtimeHeight ? this.runtimeHeight + 'px' : this.configuredHeight }; },
	},
	mounted() { window.addEventListener('message', this.onHeightMessage); },
	beforeUnmount() { window.removeEventListener('message', this.onHeightMessage); },
	methods: {
		onHeightMessage(event) {
			if (event.source !== this.$refs.frame?.contentWindow || event.data?.type !== 'pb-static-html-height') return;
			const value = Number(event.data.height);
			if (Number.isFinite(value)) this.runtimeHeight = Math.max(320, Math.min(30000, Math.ceil(value)));
		},
		requestHeight() { this.runtimeHeight = 0; },
	},
};
</script>
