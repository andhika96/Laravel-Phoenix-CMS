<template>
	<div class="pb-dynamic-tag-control">
		<button type="button" class="pb-dynamic-tag-trigger" :class="{ active: open || modelValue }" aria-label="Dynamic tags" :title="selectedOption ? `Dynamic tag: ${selectedOption.label}` : 'Choose dynamic tag'" :aria-expanded="open ? 'true' : 'false'" @click="open = !open">
			<i class="fas fa-database"></i>
		</button>
		<div v-if="open" class="pb-dynamic-tag-popover">
			<div class="pb-dynamic-tag-head">
				<strong>Dynamic Tags</strong>
				<button v-if="modelValue" type="button" aria-label="Clear dynamic tag" @click="selectTag('')"><i class="fas fa-times"></i></button>
			</div>
			<button v-for="option in options" :key="option.value" type="button" class="pb-dynamic-tag-option" :class="{ active: modelValue === option.value }" @click="selectTag(option.value)">
				<i :class="option.icon"></i><span>{{ option.label }}</span>
			</button>
		</div>
	</div>
</template>

<script>
const DYNAMIC_TAG_OPTIONS = [
	{ value: 'page_title', label: 'Page Title', icon: 'fas fa-heading' },
	{ value: 'page_excerpt', label: 'Page Excerpt', icon: 'fas fa-align-left' },
	{ value: 'featured_image', label: 'Featured Image', icon: 'far fa-image' },
	{ value: 'page_url', label: 'Page URL', icon: 'fas fa-link' },
	{ value: 'site_title', label: 'Site Title', icon: 'fas fa-globe' },
	{ value: 'site_url', label: 'Site URL', icon: 'fas fa-external-link-alt' },
	{ value: 'user_display_name', label: 'User Display Name', icon: 'fas fa-user' },
];

export default {
	name: 'DynamicTagControl',
	props: {
		modelValue: { type: String, default: '' },
		allowedValues: { type: Array, default: () => [] },
	},
	emits: ['update:modelValue'],
	data() { return { open: false }; },
	computed: {
		options() {
			if (!this.allowedValues.length) return DYNAMIC_TAG_OPTIONS;
			return DYNAMIC_TAG_OPTIONS.filter((option) => this.allowedValues.includes(option.value));
		},
		selectedOption() { return this.options.find((option) => option.value === this.modelValue) || null; },
	},
	methods: {
		selectTag(value) {
			this.$emit('update:modelValue', this.options.some((option) => option.value === value) ? value : '');
			this.open = false;
		},
	},
};
</script>

<style scoped>
.pb-dynamic-tag-control { position: relative; display: inline-flex; }
.pb-dynamic-tag-trigger { width: 34px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #d3dae6; border-radius: 0 6px 6px 0; background: #fff; color: #667085; cursor: pointer; }
.pb-dynamic-tag-trigger:hover, .pb-dynamic-tag-trigger.active { background: #eef1ff; color: #5b6cff; }
.pb-dynamic-tag-popover { position: absolute; z-index: 35; top: calc(100% + 6px); right: 0; width: 218px; padding: 6px; border: 1px solid #d3dae6; border-radius: 7px; background: #fff; box-shadow: 0 10px 26px rgba(16, 24, 40, .18); }
.pb-dynamic-tag-head { min-height: 34px; display: flex; align-items: center; justify-content: space-between; padding: 2px 7px 6px; color: #344054; font-size: 11px; }
.pb-dynamic-tag-head button { width: 26px; height: 26px; border: 0; border-radius: 4px; background: transparent; color: #667085; cursor: pointer; }
.pb-dynamic-tag-option { width: 100%; display: flex; align-items: center; gap: 9px; min-height: 34px; padding: 7px 9px; border: 0; border-radius: 5px; background: transparent; color: #475467; font-size: 11px; text-align: left; cursor: pointer; }
.pb-dynamic-tag-option i { width: 15px; text-align: center; }
.pb-dynamic-tag-option:hover, .pb-dynamic-tag-option.active { background: #eef1ff; color: #5367ff; }
</style>
