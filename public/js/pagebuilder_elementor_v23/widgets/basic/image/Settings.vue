<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--image">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='content'}" @click="editor.settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}" @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-show="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open><summary>Image</summary><div class="pb-collapsible-body">
				<div class="pb-form-group"><label class="pb-form-label">Image Source</label><select class="pb-select" :value="imageSource" @change="setImageSource($event.target.value)"><option value="ckfinder">CKFinder</option><option value="url">External URL</option></select></div>
				<div v-if="imageSource === 'ckfinder'" class="pb-form-group"><label class="pb-form-label">Image</label><div class="pb-bg-media-field pb-widget-settings__media-field" :class="{ 'has-image': !!node.settings.src }"><div class="pb-bg-media-preview" :style="node.settings.src ? { backgroundImage: 'url(' + node.settings.src + ')' } : {}"><button type="button" class="pb-bg-media-center-btn" :title="node.settings.src ? 'Change Image' : 'Choose Image'" @click="editor.chooseMedia(node.settings, 'src')"><i :class="node.settings.src ? 'fas fa-pen' : 'fas fa-plus'"></i></button></div><div class="pb-bg-media-actions"><button type="button" class="pb-bg-media-choose" @click="editor.chooseMedia(node.settings, 'src')">Choose Image</button><button type="button" class="pb-bg-media-remove" :disabled="!node.settings.src" title="Remove Image" @click="editor.clearMedia(node.settings, 'src')"><i class="fas fa-trash-alt"></i></button></div></div></div>
				<div v-else class="pb-form-group"><label class="pb-form-label">Image URL</label><input class="pb-input" type="url" v-model.trim="node.settings.src" placeholder="https://example.com/image.jpg"><div class="pb-form-note">Use a direct HTTP or HTTPS image URL.</div></div>
				<div class="pb-form-group"><label class="pb-form-label">Alt</label><input class="pb-input" v-model="node.settings.alt"></div>
			</div></details>
		</div>

		<div v-show="editor.settingsTab==='style'" class="pb-tab-content">
			<details class="pb-collapsible" open><summary>Dimensions</summary><div class="pb-collapsible-body"><DimensionSetting label="Width" control-key="image-width" setting-key="width" fallback="100%" :node="node" :editor="editor" /><DimensionSetting label="Height" control-key="image-height" setting-key="height" fallback="auto" allow-empty :node="node" :editor="editor" /></div></details>
		</div>

		<div v-show="editor.settingsTab==='advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open><summary>Attributes</summary><div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-image"></div></div></details>
		</div>
	</div>
</template>
<script>
const DimensionSetting = {
	props: {
		label: String, controlKey: String, settingKey: String, fallback: String, allowEmpty: Boolean,
		node: { type: Object, required: true }, editor: { type: Object, required: true },
	},
	template: `
		<div class="pb-form-group">
			<div class="pb-label-row pb-label-row-device">
				<label class="pb-form-label mb-0">{{ label }}</label>
				<div class="pb-control-device-wrap">
					<button class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(controlKey)" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button>
					<div v-if="editor.isControlResponsiveMenuOpen(controlKey)" class="pb-control-device-menu">
						<button v-for="device in editor.responsiveDevices" :key="controlKey + '-' + device.value" class="pb-control-device-item" :class="{active: editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(controlKey, device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button>
					</div>
				</div>
			</div>
			<div class="pb-range-value-row">
				<input type="range" class="pb-range" min="0" :max="editor.sizeControlMax(node, settingKey, fallback)" :step="editor.sizeControlStep(node, settingKey, fallback)" :value="editor.sizeControlDisplayValue(node, settingKey, fallback, { allowEmpty }) || 0" @input="editor.onSizeControlInput(node, settingKey, $event, { fallback, allowEmpty, emptyToken: fallback })">
				<div class="pb-value-with-unit">
					<input class="pb-input pb-input-compact" type="number" min="0" :max="editor.sizeControlMax(node, settingKey, fallback)" :step="editor.sizeControlStep(node, settingKey, fallback)" :value="editor.sizeControlDisplayValue(node, settingKey, fallback, { allowEmpty })" @input="editor.onSizeControlInput(node, settingKey, $event, { fallback, allowEmpty, emptyToken: fallback })" :placeholder="allowEmpty ? fallback : ''">
					<select class="pb-mini-unit" :value="editor.sizeControlUnit(node, settingKey, fallback)" @change="editor.setSizeControlUnit(node, settingKey, $event.target.value, { fallback, allowEmpty, emptyToken: fallback })"><option v-for="unit in editor.sizeControlUnits" :key="controlKey + '-unit-' + unit" :value="unit">{{ unit }}</option></select>
				</div>
			</div>
			<div v-if="allowEmpty" class="pb-form-note">Leave empty to keep auto height.</div>
		</div>`,
};

export default {
	name: 'BasicImageSettings',
	components: { DimensionSetting },
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	computed: {
		imageSource() {
			return this.node.settings?.imageSource === 'url' ? 'url' : 'ckfinder';
		},
	},
	methods: {
		setImageSource(value) {
			this.node.settings.imageSource = value === 'url' ? 'url' : 'ckfinder';
		},
	},
};
</script>
