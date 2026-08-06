<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--icon">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Icon</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Icon</label><button type="button" class="pb-icon-picker-field" @click="editor.openIconLibrary(node)"><div class="pb-icon-picker-preview"><i :class="node.settings.iconClass || 'far fa-star'"></i></div><div class="pb-icon-picker-copy"><div class="pb-icon-picker-name">{{ editor.iconWidgetCurrentLabel(node) }}</div><div class="pb-icon-picker-style">{{ editor.iconWidgetCurrentStyleLabel(node) }}</div></div><i class="fas fa-chevron-right"></i></button></div>
					<div class="pb-form-group"><label class="pb-form-label">View</label><select class="pb-select" v-model="node.settings.view"><option v-for="option in editor.iconWidgetViewOptions" :key="'icon-view-' + option.value" :value="option.value">{{ option.label }}</option></select></div>
					<div v-if="editor.iconWidgetUsesShape(node)" class="pb-form-group"><label class="pb-form-label">Shape</label><select class="pb-select" v-model="node.settings.shape"><option v-for="option in editor.iconWidgetShapeOptions" :key="'icon-shape-' + option.value" :value="option.value">{{ option.label }}</option></select></div>
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Link</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Link</label><div class="pb-input-with-action"><input class="pb-input" v-model="node.settings.link" placeholder="Paste URL or type"><button type="button" class="pb-field-action-btn" title="Link Options" @click="editor.toggleIconLinkOptions(node)"><i class="fas fa-cog"></i></button></div></div>
					<div v-if="editor.isIconLinkOptionsOpen(node)" class="pb-icon-link-options">
						<label class="pb-icon-link-check"><input type="checkbox" v-model="node.settings.openInNewWindow"><span>Open in new window</span></label>
						<label class="pb-icon-link-check"><input type="checkbox" v-model="node.settings.nofollow"><span>Add nofollow</span></label>
						<div class="pb-form-group"><div class="pb-label-row"><label class="pb-form-label mb-0">Custom Attributes</label><button type="button" class="pb-seg-btn pb-mini-btn" @click="node.settings.attributes = (node.settings.attributes || []).concat({name:'',value:''})"><i class="fas fa-plus"></i></button></div><div v-for="(attr, i) in (node.settings.attributes || [])" :key="'icon-attr-' + i" class="pb-attr-row"><input class="pb-input" v-model="attr.name" placeholder="key"><input class="pb-input" v-model="attr.value" placeholder="value"><button type="button" class="pb-btn icon-sm" @click="node.settings.attributes.splice(i, 1)"><i class="fas fa-trash"></i></button></div><div class="pb-form-note">Allowed names: data-*, aria-*, and title.</div></div>
					</div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open><summary>Attributes</summary><div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-icon"></div></div></details>
		</div>
	</div>
</template>

<script>
export default { name: 'BasicIconSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>