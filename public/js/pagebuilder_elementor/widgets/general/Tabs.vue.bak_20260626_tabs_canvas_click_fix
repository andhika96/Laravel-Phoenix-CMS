<template>
	<div :class="rootClass">
		<div class="el-widget-tabs__nav" :style="navStyle">
			<button
				v-for="tab in tabItems"
				:key="tab.id"
				type="button"
				class="el-widget-tabs__tab"
				:class="{ 'is-active': activeTab && activeTab.id===tab.id, 'has-icon': !!iconClassFor(tab, activeTab && activeTab.id===tab.id) }"
				:style="tabButtonStyle"
				@click="activateTab(tab.id)"
			>
				<i v-if="iconClassFor(tab, activeTab && activeTab.id===tab.id)" :class="iconClassFor(tab, activeTab && activeTab.id===tab.id)" aria-hidden="true"></i>
				<span>{{ tab.title || 'Tab' }}</span>
			</button>
		</div>
		<div class="el-widget-tabs__pane" :data-tab-panel="activeTab ? activeTab.id : ''">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GeneralTabs',
	props: {
		item: {
			type: Object,
			required: true,
		},
		responsiveDevice: {
			type: String,
			default: 'desktop',
		},
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		tabItems() {
			return Array.isArray(this.item.tabItems) ? this.item.tabItems : [];
		},
		activeTab() {
			if (!this.tabItems.length) return null;
			const activeId = String(this.settings.activeTabId || '').trim();
			return this.tabItems.find((tab) => String(tab.id || '') === activeId) || this.tabItems[0];
		},
		direction() {
			const raw = String(this.settings.direction || 'row').trim().toLowerCase();
			return ['row', 'row-reverse', 'column', 'column-reverse'].includes(raw) ? raw : 'row';
		},
		justify() {
			const raw = String(this.settings.justify || 'flex-start').trim().toLowerCase();
			return ['flex-start', 'center', 'flex-end', 'stretch'].includes(raw) ? raw : 'flex-start';
		},
		alignTitle() {
			const raw = String(this.settings.alignTitle || 'center').trim().toLowerCase();
			return ['left', 'center', 'right'].includes(raw) ? raw : 'center';
		},
		breakpoint() {
			const raw = String(this.settings.breakpoint || 'mobile').trim().toLowerCase();
			return ['mobile', 'tablet', 'none'].includes(raw) ? raw : 'mobile';
		},
		tabWidthCss() {
			const raw = String(this.settings.tabWidth || '').trim();
			if (!raw) return '';
			const num = Number(raw);
			if (!Number.isFinite(num) || num <= 0) return '';
			const unit = String(this.settings.tabWidthUnit || 'px').trim() === '%' ? '%' : 'px';
			return num + unit;
		},
		customClass() {
			const raw = String(this.settings.cssClass || '').trim();
			if (!raw) return '';
			return raw
				.split(/\s+/)
				.map((token) => token.replace(/^\.+/, '').trim())
				.filter(Boolean)
				.join(' ');
		},
		rootClass() {
			return [
				'el-widget-tabs',
				'is-direction-' + this.direction,
				'is-breakpoint-' + this.breakpoint,
				this.settings.horizontalScroll ? 'is-scroll-enabled' : '',
				this.justify === 'stretch' ? 'is-justify-stretch' : '',
				'align-title-' + this.alignTitle,
				this.customClass,
			].filter(Boolean);
		},
		navStyle() {
			return {
				justifyContent: this.justify === 'stretch' ? 'flex-start' : this.justify,
			};
		},
		tabButtonStyle() {
			const style = {
				textAlign: this.alignTitle,
			};
			if (this.tabWidthCss && (this.direction === 'row' || this.direction === 'row-reverse')) {
				style.flex = '0 0 ' + this.tabWidthCss;
				style.width = this.tabWidthCss;
			}
			return style;
		},
	},
	methods: {
		activateTab(tabId) {
			if (!this.item.settings || typeof this.item.settings !== 'object') return;
			this.item.settings.activeTabId = tabId;
		},
		iconClassFor(tab, isActive = false) {
			if (!tab || typeof tab !== 'object') return '';
			if (isActive && String(tab.activeIconClass || '').trim()) return String(tab.activeIconClass || '').trim();
			return String(tab.iconClass || '').trim();
		},
	},
};
</script>
