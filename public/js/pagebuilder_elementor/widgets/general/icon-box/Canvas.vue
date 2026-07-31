<template>
	<div class="pb-icon-box" :class="rootClass" :style="boxStyle">
		<div class="pb-icon-box__media">
			<component :is="safeLinkUrl ? 'a' : 'span'" class="pb-icon-box__icon-link" v-bind="linkAttributes">
				<span class="pb-icon-box__icon" :style="iconStyle"><i :class="safeIconClass" :style="iconGlyphStyle" aria-hidden="true"></i></span>
			</component>
		</div>
		<div class="pb-icon-box__content">
			<component :is="safeLinkUrl ? 'a' : 'span'" v-if="title" class="pb-icon-box__title-link" v-bind="linkAttributes"><component :is="safeTitleTag" class="pb-icon-box__title" :style="titleStyle">{{ title }}</component></component>
			<p v-if="description" class="pb-icon-box__description" :style="descriptionStyle">{{ description }}</p>
		</div>
	</div>
</template>

<script>
const TITLE_TAGS=Object.freeze(['h1','h2','h3','h4','h5','h6','div','span','p']);
const TITLE_TAG_FONT_SIZES=Object.freeze({h1:'40px',h2:'34px',h3:'29px',h4:'24px',h5:'20px',h6:'16px',div:'29px',span:'29px',p:'29px'});
const POSITIONS=Object.freeze(['top','left','right']);
const ALIGNMENTS=Object.freeze(['left','center','right','justify']);
export default {
	name:'GeneralIconBox', props:{item:{type:Object,required:true},responsiveDevice:{type:String,default:'desktop'},dynamicContext:{type:Object,default:()=>({})}},
	computed:{
		settings(){return this.item.settings||{};},
		title(){return String(this.resolveDynamicValue('title',this.settings.title||''));},
		description(){return String(this.resolveDynamicValue('description',this.settings.description||''));},
		safeTitleTag(){const tag=String(this.settings.titleTag||'h3').toLowerCase();return TITLE_TAGS.includes(tag)?tag:'h3';},
		automaticTitleFontSize(){return TITLE_TAG_FONT_SIZES[this.safeTitleTag]||'29px';},
		view(){const value=String(this.settings.view||'default').toLowerCase();return ['default','stacked','framed'].includes(value)?value:'default';},
		shape(){const value=String(this.settings.shape||'circle').toLowerCase();return ['circle','rounded','square'].includes(value)?value:'circle';},
		position(){const value=String(this.responsiveValue('iconPosition','top')).toLowerCase();return POSITIONS.includes(value)?value:'top';},
		alignment(){const value=String(this.responsiveValue('alignment','center')).toLowerCase();return ALIGNMENTS.includes(value)?value:'center';},
		safeIconClass(){const raw=String(this.settings.iconClass||'').trim();return /^(?:fas|far|fab|fal|fad) fa-[a-z0-9-]+$/.test(raw)?raw:'far fa-star';},
		safeLinkUrl(){const url=String(this.settings.linkUrl||'').trim();return /^(?:https?:|mailto:|tel:)/i.test(url)||url.startsWith('/')||url.startsWith('#')?url:'';},
		linkAttributes(){const attrs={};if(this.safeLinkUrl)attrs.href=this.safeLinkUrl;if(this.settings.linkTarget==='_blank'){attrs.target='_blank';attrs.rel='noopener noreferrer';}if(this.settings.linkNofollow)attrs.rel=[attrs.rel,'nofollow'].filter(Boolean).join(' ');Object.assign(attrs,this.safeCustomAttributes);return attrs;},
		safeCustomAttributes(){const out={};(Array.isArray(this.settings.linkCustomAttributes)?this.settings.linkCustomAttributes:[]).forEach(attribute=>{const key=String(attribute?.key||attribute?.name||'').trim();if(/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i.test(key))out[key]=String(attribute?.value??'');});return out;},
		rootClass(){return [`pb-icon-box--position-${this.position}`,`is-view-${this.view}`,`is-shape-${this.shape}`,this.settings.hoverAnimation&&this.settings.hoverAnimation!=='none'?`pb-icon-box--hover-${this.settings.hoverAnimation}`:'',this.safeClassTokens(this.settings.cssClass)].filter(Boolean);},
		boxStyle(){return{display:'flex',flexDirection:this.position==='top'?'column':(this.position==='right'?'row-reverse':'row'),alignItems:this.position==='top'?this.flexAlignment(this.alignment):'center',textAlign:this.alignment,'--pb-icon-box-icon-spacing':this.cssSize(this.responsiveValue('iconSpacing','15px'),'15px'),'--pb-icon-box-content-spacing':this.cssSize(this.responsiveValue('contentSpacing','0px'),'0px'),'--pb-icon-box-media-justify':this.position==='top'?this.flexAlignment(this.alignment):'center','--pb-icon-primary-hover':this.safeColor(this.settings.primaryColorHover,this.safeColor(this.settings.primaryColor,'#69727d')),'--pb-icon-secondary-hover':this.safeColor(this.settings.secondaryColorHover,this.safeColor(this.settings.secondaryColor,'#ffffff'))};},
		iconStyle(){const primary=this.safeColor(this.settings.primaryColor,'#69727d');const secondary=this.safeColor(this.settings.secondaryColor,'#ffffff');const radius=this.iconRadius();return{fontSize:this.cssSize(this.responsiveValue('iconSize','50px'),'50px'),padding:this.view==='default'?'0':this.cssSize(this.responsiveValue('iconPadding','0px'),'0px'),color:this.view==='stacked'?secondary:primary,backgroundColor:this.view==='stacked'?primary:'transparent',borderStyle:this.view==='framed'?'solid':'none',borderColor:this.view==='framed'?primary:'transparent',borderWidth:this.view==='framed'?this.iconSides('iconBorderWidth','1px'):'0',borderRadius:radius};},
		iconGlyphStyle(){return{transform:`rotate(${this.cssSize(this.responsiveValue('iconRotate','0deg'),'0deg')})`};},
		titleStyle(){return this.typographyStyle('title',{fontSize:this.settings.titleFontSizeMode === 'custom'?this.cssSize(this.responsiveValue('titleFontSize','29px'),'29px'):this.automaticTitleFontSize,color:this.safeColor(this.settings.titleColor,'inherit'),WebkitTextStrokeWidth:this.cssSize(this.responsiveValue('titleTextStrokeWidth','0px'),'0px'),WebkitTextStrokeColor:this.safeColor(this.settings.titleTextStrokeColor,'currentColor'),textShadow:this.safeShadow(this.settings.titleTextShadow)});},
		descriptionStyle(){return this.typographyStyle('description',{color:this.safeColor(this.settings.descriptionColor,'inherit'),textShadow:this.safeShadow(this.settings.descriptionTextShadow)});},
	},
	methods:{
		resolveDynamicValue(field,fallback){const binding=String(this.settings.dynamicBindings?.[field]||'');return binding&&Object.prototype.hasOwnProperty.call(this.dynamicContext,binding)&&this.dynamicContext[binding]!=null?this.dynamicContext[binding]:fallback;},
		responsiveValue(base,fallback=''){const device=['tablet','mobile'].includes(this.responsiveDevice)?this.responsiveDevice:'desktop';const keys=device==='mobile'?[base+'Mobile',base+'Tablet',base]:(device==='tablet'?[base+'Tablet',base]:[base]);for(const key of keys){const value=this.settings[key];if(value!==''&&value!=null)return value;}return fallback;},
		flexAlignment(value){return value==='left'?'flex-start':value==='right'?'flex-end':value==='justify'?'stretch':'center';},
		cssSize(value,fallback=''){const raw=String(value??'').trim();return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh|deg)?$/i.test(raw)?raw:fallback;},
		safeColor(value,fallback='inherit'){const raw=String(value||'').trim();return raw&&/^[#a-z0-9(),.%\s-]+$/i.test(raw)?raw:fallback;},
		safeShadow(value){const raw=String(value||'').trim();return raw&&/^[#a-z0-9(),.%\s-]+$/i.test(raw)?raw:'none';},
		safeClassTokens(value){return String(value||'').split(/\s+/).map(token=>token.replace(/^\.+/,'').replace(/[^a-zA-Z0-9_-]/g,'')).filter(Boolean).join(' ');},
		iconSides(base,fallback){return ['Top','Right','Bottom','Left'].map(side=>this.cssSize(this.responsiveValue(base+side,fallback),fallback)).join(' ');},
		iconRadius(){const custom=this.iconSides('iconBorderRadius','0px');if(custom!=='0px 0px 0px 0px')return custom;return this.shape==='circle'?'50%':this.shape==='rounded'?'12%':'0';},
		typographyStyle(prefix,additions={}){const title=prefix==='title';return{fontFamily:String(this.settings[prefix+'FontFamily']||'inherit'),fontSize:this.cssSize(this.responsiveValue(prefix+'FontSize',title?'29px':'16px'),title?'29px':'16px'),fontWeight:String(this.settings[prefix+'FontWeight']||'400'),lineHeight:this.cssSize(this.responsiveValue(prefix+'LineHeight',title?'1.2em':'1.5em'),title?'1.2em':'1.5em'),letterSpacing:this.cssSize(this.responsiveValue(prefix+'LetterSpacing','0px'),'0px'),wordSpacing:this.cssSize(this.responsiveValue(prefix+'WordSpacing','0px'),'0px'),textTransform:['none','uppercase','lowercase','capitalize'].includes(this.settings[prefix+'TextTransform'])?this.settings[prefix+'TextTransform']:'none',fontStyle:['normal','italic','oblique'].includes(this.settings[prefix+'FontStyle'])?this.settings[prefix+'FontStyle']:'normal',textDecoration:['none','underline','overline','line-through'].includes(this.settings[prefix+'TextDecoration'])?this.settings[prefix+'TextDecoration']:'none',...additions};},
	},
};
</script>

<style scoped>
.pb-icon-box{width:100%;min-width:0}.pb-icon-box__media{display:flex;flex:0 0 auto;justify-content:var(--pb-icon-box-media-justify,center)}.pb-icon-box__icon-link,.pb-icon-box__title-link{color:inherit;text-decoration:none}.pb-icon-box__icon{display:inline-flex;align-items:center;justify-content:center;line-height:1;transition:color .3s,background-color .3s,border-color .3s,transform .3s}.pb-icon-box__icon i{width:1em;height:1em;display:flex;align-items:center;justify-content:center;transition:transform .3s}.pb-icon-box__content{min-width:0;flex:1 1 auto}.pb-icon-box__title{margin:0 0 var(--pb-icon-box-content-spacing,0)}.pb-icon-box__description{margin:0}.pb-icon-box--position-top .pb-icon-box__media{width:100%;margin-bottom:var(--pb-icon-box-icon-spacing,15px)}.pb-icon-box--position-left .pb-icon-box__media{margin-right:var(--pb-icon-box-icon-spacing,15px)}.pb-icon-box--position-right .pb-icon-box__media{margin-left:var(--pb-icon-box-icon-spacing,15px)}.pb-icon-box.is-view-default:hover .pb-icon-box__icon,.pb-icon-box.is-view-framed:hover .pb-icon-box__icon{color:var(--pb-icon-primary-hover)}.pb-icon-box.is-view-framed:hover .pb-icon-box__icon{border-color:var(--pb-icon-primary-hover)}.pb-icon-box.is-view-stacked:hover .pb-icon-box__icon{background-color:var(--pb-icon-primary-hover);color:var(--pb-icon-secondary-hover)}.pb-icon-box--hover-grow:hover .pb-icon-box__icon{transform:scale(1.1)}.pb-icon-box--hover-shrink:hover .pb-icon-box__icon{transform:scale(.9)}.pb-icon-box--hover-rotate:hover .pb-icon-box__icon{transform:rotate(8deg)}.pb-icon-box--hover-grow-rotate:hover .pb-icon-box__icon{transform:scale(1.1) rotate(8deg)}.pb-icon-box--hover-float:hover .pb-icon-box__icon,.pb-icon-box--hover-bob:hover .pb-icon-box__icon{transform:translateY(-8px)}.pb-icon-box--hover-sink:hover .pb-icon-box__icon,.pb-icon-box--hover-hang:hover .pb-icon-box__icon{transform:translateY(8px)}.pb-icon-box--hover-skew:hover .pb-icon-box__icon,.pb-icon-box--hover-skew-forward:hover .pb-icon-box__icon{transform:skewX(-10deg)}.pb-icon-box--hover-skew-backward:hover .pb-icon-box__icon{transform:skewX(10deg)}.pb-icon-box[class*="pb-icon-box--hover-pulse"]:hover .pb-icon-box__icon,.pb-icon-box[class*="pb-icon-box--hover-bounce"]:hover .pb-icon-box__icon,.pb-icon-box[class*="pb-icon-box--hover-wobble"]:hover .pb-icon-box__icon,.pb-icon-box[class*="pb-icon-box--hover-buzz"]:hover .pb-icon-box__icon{animation:pb-icon-box-pulse .55s ease-in-out}.pb-icon-box--hover-push:hover .pb-icon-box__icon{animation:pb-icon-box-push .35s ease}.pb-icon-box--hover-pop:hover .pb-icon-box__icon{animation:pb-icon-box-pop .35s ease}@keyframes pb-icon-box-pulse{50%{transform:scale(1.12)}}@keyframes pb-icon-box-push{50%{transform:scale(.82)}100%{transform:scale(1)}}@keyframes pb-icon-box-pop{50%{transform:scale(1.2)}100%{transform:scale(1)}}@media(prefers-reduced-motion:reduce){.pb-icon-box__icon{animation:none!important;transition:none!important}}
</style>
