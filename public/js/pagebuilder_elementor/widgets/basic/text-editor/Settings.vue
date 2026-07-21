<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--text-editor">
		<div class="pb-widget-settings__group">
			<div class="pb-form-group">
				<div class="pb-label-row pb-widget-settings__label-row">
					<label class="pb-form-label mb-0">Content</label>
					<button type="button" class="pb-editor-expand-btn" title="Expand editor" @click="editor.openTextEditorModal()">
						<i class="fas fa-expand-alt"></i><span>Expand</span>
					</button>
				</div>
				<component :is="editor.ckEditorField" :model-value="node.settings.html" @update:modelValue="node.settings.html=$event" />
			</div>
		</div>
		<div class="pb-widget-settings__group pb-widget-settings__group--advanced">
			<div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-text"></div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'BasicTextEditorSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
};
</script>
