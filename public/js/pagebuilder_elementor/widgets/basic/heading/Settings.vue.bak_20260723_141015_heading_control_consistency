<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--heading">
		<div class="pb-widget-settings__group">
			<div class="pb-form-group">
				<label class="pb-form-label">Text</label>
				<textarea class="pb-textarea" v-model="node.settings.text"></textarea>
			</div>
			<div class="pb-form-group">
				<label class="pb-form-label">HTML Tag</label>
				<select class="pb-select" v-model="node.settings.tag">
					<option v-for="tag in tags" :key="tag" :value="tag">{{ tag }}</option>
				</select>
			</div>
			<div class="pb-form-group">
				<label class="pb-form-label">Alignment</label>
				<select class="pb-select" v-model="node.settings.align">
					<option value="left">Left</option>
					<option value="center">Center</option>
					<option value="right">Right</option>
				</select>
			</div>
			<div class="pb-form-group">
				<label class="pb-form-label">Text Color</label>
				<input class="pb-input" v-model="node.settings.color">
			</div>
		</div>
		<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
			<div class="pb-form-group">
				<label class="pb-form-label">CSS Class</label>
				<input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-heading">
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'BasicHeadingSettings',
	props: {
		node: {
			type: Object,
			required: true,
		},
	},
	data() {
		return { tags: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'] };
	},
};
</script>
