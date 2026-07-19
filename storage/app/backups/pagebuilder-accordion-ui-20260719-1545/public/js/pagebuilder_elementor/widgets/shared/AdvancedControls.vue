<template>
	<div class="pb-widget-advanced-controls">
		<div class="pb-advanced-device-note"><i :class="deviceIcon"></i> Editing {{ deviceLabel }}</div>

		<details class="pb-collapsible" open>
			<summary>Layout</summary>
			<div class="pb-collapsible-body">
				<div class="pb-advanced-subtitle">Margin</div>
				<div class="pb-advanced-four-fields"><label v-for="side in sides" :key="'margin-'+side"><span>{{ side }}</span><input class="pb-input" v-model="settings[responsiveKey('margin'+side)]"></label></div>
				<div class="pb-advanced-subtitle">Padding</div>
				<div class="pb-advanced-four-fields"><label v-for="side in sides" :key="'padding-'+side"><span>{{ side }}</span><input class="pb-input" v-model="settings[responsiveKey('padding'+side)]"></label></div>
				<label class="pb-advanced-field"><span>Width</span><select class="pb-select" v-model="settings.widthMode"><option value="default">Default</option><option value="full">Full Width</option><option value="inline">Inline</option><option value="custom">Custom</option></select></label>
				<label v-if="settings.widthMode==='custom'" class="pb-advanced-field"><span>Custom Width</span><input class="pb-input" v-model="settings[responsiveKey('customWidth')]" placeholder="320px, 50%, 24rem"></label>
				<label class="pb-advanced-field"><span>Align Self</span><select class="pb-select" v-model="settings[responsiveKey('alignSelf')]"><option value="auto">Default</option><option value="flex-start">Start</option><option value="center">Center</option><option value="flex-end">End</option><option value="stretch">Stretch</option></select></label>
				<label class="pb-advanced-field"><span>Order</span><select class="pb-select" v-model="settings[responsiveKey('orderMode')]"><option value="default">Default</option><option value="start">Start</option><option value="end">End</option><option value="custom">Custom</option></select></label>
				<label v-if="settings[responsiveKey('orderMode')]==='custom'" class="pb-advanced-field"><span>Custom Order</span><input class="pb-input" type="number" v-model.number="settings[responsiveKey('order')]"></label>
				<label class="pb-advanced-field"><span>Size</span><select class="pb-select" v-model="settings[responsiveKey('sizeMode')]"><option value="none">None</option><option value="grow">Grow</option><option value="shrink">Shrink</option><option value="custom">Custom</option></select></label>
				<div v-if="settings[responsiveKey('sizeMode')]==='custom'" class="pb-advanced-two-fields"><label><span>Flex Grow</span><input class="pb-input" type="number" step=".1" v-model.number="settings[responsiveKey('flexGrow')]"></label><label><span>Flex Shrink</span><input class="pb-input" type="number" step=".1" v-model.number="settings[responsiveKey('flexShrink')]"></label></div>
				<label class="pb-advanced-field"><span>Position</span><select class="pb-select" v-model="settings.position"><option value="default">Default</option><option value="absolute">Absolute</option><option value="fixed">Fixed</option></select></label>
				<template v-if="settings.position!=='default'">
					<div class="pb-advanced-two-fields"><label><span>Horizontal Orientation</span><select class="pb-select" v-model="settings.horizontalOrientation"><option value="left">Left</option><option value="right">Right</option></select></label><label><span>X Offset</span><input class="pb-input" v-model="settings[responsiveKey('positionX')]"></label></div>
					<div class="pb-advanced-two-fields"><label><span>Vertical Orientation</span><select class="pb-select" v-model="settings.verticalOrientation"><option value="top">Top</option><option value="bottom">Bottom</option></select></label><label><span>Y Offset</span><input class="pb-input" v-model="settings[responsiveKey('positionY')]"></label></div>
				</template>
				<label class="pb-advanced-field"><span>Z-Index</span><input class="pb-input" type="number" v-model.number="settings[responsiveKey('zIndex')]"></label>
				<label class="pb-advanced-field"><span>CSS ID</span><input class="pb-input" v-model="settings.cssId" placeholder="unique-widget-id"></label>
				<label class="pb-advanced-field"><span>CSS Classes</span><input class="pb-input" v-model="settings.cssClass" placeholder="class-one class-two"></label>
			</div>
		</details>

		<details class="pb-collapsible">
			<summary>Display Conditions</summary>
			<div class="pb-collapsible-body">
				<p class="pb-advanced-help">Rules inside a group use AND. Groups use OR.</p>
				<div v-for="(group, groupIndex) in conditionGroups" :key="group.id || groupIndex" class="pb-condition-group">
					<div class="pb-condition-group-head"><strong>Group {{ groupIndex + 1 }}</strong><button type="button" @click="removeConditionGroup(groupIndex)"><i class="fas fa-times"></i></button></div>
					<div v-for="(rule, ruleIndex) in group.rules" :key="rule.id || ruleIndex" class="pb-condition-rule">
						<select class="pb-select" v-model="rule.effect"><option value="include">Include</option><option value="exclude">Exclude</option></select>
						<select class="pb-select" v-model="rule.source"><option value="page-id">Page ID</option><option value="page-slug">Page Slug</option><option value="auth-state">Authenticated / Guest</option><option value="user-role">User Role</option><option value="date-range">Date / Time Range</option><option value="device">Device</option></select>
						<select class="pb-select" v-model="rule.operator"><option value="is">Is</option><option value="is-not">Is Not</option><option value="contains">Contains</option></select>
						<input class="pb-input" v-model="rule.value" placeholder="Value">
						<button type="button" class="pb-advanced-remove" @click="removeConditionRule(group, ruleIndex)"><i class="fas fa-trash"></i></button>
					</div>
					<button type="button" class="pb-btn" @click="addConditionRule(group)"><i class="fas fa-plus"></i> Add Condition</button>
				</div>
				<button type="button" class="pb-btn" @click="addConditionGroup"><i class="fas fa-plus"></i> Add Group</button>
			</div>
		</details>

		<details class="pb-collapsible">
			<summary>Cache Settings</summary>
			<div class="pb-collapsible-body"><label class="pb-advanced-field"><span>Cache Mode</span><select class="pb-select" v-model="settings.cacheMode"><option value="default">Default</option><option value="inactive">Inactive</option><option value="active">Active</option></select></label><p class="pb-advanced-help">Active caches the rendered widget fragment and invalidates it when content changes.</p></div>
		</details>

		<details class="pb-collapsible">
			<summary>Motion Effects</summary>
			<div class="pb-collapsible-body">
				<button type="button" class="pb-ai-disabled" disabled @click="notifyUnavailableAI"><i class="fas fa-wand-magic-sparkles"></i><span><strong>Animate With AI</strong><small>AI service is not connected</small></span></button>
				<label class="pb-advanced-toggle"><span>Scrolling Effects</span><input type="checkbox" v-model="settings.scrollingEffects"></label>
				<template v-if="settings.scrollingEffects">
					<MotionEffect label="Vertical Scroll" enabled-key="verticalScrollEnabled" direction-key="verticalScrollDirection" :directions="[['up','Up'],['down','Down']]" :settings="settings" />
					<MotionEffect label="Horizontal Scroll" enabled-key="horizontalScrollEnabled" direction-key="horizontalScrollDirection" :directions="[['left','Left'],['right','Right']]" :settings="settings" />
					<MotionEffect label="Transparency" enabled-key="transparencyEnabled" direction-key="transparencyDirection" :directions="fadeDirections" :settings="settings" level-key="transparencyLevel" />
					<MotionEffect label="Blur" enabled-key="blurEnabled" direction-key="blurDirection" :directions="fadeDirections" :settings="settings" level-key="blurLevel" />
					<MotionEffect label="Rotate" enabled-key="rotateEnabled" direction-key="rotateDirection" :directions="[['left','Left'],['right','Right']]" :settings="settings" />
					<MotionEffect label="Scale" enabled-key="scaleEnabled" direction-key="scaleDirection" :directions="scaleDirections" :settings="settings" />
					<div class="pb-advanced-subtitle">Apply Effects On</div>
					<label class="pb-advanced-toggle"><span>Desktop</span><input type="checkbox" v-model="settings.scrollApplyDesktop"></label><label class="pb-advanced-toggle"><span>Tablet Portrait</span><input type="checkbox" v-model="settings.scrollApplyTablet"></label><label class="pb-advanced-toggle"><span>Mobile Portrait</span><input type="checkbox" v-model="settings.scrollApplyMobile"></label>
					<label class="pb-advanced-field"><span>Effects Relative To</span><select class="pb-select" v-model="settings.effectsRelativeTo"><option value="default">Default</option><option value="viewport">Viewport</option><option value="page">Entire Page</option></select></label>
				</template>
				<label class="pb-advanced-toggle"><span>Mouse Effects</span><input type="checkbox" v-model="settings.mouseEffects"></label>
				<template v-if="settings.mouseEffects"><label class="pb-advanced-toggle"><span>Mouse Track</span><input type="checkbox" v-model="settings.mouseTrackEnabled"></label><div v-if="settings.mouseTrackEnabled" class="pb-advanced-two-fields"><label><span>Direction</span><select class="pb-select" v-model="settings.mouseTrackDirection"><option value="direct">Direct</option><option value="opposite">Opposite</option></select></label><label><span>Speed</span><input class="pb-input" type="number" step=".1" v-model.number="settings.mouseTrackSpeed"></label></div><label class="pb-advanced-toggle"><span>3D Tilt</span><input type="checkbox" v-model="settings.tilt3dEnabled"></label><div v-if="settings.tilt3dEnabled" class="pb-advanced-two-fields"><label><span>Direction</span><select class="pb-select" v-model="settings.tilt3dDirection"><option value="direct">Direct</option><option value="opposite">Opposite</option></select></label><label><span>Speed</span><input class="pb-input" type="number" step=".1" v-model.number="settings.tilt3dSpeed"></label></div></template>
				<label class="pb-advanced-field"><span>Sticky</span><select class="pb-select" v-model="settings.sticky"><option value="none">None</option><option value="top">Top</option><option value="bottom">Bottom</option></select></label>
				<template v-if="settings.sticky!=='none'"><label class="pb-advanced-field"><span>Sticky Offset</span><input class="pb-input" v-model="settings[responsiveKey('stickyOffset')]"></label><label class="pb-advanced-field"><span>Effects Offset</span><input class="pb-input" type="number" v-model.number="settings.stickyEffectsOffset"></label><label class="pb-advanced-field"><span>Anchor Offset</span><input class="pb-input" type="number" v-model.number="settings.stickyAnchorOffset"></label><label class="pb-advanced-toggle"><span>Stay In Column</span><input type="checkbox" v-model="settings.stickyStayInColumn"></label></template>
				<label class="pb-advanced-field"><span>Entrance Animation</span><select class="pb-select" v-model="settings.entranceAnimation"><option value="">None</option><option v-for="animation in entranceAnimations" :key="animation" :value="animation">{{ animation }}</option></select></label>
				<div v-if="settings.entranceAnimation" class="pb-advanced-two-fields"><label><span>Duration</span><select class="pb-select" v-model="settings.entranceDuration"><option value="slow">Slow</option><option value="normal">Normal</option><option value="fast">Fast</option></select></label><label><span>Delay (ms)</span><input class="pb-input" type="number" min="0" v-model.number="settings.entranceDelay"></label></div>
			</div>
		</details>

		<details class="pb-collapsible">
			<summary>Transform</summary>
			<div class="pb-collapsible-body"><StateTabs v-model="transformState" />
				<div class="pb-advanced-two-fields"><label><span>Rotate</span><input class="pb-input" v-model="settings[stateKey('transformRotate', transformState)]"></label><label><span>Perspective</span><input class="pb-input" v-model="settings[stateKey('transformPerspective', transformState)]"></label></div>
				<div class="pb-advanced-two-fields"><label><span>3D Rotate X</span><input class="pb-input" v-model="settings[stateKey('transformRotateX', transformState)]"></label><label><span>3D Rotate Y</span><input class="pb-input" v-model="settings[stateKey('transformRotateY', transformState)]"></label></div>
				<div class="pb-advanced-two-fields"><label><span>Offset X</span><input class="pb-input" v-model="settings[responsiveStateKey('transformOffsetX', transformState)]"></label><label><span>Offset Y</span><input class="pb-input" v-model="settings[responsiveStateKey('transformOffsetY', transformState)]"></label></div>
				<div class="pb-advanced-two-fields"><label><span>Scale</span><input class="pb-input" type="number" step=".01" v-model.number="settings[stateKey('transformScale', transformState)]"></label><label><span>Skew X / Y</span><div class="pb-advanced-two-fields"><input class="pb-input" v-model="settings[stateKey('transformSkewX', transformState)]"><input class="pb-input" v-model="settings[stateKey('transformSkewY', transformState)]"></div></label></div>
				<label class="pb-advanced-toggle"><span>Flip Horizontal</span><input type="checkbox" v-model="settings[stateKey('transformFlipHorizontal', transformState)]"></label><label class="pb-advanced-toggle"><span>Flip Vertical</span><input type="checkbox" v-model="settings[stateKey('transformFlipVertical', transformState)]"></label>
				<label v-if="transformState==='hover'" class="pb-advanced-field"><span>Hover Transition Duration</span><input class="pb-input" type="number" min="0" step=".1" v-model.number="settings.transformHoverDuration"></label>
				<div class="pb-advanced-two-fields"><label><span>X Anchor</span><select class="pb-select" v-model="settings.transformOriginX"><option value="left">Left</option><option value="center">Center</option><option value="right">Right</option></select></label><label><span>Y Anchor</span><select class="pb-select" v-model="settings.transformOriginY"><option value="top">Top</option><option value="center">Center</option><option value="bottom">Bottom</option></select></label></div>
			</div>
		</details>

		<details class="pb-collapsible"><summary>Background</summary><div class="pb-collapsible-body"><StateTabs v-model="backgroundState" />
			<label class="pb-advanced-field"><span>Background Type</span><select class="pb-select" v-model="settings[stateKey('advancedBackgroundType', backgroundState)]"><option value="none">None</option><option value="classic">Classic</option><option value="gradient">Gradient</option></select></label>
			<template v-if="settings[stateKey('advancedBackgroundType', backgroundState)]==='classic'"><label class="pb-advanced-field"><span>Color</span><input class="pb-input" v-model="settings[stateKey('advancedBackgroundColor', backgroundState)]"></label><label class="pb-advanced-field"><span>Image</span><input class="pb-input" v-model="settings[stateKey('advancedBackgroundImage', backgroundState)]" placeholder="https://..."></label><div class="pb-advanced-two-fields"><label><span>Position</span><input class="pb-input" v-model="settings[stateKey('advancedBackgroundPosition', backgroundState)]"></label><label><span>Attachment</span><select class="pb-select" v-model="settings[stateKey('advancedBackgroundAttachment', backgroundState)]"><option value="scroll">Scroll</option><option value="fixed">Fixed</option></select></label><label><span>Repeat</span><select class="pb-select" v-model="settings[stateKey('advancedBackgroundRepeat', backgroundState)]"><option value="no-repeat">No Repeat</option><option value="repeat">Repeat</option><option value="repeat-x">Repeat X</option><option value="repeat-y">Repeat Y</option></select></label><label><span>Size</span><select class="pb-select" v-model="settings[stateKey('advancedBackgroundSize', backgroundState)]"><option value="auto">Auto</option><option value="cover">Cover</option><option value="contain">Contain</option></select></label></div></template>
			<template v-if="settings[stateKey('advancedBackgroundType', backgroundState)]==='gradient'"><div class="pb-advanced-two-fields"><label><span>First Color</span><input class="pb-input" v-model="settings[stateKey('advancedGradientColorOne', backgroundState)]"></label><label><span>First Location</span><input class="pb-input" type="number" min="0" max="100" v-model.number="settings[stateKey('advancedGradientLocationOne', backgroundState)]"></label><label><span>Second Color</span><input class="pb-input" v-model="settings[stateKey('advancedGradientColorTwo', backgroundState)]"></label><label><span>Second Location</span><input class="pb-input" type="number" min="0" max="100" v-model.number="settings[stateKey('advancedGradientLocationTwo', backgroundState)]"></label></div><label class="pb-advanced-field"><span>Gradient Type</span><select class="pb-select" v-model="settings[stateKey('advancedGradientType', backgroundState)]"><option value="linear">Linear</option><option value="radial">Radial</option></select></label></template>
			<label v-if="backgroundState==='hover'" class="pb-advanced-field"><span>Hover Transition Duration</span><input class="pb-input" type="number" min="0" step=".1" v-model.number="settings.advancedBackgroundHoverDuration"></label>
		</div></details>

		<details class="pb-collapsible"><summary>Border</summary><div class="pb-collapsible-body"><StateTabs v-model="borderState" />
			<label class="pb-advanced-field"><span>Border Type</span><select class="pb-select" v-model="settings[stateKey('advancedBorderType', borderState)]"><option v-for="type in borderTypes" :key="type" :value="type">{{ type }}</option></select></label>
			<div v-if="settings[stateKey('advancedBorderType', borderState)]!=='none'" class="pb-advanced-two-fields"><label><span>Border Width</span><input class="pb-input" v-model="settings[stateKey('advancedBorderWidth', borderState)]"></label><label><span>Border Color</span><input class="pb-input" v-model="settings[stateKey('advancedBorderColor', borderState)]"></label></div>
			<label class="pb-advanced-field"><span>Border Radius</span><input class="pb-input" v-model="settings[responsiveStateKey('advancedBorderRadius', borderState)]"></label>
			<label class="pb-advanced-toggle"><span>Box Shadow</span><input type="checkbox" v-model="settings[stateKey('advancedBoxShadowEnabled', borderState)]"></label><div v-if="settings[stateKey('advancedBoxShadowEnabled', borderState)]" class="pb-advanced-two-fields"><label><span>Color</span><input class="pb-input" v-model="settings[stateKey('advancedBoxShadowColor', borderState)]"></label><label><span>Horizontal</span><input class="pb-input" v-model="settings[stateKey('advancedBoxShadowX', borderState)]"></label><label><span>Vertical</span><input class="pb-input" v-model="settings[stateKey('advancedBoxShadowY', borderState)]"></label><label><span>Blur</span><input class="pb-input" v-model="settings[stateKey('advancedBoxShadowBlur', borderState)]"></label><label><span>Spread</span><input class="pb-input" v-model="settings[stateKey('advancedBoxShadowSpread', borderState)]"></label><label class="pb-advanced-toggle"><span>Outline / Inset</span><input type="checkbox" v-model="settings[stateKey('advancedBoxShadowInset', borderState)]"></label></div>
			<label v-if="borderState==='hover'" class="pb-advanced-field"><span>Hover Transition Duration</span><input class="pb-input" type="number" min="0" step=".1" v-model.number="settings.advancedBorderHoverDuration"></label>
		</div></details>

		<details class="pb-collapsible"><summary>Mask</summary><div class="pb-collapsible-body"><label class="pb-advanced-toggle"><span>Enable Mask</span><input type="checkbox" v-model="settings.maskEnabled"></label><template v-if="settings.maskEnabled"><label class="pb-advanced-field"><span>Shape</span><select class="pb-select" v-model="settings.maskShape"><option v-for="shape in maskShapes" :key="shape" :value="shape">{{ shape }}</option></select></label><label v-if="settings.maskShape==='custom'" class="pb-advanced-field"><span>Custom Image or SVG</span><input class="pb-input" v-model="settings.maskCustomImage"></label><label class="pb-advanced-field"><span>Size</span><select class="pb-select" v-model="settings[responsiveKey('maskSize')]"><option value="fit">Fit</option><option value="fill">Fill</option><option value="custom">Custom</option></select></label><label v-if="settings[responsiveKey('maskSize')]==='custom'" class="pb-advanced-field"><span>Scale</span><input class="pb-input" type="number" v-model.number="settings[responsiveKey('maskScale')]"></label><label class="pb-advanced-field"><span>Position</span><select class="pb-select" v-model="settings[responsiveKey('maskPosition')]"><option v-for="position in maskPositions" :key="position" :value="position">{{ position }}</option></select></label><div v-if="settings[responsiveKey('maskPosition')]==='custom'" class="pb-advanced-two-fields"><label><span>X</span><input class="pb-input" v-model="settings[responsiveKey('maskPositionX')]"></label><label><span>Y</span><input class="pb-input" v-model="settings[responsiveKey('maskPositionY')]"></label></div><label class="pb-advanced-field"><span>Repeat</span><select class="pb-select" v-model="settings[responsiveKey('maskRepeat')]"><option v-for="repeat in maskRepeats" :key="repeat" :value="repeat">{{ repeat }}</option></select></label></template></div></details>

		<details class="pb-collapsible"><summary>Responsive</summary><div class="pb-collapsible-body"><label class="pb-advanced-toggle"><span>Hide On Desktop</span><input type="checkbox" v-model="settings.hideDesktop"></label><label class="pb-advanced-toggle"><span>Hide On Tablet Portrait</span><input type="checkbox" v-model="settings.hideTablet"></label><label class="pb-advanced-toggle"><span>Hide On Mobile Portrait</span><input type="checkbox" v-model="settings.hideMobile"></label></div></details>

		<details class="pb-collapsible"><summary>Attributes</summary><div class="pb-collapsible-body"><div v-for="(attribute,index) in attributes" :key="index" class="pb-attribute-row"><input class="pb-input" v-model="attribute.name" placeholder="key"><input class="pb-input" v-model="attribute.value" placeholder="value"><button type="button" @click="removeAttribute(index)"><i class="fas fa-times"></i></button></div><button type="button" class="pb-btn" @click="addAttribute"><i class="fas fa-plus"></i> Add Attribute</button><p class="pb-advanced-help">Use key|value semantics. Event handlers, style, id, class, and unsafe URLs are rejected on render.</p></div></details>

		<details class="pb-collapsible"><summary>Custom CSS</summary><div class="pb-collapsible-body"><textarea class="pb-input pb-advanced-css" v-model="settings.customCssCode" rows="9" placeholder="selector { color: #101828; }"></textarea><p class="pb-advanced-help">Use <code>selector</code> to scope rules to this widget.</p></div></details>
	</div>
</template>

<script>
const StateTabs = {
	props: { modelValue: { type: String, default: 'normal' } },
	emits: ['update:modelValue'],
	template: `<div class="pb-state-tabs"><button type="button" :class="{active:modelValue==='normal'}" @click="$emit('update:modelValue','normal')">Normal</button><button type="button" :class="{active:modelValue==='hover'}" @click="$emit('update:modelValue','hover')">Hover</button></div>`,
};

const MotionEffect = {
	props: {
		label: String, settings: Object, enabledKey: String, directionKey: String,
		directions: { type: Array, default: () => [] }, levelKey: { type: String, default: '' },
	},
	computed: {
		prefix() { return this.enabledKey.replace(/Enabled$/, ''); },
	},
	template: `<div class="pb-motion-effect"><label class="pb-advanced-toggle"><span>{{ label }}</span><input type="checkbox" v-model="settings[enabledKey]"></label><div v-if="settings[enabledKey]" class="pb-advanced-two-fields"><label><span>Direction</span><select class="pb-select" v-model="settings[directionKey]"><option v-for="option in directions" :key="option[0]" :value="option[0]">{{ option[1] }}</option></select></label><label><span>{{ levelKey ? 'Level' : 'Speed' }}</span><input class="pb-input" type="number" step=".1" v-model.number="settings[levelKey || (prefix+'Speed')]"></label><label><span>Viewport Start</span><input class="pb-input" type="number" min="0" max="100" v-model.number="settings[prefix+'ViewportStart']"></label><label><span>Viewport End</span><input class="pb-input" type="number" min="0" max="100" v-model.number="settings[prefix+'ViewportEnd']"></label></div></div>`,
};

export default {
	name: 'WidgetAdvancedControls',
	components: { StateTabs, MotionEffect },
	props: {
		node: { type: Object, required: true },
		responsiveDevice: { type: String, default: 'desktop' },
	},
	emits: ['unavailable-ai'],
	data() {
		return {
			sides: ['Top', 'Right', 'Bottom', 'Left'],
			backgroundState: 'normal', borderState: 'normal', transformState: 'normal',
			borderTypes: ['none', 'solid', 'double', 'dotted', 'dashed', 'groove'],
			fadeDirections: [['fade-in','Fade In'],['fade-out','Fade Out'],['fade-out-in','Fade Out In'],['fade-in-out','Fade In Out']],
			scaleDirections: [['up','Up'],['down','Down'],['down-up','Down-Up'],['up-down','Up-Down']],
			entranceAnimations: ['fadeIn','fadeInUp','fadeInDown','fadeInLeft','fadeInRight','zoomIn','bounceIn','slideInUp','slideInDown','slideInLeft','slideInRight','rotateIn','lightSpeedIn','rollIn'],
			maskShapes: ['circle', 'flower', 'sketch', 'triangle', 'blob', 'hexagon', 'custom'],
			maskPositions: ['left top','center top','right top','left center','center center','right center','left bottom','center bottom','right bottom','custom'],
			maskRepeats: ['no-repeat','repeat','repeat-x','repeat-y','round','space'],
		};
	},
	computed: {
		settings() { return this.node.settings || (this.node.settings = {}); },
		attributes() { return Array.isArray(this.settings.attributes) ? this.settings.attributes : (this.settings.attributes = []); },
		conditionGroups() { return Array.isArray(this.settings.displayConditions) ? this.settings.displayConditions : (this.settings.displayConditions = []); },
		deviceLabel() { return this.responsiveDevice === 'tablet' ? 'Tablet Portrait' : (this.responsiveDevice === 'mobile' ? 'Mobile Portrait' : 'Desktop'); },
		deviceIcon() { return this.responsiveDevice === 'tablet' ? 'fas fa-tablet-alt' : (this.responsiveDevice === 'mobile' ? 'fas fa-mobile-alt' : 'fas fa-desktop'); },
	},
	methods: {
		responsiveKey(base) { return base + (this.responsiveDevice === 'tablet' ? 'Tablet' : (this.responsiveDevice === 'mobile' ? 'Mobile' : '')); },
		stateKey(base, state) { return base + (state === 'hover' ? 'Hover' : ''); },
		responsiveStateKey(base, state) { return this.responsiveKey(this.stateKey(base, state)); },
		notifyUnavailableAI() { this.$emit('unavailable-ai'); },
		addAttribute() { this.attributes.push({ name: '', value: '' }); },
		removeAttribute(index) { this.attributes.splice(index, 1); },
		newId(prefix) { return prefix + '-' + Math.random().toString(36).slice(2, 9); },
		addConditionGroup() { this.conditionGroups.push({ id: this.newId('group'), rules: [{ id: this.newId('rule'), effect: 'include', source: 'page-slug', operator: 'is', value: '' }] }); },
		removeConditionGroup(index) { this.conditionGroups.splice(index, 1); },
		addConditionRule(group) { if (!Array.isArray(group.rules)) group.rules = []; group.rules.push({ id: this.newId('rule'), effect: 'include', source: 'page-slug', operator: 'is', value: '' }); },
		removeConditionRule(group, index) { if (Array.isArray(group.rules)) group.rules.splice(index, 1); },
	},
};
</script>

<style scoped>
.pb-widget-advanced-controls { display: flex; flex-direction: column; gap: 0; }
.pb-advanced-device-note { display: flex; align-items: center; gap: 8px; margin-bottom: 14px; padding: 9px 10px; border-radius: 8px; background: #f3f5fa; color: #526178; font-size: 11px; }
.pb-advanced-subtitle { margin: 10px 0 8px; font-size: 11px; font-weight: 700; color: #344054; }
.pb-advanced-field, .pb-advanced-two-fields > label, .pb-advanced-four-fields > label { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; font-size: 11px; color: #526178; }
.pb-advanced-two-fields, .pb-advanced-four-fields { display: grid; gap: 8px; }
.pb-advanced-two-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.pb-advanced-four-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.pb-advanced-toggle { min-height: 36px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; font-size: 12px; color: #344054; }
.pb-ai-disabled { width: 100%; display: flex; align-items: center; gap: 10px; margin-bottom: 14px; padding: 10px; border: 1px dashed #c9d1df; border-radius: 8px; background: #f8fafc; color: #667085; text-align: left; }
.pb-ai-disabled span { display: flex; flex-direction: column; }.pb-ai-disabled small { font-size: 10px; }
.pb-advanced-help { margin: 4px 0 12px; color: #7a8699; font-size: 10px; line-height: 1.5; }
.pb-condition-group { margin-bottom: 12px; padding: 10px; border: 1px solid #dce2ec; border-radius: 8px; }
.pb-condition-group-head { display: flex; justify-content: space-between; margin-bottom: 8px; }.pb-condition-group-head button,.pb-advanced-remove,.pb-attribute-row button { border: 0; background: transparent; color: #667085; }
.pb-condition-rule { display: grid; gap: 6px; margin-bottom: 8px; }.pb-attribute-row { display: grid; grid-template-columns: 1fr 1fr 30px; gap: 6px; margin-bottom: 8px; }
.pb-motion-effect { padding: 8px 0; border-top: 1px solid #edf0f5; }
.pb-advanced-css { min-height: 180px; resize: vertical; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
</style>
