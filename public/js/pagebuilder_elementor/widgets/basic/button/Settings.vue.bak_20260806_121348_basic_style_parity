<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--button">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Button</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Text</label><input class="pb-input" v-model="node.settings.text"></div>
					<div class="pb-form-group"><label class="pb-form-label">URL</label><input class="pb-input" v-model="node.settings.url"></div>
					<div class="pb-form-group"><label class="pb-form-label">Align</label><select class="pb-select" v-model="node.settings.align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
					<div class="pb-form-group pb-toggle-label-row pb-widget-settings__compact-toggle"><label class="pb-form-label mb-0">Open New Tab</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'button-new-tab-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.newTab"><label :for="'button-new-tab-' + node.id"></label></div><span class="pb-toggle-state">{{ node.settings.newTab ? 'On' : 'Off' }}</span></div></div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Attributes</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.className" placeholder="custom-button"></div></div>
			</details>
		</div>
	</div>
</template>

<script>
export default { name: 'BasicButtonSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>