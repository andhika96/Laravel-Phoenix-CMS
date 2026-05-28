<template>
	<component :is="tag" :style="styleObject">{{ text }}</component>
</template>

<script>
export default {
	name: 'BasicHeading',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		tag() {
			return this.item.settings?.tag || 'h2';
		},
		text() {
			return this.item.settings?.text || 'Heading';
		},
		styleObject() {
			return {
				textAlign: this.item.settings?.align || 'left',
				color: this.item.settings?.color || '#101828',
				margin: 0,
			};
		},
	},
};
</script>
