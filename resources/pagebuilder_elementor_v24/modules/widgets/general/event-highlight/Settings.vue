<template>
	<div class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--event-highlight">
		<div class="pb-tab-nav" role="tablist" aria-label="Event Highlight settings">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='content'}" role="tab" :aria-selected="editor.settingsTab==='content'" @click="editor.settingsTab='content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='style'}" role="tab" :aria-selected="editor.settingsTab==='style'" @click="editor.settingsTab='style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active:editor.settingsTab==='advanced'}" role="tab" :aria-selected="editor.settingsTab==='advanced'" @click="editor.settingsTab='advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab==='content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Text Content</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<label class="pb-form-label" :for="'event-highlight-heading-' + node.id">Heading</label>
						<input :id="'event-highlight-heading-' + node.id" class="pb-input" v-model="node.settings.heading" placeholder="Five reasons to be on the first tee.">
					</div>
					<div class="pb-form-group">
						<label class="pb-form-label" :for="'event-highlight-subheading-' + node.id">Subheading</label>
						<input :id="'event-highlight-subheading-' + node.id" class="pb-input" v-model="node.settings.subheading" placeholder="EVENT HIGHLIGHTS">
					</div>
					<div class="pb-form-group">
						<label class="pb-form-label" :for="'event-highlight-text-order-' + node.id">Text Order</label>
						<select :id="'event-highlight-text-order-' + node.id" class="pb-select" v-model="node.settings.textOrder">
							<option value="subheading-first">Subheading above heading</option>
							<option value="heading-first">Heading above subheading</option>
						</select>
						<div class="pb-form-note">The DOM order follows this choice for consistent screen-reader reading order.</div>
					</div>
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>CTA Link</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<label class="pb-form-label" :for="'event-highlight-link-text-' + node.id">Link Text</label>
						<input :id="'event-highlight-link-text-' + node.id" class="pb-input" v-model="node.settings.linkText" placeholder="REGISTER INTEREST">
					</div>
					<div class="pb-form-group pb-toggle-label-row">
						<label class="pb-form-label mb-0" :for="'event-highlight-arrow-' + node.id">Show Arrow</label>
						<div class="pb-toggle-switch-wrap">
							<div class="pb-toggle-wrap"><input :id="'event-highlight-arrow-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.showArrow"><label :for="'event-highlight-arrow-' + node.id"></label></div>
							<span class="pb-toggle-state">{{node.settings.showArrow ? 'Show' : 'Hide'}}</span>
						</div>
					</div>
					<div class="pb-form-group">
						<label class="pb-form-label">Link</label>
						<div class="pb-event-highlight-link-control">
							<component :is="editor.linkControl" :url="node.settings.linkUrl" :target="node.settings.linkTarget" :nofollow="node.settings.linkNofollow" :custom-attributes="node.settings.linkCustomAttributes" @update:url="node.settings.linkUrl=$event" @update:target="node.settings.linkTarget=$event" @update:nofollow="node.settings.linkNofollow=$event" @update:customAttributes="node.settings.linkCustomAttributes=$event" />
						</div>
					</div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab==='style'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Layout</summary>
				<div class="pb-collapsible-body">
					<responsive-select label="Layout Direction" base="layoutDirection" control-id="event-highlight-layout-direction" fallback="row" :node="node" :editor="editor" :options="directionOptions" />
					<div class="pb-form-row pb-form-row--two pb-event-highlight-link-position-controls"><responsive-select label="Link Vertical Position" base="linkVerticalPosition" control-id="event-highlight-link-vertical" fallback="center" :node="node" :editor="editor" :options="verticalPositionOptions" /><responsive-select label="Link Horizontal Alignment" base="linkHorizontalAlign" control-id="event-highlight-link-horizontal" fallback="right" :node="node" :editor="editor" :options="horizontalAlignOptions" /></div>
					<size-control label="Heading–Subheading Gap" base="textGap" control-id="event-highlight-text-gap" fallback="12px" :node="node" :editor="editor" :min="0" :max="120" />
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Heading</summary>
				<div class="pb-collapsible-body">
					<color-field label="Color" setting-key="headingColor" control-id="event-highlight-heading-color" :node="node" />
					<div class="pb-label-row pb-label-row-device pb-event-highlight-section-heading"><span class="pb-form-label mb-0">Typography</span><button type="button" class="pb-btn icon-sm" title="Reset heading typography" aria-label="Reset heading typography" @click="resetTypography('heading')"><i class="fas fa-undo"></i></button></div>
					<component :is="editor.typographyControl" prefix="heading" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'56px',FontWeight:'400',LineHeight:'1.05em'}" @responsive-device="editor.setResponsiveDevice" />
					<border-settings prefix="heading" label="Heading Border" control-id="event-highlight-heading-border" :node="node" :editor="editor" />
					<spacing-control label="Padding" base="headingPadding" control-id="event-highlight-heading-padding" fallback="0px" :node="node" :editor="editor" />
					<spacing-control label="Margin" base="headingMargin" control-id="event-highlight-heading-margin" fallback="0px" :node="node" :editor="editor" />
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Subheading</summary>
				<div class="pb-collapsible-body">
					<color-field label="Color" setting-key="subheadingColor" control-id="event-highlight-subheading-color" :node="node" />
					<div class="pb-label-row pb-label-row-device pb-event-highlight-section-heading"><span class="pb-form-label mb-0">Typography</span><button type="button" class="pb-btn icon-sm" title="Reset subheading typography" aria-label="Reset subheading typography" @click="resetTypography('subheading')"><i class="fas fa-undo"></i></button></div>
					<component :is="editor.typographyControl" prefix="subheading" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'14px',FontWeight:'700',LineHeight:'1.2em'}" @responsive-device="editor.setResponsiveDevice" />
					<border-settings prefix="subheading" label="Subheading Border" control-id="event-highlight-subheading-border" :node="node" :editor="editor" />
					<spacing-control label="Padding" base="subheadingPadding" control-id="event-highlight-subheading-padding" fallback="0px" :node="node" :editor="editor" />
					<spacing-control label="Margin" base="subheadingMargin" control-id="event-highlight-subheading-margin" fallback="0px" :node="node" :editor="editor" />
				</div>
			</details>

			<details class="pb-collapsible" open>
				<summary>Link</summary>
				<div class="pb-collapsible-body">
					<color-field label="Color" setting-key="linkColor" control-id="event-highlight-link-color" :node="node" />
					<div class="pb-label-row pb-label-row-device pb-event-highlight-section-heading"><span class="pb-form-label mb-0">Typography</span><button type="button" class="pb-btn icon-sm" title="Reset link typography" aria-label="Reset link typography" @click="resetTypography('link')"><i class="fas fa-undo"></i></button></div>
					<component :is="editor.typographyControl" prefix="link" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" :reset-defaults="{FontSize:'14px',FontWeight:'700',LineHeight:'1.2em'}" @responsive-device="editor.setResponsiveDevice" />
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab==='advanced'" class="pb-tab-content">
			<component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" :elementor-choices="true" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings,$event)" @clear-media="editor.clearMedia(node.settings,$event)" @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')" />
		</div>
	</div>
</template>

<script>
const ResponsiveMenu = {
	props: { editor: Object, id: String },
	template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" :title="'Responsive: '+editor.responsiveDeviceLabel()" :aria-label="'Responsive: '+editor.responsiveDeviceLabel()" @click.stop="editor.openControlResponsiveMenu(id)"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id+'-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(id,device.value)"><i :class="device.icon"></i><span>{{editor.deviceOptionLabel(device)}}</span></button></div></div>`,
};

const ResponsiveSelect = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, fallback: String, node: Object, editor: Object, options: Array },
	computed: {
		value() {
			const selected = this.node.settings[this.editor.activeResponsiveKey(this.base)];
			return selected === '' || selected === undefined || selected === null ? this.fallback : selected;
		},
	},
	template: `<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0" :for="controlId">{{label}}</label><responsive-menu :editor="editor" :id="controlId" /></div><select :id="controlId" class="pb-select" :value="value" @change="editor.setResponsiveSetting(node.settings,base,$event.target.value)"><option v-for="option in options" :key="base+'-'+option.value" :value="option.value">{{option.label}}</option></select></div>`,
};

const SizeControl = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, fallback: String, node: Object, editor: Object, min: { type: Number, default: 0 }, max: { type: Number, default: 120 } },
	template: `<div class="pb-form-group"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0" :for="controlId+'-value'">{{label}}</label><responsive-menu :editor="editor" :id="controlId" /></div><div class="pb-range-value-row"><input :id="controlId+'-range'" class="pb-range" type="range" :min="min" :max="max" :step="editor.sizeControlStep(node,base,fallback)" :value="editor.sizeControlDisplayValue(node,base,fallback)" :aria-label="label" @input="editor.onSizeControlInput(node,base,$event,{fallback,min,max})"><div class="pb-value-with-unit"><input :id="controlId+'-value'" class="pb-input pb-input-compact" type="number" :min="min" :max="max" :step="editor.sizeControlStep(node,base,fallback)" :value="editor.sizeControlDisplayValue(node,base,fallback)" :aria-label="label+' value'" @input="editor.onSizeControlInput(node,base,$event,{fallback,min,max})"><select class="pb-mini-unit" :value="editor.sizeControlUnit(node,base,fallback)" :aria-label="label+' unit'" @change="editor.setSizeControlUnit(node,base,$event.target.value,{fallback})"><option v-for="unit in editor.sizeControlUnits" :key="base+'-'+unit" :value="unit">{{unit}}</option></select></div></div></div>`,
};

const ColorField = {
	props: { label: String, settingKey: String, controlId: String, node: Object },
	template: `<div class="pb-form-group"><label class="pb-form-label" :for="controlId">{{label}}</label><input :id="controlId" class="pb-input coloris pb-coloris-input" v-model="node.settings[settingKey]" :aria-label="label"></div>`,
};

const SpacingControl = {
	components: { ResponsiveMenu },
	props: { label: String, base: String, controlId: String, fallback: { type: String, default: '0px' }, node: Object, editor: Object },
	data() { return { linked: true, sides: ['Top', 'Right', 'Bottom', 'Left'], units: ['px', '%', 'em', 'rem', 'vw', 'vh'] }; },
	methods: {
		unit() { return this.editor.sizeControlUnit(this.node, this.base + 'Top', this.fallback, { allowedUnits: this.units }); },
		value(side) { return this.editor.sizeControlDisplayValue(this.node, this.base + side, this.fallback, { allowedUnits: this.units }); },
		setSide(side, event) {
			const number = Number(event.target.value);
			if (!Number.isFinite(number)) return;
			const token = `${Math.max(0, number)}${this.unit()}`;
			(this.linked ? this.sides : [side]).forEach((target) => this.editor.setResponsiveSetting(this.node.settings, this.base + target, token));
		},
		setUnit(event) {
			const next = this.units.includes(event.target.value) ? event.target.value : 'px';
			this.sides.forEach((side) => this.editor.setResponsiveSetting(this.node.settings, this.base + side, `${this.value(side)}${next}`));
		},
	},
	template: `<div class="pb-form-group pb-event-highlight-spacing-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0" :for="controlId+'-Top'">{{label}}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :id="controlId" /><select class="pb-mini-unit" :value="unit()" :aria-label="label+' unit'" @change="setUnit($event)"><option v-for="unitOption in units" :key="base+'-'+unitOption" :value="unitOption">{{unitOption}}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="side in sides" :key="base+'-'+side" class="pb-side-input" :for="controlId+'-'+side"><input class="pb-input" :id="controlId+'-'+side" type="number" min="0" :value="value(side)" :aria-label="label+' '+side" @input="setSide(side,$event)"><span>{{side}}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:linked}" :title="linked?'Unlink values':'Link values together'" :aria-label="linked?'Unlink values':'Link values together'" @click="linked=!linked"><i class="fas" :class="linked?'fa-link':'fa-unlink'"></i></button></div></div></div>`,
};

const BorderSettings = {
	components: { ResponsiveMenu, ResponsiveSelect, SizeControl, ColorField },
	props: { prefix: String, label: String, controlId: String, node: Object, editor: Object },
	computed: {
		modeOptions() { return [{ value: 'none', label: 'None' }, { value: 'box', label: 'Border Box' }, { value: 'underline', label: 'Underline' }]; },
		widthOptions() { return [{ value: 'content', label: 'Text width' }, { value: 'full', label: 'Full width' }]; },
		typeOptions() { return [{ value: 'solid', label: 'Solid' }, { value: 'dashed', label: 'Dashed' }, { value: 'dotted', label: 'Dotted' }, { value: 'double', label: 'Double' }, { value: 'groove', label: 'Groove' }]; },
	},
	template: `<div class="pb-event-highlight-border-settings"><div class="pb-subsection-title">{{label}}</div><responsive-select label="Border" :base="prefix+'BorderMode'" :control-id="controlId+'-mode'" fallback="none" :node="node" :editor="editor" :options="modeOptions" /><responsive-select label="Width" :base="prefix+'BorderWidthMode'" :control-id="controlId+'-width'" fallback="content" :node="node" :editor="editor" :options="widthOptions" /><responsive-select label="Type" :base="prefix+'BorderType'" :control-id="controlId+'-type'" fallback="solid" :node="node" :editor="editor" :options="typeOptions" /><size-control label="Thickness" :base="prefix+'BorderThickness'" :control-id="controlId+'-thickness'" fallback="1px" :node="node" :editor="editor" :min="0" :max="20" /><color-field label="Color" :setting-key="prefix+'BorderColor'" :control-id="controlId+'-color'" :node="node" /><size-control label="Radius" :base="prefix+'BorderRadius'" :control-id="controlId+'-radius'" fallback="0px" :node="node" :editor="editor" :min="0" :max="80" /></div>`,
};

export default {
	name: 'EventHighlightWidgetSettings',
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	components: { ResponsiveSelect, SizeControl, ColorField, SpacingControl, BorderSettings },
	computed: {
		directionOptions() { return [{ value: 'row', label: 'Horizontal (row)' }, { value: 'column', label: 'Vertical (column)' }]; },
		verticalPositionOptions() { return [{ value: 'top', label: 'Top' }, { value: 'center', label: 'Center' }, { value: 'bottom', label: 'Bottom' }]; },
		horizontalAlignOptions() { return [{ value: 'left', label: 'Left' }, { value: 'center', label: 'Center' }, { value: 'right', label: 'Right' }]; },
	},
	methods: {
		resetTypography(prefix) {
			const defaults = {
				heading: { FontFamily: 'Georgia, serif', FontSize: '56px', FontWeight: '400', LineHeight: '1.05em', LetterSpacing: '0px', WordSpacing: '0px', TextTransform: 'none', FontStyle: 'normal', TextDecoration: 'none', Color: '#f4efe4' },
				subheading: { FontFamily: 'inherit', FontSize: '14px', FontWeight: '700', LineHeight: '1.2em', LetterSpacing: '3px', WordSpacing: '0px', TextTransform: 'uppercase', FontStyle: 'normal', TextDecoration: 'none', Color: '#d8ad5e' },
				link: { FontFamily: 'inherit', FontSize: '14px', FontWeight: '700', LineHeight: '1.2em', LetterSpacing: '2px', WordSpacing: '0px', TextTransform: 'uppercase', FontStyle: 'normal', TextDecoration: 'none', Color: '#d8ad5e' },
			};
			const values = defaults[prefix] || defaults.heading;
			Object.keys(values).forEach((key) => { this.node.settings[prefix + key] = values[key]; });
		},
	},
};
</script>

<style scoped>
.pb-widget-settings--event-highlight{min-width:0;max-width:100%;overflow-x:hidden}.pb-widget-settings--event-highlight .pb-form-row{display:grid;gap:12px}.pb-widget-settings--event-highlight .pb-form-row--two{grid-template-columns:repeat(2,minmax(0,1fr))}.pb-widget-settings--event-highlight .pb-form-group{margin-bottom:14px;min-width:0}.pb-widget-settings--event-highlight .pb-collapsible-body{min-width:0;padding-top:13px}.pb-widget-settings--event-highlight .pb-event-highlight-link-control{min-width:0;overflow:hidden}.pb-widget-settings--event-highlight .pb-event-highlight-section-heading{margin:2px 0 8px}.pb-widget-settings--event-highlight .pb-event-highlight-section-heading .pb-btn{flex:0 0 auto}.pb-widget-settings--event-highlight .pb-event-highlight-border-settings{border-top:1px solid #e8edf5;margin-top:15px;padding-top:13px}.pb-widget-settings--event-highlight .pb-subsection-title{font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#667085;margin:0 0 10px}.pb-widget-settings--event-highlight .pb-label-tools{display:flex;align-items:center;gap:6px;min-width:0}.pb-widget-settings--event-highlight .pb-mini-unit{min-width:54px}.pb-widget-settings--event-highlight .pb-link-btn,.pb-widget-settings--event-highlight .pb-btn.icon-sm{flex:0 0 auto}.pb-widget-settings--event-highlight button:focus-visible,.pb-widget-settings--event-highlight input:focus-visible,.pb-widget-settings--event-highlight select:focus-visible,.pb-widget-settings--event-highlight textarea:focus-visible{outline:2px solid #5367ff;outline-offset:2px}.pb-widget-settings--event-highlight .pb-input,.pb-widget-settings--event-highlight .pb-select,.pb-widget-settings--event-highlight .pb-textarea{min-width:0;max-width:100%;min-height:36px;box-sizing:border-box}.pb-widget-settings--event-highlight .pb-range-value-row{min-width:0}.pb-widget-settings--event-highlight .pb-value-with-unit{min-width:0}.pb-widget-settings--event-highlight .pb-four-sides{max-width:100%;overflow:hidden}@media(max-width:420px){.pb-widget-settings--event-highlight .pb-form-row--two{grid-template-columns:1fr}.pb-widget-settings--event-highlight .pb-label-row-device{align-items:flex-start;gap:6px}.pb-widget-settings--event-highlight .pb-label-tools{flex-wrap:wrap;justify-content:flex-end}}
.pb-widget-settings--event-highlight .pb-event-highlight-link-position-controls{grid-template-columns:minmax(0,1fr)}
.pb-widget-settings--event-highlight :deep(.pb-event-highlight-border-settings){display:grid;gap:12px;margin-bottom:16px}.pb-widget-settings--event-highlight :deep(.pb-event-highlight-border-settings > .pb-form-group){margin-bottom:0}
</style>
