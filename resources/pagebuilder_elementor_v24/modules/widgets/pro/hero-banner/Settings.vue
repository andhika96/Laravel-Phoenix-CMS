<template>
    <div class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--pro pb-hero-settings">
        <div v-if="editor.settingsTab==='content'" class="pb-tab-content">
            <details class="pb-collapsible" open>
                <summary>Content Behavior</summary>
                <div class="pb-collapsible-body">
                    <div class="pb-btn-group pb-hero-mode">
                        <button type="button" class="pb-seg-btn" :class="{active:settings.positioningMode==='grouped'}" @click="settings.positioningMode='grouped'"><i class="fas fa-object-group"></i> Grouped</button>
                        <button type="button" class="pb-seg-btn" :class="{active:settings.positioningMode==='independent'}" @click="settings.positioningMode='independent'"><i class="fas fa-layer-group"></i> Independent</button>
                    </div>
                    <p class="pb-form-note">{{settings.positioningMode==='grouped'?'Title, Subtitle, and Button Group follow one flow.':'Each content block has its own responsive position.'}}</p>
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Content</summary>
                <div class="pb-collapsible-body">
                    <div class="pb-form-group"><label class="pb-form-label">Title</label><input class="pb-input" v-model="settings.title"></div>
                    <div class="pb-form-group"><label class="pb-form-label">Title HTML Tag</label><select class="pb-select" v-model="settings.titleTag"><option v-for="tag in ['h1','h2','h3','h4','h5','h6','div']" :key="tag" :value="tag">{{tag.toUpperCase()}}</option></select></div>
                    <ToggleField :id="'hero-show-title-'+node.id" label="Show Title" v-model="settings.showTitle" />
                    <div class="pb-form-group"><label class="pb-form-label">Subtitle</label><input class="pb-input" v-model="settings.subtitle"></div>
                    <div class="pb-form-group"><label class="pb-form-label">Subtitle HTML Tag</label><select class="pb-select" v-model="settings.subtitleTag"><option v-for="tag in ['p','div','span']" :key="tag" :value="tag">{{tag.toUpperCase()}}</option></select></div>
                    <ToggleField :id="'hero-show-subtitle-'+node.id" label="Show Subtitle" v-model="settings.showSubtitle" />
                    <ToggleField :id="'hero-show-buttons-'+node.id" label="Show Button Group" v-model="settings.showButtons" />

                    <div v-if="settings.positioningMode==='grouped'" class="pb-hero-order">
                        <div class="pb-label-row"><label class="pb-form-label mb-0">Content Order</label></div>
                        <div v-for="(key,index) in settings.contentOrder" :key="key" class="pb-hero-order__row">
                            <i class="fas fa-grip-vertical"></i><strong>{{contentLabel(key)}}</strong>
                            <button type="button" :class="{'is-visible':contentVisible(key)}" :aria-label="(contentVisible(key)?'Hide ':'Show ')+contentLabel(key)" :aria-pressed="contentVisible(key)" @click="toggleContentVisibility(key)"><i :class="contentVisible(key)?'fas fa-eye':'fas fa-eye-slash'"></i></button>
                            <button type="button" :disabled="index===0" :aria-label="'Move '+contentLabel(key)+' up'" @click="moveContent(index,-1)"><i class="fas fa-arrow-up"></i></button>
                            <button type="button" :disabled="index===settings.contentOrder.length-1" :aria-label="'Move '+contentLabel(key)+' down'" @click="moveContent(index,1)"><i class="fas fa-arrow-down"></i></button>
                        </div>
                    </div>
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Buttons</summary>
                <div class="pb-collapsible-body">
                    <div class="pb-label-row"><label class="pb-form-label mb-0">Button Items</label><span class="pb-form-hint">{{settings.buttons.length}} / 3</span></div>
                    <div class="pb-hero-buttons">
                        <div v-for="(button,index) in settings.buttons" :key="button.id" class="pb-hero-button-item" :class="{'is-open':expandedButtonId===button.id}">
                            <div class="pb-hero-button-item__header" role="button" tabindex="0" :aria-expanded="expandedButtonId===button.id?'true':'false'" @click="toggleButton(button.id)" @keydown.enter.prevent="toggleButton(button.id)" @keydown.space.prevent="toggleButton(button.id)">
                                <i class="fas pb-hero-button-item__disclosure" :class="expandedButtonId===button.id?'fa-chevron-up':'fa-chevron-down'" aria-hidden="true"></i><i class="fas fa-grip-vertical"></i><strong>{{button.text||'Button '+(index+1)}}</strong>
                                <button type="button" title="Duplicate Button" :disabled="settings.buttons.length>=3" @click.stop="duplicateButton(index)"><i class="far fa-copy"></i></button>
                                <button type="button" title="Remove Button" :disabled="settings.buttons.length<=1" @click.stop="removeButton(index)"><i class="fas fa-times"></i></button>
                            </div>
                            <div v-if="expandedButtonId===button.id" class="pb-hero-button-item__body">
                                <div class="pb-form-group"><label class="pb-form-label">Button Text</label><input class="pb-input" v-model="button.text"></div>
                                <div class="pb-form-group"><label class="pb-form-label">Action Type</label><select class="pb-select" v-model="button.actionType"><option value="link">Link</option><option value="video_popup">Video Popup</option><option value="image_popup">Image Popup</option></select></div>
                                <div v-if="button.actionType==='link'" class="pb-form-group"><label class="pb-form-label">Link</label><component :is="editor.linkControl" :url="button.linkUrl||''" :target="button.linkTarget||''" :nofollow="Boolean(button.linkNofollow)" :custom-attributes="button.linkCustomAttributes||[]" @update:url="button.linkUrl=$event" @update:target="button.linkTarget=$event" @update:nofollow="button.linkNofollow=$event" @update:customAttributes="button.linkCustomAttributes=$event" /></div>
                                <template v-if="button.actionType==='video_popup'">
                                    <div class="pb-form-group"><label class="pb-form-label">Video Source</label><select class="pb-select" v-model="button.videoSource"><option value="youtube">YouTube</option><option value="vimeo">Vimeo</option><option value="dailymotion">Dailymotion</option><option value="self_hosted">Self Hosted</option></select></div>
                                    <div class="pb-form-group"><label class="pb-form-label">Video URL</label><div class="pb-media-field" :class="{ 'has-action': button.videoSource === 'self_hosted' }"><input class="pb-input" v-model="button.videoUrl"><button v-if="button.videoSource==='self_hosted'" type="button" @click="editor.chooseMedia(button,'videoUrl','Paste video URL')"><i class="fas fa-folder-open"></i></button></div></div>
                                </template>
                                <template v-if="button.actionType==='image_popup'">
                                    <div class="pb-form-group"><label class="pb-form-label">Image Source</label><select class="pb-select" v-model="button.imageSource"><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select></div>
                                    <div class="pb-form-group"><label class="pb-form-label">Image URL</label><div class="pb-media-field" :class="{ 'has-action': button.imageSource !== 'url' }"><input class="pb-input" v-model="button.imageUrl"><button v-if="button.imageSource!=='url'" type="button" @click="editor.chooseMedia(button,'imageUrl')"><i class="fas fa-folder-open"></i></button></div></div>
                                    <div class="pb-form-group"><label class="pb-form-label">Image Alt</label><input class="pb-input" v-model="button.imageAlt"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="pb-hero-add" :disabled="settings.buttons.length>=3" @click="addButton"><i class="fas fa-plus"></i> Add Button</button>
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Responsive Position</summary>
                <div class="pb-collapsible-body">
                    <div v-if="settings.positioningMode==='independent'" class="pb-btn-group pb-hero-targets"><button v-for="target in ['title','subtitle','buttons']" :key="target" type="button" class="pb-seg-btn" :class="{active:selectedTarget===target}" @click="selectedTarget=target">{{contentLabel(target)}}</button></div>
                    <position-editor :settings="settings" :editor="editor" :target="positionTarget" :node-id="node.id" :label="contentLabel(positionTarget)" />
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Button Group Layout</summary>
                <div class="pb-collapsible-body">
                    <responsive-choice label="Direction" :control-id="'hero-banner-button-direction-'+node.id" :editor="editor" :model-value="responsiveValue('buttonDirection','row')" :options="buttonDirectionOptions" @update:model-value="setResponsive('buttonDirection',$event)" />
                    <responsive-choice label="Alignment" :control-id="'hero-banner-button-align-'+node.id" :editor="editor" :model-value="buttonAlignmentValue" :options="alignmentOptions" @update:model-value="setButtonAlignment($event)" />
                    <button type="button" class="pb-hero-follow-alignment" :class="{active:buttonAlignmentMode==='inherit'}" @click="followButtonAlignment"><i class="fas fa-link"></i><span>Follow Content Alignment</span></button>
                    <size-control label="Gap" base="buttonGap" :control-id="'hero-banner-button-gap-'+node.id" fallback="10px" :node="node" :editor="editor" :min="0" :max="100" :allowed-units="['px','em','rem']" />
                    <responsive-choice label="Wrap Buttons" :control-id="'hero-banner-button-wrap-'+node.id" :editor="editor" :model-value="Boolean(responsiveValue('buttonWrap',true))" :options="booleanOptions" @update:model-value="setResponsive('buttonWrap',$event)" />
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Responsive Media</summary>
                <div class="pb-collapsible-body">
                    <DeviceTabs :editor="editor" /><InheritanceBadge :label="inheritanceLabel('imageUrl')" />
                    <div class="pb-form-group"><label class="pb-form-label">Image Source</label><select class="pb-select" :value="responsiveValue('imageSource','ckfinder')" @change="setResponsive('imageSource',$event.target.value)"><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select></div>
                    <div class="pb-form-group"><label class="pb-form-label">Image URL</label><div class="pb-media-field" :class="{ 'has-action': responsiveValue('imageSource','ckfinder') !== 'url' }"><input class="pb-input" :value="responsiveValue('imageUrl','')" @input="setResponsive('imageUrl',$event.target.value)"><button v-if="responsiveValue('imageSource','ckfinder')!=='url'" type="button" @click="chooseHeroMedia"><i class="fas fa-folder-open"></i></button></div><div v-if="responsiveValue('imageSource','ckfinder')==='url'" class="pb-form-note">Remote hosts may block hotlinking.</div></div>
                    <div class="pb-form-group"><label class="pb-form-label">Alt Text</label><input class="pb-input" :value="responsiveValue('imageAlt','')" @input="setResponsive('imageAlt',$event.target.value)"></div>
                    <div class="pb-form-group"><label class="pb-form-label">Image Layout</label><select class="pb-select" :value="responsiveValue('imageLayout','cover')" @change="setResponsive('imageLayout',$event.target.value)"><option value="cover">Cover (fixed height)</option><option value="natural">Natural Image Ratio</option></select></div>
                    <template v-if="responsiveValue('imageLayout','cover')==='cover'"><div class="pb-hero-grid"><div class="pb-form-group"><label class="pb-form-label">Object Fit</label><select class="pb-select" :value="responsiveValue('objectFit','cover')" @change="setResponsive('objectFit',$event.target.value)"><option value="cover">Cover</option><option value="contain">Contain</option><option value="fill">Fill</option></select></div><div class="pb-form-group"><label class="pb-form-label">Object Position</label><select class="pb-select" :value="responsiveValue('objectPosition','center center')" @change="setResponsive('objectPosition',$event.target.value)"><option v-for="value in objectPositions" :key="value" :value="value">{{value}}</option></select></div></div></template>
                    <p v-else class="pb-form-note">Uses the selected image's original ratio and ignores Minimum Height.</p>
                    <button v-if="currentDevice!=='desktop'" type="button" class="pb-reset-btn" @click="resetOverride(['imageSource','imageUrl','imageAlt','imageLayout','objectFit','objectPosition'])"><i class="fas fa-undo"></i> Reset Override</button>
                </div>
            </details>
        </div>

        <div v-if="editor.settingsTab==='style'" class="pb-tab-content">
            <details class="pb-collapsible" open><summary>Layout</summary><div class="pb-collapsible-body"><DeviceTabs :editor="editor"/><div class="pb-form-group"><label class="pb-form-label">Minimum Height</label><input class="pb-input" :value="responsiveValue('minHeight','500px')" @input="setResponsive('minHeight',$event.target.value)"></div><div class="pb-form-group"><label class="pb-form-label">Content Gap</label><input class="pb-input" :value="responsiveValue('contentGap','14px')" @input="setResponsive('contentGap',$event.target.value)"></div><ColorField label="Overlay Color" v-model="settings.overlayColor"/></div></details>
            <details class="pb-collapsible" open><summary>Title & Subtitle</summary><div class="pb-collapsible-body"><ColorField label="Title Color" v-model="settings.titleColor"/><div class="pb-form-group"><label class="pb-form-label">Title Size Mode</label><select class="pb-select" v-model="settings.titleFontSizeMode"><option value="auto">Auto by HTML tag</option><option value="custom">Custom</option></select></div><div class="pb-hero-grid"><div class="pb-form-group"><label class="pb-form-label">Title Size</label><input class="pb-input" :value="responsiveValue('titleFontSize','48px')" @input="setResponsive('titleFontSize',$event.target.value)"></div><div class="pb-form-group"><label class="pb-form-label">Title Weight</label><input class="pb-input" v-model="settings.titleFontWeight"></div></div><ColorField label="Subtitle Color" v-model="settings.subtitleColor"/><div class="pb-hero-grid"><div class="pb-form-group"><label class="pb-form-label">Subtitle Size</label><input class="pb-input" :value="responsiveValue('subtitleFontSize','22px')" @input="setResponsive('subtitleFontSize',$event.target.value)"></div><div class="pb-form-group"><label class="pb-form-label">Subtitle Weight</label><input class="pb-input" v-model="settings.subtitleFontWeight"></div></div></div></details>
            <details class="pb-collapsible" open><summary>Buttons</summary><div class="pb-collapsible-body"><ColorField label="Text Color" v-model="settings.buttonTextColor"/><ColorField label="Background" v-model="settings.buttonBackground"/><ColorField label="Hover Text Color" v-model="settings.buttonTextColorHover"/><ColorField label="Hover Background" v-model="settings.buttonBackgroundHover"/><div class="pb-hero-grid"><div class="pb-form-group"><label class="pb-form-label">Border Radius</label><input class="pb-input" v-model="settings.buttonRadius"></div><div class="pb-form-group"><label class="pb-form-label">Horizontal Padding</label><input class="pb-input" v-model="settings.buttonPaddingX"></div></div></div></details>
            <details class="pb-collapsible"><summary>Popup</summary><div class="pb-collapsible-body"><ColorField label="Modal Background" v-model="settings.modalBackground"/><ColorField label="UI Color" v-model="settings.modalUiColor"/><ColorField label="UI Hover Color" v-model="settings.modalUiHoverColor"/><div class="pb-form-group"><label class="pb-form-label">Video Width</label><input class="pb-input" v-model="settings.modalVideoWidth"></div></div></details>
        </div>

        <div v-if="editor.settingsTab==='advanced'" class="pb-tab-content"><component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" :elementor-choices="true" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings,$event)" @clear-media="editor.clearMedia(node.settings,$event)" /></div>
    </div>
</template>

<script>
const DeviceTabs={props:['editor'],template:`<div class="pb-hero-devices"><button v-for="device in editor.responsiveDevices" :key="device.value" type="button" class="pb-seg-btn" :class="{active:editor.responsiveDevice===device.value}" :aria-pressed="editor.responsiveDevice===device.value" @click="editor.setResponsiveDevice(device.value)"><i :class="device.icon"></i><span>{{device.label}}</span></button></div>`};
const ResponsiveMenu={props:['editor','id'],template:`<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" :aria-label="'Responsive: '+editor.responsiveDeviceLabel()" :title="'Responsive: '+editor.responsiveDeviceLabel()" @click.stop="editor.openControlResponsiveMenu(id)"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id+'-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(id,device.value)"><i :class="device.icon"></i><span>{{editor.deviceOptionLabel(device)}}</span></button></div></div>`};
const InheritanceBadge={props:['label'],template:`<div class="pb-hero-inheritance">{{label}}</div>`};
const ColorField={props:['label','modelValue'],emits:['update:modelValue'],template:`<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><input class="pb-input coloris pb-coloris-input" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)"></div>`};
const ToggleField={props:{id:String,label:String,modelValue:Boolean},emits:['update:modelValue'],template:`<div class="pb-form-group pb-toggle-label-row pb-widget-settings__compact-toggle"><label class="pb-form-label mb-0" :for="id">{{label}}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="id" class="pb-toggle" type="checkbox" :checked="modelValue" @change="$emit('update:modelValue',$event.target.checked)"><label :for="id"></label></div><span class="pb-toggle-state">{{modelValue?'On':'Off'}}</span></div></div>`};
const SizeControl={components:{ResponsiveMenu},props:{label:String,base:String,modeKey:String,controlId:String,fallback:String,node:Object,target:Object,editor:Object,min:{type:Number,default:0},max:{type:Number,default:null},allowedUnits:{type:Array,default:()=>[]}},computed:{controlNode(){return this.target?{settings:this.target}:this.node;},options(){const units=this.allowedUnits.length?this.allowedUnits:this.editor.sizeControlUnits;return{fallback:this.fallback,min:this.min,max:this.max,allowedUnits:units,fallbackUnit:units[0]||'px'};},maxValue(){const unitMax=this.editor.sizeControlMax(this.controlNode,this.base,this.fallback,this.options);return this.max===null?unitMax:Math.min(this.max,unitMax);}},methods:{markCustom(){if(this.modeKey&&this.controlNode?.settings)this.controlNode.settings[this.modeKey]='custom';}},template:`<div class="pb-form-group pb-hero-slider-size-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="maxValue" :step="editor.sizeControlStep(controlNode,base,fallback,options)" :value="editor.sizeControlDisplayValue(controlNode,base,fallback,options)" @input="editor.onSizeControlInput(controlNode,base,$event,options);markCustom()"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :min="min" :max="maxValue" :value="editor.sizeControlDisplayValue(controlNode,base,fallback,options)" @input="editor.onSizeControlInput(controlNode,base,$event,options);markCustom()"><select class="pb-mini-unit" :value="editor.sizeControlUnit(controlNode,base,fallback,options)" @change="editor.setSizeControlUnit(controlNode,base,$event.target.value,options);markCustom()"><option v-for="unit in options.allowedUnits" :key="unit" :value="unit">{{unit}}</option></select></div></div></div>`};
const ResponsiveChoice={components:{ResponsiveMenu},props:{label:String,controlId:String,editor:Object,modelValue:[String,Boolean],options:Array},emits:['update:modelValue'],template:`<div class="pb-form-group pb-hero-slider-responsive-choice"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-btn-group"><button v-for="option in options" :key="String(option.value)" type="button" class="pb-seg-btn" :class="{active:modelValue===option.value}" @click="$emit('update:modelValue',option.value)"><i v-if="option.icon" :class="option.icon"></i><span>{{option.label}}</span></button></div></div>`};
const PositionEditor = {
    components:{ResponsiveMenu},
    props:{settings:Object,editor:Object,target:String,nodeId:[String,Number],label:String},
    data(){return{anchors:['top-left','top-center','top-right','center-left','center','center-right','bottom-left','bottom-center','bottom-right']};},
    computed:{currentDevice(){return this.editor.responsiveDevice||'desktop';},controlId(){return 'hero-banner-position-'+this.nodeId+'-'+this.target;}},
    methods:{
        suffix(){return this.currentDevice==='mobile'?'Mobile':(this.currentDevice==='tablet'?'Tablet':'');},
        key(base){return this.target+base+this.suffix();},
        value(base,fallback){const device=this.currentDevice;const root=this.target+base;const keys=device==='mobile'?[root+'Mobile',root+'Tablet',root]:device==='tablet'?[root+'Tablet',root]:[root];for(const key of keys){if(this.settings[key]!==''&&this.settings[key]!=null)return this.settings[key];}return fallback;},
        set(base,value){this.settings[this.key(base)]=value;},
        setAnchor(anchor){const coordinates = {'top-left':[0,0],'top-center':[50,0],'top-right':[100,0],'center-left':[0,50],center:[50,50],'center-right':[100,50],'bottom-left':[0,100],'bottom-center':[50,100],'bottom-right':[100,100]}[anchor];if(!coordinates)return;this.set('Anchor', anchor);this.set('X', coordinates[0] + '%');this.set('Y', coordinates[1] + '%');this.set('Width', (coordinates[0] === 50 ? 70 : 50) + '%');this.set('Align', coordinates[0] === 0 ? 'left' : (coordinates[0] === 100 ? 'right' : 'center'));},
        number(base,fallback){const parsed=Number.parseFloat(this.value(base,fallback));return Number.isFinite(parsed)?parsed:fallback;},
        setNumber(base,event,min=0){const parsed=Number(event.target.value);this.set(base,Math.min(100,Math.max(min,Number.isFinite(parsed)?parsed:min))+'%');},
    },
    template:`<div class="pb-hero-slider-position-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Editing: {{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-form-group"><label class="pb-form-label">Anchor Point</label><div class="pb-hero-slider-anchor"><button v-for="anchor in anchors" :key="anchor" type="button" :class="{active:value('Anchor','center-left')===anchor}" :title="anchor" :aria-label="anchor" @click="setAnchor(anchor)"><span></span></button></div></div><div class="pb-hero-slider-grid"><div class="pb-form-group"><label class="pb-form-label">Horizontal (X)</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="100" :value="number('X',17)" @input="setNumber('X',$event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="100" :value="number('X',17)" @input="setNumber('X',$event)"><span>%</span></div></div></div><div class="pb-form-group"><label class="pb-form-label">Vertical (Y)</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="100" :value="number('Y',54)" @input="setNumber('Y',$event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="100" :value="number('Y',54)" @input="setNumber('Y',$event)"><span>%</span></div></div></div></div><div class="pb-form-group"><label class="pb-form-label">Content Width</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="10" max="100" :value="number('Width',32)" @input="setNumber('Width',$event,10)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="10" max="100" :value="number('Width',32)" @input="setNumber('Width',$event,10)"><span>%</span></div></div></div><div class="pb-form-group"><label class="pb-form-label">Alignment</label><div class="pb-btn-group"><button v-for="align in ['left','center','right']" :key="align" type="button" class="pb-seg-btn" :class="{active:value('Align','left')===align}" @click="set('Align',align)">{{align}}</button></div></div></div>`,
};

export default{
    name:'HeroBannerSettings',components:{DeviceTabs,PositionEditor,ResponsiveChoice,SizeControl,ColorField,ToggleField},props:{node:{type:Object,required:true},editor:{type:Object,required:true}},
    data(){return{expandedButtonId:'',selectedTarget:'title',objectPositions:['left top','left center','left bottom','center top','center center','center bottom','right top','right center','right bottom'],buttonDirectionOptions:[{value:'row',label:'Horizontal',icon:'fas fa-arrows-alt-h'},{value:'column',label:'Vertical',icon:'fas fa-arrows-alt-v'}],alignmentOptions:[{value:'left',label:'Left'},{value:'center',label:'Center'},{value:'right',label:'Right'}],booleanOptions:[{value:true,label:'Wrap'},{value:false,label:'No Wrap'}]};},
    computed:{settings(){return this.node.settings;},currentDevice(){return ['tablet','mobile'].includes(this.editor.responsiveDevice)?this.editor.responsiveDevice:'desktop';},positionTarget(){return this.settings.positioningMode==='grouped'?'group':this.selectedTarget;},buttonAlignmentMode(){return this.responsiveValue('buttonAlignMode','inherit')==='custom'?'custom':'inherit';},buttonAlignmentValue(){const value=this.buttonAlignmentMode==='inherit'?this.inheritedButtonAlignment():this.responsiveValue('buttonAlign','left');return ['left','center','right'].includes(value)?value:'left';}},
    created(){if(!Array.isArray(this.settings.contentOrder))this.settings.contentOrder=['title','subtitle','buttons'];if(!Array.isArray(this.settings.buttons)||!this.settings.buttons.length)this.settings.buttons=[this.newButton(0)];this.expandedButtonId=this.settings.buttons[0].id;},
    methods:{
        contentLabel(key){return{group:'Content Group',title:'Title',subtitle:'Subtitle',buttons:'Button Group'}[key]||key;},
        visibilityKey(key){return{title:'showTitle',subtitle:'showSubtitle',buttons:'showButtons'}[key];},
        contentVisible(key){return this.settings[this.visibilityKey(key)]!==false;},
        toggleContentVisibility(key){const setting=this.visibilityKey(key);if(setting)this.settings[setting]=!this.contentVisible(key);},
        suffix(device=this.currentDevice){return device==='tablet'?'Tablet':device==='mobile'?'Mobile':'';},
        activeKey(base){return base+this.suffix();},
        responsiveValue(base,fallback=''){const s=this.settings,d=this.currentDevice;const keys=d==='mobile'?[base+'Mobile',base+'Tablet',base]:d==='tablet'?[base+'Tablet',base]:[base];for(const key of keys){if(s[key]!==''&&s[key]!=null)return s[key];}return fallback;},
        setResponsive(base,value){this.settings[this.activeKey(base)]=value;if(base==='titleFontSize')this.settings.titleFontSizeMode='custom';},
        inheritedButtonAlignment(){const target=this.settings.positioningMode==='grouped'?'group':'buttons';const value=this.responsiveValue(target+'Align','left');return ['left','center','right'].includes(value)?value:'left';},
        setButtonAlignment(value){const next=['left','center','right'].includes(value)?value:'left';this.setResponsive('buttonAlignMode','custom');this.setResponsive('buttonAlign',next);},
        followButtonAlignment(){this.setResponsive('buttonAlignMode','inherit');},
        inheritanceLabel(base){if(this.currentDevice==='desktop'||this.settings[this.activeKey(base)]!==''&&this.settings[this.activeKey(base)]!=null)return'Custom override';if(this.currentDevice==='mobile'&&this.settings[base+'Tablet']!==''&&this.settings[base+'Tablet']!=null)return'Inherited from Tablet';return'Inherited from Desktop';},
        resetOverride(bases){if(this.currentDevice==='desktop')return;bases.forEach(base=>{this.settings[this.activeKey(base)]='';});},
        moveContent(index,direction){const target=index+direction;if(target<0||target>=this.settings.contentOrder.length)return;const next=[...this.settings.contentOrder];[next[index],next[target]]=[next[target],next[index]];this.settings.contentOrder=next;},
        newButton(index,source={}){const item={text:`Button ${index+1}`,actionType:'link',linkUrl:'',linkTarget:'',linkNofollow:false,linkCustomAttributes:[],videoSource:'youtube',videoUrl:'',imageSource:'ckfinder',imageUrl:'',imageAlt:'',...source};item.id=String(item.id||`hero-button-${Date.now()}-${index}`);return item;},
        toggleButton(id){this.expandedButtonId=this.expandedButtonId===id?'':id;},
        addButton(){if(this.settings.buttons.length>=3)return;const item=this.newButton(this.settings.buttons.length);this.settings.buttons.push(item);this.expandedButtonId=item.id;},
        duplicateButton(index){if(this.settings.buttons.length>=3)return;const item=this.newButton(this.settings.buttons.length,{...this.settings.buttons[index],id:'',text:(this.settings.buttons[index].text||'Button')+' Copy',linkCustomAttributes:[...(this.settings.buttons[index].linkCustomAttributes||[])]});this.settings.buttons.splice(index+1,0,item);this.expandedButtonId=item.id;},
        removeButton(index){if(this.settings.buttons.length<=1)return;this.settings.buttons.splice(index,1);this.expandedButtonId=this.settings.buttons[0].id;},
        chooseHeroMedia(){this.setResponsive('imageSource','ckfinder');this.editor.chooseMedia(this.settings,this.activeKey('imageUrl'));},
    },
};
</script>

<style scoped>
.pb-hero-mode,.pb-hero-targets{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}.pb-hero-targets{grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:12px}.pb-hero-mode button,.pb-hero-targets button{text-transform:capitalize}.pb-hero-mode .pb-seg-btn{gap:5px}.pb-hero-order,.pb-hero-buttons{display:grid;gap:7px;margin-top:12px}.pb-hero-order__row,.pb-hero-button-item__header{display:grid;grid-template-columns:16px 16px minmax(0,1fr) 28px 28px;align-items:center;gap:4px;min-height:34px;padding:4px 6px;border:1px solid #e4e7ec;border-radius:6px;background:#fff}.pb-hero-order__row{grid-template-columns:18px 1fr repeat(3,28px)}.pb-hero-order__row strong,.pb-hero-button-item__header strong{min-width:0;overflow:hidden;color:#475467;font-size:11px;text-overflow:ellipsis;white-space:nowrap}.pb-hero-button-item__disclosure{display:grid;width:16px;height:26px;place-items:center;color:#8795ad;font-size:9px}.pb-hero-button-item__header>i.fa-grip-vertical{font-size:12px;color:#6979f8}.pb-hero-order__row button,.pb-hero-button-item__header button{width:26px;height:26px;border:1px solid #d7dfed;border-radius:5px;color:#526987;background:#fff}.pb-hero-order__row button.is-visible{color:#5b5ce2;background:#eef0ff}.pb-hero-order__row button:disabled,.pb-hero-button-item__header button:disabled{cursor:not-allowed;opacity:.35}.pb-hero-button-item.is-open{border:1px solid #b8c0ff;border-radius:7px}.pb-hero-button-item__header{cursor:pointer;border:0}.pb-hero-button-item__body{padding:10px;border-top:1px solid #e4e7ec}.pb-hero-add{width:100%;margin-top:9px;padding:9px;border:1px dashed #aeb7c6;border-radius:6px;color:#5b6cff;background:#fafbff;font-size:11px}.pb-hero-add:disabled{cursor:not-allowed;opacity:.45}.pb-hero-devices{display:grid;grid-template-columns:repeat(3,1fr);gap:3px;padding:3px;margin-bottom:10px;border:1px solid var(--line);border-radius:9px;background:var(--soft)}:deep(.pb-hero-devices button){min-height:27px;border:0!important;border-radius:6px!important;color:#788397;background:transparent;font-size:9px}:deep(.pb-hero-devices button.active){color:var(--brand);background:#fff;box-shadow:var(--shadow-sm)}:deep(.pb-hero-devices span){margin-left:4px}.pb-hero-inheritance{width:max-content;margin:0 0 10px auto;padding:4px 7px;border-radius:999px;color:#26795e;background:#e8f7f0;font-size:8px}.pb-hero-anchor{width:108px;display:grid;grid-template-columns:repeat(3,36px);margin:auto;overflow:hidden;border:1px solid #d9dfeb;border-radius:7px}.pb-hero-anchor button{height:32px;border:0;border-right:1px solid #e5e9f1;border-bottom:1px solid #e5e9f1;background:#fbfcfe}.pb-hero-anchor button:nth-child(3n){border-right:0}.pb-hero-anchor button:nth-last-child(-n+3){border-bottom:0}.pb-hero-anchor span{width:5px;height:5px;display:block;margin:auto;border-radius:50%;background:#a8b1c1}.pb-hero-anchor button.active{background:#eef0ff}.pb-hero-anchor button.active span{width:8px;height:8px;background:#5b6cff}.pb-hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:9px}:deep(.pb-hero-number){display:grid;grid-template-columns:1fr 34px}:deep(.pb-hero-number input){border-radius:6px 0 0 6px!important}:deep(.pb-hero-number span){display:grid;place-items:center;border:1px solid #d0d5dd;border-left:0;border-radius:0 6px 6px 0;color:#667085;font-size:10px;background:#f9fafb}.pb-media-field,:deep(.pb-hero-color){display:flex}.pb-media-field.has-action input{border-radius:6px 0 0 6px!important}:deep(.pb-hero-color .pb-input){border-radius:6px 0 0 6px!important}.pb-media-field button,:deep(.pb-hero-color input[type=color]){width:36px;border:1px solid #d0d5dd;border-left:0;border-radius:0 6px 6px 0;background:#f9fafb}:deep(.pb-hero-color input[type=color]){width:36px!important;min-width:36px;flex:0 0 36px;height:34px!important;padding:3px;order:2}.pb-reset-btn{width:100%;padding:8px;border:1px solid #d0d5dd;border-radius:6px;color:#667085;background:#fff;font-size:10px}
</style>

<style scoped>
.pb-hero-settings :deep(.pb-hero-devices button){gap:5px}
.pb-hero-settings :deep(.pb-hero-devices span){margin-left:0}
.pb-hero-settings :deep(.pb-hero-slider-position-control){display:grid;gap:8px}
.pb-hero-settings :deep(.pb-hero-slider-grid){display:grid;grid-template-columns:minmax(0,1fr);gap:8px}
.pb-hero-settings :deep(.pb-hero-slider-grid .pb-range-value-row){grid-template-columns:minmax(0,1fr) 104px;gap:6px;align-items:center}
.pb-hero-settings :deep(.pb-hero-slider-grid .pb-range){width:100%;min-width:0}
.pb-hero-settings :deep(.pb-hero-slider-anchor){display:grid;grid-template-columns:repeat(3,32px);width:max-content;margin:0 auto;overflow:hidden;border:1px solid var(--line);border-radius:7px}
.pb-hero-settings :deep(.pb-hero-slider-anchor button){width:32px;height:29px;padding:0;border:0;border-right:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff;cursor:pointer}
.pb-hero-settings :deep(.pb-hero-slider-anchor button:nth-child(3n)){border-right:0}
.pb-hero-settings :deep(.pb-hero-slider-anchor button:nth-last-child(-n+3)){border-bottom:0}
.pb-hero-settings :deep(.pb-hero-slider-anchor span){display:block;width:5px;height:5px;margin:auto;border-radius:50%;background:#a8b1c1}
.pb-hero-settings :deep(.pb-hero-slider-anchor button.active){background:#eef0ff}
.pb-hero-settings :deep(.pb-hero-slider-anchor button.active span){width:8px;height:8px;background:#5b6cff}
.pb-hero-settings :deep(.pb-hero-slider-responsive-choice .pb-btn-group){width:100%}
.pb-hero-settings :deep(.pb-hero-slider-responsive-choice .pb-btn-group .pb-seg-btn){display:inline-flex;height:27px!important;min-height:27px!important;align-items:center;justify-content:center;gap:4px;padding:0 6px!important;font-size:10px!important;line-height:1}
.pb-hero-settings :deep(.pb-hero-slider-responsive-choice .pb-control-device-item){display:flex;align-items:center;gap:3px}
.pb-hero-settings :deep(.pb-hero-follow-alignment){display:flex;width:100%;min-height:27px;align-items:center;justify-content:center;gap:5px;margin-top:6px;padding:0 8px;border:1px solid var(--line);border-radius:6px;color:var(--muted);background:#fff;font-size:10px;line-height:1;cursor:pointer}
.pb-hero-settings :deep(.pb-hero-follow-alignment.active){border-color:#c7ccff;color:var(--brand);background:#eef0ff}
.pb-hero-settings :deep(.pb-value-with-unit){display:flex;min-width:0}
.pb-hero-settings :deep(.pb-value-with-unit .pb-input){min-width:0;border-radius:6px 0 0 6px!important}
.pb-hero-settings :deep(.pb-value-with-unit>span){display:grid;min-width:30px;place-items:center;border:1px solid var(--line);border-left:0;border-radius:0 6px 6px 0;color:var(--muted);font-size:9px;background:#fff}
</style>
