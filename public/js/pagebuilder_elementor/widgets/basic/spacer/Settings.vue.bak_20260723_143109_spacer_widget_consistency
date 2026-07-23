<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--spacer">
		<div class="pb-widget-settings__group"><div class="pb-form-group">
			<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Height</label><div class="pb-control-device-wrap"><button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('spacer-height')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('spacer-height')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'spacer-height-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('spacer-height', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div>
			<div class="pb-range-value-row"><input type="range" class="pb-range" min="0" :max="editor.spacerHeightMax(node)" :step="editor.spacerHeightStep(node)" :value="editor.spacerHeightValue(node)" @input="editor.onSpacerHeightInput(node, $event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :max="editor.spacerHeightMax(node)" :step="editor.spacerHeightStep(node)" :value="editor.spacerHeightValue(node)" @input="editor.onSpacerHeightInput(node, $event)"><select class="pb-mini-unit" :value="editor.spacerHeightUnit(node)" @change="editor.setSpacerHeightUnit(node, $event.target.value)"><option v-for="unit in editor.sizeControlUnits" :key="'spacer-height-unit-' + unit" :value="unit">{{ unit }}</option></select></div></div>
		</div></div>
		<div class="pb-widget-settings__group pb-widget-settings__group--advanced"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-spacer"></div></div>
	</div>
</template>

<script>
export default { name: 'BasicSpacerSettings', props: { node: { type: Object, required: true }, editor: { type: Object, required: true } } };
</script>
