<template>
	<div class="pb-text-effect-control">
		<div class="pb-text-effect-trigger-row">
			<span>Text Shadow</span>
			<button type="button" class="pb-text-effect-trigger" :class="{ active: open || modelValue !== 'none' }" title="Edit text shadow" aria-label="Edit text shadow" :aria-expanded="open ? 'true' : 'false'" @click="$emit('request-open', open ? '' : controlId)">
				<i class="fas fa-pen"></i>
			</button>
		</div>
		<div v-if="open" class="pb-text-effect-popover">
			<div class="pb-text-effect-popover-head">
				<strong>Text Shadow</strong>
				<button type="button" title="Reset text shadow" aria-label="Reset text shadow" @click="reset"><i class="fas fa-undo-alt"></i></button>
			</div>
			<label class="pb-text-effect-color-field"><span>Color</span><input class="pb-input pb-coloris-input" v-model="shadow.color" @input="emitValue"></label>
			<div v-for="control in controls" :key="control.key" class="pb-text-effect-field">
				<label>{{ control.label }}</label>
				<div class="pb-text-effect-range-row">
					<input class="pb-range" type="range" :min="control.min" :max="control.max" v-model.number="shadow[control.key]" @input="emitValue">
					<input class="pb-input pb-input-compact" type="number" :min="control.min" :max="control.max" v-model.number="shadow[control.key]" @input="emitValue">
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'TextShadowControl',
	props: {
		modelValue: { type: String, default: 'none' },
		open: { type: Boolean, default: false },
		controlId: { type: String, default: 'text-shadow' },
	},
	emits: ['update:modelValue', 'request-open'],
	data() {
		return {
			shadow: { color: 'rgba(0,0,0,.3)', blur: 0, horizontal: 0, vertical: 0 },
			controls: [
				{ key: 'blur', label: 'Blur', min: 0, max: 100 },
				{ key: 'horizontal', label: 'Horizontal', min: -100, max: 100 },
				{ key: 'vertical', label: 'Vertical', min: -100, max: 100 },
			],
		};
	},
	created() { this.readValue(this.modelValue); },
	watch: { modelValue(value) { this.readValue(value); } },
	methods: {
		readValue(value) {
			const match = String(value || '').trim().match(/^(-?\d+(?:\.\d+)?)px\s+(-?\d+(?:\.\d+)?)px\s+(\d+(?:\.\d+)?)px\s+(.+)$/);
			if (!match) return;
			this.shadow.horizontal = Number(match[1]);
			this.shadow.vertical = Number(match[2]);
			this.shadow.blur = Number(match[3]);
			this.shadow.color = match[4];
		},
		emitValue() {
			const clamp = (value, min, max) => Math.min(max, Math.max(min, Number(value) || 0));
			const horizontal = clamp(this.shadow.horizontal, -100, 100);
			const vertical = clamp(this.shadow.vertical, -100, 100);
			const blur = clamp(this.shadow.blur, 0, 100);
			const color = String(this.shadow.color || 'rgba(0,0,0,.3)').trim();
			this.$emit('update:modelValue', `${horizontal}px ${vertical}px ${blur}px ${color}`);
		},
		reset() {
			this.shadow = { color: 'rgba(0,0,0,.3)', blur: 0, horizontal: 0, vertical: 0 };
			this.$emit('update:modelValue', 'none');
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
.pb-text-effect-color-field { display: block; margin-bottom: 14px; color: #344054; font-size: 11px; }
.pb-text-effect-color-field span, .pb-text-effect-field > label { display: block; margin-bottom: 7px; }
.pb-text-effect-color-field .pb-input { height: 36px; font-size: 12px; }
.pb-text-effect-field { margin-bottom: 12px; color: #344054; font-size: 11px; }
.pb-text-effect-field:last-child { margin-bottom: 0; }
.pb-text-effect-range-row { display: grid; grid-template-columns: minmax(0, 1fr) 64px; align-items: center; gap: 10px; }
.pb-text-effect-range-row .pb-input { min-width: 0; height: 34px; text-align: center; appearance: textfield; }
.pb-text-effect-range-row input[type="number"]::-webkit-inner-spin-button, .pb-text-effect-range-row input[type="number"]::-webkit-outer-spin-button { margin: 0; -webkit-appearance: none; }
</style>