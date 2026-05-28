<template>
	<div :style="spacerStyle"></div>
</template>

<script>
export default {
	name: 'BasicSpacer',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		spacerStyle() {
			return {
				height: this.item.settings?.height || '32px',
			};
		},
	},
};
</script>
