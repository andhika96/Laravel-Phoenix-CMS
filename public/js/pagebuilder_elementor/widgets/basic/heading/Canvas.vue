<template>
	<component :is="safeTag" :class="customClass" :style="styleObject">{{ text }}</component>
</template>

<script>
const ALLOWED_HEADING_TAGS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'];

export default {
	name: 'BasicHeadingCanvas',
	props: {
		item: { type: Object, required: true },
	},
	computed: {
		safeTag() {
			const tag = String(this.item.settings?.tag || 'h2').toLowerCase();
			return ALLOWED_HEADING_TAGS.includes(tag) ? tag : 'h2';
		},
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value.split(/\s+/).map((token) => token.replace(/^\.+/, '').trim()).filter(Boolean).join(' ');
		},
		text() { return this.item.settings?.text || 'Heading'; },
		styleObject() {
			return { textAlign: this.item.settings?.align || 'left', color: this.item.settings?.color || '#101828', margin: 0 };
		},
	},
};
</script>