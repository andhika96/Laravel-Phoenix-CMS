<template>
	<div class="pb-accordion-settings pb-widget-settings pb-widget-settings--accordion">
		<div class="pb-tab-nav">
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'content'}" @click="editor.settingsTab = 'content'"><i class="fas fa-edit"></i><span>Content</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'style'}" @click="editor.settingsTab = 'style'"><i class="fas fa-adjust"></i><span>Style</span></button>
			<button type="button" class="pb-tab-btn pb-tab-btn-icon" :class="{active: editor.settingsTab === 'advanced'}" @click="editor.settingsTab = 'advanced'"><i class="fas fa-gear"></i><span>Advanced</span></button>
		</div>

		<div v-if="editor.settingsTab === 'content'" class="pb-tab-content">
			<details class="pb-collapsible" open>
				<summary>Layout</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group pb-accordion-items-control">
						<div class="pb-label-row"><label class="pb-form-label mb-0">Items</label></div>
						<component :is="editor.draggable" v-model="node.accordionItems" item-key="id" :group="{ name: 'pb-accordion-items', pull: false, put: false }" handle=".pb-accordion-item-header" class="pb-accordion-items-list">
							<template #item="{ element: item, index }">
								<div class="pb-accordion-item-row" :class="{ active: isItemExpanded(item), expanded: isItemExpanded(item) }">
									<div class="pb-accordion-item-header">
										<button type="button" class="pb-accordion-item-main" :aria-expanded="isItemExpanded(item)" @click="toggleAccordionItem(item)"><i class="fas pb-accordion-item-disclosure" :class="isItemExpanded(item)?'fa-chevron-up':'fa-chevron-down'" aria-hidden="true"></i><span>{{ editor.accordionItemSummary(item, index) }}</span></button>
										<button type="button" class="pb-accordion-item-action" title="Duplicate" aria-label="Duplicate item" @click.stop="duplicateAccordionItem(item.id)"><i class="far fa-copy"></i></button>
										<button type="button" class="pb-accordion-item-action" title="Remove" aria-label="Remove item" :disabled="editor.accordionItemsForNode(node).length <= 1" @click.stop="removeAccordionItem(item.id)"><i class="fas fa-times"></i></button>
									</div>
									<div v-if="isItemExpanded(item) && editingItem" class="pb-accordion-item-fields">
										<div class="pb-form-group"><label class="pb-form-label">Title</label><input class="pb-input" v-model="editingItem.title" placeholder="Item Title"></div>
										<div class="pb-form-group"><label class="pb-form-label">CSS ID</label><input class="pb-input" v-model="editingItem.cssId"></div>
									</div>
								</div>
							</template>
						</component>
						<button type="button" class="pb-btn pb-accordion-add-btn" @click="addAccordionItem"><i class="fas fa-plus"></i><span>Add Item</span></button>
					</div>

					<responsive-choice label="Item Position" base="itemPosition" control-id="accordion-item-position" :node="node" :editor="editor" :options="itemPositionOptions" />
					<div class="pb-subsection-title">Icon</div>
					<responsive-choice label="Position" base="iconPosition" control-id="accordion-icon-position" :node="node" :editor="editor" :options="iconPositionOptions" />
					<icon-source-control label="Expand" role-name="expand" :node="node" :editor="editor" />
					<icon-source-control label="Collapse" role-name="collapse" :node="node" :editor="editor" />

					<div class="pb-form-group"><label class="pb-form-label">Title HTML Tag</label><select class="pb-select" v-model="node.settings.titleTag"><option v-for="tag in ['h1','h2','h3','h4','h5','h6','div','span','p']" :key="tag" :value="tag">{{ tag === 'div' || tag === 'span' || tag === 'p' ? tag : tag.toUpperCase() }}</option></select></div>
					<div class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">FAQ Schema</label><div class="pb-toggle-wrap"><input :id="'accordion-faq-' + node.id" type="checkbox" class="pb-toggle" v-model="node.settings.faqSchema"><label :for="'accordion-faq-' + node.id"></label></div></div>
				</div>
			</details>

			<details class="pb-collapsible">
				<summary>Interactions</summary>
				<div class="pb-collapsible-body">
					<div class="pb-form-group"><label class="pb-form-label">Default State</label><select class="pb-select" v-model="node.settings.defaultState" @change="editor.resetAccordionRuntimeFromDefaults(node)"><option value="first-expanded">First expanded</option><option value="all-collapsed">All collapsed</option></select></div>
					<div class="pb-form-group"><label class="pb-form-label">Max Items Expanded</label><select class="pb-select" v-model="node.settings.maxExpanded" @change="editor.accordionRuntimeForNode(node)"><option value="one">One</option><option value="multiple">Multiple</option></select></div>
					<responsive-dimension-control label="Animation Duration" base="animationDuration" control-id="accordion-animation-duration" fallback="400ms" :node="node" :editor="editor" :max="5000" :units="['ms']" />
				</div>
			</details>
			<a class="pb-accordion-help-link" href="https://go.elementor.com/widget-nested-accordion" target="_blank" rel="noopener noreferrer">Need Help <i class="fas fa-external-link-alt"></i></a>
		</div>

		<div v-if="editor.settingsTab === 'style'" class="pb-tab-content pb-accordion-style-settings">
			<details class="pb-collapsible" open>
				<summary>Accordion</summary>
				<div class="pb-collapsible-body">
					<responsive-dimension-control label="Space between Items" base="accordionItemGap" control-id="accordion-item-gap" fallback="0px" :node="node" :editor="editor" :max="200" />
					<responsive-dimension-control label="Distance from content" base="accordionContentDistance" control-id="accordion-content-distance" fallback="0px" :node="node" :editor="editor" :max="300" />
					<div class="pb-state-tabs pb-accordion-style-state"><button v-for="state in styleStates" :key="state.value" type="button" :class="{active: styleState === state.value}" @click="styleState = state.value">{{ state.label }}</button></div>
					<background-control prefix="accordion" :suffix="capitalize(styleState)" :node="node" />
					<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="node.settings[stateKey('accordionBorderType')]"><option v-for="type in borderTypes" :key="type" :value="type">{{ capitalize(type) }}</option></select></div>
					<responsive-box-control v-if="!['default','none'].includes(node.settings[stateKey('accordionBorderType')])" label="Border Width" :base="stateKey('accordionBorderWidth')" :control-id="'accordion-' + styleState + '-border-width'" fallback="1px" :node="node" :editor="editor" />
					<div v-if="!['default','none'].includes(node.settings[stateKey('accordionBorderType')])" class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings[stateKey('accordionBorderColor')]"></div></div>
					<box-shadow-control prefix="accordionBoxShadow" :suffix="capitalize(styleState)" :node="node" />
					<responsive-box-control label="Border Radius" base="accordionBorderRadius" control-id="accordion-border-radius" fallback="0px" kind="corners" :node="node" :editor="editor" />
					<responsive-box-control label="Padding" base="accordionPadding" control-id="accordion-padding" fallback="0px" :node="node" :editor="editor" />
				</div>
			</details>

			<details class="pb-collapsible">
				<summary>Header</summary>
				<div class="pb-collapsible-body">
					<div class="pb-subsection-title">Title</div>
					<component :is="editor.typographyControl" :settings="node.settings" :responsive-device="editor.responsiveDevice" :font-families="editor.fontFamilies" font-size-mode-key="headerFontSizeMode" @responsive-device="editor.setResponsiveDevice" />
					<div class="pb-state-tabs pb-accordion-style-state"><button v-for="state in styleStates" :key="'title-' + state.value" type="button" :class="{active: titleState === state.value}" @click="titleState = state.value">{{ state.label }}</button></div>
					<div class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings[stateKey('headerTitleColor', titleState)]"></div></div>
					<component :is="editor.textShadowControl" aria-label="Text Shadow" :model-value="node.settings[stateKey('headerTextShadow', titleState)]" :control-id="'accordion-title-shadow-' + titleState" :open="activeTextEffect === ('accordion-title-shadow-' + titleState)" @request-open="activeTextEffect = $event" @update:modelValue="node.settings[stateKey('headerTextShadow', titleState)] = $event" />
					<component :is="editor.textStrokeControl" aria-label="Text Stroke" :settings="node.settings" :width-key="stateKey('headerTextStrokeWidth', titleState)" :color-key="stateKey('headerTextStrokeColor', titleState)" :responsive-device="editor.responsiveDevice" :control-id="'accordion-title-stroke-' + titleState" :open="activeTextEffect === ('accordion-title-stroke-' + titleState)" @request-open="activeTextEffect = $event" />

					<div class="pb-subsection-title">Icon</div>
					<responsive-dimension-control label="Size" base="headerIconSize" control-id="accordion-icon-size" fallback="15px" :node="node" :editor="editor" :max="160" />
					<responsive-dimension-control label="Spacing" base="headerIconSpacing" control-id="accordion-icon-spacing" fallback="10px" :node="node" :editor="editor" :max="120" />
					<div class="pb-state-tabs pb-accordion-style-state"><button v-for="state in styleStates" :key="'icon-' + state.value" type="button" :class="{active: iconState === state.value}" @click="iconState = state.value">{{ state.label }}</button></div>
					<div class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings[stateKey('headerIconColor', iconState)]"></div></div>
				</div>
			</details>

			<details class="pb-collapsible">
				<summary>Content</summary>
				<div class="pb-collapsible-body">
					<background-control prefix="content" :node="node" />
					<div class="pb-form-group"><label class="pb-form-label">Border Type</label><select class="pb-select" v-model="node.settings.contentBorderType"><option v-for="type in borderTypes" :key="'content-' + type" :value="type">{{ capitalize(type) }}</option></select></div>
					<responsive-box-control v-if="!['default','none'].includes(node.settings.contentBorderType)" label="Border Width" base="contentBorderWidth" control-id="accordion-content-border-width" fallback="0px" :node="node" :editor="editor" />
					<div v-if="!['default','none'].includes(node.settings.contentBorderType)" class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Border Color</label><div class="pb-color-row"><input class="pb-input coloris pb-coloris-input" v-model="node.settings.contentBorderColor"></div></div>
					<responsive-box-control label="Border Radius" base="contentBorderRadius" control-id="accordion-content-radius" fallback="0px" kind="corners" :node="node" :editor="editor" />
					<responsive-box-control label="Padding" base="contentPadding" control-id="accordion-content-padding" fallback="0px" :node="node" :editor="editor" />
				</div>
			</details>
			<a class="pb-accordion-help-link" href="https://go.elementor.com/widget-nested-accordion" target="_blank" rel="noopener noreferrer">Need Help <i class="fas fa-external-link-alt"></i></a>
		</div>

		<div v-if="editor.settingsTab === 'advanced'" class="pb-tab-content pb-accordion-advanced-settings">
			<component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" :elementor-choices="true" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings,$event)" @clear-media="editor.clearMedia(node.settings,$event)" @unavailable-ai="editor.showUnsupportedControlNotice('Animate With AI', 'AI service is not connected to this page builder.')" />
			<a class="pb-accordion-help-link" href="https://go.elementor.com/widget-nested-accordion" target="_blank" rel="noopener noreferrer">Need Help <i class="fas fa-external-link-alt"></i></a>
		</div>
	</div>
</template>

<script>
const DIMENSION_PATTERN = /^(-?\d+(?:\.\d+)?)(px|pt|%|em|rem|vw|vh|ms)?$/i;
const DIMENSION_UNITS = ['px', 'pt', '%', 'em', 'rem', 'vw', 'vh'];
function parseDimension(value, fallback = '0px') { const f=String(fallback||'0px').trim().match(DIMENSION_PATTERN);const m=String(value??'').trim().match(DIMENSION_PATTERN);const unit=m?.[2]||f?.[2]||'px';return{value:Number(m?.[1]??f?.[1]??0),unit}; }
function expandBoxValue(value, fallback = '0px') { const t=String(value||fallback).trim().split(/\s+/).filter(Boolean);if(t.length===1)return[t[0],t[0],t[0],t[0]];if(t.length===2)return[t[0],t[1],t[0],t[1]];if(t.length===3)return[t[0],t[1],t[2],t[1]];return t.length>=4?t.slice(0,4):['0px','0px','0px','0px']; }
function dimensionLimit(unit) { if(unit==='%')return 100;if(unit==='em'||unit==='rem')return 30;if(unit==='vw'||unit==='vh')return 100;if(unit==='ms')return 5000;return 400; }
function dimensionStep(unit) { return unit==='em'||unit==='rem'?0.01:(unit==='ms'?50:1); }
const ResponsiveMenu={props:{editor:Object,controlId:String},template:`<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" :title="'Responsive: '+editor.responsiveDeviceLabel()" @click.stop="editor.openControlResponsiveMenu(controlId)"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(controlId)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="controlId+'-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(controlId,device.value)"><i :class="device.icon"></i><span>{{editor.deviceOptionLabel(device)}}</span></button></div></div>`};
const ResponsiveChoice={components:{ResponsiveMenu},props:{label:String,base:String,controlId:String,node:Object,editor:Object,options:Array},computed:{settingKey(){return this.editor.activeResponsiveKey(this.base);},value(){const current=this.node.settings[this.settingKey];return current===''||current==null?(this.node.settings[this.base]||this.options?.[0]?.value||''):current;}},template:`<div class="pb-form-group pb-accordion-inline-control"><div class="pb-label-row pb-label-row-device"><div class="pb-accordion-control-label"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :control-id="controlId"/></div><div class="pb-seg-group"><button v-for="option in options" :key="option.value" type="button" class="pb-seg-btn" :class="{active:value===option.value}" :title="option.label" :aria-label="option.label" @click="node.settings[settingKey]=option.value"><i :class="option.icon"></i></button></div></div></div>`};
const ResponsiveDimensionControl={components:{ResponsiveMenu},props:{label:String,base:String,controlId:String,fallback:String,node:Object,editor:Object,min:{type:Number,default:0},max:{type:Number,default:400},units:{type:Array,default:()=>DIMENSION_UNITS}},computed:{settingKey(){return this.base==='animationDuration'?this.base:this.editor.activeResponsiveKey(this.base);},parsed(){return parseDimension(this.node.settings[this.settingKey]||this.node.settings[this.base],this.fallback);},allowedUnits(){return this.units?.length?this.units:DIMENSION_UNITS;},maxValue(){return Math.min(this.max,dimensionLimit(this.parsed.unit));},stepValue(){return dimensionStep(this.parsed.unit);}},methods:{setValue(raw){const value=Number(raw);if(!Number.isFinite(value))return;const safe=Math.min(this.maxValue,Math.max(this.min,value));this.node.settings[this.settingKey]=this.parsed.unit==='ms'?safe:`${safe}${this.parsed.unit}`;},setUnit(unit){const safe=this.allowedUnits.includes(unit)?unit:this.allowedUnits[0];this.node.settings[this.settingKey]=safe==='ms'?this.parsed.value:`${this.parsed.value}${safe}`;}},template:`<div class="pb-form-group pb-accordion-dimension-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><div class="pb-label-tools"><responsive-menu v-if="base!=='animationDuration'" :editor="editor" :control-id="controlId"/></div></div><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="maxValue" :step="stepValue" :value="parsed.value" @input="setValue($event.target.value)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :min="min" :max="maxValue" :step="stepValue" :value="parsed.value" :aria-label="label" @input="setValue($event.target.value)"><select class="pb-mini-unit" :value="parsed.unit" :aria-label="label+' unit'" @change="setUnit($event.target.value)"><option v-for="unit in allowedUnits" :key="unit" :value="unit">{{unit}}</option></select></div></div></div>`};
const ResponsiveBoxControl={components:{ResponsiveMenu},props:{label:String,base:String,controlId:String,fallback:String,kind:{type:String,default:'edges'},node:Object,editor:Object},data(){return{linked:true};},computed:{settingKey(){return this.editor.activeResponsiveKey(this.base);},sides(){return this.kind==='corners'?[{label:'Top Left'},{label:'Top Right'},{label:'Bottom Right'},{label:'Bottom Left'}]:[{label:'Top'},{label:'Right'},{label:'Bottom'},{label:'Left'}];},tokens(){return expandBoxValue(this.node.settings[this.settingKey]||this.node.settings[this.base]||this.fallback,this.fallback).map(token=>parseDimension(token,this.fallback));},unit(){return this.tokens[0]?.unit||'px';},maxValue(){return dimensionLimit(this.unit);},stepValue(){return dimensionStep(this.unit);}},methods:{setValue(index,raw){const n=Number(raw);if(!Number.isFinite(n))return;const safe=Math.min(this.maxValue,Math.max(0,n));const values=this.tokens.map(token=>token.value);if(this.linked)values.fill(safe);else values[index]=safe;this.node.settings[this.settingKey]=values.map(value=>`${value}${this.unit}`).join(' ');},setUnit(unit){const safe=DIMENSION_UNITS.includes(unit)?unit:'px';this.node.settings[this.settingKey]=this.tokens.map(token=>`${token.value}${safe}`).join(' ');}},template:`<div class="pb-form-group pb-accordion-box-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :control-id="controlId"/><select class="pb-mini-unit" :value="unit" :aria-label="label+' unit'" @change="setUnit($event.target.value)"><option v-for="option in ['px','pt','%','em','rem','vw','vh']" :key="option" :value="option">{{option}}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in sides" :key="side.label" class="pb-side-input"><input class="pb-input" type="number" min="0" :max="maxValue" :step="stepValue" :value="tokens[index].value" @input="setValue(index,$event.target.value)"><span>{{side.label}}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:linked}" :title="linked?'Unlink values':'Link values'" @click="linked=!linked"><i class="fas" :class="linked?'fa-link':'fa-unlink'"></i></button></div></div></div>`};
const BackgroundControl={props:{prefix:String,suffix:{type:String,default:''},node:Object},computed:{typeKey(){return this.settingKey('BackgroundType');},colorKey(){return this.settingKey('BackgroundColor');}},methods:{settingKey(field){return this.prefix+field+this.suffix;}},template:`<div class="pb-accordion-background-control"><div class="pb-form-group pb-accordion-inline-control"><div class="pb-label-row"><label class="pb-form-label mb-0">Background Type</label><div class="pb-seg-group"><button type="button" class="pb-seg-btn" :class="{active:node.settings[typeKey]==='classic'}" title="Classic" aria-label="Classic" @click="node.settings[typeKey]='classic'"><i class="fas fa-paint-brush"></i></button><button type="button" class="pb-seg-btn" :class="{active:node.settings[typeKey]==='gradient'}" title="Gradient" aria-label="Gradient" @click="node.settings[typeKey]='gradient'"><i class="fas fa-adjust"></i></button></div></div></div><div v-if="node.settings[typeKey]==='gradient'" class="pb-accordion-gradient-fields"><div class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">First Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[settingKey('GradientColorOne')]"></div><div class="pb-form-group"><label class="pb-form-label">First Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="node.settings[settingKey('GradientLocationOne')]"></div><div class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Second Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[settingKey('GradientColorTwo')]"></div><div class="pb-form-group"><label class="pb-form-label">Second Location</label><input class="pb-input" type="number" min="0" max="100" v-model.number="node.settings[settingKey('GradientLocationTwo')]"></div><div class="pb-form-group"><label class="pb-form-label">Gradient Type</label><select class="pb-select" v-model="node.settings[settingKey('GradientType')]"><option value="linear">Linear</option><option value="radial">Radial</option></select></div><div v-if="node.settings[settingKey('GradientType')]!=='radial'" class="pb-form-group"><label class="pb-form-label">Angle</label><input class="pb-input" type="number" min="0" max="360" v-model.number="node.settings[settingKey('GradientAngle')]"></div><div v-else class="pb-form-group"><label class="pb-form-label">Position</label><select class="pb-select" v-model="node.settings[settingKey('GradientPosition')]"><option value="center center">Center Center</option><option value="center top">Center Top</option><option value="center bottom">Center Bottom</option><option value="left center">Left Center</option><option value="right center">Right Center</option></select></div></div><div v-else class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[colorKey]"></div></div>`};
const BoxShadowControl={props:{prefix:String,suffix:{type:String,default:''},node:Object},computed:{enabledKey(){return this.settingKey('Enabled');}},methods:{settingKey(field){return this.prefix+field+this.suffix;}},template:`<details class="pb-accordion-inline-editor"><summary><span>Box Shadow</span><span class="pb-accordion-edit-button">Edit</span></summary><div class="pb-accordion-inline-editor__body"><div class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">Enable</label><div class="pb-toggle-wrap"><input :id="prefix+suffix+'-shadow'" type="checkbox" class="pb-toggle" v-model="node.settings[enabledKey]"><label :for="prefix+suffix+'-shadow'"></label></div></div><div v-if="node.settings[enabledKey]" class="pb-accordion-shadow-options"><div class="pb-form-group pb-accordion-color-control"><label class="pb-form-label">Color</label><input class="pb-input coloris pb-coloris-input" v-model="node.settings[settingKey('Color')]"></div><div class="pb-accordion-shadow-grid"><label v-for="field in ['X','Y','Blur','Spread']" :key="field"><span>{{field}}</span><input class="pb-input" v-model="node.settings[settingKey(field)]"></label></div><div class="pb-form-group pb-toggle-label-row"><label class="pb-form-label mb-0">Inset</label><div class="pb-toggle-wrap"><input :id="prefix+suffix+'-shadow-inset'" type="checkbox" class="pb-toggle" v-model="node.settings[settingKey('Inset')]"><label :for="prefix+suffix+'-shadow-inset'"></label></div></div></div></div></details>`};
const IconSourceControl={props:{label:String,roleName:String,node:Object,editor:Object},computed:{sourceKey(){return this.roleName+'IconSource';},classKey(){return this.roleName+'IconClass';},svgKey(){return this.roleName+'IconSvg';}},methods:{clear(){this.node.settings[this.sourceKey]='none';},chooseLibrary(){this.node.settings[this.sourceKey]='library';this.editor.openAccordionIconLibrary(this.roleName,this.node);},chooseSvg(){this.editor.chooseAccordionSvg(this.roleName,this.node);}},template:`<div class="pb-form-group pb-accordion-inline-control pb-accordion-icon-source-control"><div class="pb-label-row"><label class="pb-form-label mb-0">{{label}}</label><div class="pb-accordion-icon-actions" role="group" :aria-label="label+' icon source'"><button type="button" class="pb-accordion-icon-mode-btn" :class="{active:node.settings[sourceKey]==='none'}" title="None" aria-label="None" @click="clear"><i class="fas fa-ban"></i></button><button type="button" class="pb-accordion-icon-mode-btn" :class="{active:node.settings[sourceKey]==='svg'}" title="Upload SVG" aria-label="Upload SVG" @click="chooseSvg"><i class="fas fa-upload"></i></button><button type="button" class="pb-accordion-icon-mode-btn" :class="{active:node.settings[sourceKey]==='library'}" title="Icon Library" aria-label="Icon Library" @click="chooseLibrary"><i :class="node.settings[classKey]||'far fa-circle'"></i></button></div></div></div>`};

export default {
	name: 'AccordionWidgetSettings',
	components: { ResponsiveChoice, ResponsiveDimensionControl, ResponsiveBoxControl, BackgroundControl, BoxShadowControl, IconSourceControl },
	props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
	data() {
		return {
			expandedItemId: this.editor.accordionRuntimeForNode(this.node).editingItemId || null,
			styleState: 'normal', titleState: 'normal', iconState: 'normal', activeTextEffect: '',
			styleStates: [{value:'normal',label:'Normal'},{value:'hover',label:'Hover'},{value:'active',label:'Active'}],
			borderTypes: ['default','none','solid','double','dotted','dashed','groove'],
			itemPositionOptions: [{value:'start',label:'Start',icon:'fas fa-align-left'},{value:'center',label:'Center',icon:'fas fa-align-center'},{value:'end',label:'End',icon:'fas fa-align-right'},{value:'stretch',label:'Stretch',icon:'fas fa-align-justify'}],
			iconPositionOptions: [{value:'start',label:'Start',icon:'fas fa-arrow-left'},{value:'end',label:'End',icon:'fas fa-arrow-right'}],
		};
	},
	computed: {
		editingItem() { return this.editor.accordionEditingItem(this.node); },
	},
	methods: {
		capitalize(value) { return String(value||'').charAt(0).toUpperCase()+String(value||'').slice(1); },
		stateKey(base, state = this.styleState) { return `${base}${this.capitalize(state)}`; },
		isItemExpanded(item) { return this.expandedItemId === item.id; },
		toggleAccordionItem(item) { if(this.expandedItemId===item.id){this.expandedItemId=null;return;}this.editor.selectAccordionItem(this.node,item.id);this.expandedItemId=item.id; },
		addAccordionItem() { this.editor.addAccordionItem(this.node);this.expandedItemId=this.editor.accordionEditingItem(this.node)?.id||null; },
		duplicateAccordionItem(itemId) { this.editor.duplicateAccordionItem(this.node,itemId);this.expandedItemId=this.editor.accordionEditingItem(this.node)?.id||null; },
		removeAccordionItem(itemId) { this.editor.removeAccordionItem(this.node,itemId);this.expandedItemId=this.editor.accordionEditingItem(this.node)?.id||null; },
	},
};
</script>
