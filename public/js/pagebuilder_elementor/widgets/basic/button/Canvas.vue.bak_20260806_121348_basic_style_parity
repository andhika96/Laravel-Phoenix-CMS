<template>
	<div :style="wrapperStyle">
		<a :href="href" :target="target" :rel="rel" :class="['el-widget-button', buttonClass]">{{ text }}</a>
	</div>
</template>

<script>
export default {
	name: 'BasicButton',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		text() {
			return this.item.settings?.text ?? 'Click here';
		},
		href() {
			return this.item.settings?.url ?? '#';
		},
		target() {
			return this.item.settings?.newTab ? '_blank' : null;
		},
		rel() {
			return this.item.settings?.newTab ? 'noopener' : null;
		},
		buttonClass() {
			return this.item.settings?.className || 'btn btn-primary';
		},

		wrapperStyle() {
			return {
				textAlign: this.item.settings?.align || 'left',
			};
		},
	},
};
</script>
