<template>
	<div class="pb-link-control">
		<div class="pb-link-control-row">
			<input class="pb-input" type="url" :value="url" placeholder="Paste URL or type" aria-label="URL" @input="$emit('update:url', $event.target.value)">
			<button type="button" class="pb-link-options-trigger" :class="{ active: optionsOpen }" aria-label="Link options" :aria-expanded="optionsOpen ? 'true' : 'false'" @click="optionsOpen = !optionsOpen">
				<i class="fas fa-cog"></i>
			</button>
		</div>

		<div v-if="optionsOpen" class="pb-link-options-popover">
			<label class="pb-link-toggle-row">
				<span>Open in new window</span>
				<input type="checkbox" :checked="target === '_blank'" @change="$emit('update:target', $event.target.checked ? '_blank' : '')">
			</label>
			<label class="pb-link-toggle-row">
				<span>Add nofollow</span>
				<input type="checkbox" :checked="nofollow" @change="$emit('update:nofollow', $event.target.checked)">
			</label>
			<label class="pb-link-attributes-field">
				<span>Custom Attributes</span>
				<textarea class="pb-input" :value="customAttributeText" rows="3" placeholder="key|value, one per line" @input="updateAttributes($event.target.value)"></textarea>
			</label>
			<p class="pb-link-control-hint">Use one <code>key|value</code> pair per line.</p>
		</div>
	</div>
</template>

<script>
export default {
	name: 'LinkControl',
	props: {
		url: { type: String, default: '' },
		target: { type: String, default: '' },
		nofollow: { type: Boolean, default: false },
		customAttributes: { type: Array, default: () => [] },
	},
	emits: ['update:url', 'update:target', 'update:nofollow', 'update:customAttributes'],
	data() { return { optionsOpen: false }; },
	computed: {
		customAttributeText() {
			return this.customAttributes
				.filter((item) => item && item.key)
				.map((item) => `${item.key}|${item.value ?? ''}`)
				.join('\n');
		},
		relTokens() {
			const rel = this.target === '_blank'
				? { rel: ['noopener', 'noreferrer', ...(this.nofollow ? ['nofollow'] : [])] }
				: { rel: this.nofollow ? ['nofollow'] : [] };
			return rel.rel;
		},
	},
	methods: {
		updateAttributes(raw) {
			const attributes = String(raw || '').split(/\r?\n/).map((line) => {
				const separator = line.indexOf('|');
				if (separator < 1) return null;
				return { key: line.slice(0, separator).trim(), value: line.slice(separator + 1).trim() };
			}).filter((item) => item && item.key);
			this.$emit('update:customAttributes', attributes);
		},
	},
};
</script>

<style scoped>
.pb-link-control { position: relative; }
.pb-link-control-row { display: grid; grid-template-columns: minmax(0, 1fr) 38px; }
.pb-link-control-row .pb-input { min-width: 0; border-radius: 6px 0 0 6px; }
.pb-link-options-trigger { min-height: 38px; border: 1px solid #d3dae6; border-left: 0; border-radius: 0 6px 6px 0; background: #fff; color: #667085; cursor: pointer; }
.pb-link-options-trigger:hover, .pb-link-options-trigger.active { background: #eef1ff; color: #5b6cff; }
.pb-link-options-popover { position: relative; z-index: 18; display: grid; gap: 12px; margin-top: 8px; padding: 14px; border: 1px solid #e0e5ee; border-radius: 7px; background: #fff; box-shadow: 0 10px 26px rgba(16, 24, 40, .15); }
.pb-link-toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #344054; font-size: 11px; }
.pb-link-attributes-field { display: grid; gap: 7px; color: #344054; font-size: 11px; }
.pb-link-attributes-field textarea { resize: vertical; }
.pb-link-control-hint { margin: -4px 0 0; color: #7a8699; font-size: 10px; line-height: 1.4; }
</style>
