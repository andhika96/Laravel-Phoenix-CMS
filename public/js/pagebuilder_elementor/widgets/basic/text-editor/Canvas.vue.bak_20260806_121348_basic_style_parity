<template>
	<div :class="['el-widget-text-editor', customClass]" v-html="html"></div>
</template>

<script>
export default {
	name: 'BasicTextEditor',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		customClass() {
			const value = String(this.item.settings?.cssClass ?? '').trim();
			if (!value) return '';
			return value
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
		},
		html() {
			return String(this.item.settings?.html ?? '');
		},
	},
};
</script>
