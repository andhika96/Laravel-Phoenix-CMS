<template>
	<div class="el-widget-accordion" :class="rootClass" :style="rootStyle" data-pb-interactive="true" @click.stop>
		<div v-for="item in items" :key="item.id" class="el-widget-accordion__item" :class="{ 'is-active': isExpanded(item.id) }">
			<button
				type="button"
				class="el-widget-accordion__header"
				:id="headerId(item.id)"
				:aria-expanded="isExpanded(item.id) ? 'true' : 'false'"
				:aria-controls="panelId(item.id)"
				data-pb-interactive="true"
				@click.stop="toggleItem(item)"
			>
				<span v-if="iconPosition==='start'" class="el-widget-accordion__icon" aria-hidden="true"><i :class="iconClass(item.id)"></i></span>
				<component :is="titleTag" class="el-widget-accordion__title">{{ item.title || 'Item' }}</component>
				<span v-if="iconPosition==='end'" class="el-widget-accordion__icon" aria-hidden="true"><i :class="iconClass(item.id)"></i></span>
			</button>
			<div
				ref="panels"
				class="el-widget-accordion__panel"
				:class="{ 'is-active': isExpanded(item.id) }"
				:id="panelId(item.id)"
				:data-accordion-item-id="item.id"
				:aria-labelledby="headerId(item.id)"
				:aria-hidden="isExpanded(item.id) ? 'false' : 'true'"
				:style="panelStyle(item.id)"
				@transitionend="onPanelTransitionEnd(item.id, $event)"
			>
				<div class="el-widget-accordion__panel-inner">
					<slot name="panel" :item="item" :expanded="isExpanded(item.id)"></slot>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AdvancedAccordion',
	props: {
		item: { type: Object, required: true },
		expandedItemIds: { type: Array, default: () => [] },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	emits: ['toggle-item'],
	data() {
		return {
			panelHeights: {},
			panelRaf: 0,
		};
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		items() {
			return Array.isArray(this.item.accordionItems) ? this.item.accordionItems : [];
		},
		titleTag() {
			const tag = String(this.settings.titleTag || 'div').toLowerCase();
			return ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p'].includes(tag) ? tag : 'div';
		},
		iconPosition() {
			const suffix = this.responsiveDevice === 'tablet' ? 'Tablet' : (this.responsiveDevice === 'mobile' ? 'Mobile' : '');
			const value = String(this.settings['iconPosition' + suffix] || this.settings.iconPosition || 'start').toLowerCase();
			return value === 'end' ? 'end' : 'start';
		},
		animationDuration() {
			const duration = Number(this.settings.animationDuration);
			return Number.isFinite(duration) ? Math.min(5000, Math.max(0, duration)) : 400;
		},
		rootClass() {
			return String(this.settings.cssClass || '').trim().split(/\s+/).filter(Boolean);
		},
		rootStyle() {
			return { '--accordion-animation-duration': this.animationDuration + 'ms' };
		},
	},
	watch: {
		expandedItemIds: {
			deep: true,
			handler() {
				this.queuePanelHeightSync();
			},
		},
	},
	mounted() {
		this.queuePanelHeightSync(true);
	},
	beforeUnmount() {
		if (this.panelRaf) cancelAnimationFrame(this.panelRaf);
	},
	methods: {
		isExpanded(itemId) {
			return this.expandedItemIds.map(String).includes(String(itemId));
		},
		headerId(itemId) {
			return 'pb-accordion-header-' + String(this.item.id || 'node') + '-' + String(itemId || 'item');
		},
		panelId(itemId) {
			return 'pb-accordion-panel-' + String(this.item.id || 'node') + '-' + String(itemId || 'item');
		},
		iconClass(itemId) {
			return this.isExpanded(itemId)
				? String(this.settings.collapseIconClass || 'fas fa-minus')
				: String(this.settings.expandIconClass || 'fas fa-plus');
		},
		toggleItem(item) {
			if (!item) return;
			this.$emit('toggle-item', item.id);
		},
		panelStyle(itemId) {
			const value = this.panelHeights[String(itemId)];
			return { height: value == null ? (this.isExpanded(itemId) ? 'auto' : '0px') : value };
		},
		queuePanelHeightSync(immediate = false) {
			if (this.panelRaf) cancelAnimationFrame(this.panelRaf);
			const apply = () => {
				this.panelRaf = 0;
				this.syncPanelHeights();
			};
			if (immediate) this.$nextTick(apply);
			else this.$nextTick(() => { this.panelRaf = requestAnimationFrame(apply); });
		},
		syncPanelHeights() {
			const panels = Array.isArray(this.$refs.panels) ? this.$refs.panels : [];
			const next = { ...this.panelHeights };
			panels.forEach((panel) => {
				const itemId = String(panel?.dataset?.accordionItemId || '');
				if (!itemId) return;
				if (this.isExpanded(itemId)) {
					next[itemId] = panel.scrollHeight + 'px';
				} else {
					const currentHeight = panel.getBoundingClientRect().height;
					next[itemId] = currentHeight > 0 ? currentHeight + 'px' : '0px';
					requestAnimationFrame(() => {
						this.panelHeights = { ...this.panelHeights, [itemId]: '0px' };
					});
				}
			});
			this.panelHeights = next;
		},
		onPanelTransitionEnd(itemId, event) {
			if (!event || event.propertyName !== 'height' || !this.isExpanded(itemId)) return;
			this.panelHeights = { ...this.panelHeights, [String(itemId)]: 'auto' };
		},
	},
};
</script>

<style scoped>
.el-widget-accordion__panel {
	overflow: hidden;
	transition: height var(--accordion-animation-duration, 400ms) ease;
}

@media (prefers-reduced-motion: reduce) {
	.el-widget-accordion__panel {
		transition-duration: 0ms;
	}
}
</style>
