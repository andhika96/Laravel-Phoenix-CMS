<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--heading">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='content'}" @click="editor.settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}" @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Heading</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Text</label><textarea class="pb-textarea" v-model="node.settings.text"></textarea></div>
					<div class="pb-form-group"><label class="pb-form-label">HTML Tag</label><select class="pb-select" v-model="node.settings.tag"><option v-for="tag in tags" :key="tag" :value="tag">{{ tag.toUpperCase() }}</option></select></div>
					<div class="pb-form-group"><label class="pb-form-label">Alignment</label><select class="pb-select" v-model="node.settings.align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab==='style'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Heading</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Text Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="node.settings.color"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.color" placeholder="#101828"></div></div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab==='advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Attributes</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-heading"></div></div>
			</details>
		</div>
	</div>
</template>

<script>
export default {
	name: 'BasicHeadingSettings',
	props: {
		node: { type: Object, required: true },
		editor: { type: Object, required: true },
	},
	data() {
		return { tags: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'] };
	},
};
</script>