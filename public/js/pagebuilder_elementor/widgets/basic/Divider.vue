<template>
	<div :style="wrapStyle">
		<hr :style="lineStyle">
	</div>
</template>

<script>
export default {
	name: 'BasicDivider',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		wrapStyle() {
			return {
				textAlign: this.item.settings?.align || 'center',
			};
		},
		lineStyle() {
			const styleType = this.item.settings?.style || 'solid';
			return {
				margin: 0,
				border: 'none',
				borderTop: (this.item.settings?.thickness || 2) + 'px ' + styleType + ' ' + (this.item.settings?.color || '#d0d7e6'),
				width: this.item.settings?.width || '100%',
				display: 'inline-block',
			};
		},
	},
};
</script>
