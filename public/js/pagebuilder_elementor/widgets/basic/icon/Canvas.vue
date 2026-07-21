<template>
	<div :class="wrapperClass">
		<component :is="linkTag" v-bind="linkAttrs" class="el-widget-icon-link">
			<span v-if="usesShape" class="el-widget-icon-box">
				<i :class="iconClass" aria-hidden="true"></i>
			</span>
			<i v-else :class="iconClass" aria-hidden="true"></i>
		</component>
	</div>
</template>

<script>
export default {
	name: 'BasicIcon',
	props: {
		item: {
			type: Object,
			required: true,
		},
	},
	computed: {
		settings() {
			return this.item.settings || {};
		},
		iconClass() {
			const raw = String(this.settings.iconClass || '').trim();
			return raw || 'far fa-star';
		},
		view() {
			const value = String(this.settings.view || 'default').trim().toLowerCase();
			return ['default', 'stacked', 'framed'].includes(value) ? value : 'default';
		},
		shape() {
			const value = String(this.settings.shape || 'circle').trim().toLowerCase();
			return ['circle', 'rounded', 'square'].includes(value) ? value : 'circle';
		},
		usesShape() {
			return this.view === 'stacked' || this.view === 'framed';
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
		wrapperClass() {
			return [
				'el-widget-icon',
				'is-view-' + this.view,
				this.usesShape ? 'is-shape-' + this.shape : '',
				this.customClass,
			].filter(Boolean);
		},
		linkTag() {
			return this.linkHref ? 'a' : 'span';
		},
		linkHref() {
			return String(this.settings.link || '').trim();
		},
		linkRel() {
			const rel = [];
			if (this.settings.openInNewWindow) {
				rel.push('noopener', 'noreferrer');
			}
			if (this.settings.nofollow) {
				rel.push('nofollow');
			}
			return rel.join(' ');
		},
		linkAttrs() {
			const attrs = {};
			if (this.linkHref) {
				attrs.href = this.linkHref;
			}
			if (this.settings.openInNewWindow) {
				attrs.target = '_blank';
			}
			if (this.linkRel) {
				attrs.rel = this.linkRel;
			}
			return attrs;
		},
	},
};
</script>
