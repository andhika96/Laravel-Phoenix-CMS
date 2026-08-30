<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--static-html">
		<div v-if="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Exact Visual Preview</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-note">HTML sumber dirender di iframe sandbox. Widget ini dibuat hanya oleh Static Import dan tidak tersedia di toolbox manual.</div>
					<div class="pb-form-group"><label class="pb-form-label">Frame title</label><input class="pb-input" v-model="node.settings.title" maxlength="255"></div>
					<div class="pb-form-group"><label class="pb-form-label">Fallback height</label><input class="pb-input" v-model="node.settings.height" placeholder="1200px"></div>
					<div class="pb-form-group"><label class="pb-form-label">Source size</label><div class="pb-static-html-source-size">{{ sourceSize }} characters</div></div>
				</div>
			</details>
		</div>
		<div v-if="editor.settingsTab==='advanced'" class="pb-tab-content">
			<component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" @responsive-device="editor.setResponsiveDevice" />
		</div>
	</div>
</template>

<script>
export default {
	name: 'StaticHtmlPreviewSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	computed: { sourceSize() { return String(this.node.settings?.srcdoc || '').length.toLocaleString(); } },
};
</script>
