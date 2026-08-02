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
					<div class="pb-form-group"><label class="pb-form-label">Title</label><textarea class="pb-textarea" rows="4" v-model="node.settings.text"></textarea></div>
					<div class="pb-form-group"><label class="pb-form-label">Link</label><component :is="editor.linkControl" :url="node.settings.linkUrl" :target="node.settings.linkTarget" :nofollow="node.settings.linkNofollow" :custom-attributes="node.settings.linkCustomAttributes" @update:url="node.settings.linkUrl=$event" @update:target="node.settings.linkTarget=$event" @update:nofollow="node.settings.linkNofollow=$event" @update:customAttributes="node.settings.linkCustomAttributes=$event" /></div>
					<div class="pb-form-group"><label class="pb-form-label">HTML Tag</label><select class="pb-select" v-model="node.settings.tag"><option v-for="tag in tags" :key="tag" :value="tag">{{ tag.toUpperCase() }}</option></select></div>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab==='style'" class="pb-tab-content pb-heading-style-settings">
			<details class="pb-collapsible" open>
				<summary>Heading</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group pb-heading-choice-row"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Alignment</label><div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu('heading-alignment')" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen('heading-alignment')" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="'heading-alignment-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice('heading-alignment', device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div></div><div class="pb-btn-group pb-heading-segmented"><button v-for="option in [{value:'left',icon:'fas fa-align-left',label:'Left'},{value:'center',icon:'fas fa-align-center',label:'Center'},{value:'right',icon:'fas fa-align-right',label:'Right'},{value:'justify',icon:'fas fa-align-justify',label:'Justified'}]" :key="option.value" type="button" class="pb-seg-btn" :class="{active:node.settings[editor.activeResponsiveKey('align')]===option.value}" :aria-pressed="node.settings[editor.activeResponsiveKey('align')]===option.value" :title="option.label" @click.prevent="editor.setResponsiveSetting(node.settings, 'align', option.value)"><i :class="option.icon"></i><span class="sr-only">{{ option.label }}</span></button></div></div>
					<component :is="editor.typographyControl" prefix="heading" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'32px',FontWeight:'600',LineHeight:'1.2em'}" @responsive-device="editor.setResponsiveDevice" />
					<component :is="editor.textStrokeControl" :settings="node.settings" width-key="headingTextStrokeWidth" color-key="headingTextStrokeColor" :responsive-device="editor.responsiveDevice" control-id="heading-stroke" :open="activeTextEffect==='heading-stroke'" @request-open="activeTextEffect=$event" />
					<component :is="editor.textShadowControl" :model-value="node.settings.headingTextShadow" control-id="heading-shadow" :open="activeTextEffect==='heading-shadow'" @request-open="activeTextEffect=$event" @update:modelValue="node.settings.headingTextShadow=$event" />
					<div class="pb-form-group"><label class="pb-form-label">Blend Mode</label><select class="pb-select" v-model="node.settings.blendMode"><option v-for="mode in blendModes" :key="mode.value" :value="mode.value">{{ mode.label }}</option></select></div>
					<div class="pb-state-tabs pb-state-tabs--two"><button type="button" :class="{active:styleState==='normal'}" @click="styleState='normal'">Normal</button><button type="button" :class="{active:styleState==='hover'}" @click="styleState='hover'">Hover</button></div>
					<div v-if="styleState==='normal'" class="pb-form-group"><label class="pb-form-label">Text Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.color" placeholder="#101828"></div></div>
					<template v-else><div class="pb-form-group"><label class="pb-form-label">Link Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.hoverColor" placeholder="#101828"></div></div><div class="pb-form-group"><label class="pb-form-label">Transition Duration</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="5" step="0.1" v-model.number="node.settings.hoverTransitionDuration"><input class="pb-input pb-input-compact" type="number" min="0" max="10" step="0.1" v-model.number="node.settings.hoverTransitionDuration"></div></div></template>
				</div>
			</details>
		</div>

		<div v-show="editor.settingsTab==='advanced'" class="pb-tab-content">
			<component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings,$event)" @clear-media="editor.clearMedia(node.settings,$event)" @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')" />
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
		return {
			tags: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'],
			styleState: 'normal',
			activeTextEffect: '',
			blendModes: [
				['normal', 'Normal'], ['multiply', 'Multiply'], ['screen', 'Screen'], ['overlay', 'Overlay'], ['darken', 'Darken'], ['lighten', 'Lighten'], ['color-dodge', 'Color Dodge'], ['saturation', 'Saturation'], ['color', 'Color'], ['difference', 'Difference'], ['exclusion', 'Exclusion'], ['hue', 'Hue'], ['luminosity', 'Luminosity'],
			].map(([value, label]) => ({ value, label })),
		};
	},
};
</script>

<style scoped>
.pb-heading-choice-row { margin-bottom: 10px; }
.pb-heading-segmented { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.pb-heading-segmented .pb-seg-btn { min-width: 0; }
</style>
