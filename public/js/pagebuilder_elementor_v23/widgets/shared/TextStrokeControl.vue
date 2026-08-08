<template>
	<div class="pb-text-effect-control">
		<div class="pb-text-effect-trigger-row">
			<span>Text Stroke</span>
			<button type="button" class="pb-text-effect-trigger" :class="{ active: open || numericValue > 0 }" title="Edit text stroke" aria-label="Edit text stroke" :aria-expanded="open ? 'true' : 'false'" @click="$emit('request-open', open ? '' : controlId)">
				<i class="fas fa-pen"></i>
			</button>
		</div>
		<div v-if="open" class="pb-text-effect-popover">
			<div class="pb-text-effect-popover-head">
				<strong>Text Stroke</strong>
				<button type="button" title="Reset text stroke" aria-label="Reset text stroke" @click="reset"><i class="fas fa-undo-alt"></i></button>
			</div>
			<div class="pb-text-effect-field">
				<div class="pb-text-effect-field-head"><label>Width</label><select class="pb-mini-unit" v-model="unit"><option v-for="option in ['px', 'em', 'rem']" :key="option" :value="option">{{ option }}</option></select></div>
				<div class="pb-text-effect-range-row">
					<input class="pb-range" type="range" min="0" :max="unit === 'px' ? 10 : 1" :step="unit === 'px' ? .1 : .01" :value="numericValue" @input="setWidth($event.target.value)">
					<input class="pb-input pb-input-compact" type="number" min="0" :max="unit === 'px' ? 10 : 1" :step="unit === 'px' ? .1 : .01" :value="numericValue" @input="setWidth($event.target.value)">
				</div>
			</div>
			<label class="pb-text-effect-color-field"><span>Stroke Color</span><input class="pb-input pb-coloris-input" :value="settings[colorKey]" @input="settings[colorKey] = $event.target.value"></label>
		</div>
	</div>
</template>

<script>
export default {
	name: 'TextStrokeControl',
	props: {
		settings: { type: Object, required: true },
		widthKey: { type: String, default: 'titleTextStrokeWidth' },
		colorKey: { type: String, default: 'titleTextStrokeColor' },
		responsiveDevice: { type: String, default: 'desktop' },
		open: { type: Boolean, default: false },
		controlId: { type: String, default: 'text-stroke' },
	},
	emits: ['request-open'],
	data() { return { selectedUnit: '' }; },
	computed: {
		activeWidthKey() {
			return this.responsiveDevice === 'mobile'
				? this.widthKey + 'Mobile'
				: (this.responsiveDevice === 'tablet' ? this.widthKey + 'Tablet' : this.widthKey);
		},
		currentToken() {
			const responsive = this.settings[this.activeWidthKey];
			return String(responsive === '' || responsive == null ? (this.settings[this.widthKey] || '0px') : responsive);
		},
		numericValue() {
			const match = this.currentToken.match(/^-?\d+(?:\.\d+)?/);
			return match ? Math.max(0, Number(match[0])) : 0;
		},
		unit: {
			get() {
				if (this.selectedUnit) return this.selectedUnit;
				const match = this.currentToken.match(/(px|em|rem)$/i);
				return match ? match[1].toLowerCase() : 'px';
			},
			set(value) {
				this.selectedUnit = ['px', 'em', 'rem'].includes(value) ? value : 'px';
				this.setWidth(this.numericValue);
			},
		},
	},
	methods: {
		setWidth(value) {
			const number = Number(value);
			this.settings[this.activeWidthKey] = `${Number.isFinite(number) ? Math.max(0, number) : 0}${this.unit}`;
		},
		reset() {
			this.settings[this.activeWidthKey] = this.responsiveDevice === 'desktop' ? '0px' : '';
			this.settings[this.colorKey] = '#000000';
			this.selectedUnit = '';
		},
	},
};
</script>

<style scoped>
.pb-text-effect-control { position: relative; margin-bottom: 4px; }
.pb-text-effect-trigger-row { min-height: 38px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #344054; font-size: 12px; }
.pb-text-effect-trigger { width: 38px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; color: #526178; cursor: pointer; }
.pb-text-effect-trigger:hover, .pb-text-effect-trigger.active { border-color: #8c9aff; background: #eef1ff; color: #5b6cff; }
.pb-text-effect-popover { position: absolute; z-index: 24; top: calc(100% + 6px); right: 0; left: 0; padding: 14px; border: 1px solid #e0e5ee; border-radius: 7px; background: #fff; box-shadow: 0 10px 26px rgba(16, 24, 40, .15); }
.pb-text-effect-popover-head { display: flex; align-items: center; justify-content: space-between; min-height: 30px; margin-bottom: 12px; color: #1f2937; font-size: 12px; }
.pb-text-effect-popover-head button { width: 28px; height: 28px; border: 0; border-radius: 5px; background: transparent; color: #526178; cursor: pointer; }
.pb-text-effect-popover-head button:hover { background: #f2f4f7; color: #5b6cff; }
.pb-text-effect-field { margin-bottom: 14px; }
.pb-text-effect-field-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 7px; color: #344054; font-size: 11px; }
.pb-text-effect-range-row { display: grid; grid-template-columns: minmax(0, 1fr) 64px; align-items: center; gap: 10px; }
.pb-text-effect-range-row .pb-input { min-width: 0; height: 34px; text-align: center; appearance: textfield; }
.pb-text-effect-range-row input[type="number"]::-webkit-inner-spin-button, .pb-text-effect-range-row input[type="number"]::-webkit-outer-spin-button { margin: 0; -webkit-appearance: none; }
.pb-text-effect-color-field { display: block; color: #344054; font-size: 11px; }
.pb-text-effect-color-field span { display: block; margin-bottom: 7px; }
.pb-text-effect-color-field .pb-input { height: 36px; font-size: 12px; }
</style>