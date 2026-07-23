<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--divider">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'style'}" @click="editor.settingsTab = 'style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Divider</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">Style</label><select class="pb-select" v-model="node.settings.style"><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option></select></div></div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'style'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Divider</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Width</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('divider-width')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('divider-width')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'divider-width-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice('divider-width', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div>
						<div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="editor.sizeControlMax(node, 'width', '100%')" :step="editor.sizeControlStep(node, 'width', '100%')" :value="editor.sizeControlDisplayValue(node, 'width', '100%') || 0" @input="editor.onSizeControlInput(node, 'width', $event, { fallback: '100%', emptyToken: '100%' })"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :max="editor.sizeControlMax(node, 'width', '100%')" :step="editor.sizeControlStep(node, 'width', '100%')" :value="editor.sizeControlDisplayValue(node, 'width', '100%')" @input="editor.onSizeControlInput(node, 'width', $event, { fallback: '100%', emptyToken: '100%' })"><select class="pb-mini-unit" :value="editor.sizeControlUnit(node, 'width', '100%')" @change="editor.setSizeControlUnit(node, 'width', $event.target.value, { fallback: '100%', emptyToken: '100%' })"><option v-for="unit in editor.sizeControlUnits" :key="'divider-width-unit-' + unit" :value="unit">{{ unit }}</option></select></div></div>
					</div>
					<div class="pb-form-group"><label class="pb-form-label">Thickness</label><div class="pb-range-value-row"><input type="range" class="pb-range" min="0" max="20" step="1" v-model.number="node.settings.thickness"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="20" step="1" v-model.number="node.settings.thickness"><select class="pb-mini-unit" disabled aria-label="Thickness unit"><option value="px">px</option></select></div></div></div>
					<div class="pb-form-group"><label class="pb-form-label">Color</label><div class="pb-color-row"><input type="color" class="pb-color-swatch" v-model="node.settings.color"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.color" placeholder="#d0d7e6"></div></div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab === 'advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open><summary>Attributes</summary><div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-divider"></div></div></details>
		</div>
	</div>
</template>

<script>
export default { name: 'BasicDividerSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>