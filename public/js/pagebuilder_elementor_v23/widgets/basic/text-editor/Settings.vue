<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--text-editor">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='content'}" @click="editor.settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}" @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Text Editor</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<div class="pb-label-row pb-widget-settings__label-row"><label class="pb-form-label mb-0">Content</label><button type="button" class="pb-editor-expand-btn" title="Expand editor" @click="editor.openTextEditorModal()"><i class="fas fa-expand-alt"></i><span>Expand</span></button></div>
						<component :is="editor.ckEditorField" :model-value="node.settings.html" @update:modelValue="node.settings.html=$event" />
					</div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab==='style'" class="pb-tab-content pb-basic-text-style-settings">
			<details class="pb-collapsible" open>
				<summary>Text Editor</summary>
				<div class="pb-collapsible-body">
					<ResponsiveChoice label="Alignment" base="align" control-id="text-editor-alignment" :node="node" :editor="editor" :options="alignmentOptions" />
					<component :is="editor.typographyControl" prefix="textEditor" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'16px',FontWeight:'400',LineHeight:'1.5em'}" @responsive-device="editor.setResponsiveDevice" />
					<component :is="editor.textShadowControl" :model-value="node.settings.textEditorTextShadow" control-id="text-editor-shadow" :open="activeTextEffect === 'text-editor-shadow'" @request-open="activeTextEffect = $event" @update:modelValue="node.settings.textEditorTextShadow = $event" />
					<ResponsiveDimensionControl label="Paragraph Spacing" base="paragraphSpacing" control-id="text-editor-paragraph-spacing" fallback="1em" :node="node" :editor="editor" :units="['px','em','rem']" :max="100" />
					<div class="pb-state-tabs pb-state-tabs--two"><button type="button" :class="{active: styleState === 'normal'}" @click="styleState = 'normal'">Normal</button><button type="button" :class="{active: styleState === 'hover'}" @click="styleState = 'hover'">Hover</button></div>
					<div class="pb-form-group"><label class="pb-form-label">Text Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[styleState === 'hover' ? 'textEditorTextColorHover' : 'textEditorTextColor']" placeholder="#475467"></div>
					<div class="pb-form-group"><label class="pb-form-label">Link Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[styleState === 'hover' ? 'textEditorLinkColorHover' : 'textEditorLinkColor']" placeholder="#4f46e5"></div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab==='advanced'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Attributes</summary>
				<div class="pb-collapsible-body"><div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="node.settings.cssClass" placeholder="custom-text"></div></div>
			</details>
		</div>
	</div>
</template>

<script>
const ResponsiveMenu = {
	props: { editor: { type: Object, required: true }, id: { type: String, required: true } },
	template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(id)" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id + '-' + device.value" type="button" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice(id, device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div>`,
};

const ResponsiveChoice = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, node: Object, editor: Object, options: Array },
	computed: {
		settingKey() { return this.editor.activeResponsiveKey(this.base); },
		value() { return this.node.settings[this.settingKey] || this.node.settings[this.base] || this.options?.[0]?.value || ''; },
	},
	methods: { setValue(value) { this.editor.setResponsiveSetting(this.node.settings, this.base, value); } },
	template: `<div class="pb-form-group pb-basic-responsive-choice"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{ label }}</label><responsive-menu :editor="editor" :id="controlId" /></div><div class="pb-btn-group pb-basic-segmented"><button v-for="option in options" :key="option.value" type="button" class="pb-seg-btn" :class="{active: value === option.value}" :aria-pressed="value === option.value" :title="option.label" @click.prevent="setValue(option.value)"><i :class="option.icon"></i><span class="sr-only">{{ option.label }}</span></button></div></div>`,
};

function parseDimension(raw, fallback = '0px', units = ['px', 'pt', '%', 'em', 'rem']) {
	const value = String(raw || fallback).trim();
	const match = value.match(/^(-?\d+(?:\.\d+)?)([a-z%]*)$/i);
	const unit = match && units.includes((match[2] || 'px').toLowerCase()) ? (match[2] || 'px').toLowerCase() : 'px';
	return { value: match ? Number(match[1]) : Number.parseFloat(fallback) || 0, unit };
}

const ResponsiveDimensionControl = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, fallback: String, node: Object, editor: Object, units: { type: Array, default: () => ['px', 'pt', '%', 'em', 'rem'] }, max: { type: Number, default: 400 } },
	computed: {
		settingKey() { return this.editor.activeResponsiveKey(this.base); },
		source() { return this.node.settings[this.settingKey] || this.node.settings[this.base] || this.fallback; },
		parsed() { return parseDimension(this.source, this.fallback, this.units); },
	},
	methods: {
		setValue(raw) { const value = Number(raw); if (!Number.isFinite(value)) return; this.editor.setResponsiveSetting(this.node.settings, this.base, `${Math.min(this.max, Math.max(0, value))}${this.parsed.unit}`); },
		setUnit(unit) { const safe = this.units.includes(unit) ? unit : this.units[0]; this.editor.setResponsiveSetting(this.node.settings, this.base, `${this.parsed.value}${safe}`); },
	},
	template: `<div class="pb-form-group pb-basic-dimension-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{ label }}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :id="controlId" /></div></div><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" :max="max" :step="parsed.unit === 'px' ? 1 : .1" :value="parsed.value" @input="setValue($event.target.value)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" :max="max" :step="parsed.unit === 'px' ? 1 : .1" :value="parsed.value" @input="setValue($event.target.value)"><select class="pb-mini-unit" :value="parsed.unit" :aria-label="label + ' unit'" @change="setUnit($event.target.value)"><option v-for="option in units" :key="option" :value="option">{{ option }}</option></select></div></div></div>`,
};

export default {
	name: 'BasicTextEditorSettings',
	components: { ResponsiveChoice, ResponsiveDimensionControl },
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	data() {
		return {
			styleState: 'normal',
			activeTextEffect: '',
			alignmentOptions: [
				{ value: 'left', label: 'Left', icon: 'fas fa-align-left' },
				{ value: 'center', label: 'Center', icon: 'fas fa-align-center' },
				{ value: 'right', label: 'Right', icon: 'fas fa-align-right' },
				{ value: 'justify', label: 'Justified', icon: 'fas fa-align-justify' },
			],
		};
	},
};
</script>

<style scoped>
.pb-basic-segmented { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.pb-basic-segmented .pb-seg-btn { min-width: 0; }
.pb-basic-dimension-control .pb-label-tools { display: inline-flex; align-items: center; gap: 7px; margin-left: auto; }
.pb-basic-dimension-control .pb-label-tools .pb-mini-unit { width: 56px; min-width: 56px; }
</style>
