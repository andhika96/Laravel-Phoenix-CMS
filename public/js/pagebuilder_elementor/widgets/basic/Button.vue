<template>
	<div :style="wrapperStyle">
		<a :href="href" :target="target" :class="btnClass">{{ text }}</a>
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
			return this.item.settings?.text || 'Button';
		},
		href() {
			return this.item.settings?.url || '#';
		},
		target() {
			return this.item.settings?.newTab ? '_blank' : '_self';
		},
		btnClass() {
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
