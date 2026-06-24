<template>
	<div class="basic-text-editor" v-html="html"></div>
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
		html() {
			return this.item.settings?.html || '<p>Text editor content</p>';
		},
	},
};
</script>
