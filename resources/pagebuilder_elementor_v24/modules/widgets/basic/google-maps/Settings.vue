<template>
	<div class="pb-widget-settings pb-widget-settings--basic pb-widget-settings--google-maps">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'style'}" @click="editor.settingsTab = 'style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Map</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<label class="pb-form-label">Location</label>
						<input class="pb-input" type="text" v-model.trim="node.settings.location" placeholder="New York, NY">
						<div class="pb-form-note">Enter an address, place name, or coordinates.</div>
					</div>
					<div class="pb-form-group">
						<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Zoom</label></div>
						<div class="pb-range-value-row"><input class="pb-range" type="range" min="1" max="20" step="1" :value="activeZoom" @input="setZoom($event.target.value)"><input class="pb-input pb-input-compact" type="number" min="1" max="20" step="1" :value="activeZoom" @input="setZoom($event.target.value)"></div>
					</div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab === 'style'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Map</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group">
						<div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Height</label><ResponsiveMenu :editor="editor" id="google-maps-height" /></div>
						<div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="1200" step="1" :value="heightNumber" @input="setHeight($event.target.value)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="1200" step="1" :value="heightNumber" @input="setHeight($event.target.value)"><select class="pb-mini-unit" :value="heightUnit" @change="setHeightUnit($event.target.value)"><option v-for="unit in heightUnits" :key="unit" :value="unit">{{ unit }}</option></select></div></div>
					</div>

					<div class="pb-state-tabs pb-state-tabs--two"><button type="button" :class="{active: filterState === 'normal'}" @click="filterState = 'normal'">Normal</button><button type="button" :class="{active: filterState === 'hover'}" @click="filterState = 'hover'">Hover</button></div>
					<div class="pb-form-group"><component :is="editor.cssFilterControl" :model-value="activeFilter" @update:modelValue="updateFilter" /></div>
					<div class="pb-form-group"><label class="pb-form-label">Transition Duration</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="10" step="0.1" v-model.number="node.settings.transitionDuration"><input class="pb-input pb-input-compact" type="number" min="0" max="10" step="0.1" v-model.number="node.settings.transitionDuration"></div></div>
				</div>
			</details>
		</div>

		<div v-if="editor.settingsTab === 'advanced'" class="pb-tab-content">
			<component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings, $event)" @clear-media="editor.clearMedia(node.settings, $event)" @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')" />
		</div>
	</div>
</template>

<script>
const ResponsiveMenu = {
	props: ['editor', 'id'],
	template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" @click.stop="editor.openControlResponsiveMenu(id)" :title="'Responsive: ' + editor.responsiveDeviceLabel()"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id + '-' + device.value" type="button" class="pb-control-device-item" :class="{active: editor.responsiveDevice === device.value}" @click.stop="editor.applyResponsiveDevice(id, device.value)"><i :class="device.icon"></i><span>{{ editor.deviceOptionLabel(device) }}</span></button></div></div>`,
};

export default {
	name: 'GoogleMapsWidgetSettings',
	components: { ResponsiveMenu },
	props: {
		node: { type: Object, required: true },
		editor: { type: Object, required: true },
	},
	data() {
		return {
			filterState: 'normal',
			heightUnits: ['px', '%', 'em', 'rem', 'vh', 'vw'],
		};
	},
	computed: {
		activeZoom() {
			const value = Number(this.node.settings?.zoom);
			return Number.isFinite(value) ? Math.min(20, Math.max(1, Math.round(value))) : 14;
		},
		activeHeight() {
			const key = typeof this.editor.activeResponsiveKey === 'function' ? this.editor.activeResponsiveKey('height') : 'height';
			const value = this.node.settings?.[key];
			if (value !== '' && value !== null && value !== undefined) return String(value);
			return String(this.node.settings?.height || '400px');
		},
		heightNumber() {
			const match = this.activeHeight.match(/^(\d+(?:\.\d+)?)(?:px|%|em|rem|vh|vw)$/i);
			return match ? Number(match[1]) : 400;
		},
		heightUnit() {
			const match = this.activeHeight.match(/^\d+(?:\.\d+)?(px|%|em|rem|vh|vw)$/i);
			return match ? match[1].toLowerCase() : 'px';
		},
		activeFilter() {
			const key = this.filterState === 'hover' ? 'mapHoverFilter' : 'mapNormalFilter';
			return this.node.settings?.[key] || { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
		},
	},
	created() {
		if (!this.node.settings || typeof this.node.settings !== 'object') this.node.settings = {};
		if (!this.node.settings.mapNormalFilter) this.node.settings.mapNormalFilter = { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
		if (!this.node.settings.mapHoverFilter) this.node.settings.mapHoverFilter = { blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 };
	},
	methods: {
		setResponsive(base, value) {
			if (typeof this.editor.setResponsiveSetting === 'function') this.editor.setResponsiveSetting(this.node.settings, base, value);
			else this.node.settings[base] = value;
		},
		setZoom(value) {
			const number = Number(value);
			this.node.settings.zoom = Number.isFinite(number) ? Math.min(20, Math.max(1, Math.round(number))) : 14;
		},
		setHeight(value) {
			const number = Number(value);
			const safe = Number.isFinite(number) ? Math.min(1200, Math.max(0, number)) : 400;
			this.setResponsive('height', `${safe}${this.heightUnit}`);
		},
		setHeightUnit(unit) {
			const safeUnit = this.heightUnits.includes(unit) ? unit : 'px';
			this.setResponsive('height', `${this.heightNumber}${safeUnit}`);
		},
		updateFilter(value) {
			this.node.settings[this.filterState === 'hover' ? 'mapHoverFilter' : 'mapNormalFilter'] = value;
		},
	},
};
</script>
