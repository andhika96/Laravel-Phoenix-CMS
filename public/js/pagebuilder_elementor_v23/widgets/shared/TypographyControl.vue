<template>
	<div class="pb-typography-control">
		<div class="pb-typography-trigger-row">
			<span>Typography</span>
			<div class="pb-typography-trigger-actions">
				<button type="button" class="pb-typography-trigger" :class="{ active: popoverOpen && familyMenuOpen }" title="Choose a font family" aria-label="Choose a font family" @click="openFontPicker">
					<i class="fas fa-globe"></i>
				</button>
				<button type="button" class="pb-typography-trigger" :class="{ active: popoverOpen }" title="Edit typography" aria-label="Edit typography" :aria-expanded="popoverOpen ? 'true' : 'false'" @click="togglePopover">
					<i class="fas fa-pen"></i>
				</button>
			</div>
		</div>

		<div v-if="popoverOpen" class="pb-typography-popover">
			<div class="pb-typography-popover-head">
				<strong>Typography</strong>
				<button type="button" title="Reset typography" aria-label="Reset typography" @click="resetTypography">
					<i class="fas fa-undo-alt"></i>
				</button>
			</div>

			<div class="pb-typography-field pb-typography-family-field">
				<label :id="familyLabelId">Family</label>
				<div class="pb-font-family-select">
					<button type="button" class="pb-font-family-button" aria-haspopup="listbox" :aria-labelledby="familyLabelId" :aria-expanded="familyMenuOpen ? 'true' : 'false'" @click="familyMenuOpen = !familyMenuOpen">
						<span>{{ selectedFamilyLabel }}</span>
						<i class="fas" :class="familyMenuOpen ? 'fa-caret-up' : 'fa-caret-down'"></i>
					</button>
					<div v-if="familyMenuOpen" class="pb-font-family-menu">
						<div class="pb-font-family-search">
							<i class="fas fa-search"></i>
							<input ref="familySearch" v-model.trim="fontSearch" type="search" placeholder="Search fonts" aria-label="Search fonts">
						</div>
						<div class="pb-font-family-options" role="listbox" :aria-labelledby="familyLabelId">
							<button type="button" role="option" :aria-selected="isSelectedFamily('inherit')" :class="{ active: isSelectedFamily('inherit') }" @click="selectFamily({ label: 'Default', value: 'inherit' })">
								Default
							</button>
							<template v-if="filteredCustomFonts.length">
								<div class="pb-font-family-group">Custom Fonts</div>
								<button v-for="font in filteredCustomFonts" :key="'custom-' + font.value" type="button" role="option" :aria-selected="isSelectedFamily(font.value)" :class="{ active: isSelectedFamily(font.value) }" :style="{ fontFamily: font.value }" @click="selectFamily(font)">
									{{ font.label }}
								</button>
							</template>
							<template v-if="filteredSystemFonts.length">
								<div class="pb-font-family-group">System</div>
								<button v-for="font in filteredSystemFonts" :key="'system-' + font.value" type="button" role="option" :aria-selected="isSelectedFamily(font.value)" :class="{ active: isSelectedFamily(font.value) }" :style="{ fontFamily: font.value }" @click="selectFamily(font)">
									{{ font.label }}
								</button>
							</template>
							<div v-if="!filteredCustomFonts.length && !filteredSystemFonts.length" class="pb-font-family-empty">No matching fonts</div>
						</div>
					</div>
				</div>
			</div>

			<DimensionField label="Size" :control-key="prefix + '-typography-font-size'" :settings="settings" :setting-key="responsiveKey('FontSize')" :fallback="resetDefaults.FontSize || '16px'" :units="['px', 'em', 'rem', '%']" :responsive-device="responsiveDevice" @responsive-device="selectResponsiveDevice" @value-change="markFontSizeCustom" />

			<label class="pb-typography-select-field">
				<span>Weight</span>
				<select class="pb-select" v-model="settings[settingKey('FontWeight')]">
					<option value="inherit">Default</option>
					<option v-for="weight in fontWeights" :key="weight" :value="String(weight)">{{ weight }}</option>
				</select>
			</label>

			<label class="pb-typography-select-field">
				<span>Transform</span>
				<select class="pb-select" v-model="settings[settingKey('TextTransform')]">
					<option value="none">Default</option>
					<option value="uppercase">Uppercase</option>
					<option value="lowercase">Lowercase</option>
					<option value="capitalize">Capitalize</option>
				</select>
			</label>

			<label class="pb-typography-select-field">
				<span>Font Style</span>
				<select class="pb-select" v-model="settings[settingKey('FontStyle')]">
					<option value="normal">Default</option>
					<option value="italic">Italic</option>
					<option value="oblique">Oblique</option>
				</select>
			</label>

			<label class="pb-typography-select-field">
				<span>Decoration</span>
				<select class="pb-select" v-model="settings[settingKey('TextDecoration')]">
					<option value="none">Default</option>
					<option value="underline">Underline</option>
					<option value="overline">Overline</option>
					<option value="line-through">Line Through</option>
				</select>
			</label>

			<DimensionField label="Line Height" :control-key="prefix + '-typography-line-height'" :settings="settings" :setting-key="responsiveKey('LineHeight')" :fallback="resetDefaults.LineHeight || '1.4em'" :units="['px', 'em', 'rem']" :responsive-device="responsiveDevice" @responsive-device="selectResponsiveDevice" />
			<DimensionField label="Letter Spacing" :control-key="prefix + '-typography-letter-spacing'" :settings="settings" :setting-key="responsiveKey('LetterSpacing')" :fallback="resetDefaults.LetterSpacing || '0px'" :units="['px', 'em', 'rem']" allow-negative :responsive-device="responsiveDevice" @responsive-device="selectResponsiveDevice" />
			<DimensionField label="Word Spacing" :control-key="prefix + '-typography-word-spacing'" :settings="settings" :setting-key="responsiveKey('WordSpacing')" :fallback="resetDefaults.WordSpacing || '0px'" :units="['px', 'em', 'rem']" allow-negative :responsive-device="responsiveDevice" @responsive-device="selectResponsiveDevice" />
		</div>
	</div>
</template>

<script>
const RESPONSIVE_DEVICES = [
	{ value: 'desktop', label: 'Desktop', icon: 'fas fa-desktop' },
	{ value: 'tablet', label: 'Tablet Portrait', icon: 'fas fa-tablet-alt' },
	{ value: 'mobile', label: 'Mobile Portrait', icon: 'fas fa-mobile-alt' },
];

const SYSTEM_FONTS = [
	{ label: 'Arial', value: 'Arial, sans-serif' },
	{ label: 'Tahoma', value: 'Tahoma, sans-serif' },
	{ label: 'Verdana', value: 'Verdana, sans-serif' },
	{ label: 'Georgia', value: 'Georgia, serif' },
	{ label: 'Times New Roman', value: 'Times New Roman, serif' },
	{ label: 'Courier New', value: 'Courier New, monospace' },
];

const ResponsivePicker = {
	props: {
		modelValue: { type: String, default: 'desktop' },
		controlKey: { type: String, required: true },
	},
	emits: ['select'],
	data() { return { open: false, devices: RESPONSIVE_DEVICES }; },
	computed: {
		activeDevice() { return this.devices.find((device) => device.value === this.modelValue) || this.devices[0]; },
	},
	methods: {
		select(device) {
			this.open = false;
			this.$emit('select', device.value);
		},
	},
	template: `<div class="pb-control-device-wrap" :data-control-key="controlKey"><button type="button" class="pb-control-device-btn" :title="'Edit ' + activeDevice.label" :aria-label="'Edit ' + activeDevice.label" :aria-expanded="open ? 'true' : 'false'" @click.stop="open=!open"><i :class="activeDevice.icon"></i></button><div v-if="open" class="pb-control-device-menu"><button v-for="device in devices" :key="device.value" type="button" :class="{ active: device.value===modelValue }" :title="device.label" :aria-label="device.label" @click.stop="select(device)"><i :class="device.icon"></i><span>{{ device.label }}</span></button></div></div>`,
};

const DIMENSION_PATTERN = /^(-?\d+(?:\.\d+)?)(px|%|em|rem)?$/i;

function parseDimension(value, fallback, units) {
	const fallbackMatch = String(fallback || '0px').trim().match(DIMENSION_PATTERN);
	const rawMatch = String(value ?? '').trim().match(DIMENSION_PATTERN);
	const fallbackUnit = fallbackMatch?.[2] || units[0] || 'px';
	const requestedUnit = rawMatch?.[2] || fallbackUnit;
	return {
		value: Number(rawMatch?.[1] ?? fallbackMatch?.[1] ?? 0),
		unit: units.includes(requestedUnit) ? requestedUnit : fallbackUnit,
	};
}

function dimensionLimit(unit) {
	if (unit === '%') return 100;
	if (unit === 'em' || unit === 'rem') return 30;
	return 400;
}

function dimensionStep(unit) {
	return unit === 'em' || unit === 'rem' ? 0.01 : 1;
}

const DimensionField = {
	components: { ResponsivePicker },
	props: {
		settings: { type: Object, required: true },
		settingKey: { type: String, required: true },
		label: { type: String, required: true },
		controlKey: { type: String, required: true },
		fallback: { type: String, default: '0px' },
		units: { type: Array, default: () => ['px', 'em', 'rem'] },
		allowNegative: { type: Boolean, default: false },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	emits: ['responsive-device', 'value-change'],
	computed: {
		desktopSettingKey() { return this.settingKey.replace(/(?:Tablet|Mobile)$/, ''); },
		resolvedValue() {
			const ownValue = String(this.settings[this.settingKey] ?? '').trim();
			if (ownValue) return ownValue;
			return String(this.settings[this.desktopSettingKey] ?? '').trim() || this.fallback;
		},
		parsed() { return parseDimension(this.resolvedValue, this.fallback, this.units); },
		minValue() { return this.allowNegative ? -dimensionLimit(this.parsed.unit) : 0; },
		maxValue() { return dimensionLimit(this.parsed.unit); },
		stepValue() { return dimensionStep(this.parsed.unit); },
	},
	methods: {
		setValue(raw) {
			const number = Number(raw);
			if (!Number.isFinite(number)) return;
			const safeValue = Math.min(this.maxValue, Math.max(this.minValue, number));
			this.settings[this.settingKey] = String(safeValue) + this.parsed.unit;
			this.$emit('value-change');
		},
		setUnit(unit) {
			const safeUnit = this.units.includes(unit) ? unit : this.units[0];
			this.settings[this.settingKey] = String(this.parsed.value) + safeUnit;
			this.$emit('value-change');
		},
	},
	template: `<div class="pb-typography-dimension"><div class="pb-typography-dimension-head"><span>{{ label }}</span><div class="pb-typography-dimension-tools"><ResponsivePicker :model-value="responsiveDevice" :control-key="controlKey" @select="$emit('responsive-device',$event)" /><select class="pb-mini-unit" :value="parsed.unit" :aria-label="label + ' unit'" @change="setUnit($event.target.value)"><option v-for="unit in units" :key="unit" :value="unit">{{ unit }}</option></select></div></div><div class="pb-typography-range-row"><input class="pb-range" type="range" :min="minValue" :max="maxValue" :step="stepValue" :value="parsed.value" :aria-label="label" @input="setValue($event.target.value)"><input class="pb-input pb-input-compact" type="number" :min="minValue" :max="maxValue" :step="stepValue" :value="parsed.value" :aria-label="label + ' value'" @input="setValue($event.target.value)"></div></div>`,
};

export default {
	name: 'TypographyControl',
	components: { DimensionField },
	props: {
		settings: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
		fontFamilies: { type: [Array, Object], default: () => [] },
		prefix: { type: String, default: 'header' },
		resetDefaults: { type: Object, default: () => ({}) },
		fontSizeModeKey: { type: String, default: '' },
	},
	emits: ['responsive-device'],
	data() {
		return {
			popoverOpen: false,
			familyMenuOpen: false,
			fontSearch: '',
			fontWeights: [100, 200, 300, 400, 500, 600, 700, 800, 900],
		};
	},
	computed: {
		customFontOptions() {
			const entries = Array.isArray(this.fontFamilies)
				? this.fontFamilies
				: Object.entries(this.fontFamilies || {}).map(([value, label]) => ({ value, label }));
			const seen = new Set();
			return entries.filter((font) => typeof font === 'string' || !font?.group || font.group === 'custom').map((font) => {
				if (typeof font === 'string') return { label: font, value: font, group: 'custom' };
				const label = String(font?.label || font?.name || font?.family || font?.value || '').trim();
				const value = String(font?.value || font?.family || font?.name || label).trim();
				return { label, value, group: 'custom' };
			}).filter((font) => font.label && font.value && !seen.has(font.value.toLowerCase()) && seen.add(font.value.toLowerCase()));
		},
		filteredCustomFonts() { return this.filterFonts(this.customFontOptions); },
		filteredSystemFonts() { return this.filterFonts(SYSTEM_FONTS); },
		familyLabelId() { return `pb-typography-${this.prefix}-family-label`; },
		selectedFamilyLabel() {
			const current = String(this.settings[this.settingKey('FontFamily')] || 'inherit');
			if (current === 'inherit') return 'Default';
			return [...this.customFontOptions, ...SYSTEM_FONTS].find((font) => font.value === current)?.label || current.replace(/^['\"]|['\"].*$/g, '');
		},
	},
	methods: {
		settingKey(base) {
			return this.prefix + base;
		},
		responsiveKey(base) {
			return this.settingKey(base) + (this.responsiveDevice === 'tablet' ? 'Tablet' : (this.responsiveDevice === 'mobile' ? 'Mobile' : ''));
		},
		filterFonts(fonts) {
			const query = this.fontSearch.toLocaleLowerCase();
			return query ? fonts.filter((font) => `${font.label} ${font.value}`.toLocaleLowerCase().includes(query)) : fonts;
		},
		isSelectedFamily(value) { return String(this.settings[this.settingKey('FontFamily')] || 'inherit') === value; },
		selectFamily(font) {
			this.settings[this.settingKey('FontFamily')] = font.value;
			this.familyMenuOpen = false;
			this.fontSearch = '';
		},
		selectResponsiveDevice(device) { this.$emit('responsive-device', device); },
		togglePopover() {
			this.popoverOpen = !this.popoverOpen;
			if (!this.popoverOpen) {
				this.familyMenuOpen = false;
				this.fontSearch = '';
			}
		},
		openFontPicker() {
			this.popoverOpen = true;
			this.familyMenuOpen = true;
			this.$nextTick(() => this.$refs.familySearch?.focus());
		},
		markFontSizeCustom() {
			if (this.fontSizeModeKey) this.settings[this.fontSizeModeKey] = 'custom';
		},
		resetTypography() {
			const defaults = {
				FontFamily: 'inherit', FontSize: '16px', FontWeight: '600',
				TextTransform: 'none', FontStyle: 'normal', TextDecoration: 'none',
				LineHeight: '1.4em', LetterSpacing: '0px', WordSpacing: '0px',
				...this.resetDefaults,
			};
			const values = {};
			Object.entries(defaults).forEach(([key, value]) => { values[this.settingKey(key)] = value; });
			['FontSize', 'LineHeight', 'LetterSpacing', 'WordSpacing'].forEach((key) => {
				values[this.settingKey(key) + 'Tablet'] = '';
				values[this.settingKey(key) + 'Mobile'] = '';
			});
			Object.assign(this.settings, values);
			if (this.fontSizeModeKey) this.settings[this.fontSizeModeKey] = 'auto';
		},
	},
};
</script>

<style scoped>
.pb-typography-control { position: relative; }
.pb-typography-trigger-row { min-height: 38px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #344054; font-size: 12px; }
.pb-typography-trigger-actions { display: inline-flex; }
.pb-typography-trigger { width: 38px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d3dae6; border-right-width: 0; background: #fff; color: #526178; cursor: pointer; }
.pb-typography-trigger:first-child { border-radius: 6px 0 0 6px; }
.pb-typography-trigger:last-child { border-right-width: 1px; border-radius: 0 6px 6px 0; }
.pb-typography-trigger:hover, .pb-typography-trigger.active { background: #eef1ff; color: #5b6cff; }
.pb-typography-popover { position: relative; z-index: 12; margin-top: 8px; padding: 14px; border: 1px solid #e0e5ee; border-radius: 7px; background: #fff; box-shadow: 0 10px 26px rgba(16, 24, 40, .15); }
.pb-typography-popover-head { display: flex; align-items: center; justify-content: space-between; min-height: 30px; margin-bottom: 12px; color: #1f2937; font-size: 12px; }
.pb-typography-popover-head button { width: 28px; height: 28px; border: 0; border-radius: 5px; background: transparent; color: #526178; cursor: pointer; }
.pb-typography-popover-head button:hover { background: #f2f4f7; color: #5b6cff; }
.pb-typography-field { margin-bottom: 14px; }
.pb-typography-family-field > label { display: block; margin-bottom: 7px; color: #344054; font-size: 12px; }
.pb-font-family-select { position: relative; }
.pb-font-family-button { width: 100%; height: 34px; display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 7px 10px; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; color: #344054; font-size: 12px; text-align: left; cursor: pointer; }
.pb-font-family-menu { position: absolute; z-index: 30; top: calc(100% + 4px); right: 0; left: 0; overflow: hidden; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; box-shadow: 0 9px 24px rgba(16, 24, 40, .18); }
.pb-font-family-search { display: grid; grid-template-columns: 20px minmax(0, 1fr); align-items: center; gap: 4px; margin: 7px; padding: 0 7px; border: 1px solid #b9c2d0; border-radius: 5px; color: #7a8699; }
.pb-font-family-search input { min-width: 0; height: 32px; border: 0; outline: 0; background: transparent; color: #344054; font: inherit; }
.pb-font-family-options { max-height: 240px; overflow-y: auto; padding: 0 7px 7px; }
.pb-font-family-options button { width: 100%; display: block; padding: 7px 8px; border: 0; border-radius: 4px; background: transparent; color: #344054; font-size: 12px; text-align: left; cursor: pointer; }
.pb-font-family-options button:hover, .pb-font-family-options button.active { background: #eef1ff; color: #5367ff; }
.pb-font-family-group { padding: 10px 8px 4px; color: #667085; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
.pb-font-family-empty { padding: 14px 8px; color: #7a8699; font-size: 12px; text-align: center; }
.pb-typography-select-field { display: grid; grid-template-columns: minmax(0, 1fr) 136px; align-items: center; gap: 10px; margin-bottom: 10px; color: #344054; font-size: 12px; line-height: 1.35; }
.pb-typography-select-field .pb-select { min-width: 0; height: 34px; padding: 6px 28px 6px 9px; border-radius: 5px; font-size: 12px; line-height: 1.2; }
.pb-typography-control :deep(.pb-typography-dimension) { margin-bottom: 12px; }
.pb-typography-control :deep(.pb-typography-dimension-head) { min-height: 28px; display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 6px; color: #344054; font-size: 12px; line-height: 1.35; }
.pb-typography-control :deep(.pb-typography-dimension-tools) { display: inline-flex; align-items: center; flex: 0 0 auto; gap: 4px; }
.pb-typography-control :deep(.pb-typography-dimension-tools .pb-mini-unit) { width: 56px; min-width: 56px; height: 30px; padding: 4px 22px 4px 7px; border-radius: 5px; font-size: 12px; }
.pb-typography-control :deep(.pb-typography-range-row) { display: grid; grid-template-columns: minmax(0, 1fr) 68px; align-items: center; gap: 9px; }
.pb-typography-control :deep(.pb-typography-range-row .pb-range) { min-width: 0; width: 100%; margin: 0; }
.pb-typography-control :deep(.pb-typography-range-row .pb-input) { min-width: 0; width: 68px; height: 34px; padding: 5px 8px; border-radius: 5px; font-size: 12px; line-height: 1.2; text-align: center; appearance: textfield; }
.pb-typography-control :deep(.pb-typography-range-row input[type="number"]::-webkit-inner-spin-button), .pb-typography-control :deep(.pb-typography-range-row input[type="number"]::-webkit-outer-spin-button) { margin: 0; -webkit-appearance: none; }
.pb-typography-control :deep(.pb-control-device-wrap) { position: relative; }
.pb-typography-control :deep(.pb-control-device-btn) { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d3dae6; border-radius: 5px; background: #fff; color: #667085; font-size: 10px; cursor: pointer; }
.pb-typography-control :deep(.pb-control-device-btn:hover) { border-color: #8c9aff; background: #eef1ff; color: #5b6cff; }
.pb-typography-control :deep(.pb-control-device-menu) { position: absolute; z-index: 40; top: calc(100% + 3px); right: 0; min-width: 150px; overflow: hidden; padding: 4px; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; box-shadow: 0 8px 20px rgba(16, 24, 40, .18); }
.pb-typography-control :deep(.pb-control-device-menu button) { width: 100%; display: flex; align-items: center; gap: 8px; padding: 7px 8px; border: 0; border-radius: 4px; background: transparent; color: #526178; font-size: 12px; text-align: left; cursor: pointer; }
.pb-typography-control :deep(.pb-control-device-menu button:hover), .pb-typography-control :deep(.pb-control-device-menu button.active) { background: #eef1ff; color: #5b6cff; }
</style>
