<template>
	<div class="pb-css-filter-control">
		<div class="pb-css-filter-trigger-row">
			<span>CSS Filters</span>
			<button type="button" class="pb-css-filter-trigger" :class="{ active: open }" aria-label="CSS Filters" :aria-expanded="open ? 'true' : 'false'" @click="open = !open">
				<i class="fas fa-pen"></i>
			</button>
		</div>
		<div v-if="open" class="pb-css-filter-popover">
			<div class="pb-css-filter-head"><strong>CSS Filters</strong><button type="button" aria-label="Reset CSS filters" @click="resetFilters"><i class="fas fa-undo-alt"></i></button></div>
			<label v-for="field in fields" :key="field.key" class="pb-css-filter-field">
				<span>{{ field.label }}</span>
				<div class="pb-css-filter-range-row">
					<input class="pb-range" type="range" :min="field.min" :max="field.max" :step="field.step" :value="filterValue(field.key)" @input="updateFilter(field.key, $event.target.value)">
					<input class="pb-input pb-input-compact" type="number" :min="field.min" :max="field.max" :step="field.step" :value="filterValue(field.key)" @input="updateFilter(field.key, $event.target.value)">
				</div>
			</label>
		</div>
	</div>
</template>

<script>
const FILTER_DEFAULTS = Object.freeze({ blur: 0, brightness: 100, contrast: 100, saturation: 100, hue: 0 });

export default {
	name: 'CssFilterControl',
	props: { modelValue: { type: Object, default: () => ({ ...FILTER_DEFAULTS }) } },
	emits: ['update:modelValue'],
	data() {
		return {
			open: false,
			fields: [
				{ key: 'blur', label: 'Blur', min: 0, max: 100, step: 1 },
				{ key: 'brightness', label: 'Brightness', min: 0, max: 200, step: 1 },
				{ key: 'contrast', label: 'Contrast', min: 0, max: 200, step: 1 },
				{ key: 'saturation', label: 'Saturation', min: 0, max: 200, step: 1 },
				{ key: 'hue', label: 'Hue', min: 0, max: 360, step: 1 },
			],
		};
	},
	methods: {
		filterValue(key) {
			const value = Number(this.modelValue?.[key]);
			return Number.isFinite(value) ? value : FILTER_DEFAULTS[key];
		},
		updateFilter(key, raw) {
			const field = this.fields.find((item) => item.key === key);
			if (!field) return;
			const value = Math.min(field.max, Math.max(field.min, Number(raw) || 0));
			this.$emit('update:modelValue', { ...FILTER_DEFAULTS, ...(this.modelValue || {}), [key]: value });
		},
		resetFilters() {
			this.$emit('update:modelValue', { ...FILTER_DEFAULTS });
		},
	},
};
</script>

<style scoped>
.pb-css-filter-control { position: relative; }
.pb-css-filter-trigger-row { min-height: 38px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #344054; font-size: 11px; }
.pb-css-filter-trigger { width: 38px; height: 36px; border: 1px solid #d3dae6; border-radius: 6px; background: #fff; color: #667085; cursor: pointer; }
.pb-css-filter-trigger:hover, .pb-css-filter-trigger.active { background: #eef1ff; color: #5b6cff; }
.pb-css-filter-popover { position: relative; z-index: 18; display: grid; gap: 14px; margin-top: 8px; padding: 14px; border: 1px solid #e0e5ee; border-radius: 7px; background: #fff; box-shadow: 0 10px 26px rgba(16, 24, 40, .15); }
.pb-css-filter-head { display: flex; align-items: center; justify-content: space-between; color: #344054; font-size: 11px; }
.pb-css-filter-head button { width: 28px; height: 28px; border: 0; border-radius: 5px; background: transparent; color: #667085; cursor: pointer; }
.pb-css-filter-field { display: grid; gap: 8px; color: #344054; font-size: 11px; }
.pb-css-filter-range-row { display: grid; grid-template-columns: minmax(0, 1fr) 72px; align-items: center; gap: 9px; }
.pb-css-filter-range-row .pb-input { min-width: 0; text-align: center; }
</style>
