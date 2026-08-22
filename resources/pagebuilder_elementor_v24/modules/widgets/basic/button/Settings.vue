<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--button">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'style'}" @click="editor.settingsTab = 'style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Button</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Text</label><input class="pb-input" v-model="node.settings.text"></div>
					<div class="pb-form-group"><label class="pb-form-label">URL</label><input class="pb-input" v-model="node.settings.url"></div>
					<ButtonIconControl :node="node" :editor="editor" />
					<div class="pb-form-group"><label class="pb-form-label">Align</label><select class="pb-select" v-model="node.settings.align"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option><option value="stretch">Justified</option></select></div>
					<div class="pb-form-group pb-toggle-label-row pb-widget-settings__compact-toggle"><label class="pb-form-label mb-0">Open New Tab</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="'button-new-tab-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.newTab"><label :for="'button-new-tab-' + node.id"></label></div><span class="pb-toggle-state">{{ node.settings.newTab ? 'On' : 'Off' }}</span></div></div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab === 'style'" class="pb-tab-content pb-basic-button-style-settings">
			<details class="pb-collapsible" open>
				<summary>Button</summary>
				<div class="pb-collapsible-body">
					<ResponsiveChoice label="Position" base="align" control-id="button-position" :node="node" :editor="editor" :options="alignmentOptions" />
					<component :is="editor.typographyControl" prefix="button" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'16px',FontWeight:'600',LineHeight:'1.2em'}" @responsive-device="editor.setResponsiveDevice" />
					<component :is="editor.textShadowControl" :model-value="node.settings.buttonTextShadow" control-id="button-text-shadow" :open="activeTextEffect === 'button-text-shadow'" @request-open="activeTextEffect = $event" @update:modelValue="node.settings.buttonTextShadow = $event" />
					<div class="pb-state-tabs pb-state-tabs--two"><button type="button" :class="{active: styleState === 'normal'}" @click="styleState = 'normal'">Normal</button><button type="button" :class="{active: styleState === 'hover'}" @click="styleState = 'hover'">Hover</button></div>
					<div class="pb-form-group"><label class="pb-form-label">Text Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[styleState === 'hover' ? 'buttonTextColorHover' : 'buttonTextColor']" placeholder="#ffffff"></div>
					<BackgroundControl :node="node" :state="styleState" />
					<BoxShadowControl :node="node" :state="styleState" />
					<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="node.settings[styleState === 'hover' ? 'buttonBorderTypeHover' : 'buttonBorderType']"><option value="none">Default</option><option value="none">None</option><option value="solid">Solid</option><option value="double">Double</option><option value="dotted">Dotted</option><option value="dashed">Dashed</option><option value="groove">Groove</option></select></div>
					<div v-if="node.settings[styleState === 'hover' ? 'buttonBorderTypeHover' : 'buttonBorderType'] !== 'none'" class="pb-form-group"><label class="pb-form-label">Border Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[styleState === 'hover' ? 'buttonBorderColorHover' : 'buttonBorderColor']" placeholder="#0d6efd"></div>
					<ResponsiveBoxControl label="Border Radius" base="buttonBorderRadius" control-id="button-border-radius" fallback="5px" :node="node" :editor="editor" />
					<ResponsiveBoxControl label="Padding" base="buttonPadding" control-id="button-padding" fallback="12px 24px" :node="node" :editor="editor" />
				</div>
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
const ResponsiveMenu = {
	props: { editor: { type: Object, required: true }, id: { type: String, required: true } },
	template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(id)" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id + '-' + device.value" type="button" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice(id, device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div>`,
};

const ButtonIconControl = {
	components: { ResponsiveMenu },
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	data() {
		return {
			sourceOptions: [
				{ value: 'none', label: 'None', icon: 'fas fa-ban', title: 'Remove icon' },
				{ value: 'svg', label: 'SVG', icon: 'fas fa-upload', title: 'Upload SVG' },
				{ value: 'library', label: 'Library', icon: 'fas fa-icons', title: 'Icon Library' },
			],
			positionOptions: [
				{ value: 'row', label: 'Start', icon: 'fas fa-arrow-left' },
				{ value: 'row-reverse', label: 'End', icon: 'fas fa-arrow-right' },
			],
			spacingUnits: ['px', 'em', 'rem'],
		};
	},
	computed: {
		source() {
			const value = String(this.node.settings.iconSource || 'none').toLowerCase();
			return ['none', 'library', 'svg'].includes(value) ? value : 'none';
		},
		iconClassValue() { return String(this.node.settings.iconClass || '').trim(); },
		iconSvgValue() { return String(this.node.settings.iconSvg || '').trim(); },
		hasIcon() {
			return (this.source === 'library' && /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(this.iconClassValue))
				|| (this.source === 'svg' && this.iconSvgValue.startsWith('<svg'));
		},
		iconLabel() {
			if (this.source === 'svg') return 'Uploaded SVG';
			return typeof this.editor.iconWidgetCurrentLabel === 'function' ? this.editor.iconWidgetCurrentLabel(this.node) : 'Selected icon';
		},
		iconStyleLabel() {
			if (this.source === 'svg') return 'Custom SVG';
			return typeof this.editor.iconWidgetCurrentStyleLabel === 'function' ? this.editor.iconWidgetCurrentStyleLabel(this.node) : 'Font Awesome';
		},
		iconSvgDataUri() {
			return this.iconSvgValue.startsWith('<svg') ? 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(this.iconSvgValue) : '';
		},
		position() { return ['row', 'row-reverse'].includes(this.node.settings.buttonIconPosition) ? this.node.settings.buttonIconPosition : 'row'; },
		spacingKey() { return this.editor.activeResponsiveKey('buttonIconSpacing'); },
		spacingRaw() { return this.node.settings[this.spacingKey] || this.node.settings.buttonIconSpacing || '8px'; },
		spacingParsed() {
			const raw = String(this.spacingRaw || '').trim();
			const match = raw.match(/^(-?\d+(?:\.\d+)?)(px|em|rem)$/i);
			const unit = match && this.spacingUnits.includes(match[2].toLowerCase()) ? match[2].toLowerCase() : 'px';
			return { value: match ? Number(match[1]) : 8, unit };
		},
	},
	methods: {
		clearIcon() {
			this.node.settings.iconSource = 'none';
			this.node.settings.iconClass = '';
			this.node.settings.iconSvg = '';
		},
		setSource(source) {
			if (source === 'none') { this.clearIcon(); return; }
			if (source === 'library' && typeof this.editor.openIconLibrary === 'function') this.editor.openIconLibrary(this.node);
			if (source === 'svg' && typeof this.editor.chooseButtonSvg === 'function') this.editor.chooseButtonSvg(this.node);
		},
		setPosition(value) { this.node.settings.buttonIconPosition = ['row', 'row-reverse'].includes(value) ? value : 'row'; },
		setSpacing(raw) {
			const value = Number(raw);
			if (!Number.isFinite(value)) return;
			const safe = Math.min(50, Math.max(0, value));
			this.editor.setResponsiveSetting(this.node.settings, 'buttonIconSpacing', `${safe}${this.spacingParsed.unit}`);
		},
		setSpacingUnit(unit) {
			const safe = this.spacingUnits.includes(unit) ? unit : 'px';
			this.editor.setResponsiveSetting(this.node.settings, 'buttonIconSpacing', `${this.spacingParsed.value}${safe}`);
		},
	},
	template: `<div class="pb-form-group pb-basic-button-icon-control"><div class="pb-label-row pb-basic-button-icon-label-row"><label class="pb-form-label mb-0">Icon</label><span v-if="hasIcon" class="pb-basic-button-icon-status">{{ iconLabel }}</span></div><div class="pb-basic-button-icon-source-grid" role="group" aria-label="Icon source"><button v-for="option in sourceOptions" :key="option.value" type="button" class="pb-basic-button-icon-source-btn" :class="{active: source === option.value}" :aria-pressed="source === option.value" :aria-label="option.title" :title="option.title" @click.prevent="setSource(option.value)"><i :class="option.icon" aria-hidden="true"></i><span>{{ option.label }}</span></button></div><div v-if="hasIcon" class="pb-basic-button-icon-selected"><div class="pb-basic-button-icon-preview"><img v-if="source === 'svg'" :src="iconSvgDataUri" alt="" aria-hidden="true"><i v-else :class="iconClassValue" aria-hidden="true"></i></div><div class="pb-basic-button-icon-copy"><strong>{{ iconLabel }}</strong><small>{{ iconStyleLabel }}</small></div><div class="pb-basic-button-icon-actions"><button type="button" class="pb-basic-button-icon-action" title="Change icon" aria-label="Change icon" @click="setSource(source)"><i class="fas fa-pen" aria-hidden="true"></i></button><button type="button" class="pb-basic-button-icon-action is-remove" title="Remove icon" aria-label="Remove icon" @click="clearIcon"><i class="fas fa-times" aria-hidden="true"></i></button></div></div><div v-if="hasIcon" class="pb-form-group pb-basic-button-icon-option"><div class="pb-label-row"><label class="pb-form-label mb-0">Icon Position</label></div><div class="pb-btn-group pb-basic-button-icon-position" role="group" aria-label="Icon position"><button v-for="option in positionOptions" :key="option.value" type="button" class="pb-seg-btn" :class="{active: position === option.value}" :aria-pressed="position === option.value" :aria-label="option.label" :title="option.label" @click.prevent="setPosition(option.value)"><i :class="option.icon" aria-hidden="true"></i><span class="sr-only">{{ option.label }}</span></button></div></div><div v-if="hasIcon" class="pb-form-group pb-basic-button-icon-option pb-basic-button-icon-spacing"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Icon Spacing</label><responsive-menu :editor="editor" id="button-icon-spacing" /></div><div class="pb-basic-button-icon-spacing-row"><input class="pb-range" type="range" min="0" max="50" step="1" :value="spacingParsed.value" aria-label="Icon Spacing" @input="setSpacing($event.target.value)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="50" step="1" :value="spacingParsed.value" aria-label="Icon Spacing value" @input="setSpacing($event.target.value)"><select class="pb-mini-unit" :value="spacingParsed.unit" aria-label="Icon Spacing unit" @change="setSpacingUnit($event.target.value)"><option v-for="unit in spacingUnits" :key="unit" :value="unit">{{ unit }}</option></select></div></div></div></div>`,
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

function parseDimension(raw, fallback = '0px', units = ['px', 'pt', '%', 'em', 'rem', 'vw', 'vh']) {
	const value = String(raw || fallback).trim();
	const match = value.match(/^(-?\d+(?:\.\d+)?)([a-z%]*)$/i);
	const unit = match && units.includes((match[2] || 'px').toLowerCase()) ? (match[2] || 'px').toLowerCase() : 'px';
	return { value: match ? Number(match[1]) : Number.parseFloat(fallback) || 0, unit };
}
function expandBoxValue(raw, fallback = '0px') {
	const tokens = String(raw || fallback).trim().split(/\s+/).filter(Boolean);
	if (tokens.length === 1) return [tokens[0], tokens[0], tokens[0], tokens[0]];
	if (tokens.length === 2) return [tokens[0], tokens[1], tokens[0], tokens[1]];
	if (tokens.length === 3) return [tokens[0], tokens[1], tokens[2], tokens[1]];
	return tokens.slice(0, 4);
}

const ResponsiveBoxControl = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, fallback: String, node: Object, editor: Object },
	data() { return { linked: true, sides: ['Top', 'Right', 'Bottom', 'Left'], units: ['px', 'pt', '%', 'em', 'rem', 'vw', 'vh'] }; },
	computed: {
		settingKey() { return this.editor.activeResponsiveKey(this.base); },
		source() { return this.node.settings[this.settingKey] || this.node.settings[this.base] || this.fallback; },
		tokens() { return expandBoxValue(this.source, this.fallback).map((token) => parseDimension(token, this.fallback, this.units)); },
		unit() { return this.tokens[0]?.unit || 'px'; },
	},
	methods: {
		setValue(index, raw) {
			const value = Number(raw);
			if (!Number.isFinite(value)) return;
			const safe = Math.min(400, Math.max(0, value));
			const values = this.tokens.map((token) => token.value);
			if (this.linked) values.fill(safe); else values[index] = safe;
			this.editor.setResponsiveSetting(this.node.settings, this.base, values.map((item) => `${item}${this.unit}`).join(' '));
		},
		setUnit(unit) { const safe = this.units.includes(unit) ? unit : 'px'; this.editor.setResponsiveSetting(this.node.settings, this.base, this.tokens.map((token) => `${token.value}${safe}`).join(' ')); },
	},
	template: `<div class="pb-form-group pb-basic-box-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{ label }}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :id="controlId" /><select class="pb-mini-unit" :value="unit" :aria-label="label + ' unit'" @change="setUnit($event.target.value)"><option v-for="option in units" :key="option" :value="option">{{ option }}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in sides" :key="side" class="pb-side-input"><input class="pb-input" type="number" min="0" max="400" :value="tokens[index].value" @input="setValue(index,$event.target.value)"><span>{{ side }}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:linked}" @click="linked = !linked" :title="linked ? 'Unlink values' : 'Link values'"><i class="fas" :class="linked ? 'fa-link' : 'fa-unlink'"></i></button></div></div></div>`,
};

const BackgroundControl = {
	props: { node: Object, state: String },
	computed: {
		suffix() { return this.state === 'hover' ? 'Hover' : ''; },
		typeKey() { return 'buttonBackgroundType' + this.suffix; },
		colorKey() { return 'buttonBackgroundColor' + this.suffix; },
		firstKey() { return 'buttonGradientColorOne' + this.suffix; },
		secondKey() { return 'buttonGradientColorTwo' + this.suffix; },
		angleKey() { return 'buttonGradientAngle' + this.suffix; },
	},
	template: `<div class="pb-basic-background-control"><div class="pb-form-group"><div class="pb-label-row"><label class="pb-form-label mb-0">Background Type</label><div class="pb-seg-group"><button type="button" class="pb-seg-btn" :class="{active:node.settings[typeKey] === 'classic'}" title="Classic" aria-label="Classic" @click="node.settings[typeKey] = 'classic'"><i class="fas fa-paint-brush"></i></button><button type="button" class="pb-seg-btn" :class="{active:node.settings[typeKey] === 'gradient'}" title="Gradient" aria-label="Gradient" @click="node.settings[typeKey] = 'gradient'"><i class="fas fa-adjust"></i></button></div></div></div><div v-if="node.settings[typeKey] === 'gradient'"><div class="pb-form-group"><label class="pb-form-label">First Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[firstKey]"></div><div class="pb-form-group"><label class="pb-form-label">Second Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[secondKey]"></div><div class="pb-form-group"><label class="pb-form-label">Angle</label><input class="pb-input" type="number" min="0" max="360" v-model.number="node.settings[angleKey]"></div></div><div v-else class="pb-form-group"><label class="pb-form-label">Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[colorKey]"></div></div>`,
};

const BoxShadowControl = {
	props: { node: Object, state: String },
	computed: {
		suffix() { return this.state === 'hover' ? 'Hover' : ''; },
		key() { return (base) => 'buttonBoxShadow' + base + this.suffix; },
		enabled() { return this.isEnabled(this.node.settings[this.key('Enabled')]); },
		position() { return this.isEnabled(this.node.settings[this.key('Inset')]) ? 'inset' : 'outline'; },
		fields() {
			return [
				{ key: 'X', label: 'Horizontal', min: -100, max: 100, fallback: 0, step: 1 },
				{ key: 'Y', label: 'Vertical', min: -100, max: 100, fallback: 4, step: 1 },
				{ key: 'Blur', label: 'Blur', min: 0, max: 100, fallback: 12, step: 1 },
				{ key: 'Spread', label: 'Spread', min: -100, max: 100, fallback: 0, step: 1 },
			];
		},
	},
	data() { return { open: false }; },
	methods: {
		isEnabled(value) { return value === true || value === 1 || value === '1' || value === 'true'; },
		inputId(field) { return 'button-shadow-' + this.state + '-' + field.key; },
		numericValue(field) {
			const raw = String(this.node.settings[this.key(field.key)] ?? (field.fallback + 'px'));
			const match = raw.match(/-?\d+(?:\.\d+)?/);
			return match ? Number(match[0]) : field.fallback;
		},
		setDimension(field, raw) {
			const value = Number(raw);
			if (!Number.isFinite(value)) return;
			const clamped = Math.min(field.max, Math.max(field.min, value));
			this.node.settings[this.key(field.key)] = clamped + 'px';
		},
		setPosition(value) { this.node.settings[this.key('Inset')] = value === 'inset'; },
		openEditor() {
			if (!this.enabled) this.node.settings[this.key('Enabled')] = true;
			this.open = !this.open;
		},
		reset() {
			const defaults = this.state === 'hover'
				? { Color: 'rgba(0,0,0,.2)', X: '0px', Y: '6px', Blur: '16px', Spread: '0px', Inset: false }
				: { Color: 'rgba(0,0,0,.16)', X: '0px', Y: '4px', Blur: '12px', Spread: '0px', Inset: false };
			Object.entries(defaults).forEach(([base, value]) => { this.node.settings[this.key(base)] = value; });
			this.node.settings[this.key('Enabled')] = false;
			this.open = false;
		},
	},
	template: `<div class="pb-basic-inline-editor" :class="{ 'is-open': open }"><div class="pb-basic-inline-editor__summary"><span>Box Shadow</span><div class="pb-basic-shadow-actions"><button type="button" class="pb-basic-edit-button" :class="{active:open || enabled}" :aria-expanded="open ? 'true' : 'false'" :aria-label="open ? 'Close Box Shadow' : 'Edit Box Shadow'" :title="open ? 'Close Box Shadow' : 'Edit Box Shadow'" @click="openEditor"><i class="fas fa-pen"></i><span class="sr-only">Edit</span></button><button type="button" class="pb-basic-reset-button" :disabled="!enabled" aria-label="Back to default" title="Back to default" @click="reset"><i class="fas fa-undo-alt"></i><span class="sr-only">Back to default</span></button></div></div><div v-if="open" class="pb-basic-inline-editor__body"><div class="pb-form-group pb-basic-shadow-color"><label class="pb-form-label">Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[key('Color')]"></div><div class="pb-basic-shadow-fields"><div v-for="field in fields" :key="field.key" class="pb-basic-shadow-field"><div class="pb-basic-shadow-field__head"><label :for="inputId(field) + '-range'">{{ field.label }}</label><span>px</span></div><div class="pb-range-value-row"><input :id="inputId(field) + '-range'" class="pb-range" type="range" :min="field.min" :max="field.max" :step="field.step" :value="numericValue(field)" :aria-label="field.label" @input="setDimension(field, $event.target.value)"><input :id="inputId(field) + '-value'" class="pb-input pb-input-compact" type="number" :min="field.min" :max="field.max" :step="field.step" :value="numericValue(field)" :aria-label="field.label + ' value'" @input="setDimension(field, $event.target.value)"></div></div></div><div class="pb-form-group pb-basic-shadow-position"><label class="pb-form-label" :for="'button-shadow-position-' + state">Position</label><select :id="'button-shadow-position-' + state" class="pb-select" :value="position" aria-label="Position" @change="setPosition($event.target.value)"><option value="outline">Outline</option><option value="inset">Inset</option></select></div></div></div>`,
};

export default {
	name: 'BasicButtonSettings',
	components: { ButtonIconControl, ResponsiveChoice, ResponsiveBoxControl, BackgroundControl, BoxShadowControl },
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	data() {
		return {
			styleState: 'normal',
			activeTextEffect: '',
			alignmentOptions: [
				{ value: 'left', label: 'Left', icon: 'fas fa-align-left' },
				{ value: 'center', label: 'Center', icon: 'fas fa-align-center' },
				{ value: 'right', label: 'Right', icon: 'fas fa-align-right' },
				{ value: 'stretch', label: 'Stretch', icon: 'fas fa-arrows-alt-h' },
			],
		};
	},
};
</script>

<style scoped>
.pb-basic-segmented { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
.pb-basic-segmented .pb-seg-btn { min-width: 0; }
.pb-basic-box-control .pb-label-tools { display: inline-flex; align-items: center; gap: 7px; margin-left: auto; }
.pb-basic-box-control .pb-label-tools .pb-mini-unit { width: 56px; min-width: 56px; }
.pb-basic-inline-editor { margin: 0 0 15px; }
.pb-basic-inline-editor__summary { display: flex; align-items: center; justify-content: space-between; min-height: 32px; color: #344054; font-size: 12px; }
.pb-basic-shadow-actions { display: inline-flex; align-items: center; gap: 4px; }
.pb-basic-edit-button, .pb-basic-reset-button { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border: 1px solid #d3dae6; border-radius: 5px; background: #fff; color: #526178; cursor: pointer; }
.pb-basic-edit-button:hover, .pb-basic-edit-button.active, .pb-basic-reset-button:hover { border-color: #8c9aff; background: #eef1ff; color: #5b6cff; }
.pb-basic-reset-button:disabled { opacity: .45; cursor: not-allowed; }
.pb-basic-inline-editor__body { margin-top: 8px; padding: 12px; border: 1px solid #e2e7ef; border-radius: 6px; }
.pb-basic-shadow-fields { display: flex; flex-direction: column; gap: 15px; margin-bottom: 12px; }
.pb-basic-shadow-field { color: #344054; font-size: 11px; }
.pb-basic-shadow-field__head { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px; }
.pb-basic-shadow-field__head label { margin: 0; }
.pb-basic-shadow-field__head span { color: #7a8699; font-size: 10px; }
.pb-basic-shadow-field .pb-range-value-row { width: 100%; margin-left: 0; }
.pb-basic-shadow-field .pb-input-compact { width: 68px; min-width: 68px; }
.pb-basic-shadow-position { margin-bottom: 0; }
.pb-basic-button-icon-control { margin-bottom: 15px; }
.pb-basic-button-icon-label-row { gap: 8px; }
.pb-basic-button-icon-status { min-width: 0; overflow: hidden; color: #7a8699; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
.pb-basic-button-icon-source-grid, .pb-basic-button-icon-position { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 5px; }
.pb-basic-button-icon-position { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.pb-basic-button-icon-source-btn, .pb-basic-button-icon-action { min-width: 0; min-height: 30px; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; color: #526178; font-size: 10px; }
.pb-basic-button-icon-source-btn { display: inline-flex; align-items: center; justify-content: center; gap: 5px; padding: 0 5px; }
.pb-basic-button-icon-source-btn.active, .pb-basic-button-icon-source-btn:hover, .pb-basic-button-icon-action:hover { border-color: #8c9aff; background: #eef1ff; color: #5b6cff; }
.pb-basic-button-icon-selected { display: grid; grid-template-columns: 30px minmax(0, 1fr) auto; gap: 8px; align-items: center; min-height: 42px; margin-top: 8px; padding: 6px 7px; border: 1px solid #e2e7ef; border-radius: 6px; background: #fbfcff; }
.pb-basic-button-icon-preview { display: inline-flex; width: 30px; height: 30px; align-items: center; justify-content: center; color: #526178; }
.pb-basic-button-icon-preview i { font-size: 16px; }
.pb-basic-button-icon-preview img { width: 22px; height: 22px; object-fit: contain; }
.pb-basic-button-icon-copy { min-width: 0; display: grid; gap: 2px; }
.pb-basic-button-icon-copy strong, .pb-basic-button-icon-copy small { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.pb-basic-button-icon-copy strong { color: #344054; font-size: 11px; font-weight: 600; }
.pb-basic-button-icon-copy small { color: #7a8699; font-size: 10px; }
.pb-basic-button-icon-actions { display: inline-flex; gap: 4px; }
.pb-basic-button-icon-action { width: 28px; min-width: 28px; height: 28px; min-height: 28px; padding: 0; }
.pb-basic-button-icon-action.is-remove:hover { border-color: #e8a2aa; background: #fff5f6; color: #b4233b; }
.pb-basic-button-icon-option { margin-top: 15px; margin-bottom: 0; }
.pb-basic-button-icon-spacing .pb-label-row-device { align-items: center; }
.pb-basic-button-icon-spacing .pb-mini-unit { width: 52px; min-width: 52px; }
.pb-basic-button-icon-spacing-row { display: grid; grid-template-columns: minmax(0, 1fr) 68px; gap: 8px; align-items: center; }
.pb-basic-button-icon-spacing-row .pb-input-compact { width: 68px; min-width: 68px; }
</style>
