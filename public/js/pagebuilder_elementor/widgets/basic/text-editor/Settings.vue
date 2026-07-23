<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--text-editor">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='content'}" @click="editor.settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Text Editor</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<div class="pb-label-row pb-widget-settings__label-row"><label class="pb-form-label mb-0">Content</label><button type="button" class="pb-editor-expand-btn" title="Expand editor" @click="editor.openTextEditorModal()"><i class="fas fa-expand-alt"></i><span>Expand</span></button></div>
						<component :is="editor.ckEditorField" :model-value="node.settings.html" @update:modelValue="node.settings.html=$event" />
					</div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab==='advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Attributes</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-text"></div></div>
			</details>
		</div>
	</div>
</template>

<script>
export default {
	name: 'BasicTextEditorSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
};
</script>