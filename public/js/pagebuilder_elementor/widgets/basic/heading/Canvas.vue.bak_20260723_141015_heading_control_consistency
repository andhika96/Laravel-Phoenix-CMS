<template>
	<component :is="tag" :class="customClass" :style="styleObject">{{ text }}</component>
</template>

<script>
export default {
	name: 'BasicHeadingCanvas',
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
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
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
