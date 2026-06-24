<template>
	<div class="ratio" :style="ratioStyle">
		<iframe v-if="isYoutube" :src="videoUrl" title="Video" allowfullscreen></iframe>
		<video v-else controls :src="videoUrl"></video>
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
	},
	computed: {
		isYoutube() {
			return this.item.settings?.sourceType !== 'file';
		},
		videoUrl() {
			const settings = this.item.settings || {};
			if (settings.sourceType === 'file') {
				return settings.fileUrl || '';
			}
			return settings.youtubeEmbed || 'https://www.youtube.com/embed/dQw4w9WgXcQ';
		},
		ratioStyle() {
			const ratio = this.item.settings?.ratio || '16/9';
			const parts = ratio.split('/').map(Number);
			if (parts.length !== 2 || !parts[0] || !parts[1]) {
				return { aspectRatio: '16/9' };
			}
			return { aspectRatio: ratio };
		},
	},
};
</script>
