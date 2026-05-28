<template>
	<img :src="src" :alt="alt" :style="imageStyle">
</template>

<script>
export default {
	name: 'BasicImage',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		src() {
			return this.item.settings?.src || 'https://placehold.co/640x360';
		},
		alt() {
			return this.item.settings?.alt || 'Image';
		},
		imageStyle() {
			return {
				width: this.item.settings?.width || '100%',
				height: this.item.settings?.height || 'auto',
				display: 'block',
			};
		},
	},
};
</script>
