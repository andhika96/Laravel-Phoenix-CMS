<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--icon">
		<div class="pb-widget-settings__group">
			<div class="pb-widget-settings__section-title">Icon</div>
			<div class="pb-form-group"><label class="pb-form-label">Icon</label><button type="button" class="pb-icon-picker-field" @click="editor.openIconLibrary(node)"><div class="pb-icon-picker-preview"><i :class="node.settings.iconClass || 'far fa-star'"></i></div><div class="pb-icon-picker-copy"><div class="pb-icon-picker-name">{{ editor.iconWidgetCurrentLabel(node) }}</div><div class="pb-icon-picker-style">{{ editor.iconWidgetCurrentStyleLabel(node) }}</div></div><i class="fas fa-chevron-right"></i></button></div>
			<div class="pb-form-group"><label class="pb-form-label">View</label><select class="pb-select" v-model="node.settings.view"><option v-for="option in editor.iconWidgetViewOptions" :key="'icon-view-' + option.value" :value="option.value">{{ option.label }}</option></select></div>
			<div class="pb-form-group" v-if="editor.iconWidgetUsesShape(node)"><label class="pb-form-label">Shape</label><select class="pb-select" v-model="node.settings.shape"><option v-for="option in editor.iconWidgetShapeOptions" :key="'icon-shape-' + option.value" :value="option.value">{{ option.label }}</option></select></div>
			<div class="pb-form-group"><label class="pb-form-label">Link</label><div class="pb-input-with-action"><input class="pb-input" v-model="node.settings.link" placeholder="Paste URL or type"><button type="button" class="pb-field-action-btn" title="Link Options" @click="editor.toggleIconLinkOptions(node)"><i class="fas fa-cog"></i></button></div></div>
			<div v-if="editor.isIconLinkOptionsOpen(node)" class="pb-icon-link-options">
				<label class="pb-icon-link-check"><input type="checkbox" v-model="node.settings.openInNewWindow"><span>Open in new window</span></label>
				<label class="pb-icon-link-check"><input type="checkbox" v-model="node.settings.nofollow"><span>Add nofollow</span></label>
				<div class="pb-form-group"><div class="pb-label-row"><label class="pb-form-label mb-0">Custom Attributes</label><button class="pb-seg-btn pb-mini-btn" @click="node.settings.attributes=(node.settings.attributes||[]).concat({name:'',value:''})"><i class="fas fa-plus"></i></button></div><div v-for="(attr,i) in (node.settings.attributes||[])" :key="'icon-attr-'+i" class="pb-attr-row"><input class="pb-input" v-model="attr.name" placeholder="key"><input class="pb-input" v-model="attr.value" placeholder="value"><button class="pb-btn icon-sm" @click="node.settings.attributes.splice(i,1)"><i class="fas fa-trash"></i></button></div></div>
			</div>
		</div>
	</div>
</template>

<script>
export default { name: 'BasicIconSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>
