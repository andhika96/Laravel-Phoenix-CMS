<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--spacer">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Spacer</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group">
					<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Height</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('spacer-height')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('spacer-height')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'spacer-height-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice('spacer-height', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div>
					<div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="editor.spacerHeightMax(node)" :step="editor.spacerHeightStep(node)" :value="editor.spacerHeightValue(node)" @input="editor.onSpacerHeightInput(node, $event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :max="editor.spacerHeightMax(node)" :step="editor.spacerHeightStep(node)" :value="editor.spacerHeightValue(node)" @input="editor.onSpacerHeightInput(node, $event)"><select class="pb-mini-unit" :value="editor.spacerHeightUnit(node)" @change="editor.setSpacerHeightUnit(node, $event.target.value)"><option v-for="unit in editor.sizeControlUnits" :key="'spacer-height-unit-' + unit" :value="unit">{{ unit }}</option></select></div></div>
				</div></div>
			</details>
		</div>

		<div v-if="editor.settingsTab === 'advanced'" class="pb-tab-content">
            <component
                :is="editor.widgetAdvancedControls"
                :node="node"
                :responsive-device="editor.responsiveDevice"
                :elementor-choices="true"
                @responsive-device="editor.setResponsiveDevice"
                @choose-media="editor.chooseMedia(node.settings,$event)"
                @clear-media="editor.clearMedia(node.settings,$event)"
                @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')"
            />
</div>
	</div>
</template>

<script>
export default { name: 'BasicSpacerSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>
