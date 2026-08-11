<template>
	<div class="el-widget-image pb-image" :class="hoverAnimationClass" :style="rootStyle" data-basic-image>
		<figure class="pb-image__figure">
			<a v-if="linkUrl" class="pb-image__link" :href="linkUrl" :target="linkTarget" :rel="linkRel" v-bind="safeCustomAttributes" :data-basic-image-lightbox="usesLightbox ? 'true' : null" data-pb-interactive="true" @click.stop="onImageClick">
				<img v-if="imageUrl" class="pb-image__img" :src="imageUrl" :alt="alt" :style="imageStyle">
				<div v-else class="pb-image__empty" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div>
			</a>
			<template v-else><img v-if="imageUrl" class="pb-image__img" :src="imageUrl" :alt="alt" :style="imageStyle"><div v-else class="pb-image__empty" role="img" aria-label="Choose an image"><i class="far fa-image" aria-hidden="true"></i></div></template>
			<figcaption v-if="caption" class="pb-image__caption" :style="captionStyle">{{ caption }}</figcaption>
		</figure>
		<div v-if="lightboxUrl" class="pb-image-lightbox" role="dialog" aria-modal="true" @click.self="lightboxUrl=''" @keydown.esc="lightboxUrl=''" data-pb-interactive="true"><button type="button" aria-label="Close lightbox" @click="lightboxUrl=''">&times;</button><img :src="lightboxUrl" :alt="alt"></div>
	</div>
</template>

<script>
const BORDER_TYPES=Object.freeze(['solid','double','dotted','dashed','groove']);
const OBJECT_FITS=Object.freeze(['fill','cover','contain','scale-down']);
const ALIGNMENTS=Object.freeze(['left','center','right']);
export default{
	name:'BasicImage',props:{item:{type:Object,required:true},responsiveDevice:{type:String,default:'desktop'},dynamicContext:{type:Object,default:()=>({})}},
	data(){return{resolvedImageUrl:'',renditionRequest:0,lightboxUrl:''};},
	computed:{
		settings(){return this.item.settings||{};},
		rawImageUrl(){return this.safeImageUrl(this.resolveDynamicValue('src',this.settings.src||''));},
		imageUrl(){return this.resolvedImageUrl||this.rawImageUrl;},
		imageRenditionKey(){return `${this.rawImageUrl}|${this.settings.imageResolution||'large'}|${this.customDimension('customImageWidth')||''}|${this.customDimension('customImageHeight')||''}`;},
		alt(){return String(this.settings.alt||'');},
		caption(){if(this.settings.captionType==='custom')return String(this.resolveDynamicValue('customCaption',this.settings.customCaption||''));if(this.settings.captionType==='attachment')return String(this.settings.attachmentCaption||'');return'';},
		usesLightbox(){return this.settings.linkType==='media'&&this.settings.lightbox!=='no'&&!!this.imageUrl;},
		linkUrl(){if(this.settings.linkType==='media')return this.imageUrl;if(this.settings.linkType==='custom')return this.safeLinkUrl(this.resolveDynamicValue('customLinkUrl',this.settings.customLinkUrl||''));return'';},
		linkTarget(){return this.settings.linkType==='custom'&&this.settings.linkTarget==='_blank'?'_blank':null;},
		linkRel(){if(this.settings.linkType!=='custom')return null;const rel=[];if(this.linkTarget==='_blank')rel.push('noopener','noreferrer');if(this.settings.linkNofollow)rel.push('nofollow');return[...new Set(rel)].join(' ')||null;},
		safeCustomAttributes(){if(this.settings.linkType!=='custom')return{};const output={};const allowed=/^(?:aria-[a-z0-9_-]+|data-[a-z0-9_-]+|title|download|hreflang)$/i;(Array.isArray(this.settings.linkCustomAttributes)?this.settings.linkCustomAttributes:[]).forEach(attribute=>{const key=String(attribute?.key||attribute?.name||'').trim();if(allowed.test(key))output[key]=String(attribute?.value??'');});return output;},
		alignment(){const value=String(this.responsiveValue('alignment','center'));return ALIGNMENTS.includes(value)?value:'center';},
		objectFit(){const value=String(this.responsiveValue('objectFit','default'));return OBJECT_FITS.includes(value)?value:'';},
		rootStyle(){return{textAlign:this.alignment,'--pb-image-hover-filter':this.filterCss(this.settings.imageHoverFilter),'--pb-image-hover-opacity':String(this.opacity(this.settings.imageHoverOpacity,1)),'--pb-image-hover-duration':`${this.duration(this.settings.imageHoverTransition)}s`};},
		imageStyle(){const border=BORDER_TYPES.includes(this.settings.imageBorderType)?this.settings.imageBorderType:'none';return{width:this.cssSize(this.responsiveValue('width','100%'),'100%'),maxWidth:this.cssSize(this.responsiveValue('maxWidth','100%'),'100%'),height:this.cssSize(this.responsiveValue('height','auto'),'auto'),objectFit:this.objectFit,objectPosition:String(this.responsiveValue('objectPosition','center center')),display:'block',borderStyle:border,borderWidth:border==='none'?'0':this.cssSize(this.responsiveValue('imageBorderWidth','1px'),'1px'),borderColor:this.safeColor(this.settings.imageBorderColor,'#000000'),borderRadius:this.cssSize(this.responsiveValue('imageBorderRadius','0px'),'0px'),boxShadow:this.boxShadow,filter:this.filterCss(this.settings.imageNormalFilter),opacity:String(this.opacity(this.settings.imageNormalOpacity,1))};},
		boxShadow(){if(!this.settings.imageBoxShadowEnabled)return'none';return`${this.cssSize(this.settings.imageBoxShadowX,'0px')} ${this.cssSize(this.settings.imageBoxShadowY,'0px')} ${this.cssSize(this.settings.imageBoxShadowBlur,'10px')} ${this.cssSize(this.settings.imageBoxShadowSpread,'0px')} ${this.safeColor(this.settings.imageBoxShadowColor,'rgba(0,0,0,.25)')}`;},
		captionStyle(){return{color:this.safeColor(this.settings.captionColor,'inherit'),backgroundColor:this.safeColor(this.settings.captionBackgroundColor,'transparent'),textAlign:this.captionAlignment,fontFamily:String(this.settings.captionFontFamily||'inherit'),fontSize:this.cssSize(this.responsiveValue('captionFontSize','16px'),'16px'),fontWeight:String(this.settings.captionFontWeight||'400'),lineHeight:this.cssSize(this.responsiveValue('captionLineHeight','1.5em'),'1.5em'),letterSpacing:this.cssSize(this.responsiveValue('captionLetterSpacing','0px'),'0px'),wordSpacing:this.cssSize(this.responsiveValue('captionWordSpacing','0px'),'0px'),textTransform:this.settings.captionTextTransform||'none',fontStyle:this.settings.captionFontStyle||'normal',textDecoration:this.settings.captionTextDecoration||'none',textShadow:this.safeShadow(this.settings.captionTextShadow),marginTop:this.cssSize(this.responsiveValue('captionSpacing','8px'),'8px')};},
		captionAlignment(){const value=String(this.responsiveValue('captionAlignment','center'));return['left','center','right','justify'].includes(value)?value:'center';},
		hoverAnimationClass(){const value=String(this.settings.imageHoverAnimation||'none').replace(/[^a-z0-9-]/g,'');return value&&value!=='none'?`pb-image--hover-${value}`:'';},
	},
	watch:{imageRenditionKey:{immediate:true,handler(){this.resolveImageRendition();}}},
	methods:{
		responsiveValue(base,fallback=''){const settings=this.settings;const device=String(this.responsiveDevice||'desktop').toLowerCase();const keys=device==='mobile'?[base+'Mobile',base+'Tablet',base]:device==='tablet'?[base+'Tablet',base]:[base];for(const key of keys){const value=settings[key];if(value!==''&&value!=null)return value;}return fallback;},
		resolveDynamicValue(field,fallback){const binding=String(this.settings.dynamicBindings?.[field]||'');return binding&&Object.prototype.hasOwnProperty.call(this.dynamicContext,binding)?this.dynamicContext[binding]:fallback;},
		safeImageUrl(value){const raw=String(value||'').trim();if(!raw||raw.startsWith('//'))return'';return/^(?:https?:|data:image\/(?:png|gif|jpe?g|webp);base64,|\/)/i.test(raw)?raw:'';},
		safeLinkUrl(value){const raw=String(value||'').trim();if(!raw||raw.startsWith('//'))return'';return/^(?:https?:|mailto:|tel:|\/|#)/i.test(raw)?raw:'';},
		cssSize(value,fallback=''){const raw=String(value??'').trim();return/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw)||raw==='auto'?raw:fallback;},
		safeColor(value,fallback){const raw=String(value||'').trim();return raw&&/^[#a-z0-9(),.%\s-]+$/i.test(raw)?raw:fallback;},safeShadow(value){const raw=String(value||'none').trim();return/^[#a-z0-9(),.%\s-]+$/i.test(raw)?raw:'none';},
		opacity(value,fallback){const number=Number(value);return Number.isFinite(number)?Math.min(1,Math.max(0,number)):fallback;},duration(value){const number=Number(value);return Number.isFinite(number)?Math.min(10,Math.max(0,number)):0.3;},
		filterCss(filters){const source=filters&&typeof filters==='object'?filters:{};const clamp=(value,min,max,fallback)=>Math.min(max,Math.max(min,Number(value??fallback)||0));return`blur(${clamp(source.blur,0,100,0)}px) brightness(${clamp(source.brightness,0,200,100)}%) contrast(${clamp(source.contrast,0,200,100)}%) saturate(${clamp(source.saturation,0,200,100)}%) hue-rotate(${clamp(source.hue,0,360,0)}deg)`;},
		customDimension(key){const raw=String(this.settings[key]??'').trim();if(!raw)return null;const value=Math.round(Number(raw));return Number.isFinite(value)?Math.min(4096,Math.max(1,value)):null;},
		onImageClick(event){if(!this.usesLightbox)return;if(event)event.preventDefault();this.lightboxUrl=this.imageUrl;},
		async resolveImageRendition(){const request=++this.renditionRequest;const original=this.rawImageUrl;this.resolvedImageUrl=original;if(!original||typeof window==='undefined')return;const size=String(this.settings.imageResolution||'large');const endpoint=String(window.PAGE_BUILDER_ELEMENTOR_V23_CONTEXT?.imageRenditionUrl||'');if(!endpoint||!window.axios||size==='full')return;const params={url:original,size};if(size==='custom'){const width=this.customDimension('customImageWidth');const height=this.customDimension('customImageHeight');if(!width&&!height)return;if(width)params.width=width;if(height)params.height=height;}try{const response=await window.axios.get(endpoint,{params});if(request===this.renditionRequest)this.resolvedImageUrl=this.safeImageUrl(response.data?.url)||original;}catch(_){if(request===this.renditionRequest)this.resolvedImageUrl=original;}},
	},
};
</script>

<style scoped>
.pb-image{width:100%;min-width:0}.pb-image__figure{display:inline-flex;max-width:100%;margin:0;flex-direction:column}.pb-image__link{display:inline-block;max-width:100%;color:inherit}.pb-image__img{transition:filter var(--pb-image-hover-duration,.3s) ease,opacity var(--pb-image-hover-duration,.3s) ease,transform var(--pb-image-hover-duration,.3s) ease}.pb-image:hover .pb-image__img{filter:var(--pb-image-hover-filter);opacity:var(--pb-image-hover-opacity)}.pb-image__caption{max-width:100%;padding:0}.pb-image__empty{width:min(100%,640px);aspect-ratio:16/9;display:grid;place-items:center;background:#eef1f4;color:#98a2b3;font-size:44px}.pb-image-lightbox{position:fixed;inset:0;z-index:99999;display:grid;place-items:center;padding:40px;background:rgba(0,0,0,.86)}.pb-image-lightbox img{max-width:90vw;max-height:85vh;object-fit:contain}.pb-image-lightbox button{position:absolute;top:14px;right:20px;border:0;background:transparent;color:#fff;font-size:38px}.pb-image--hover-grow:hover .pb-image__img,.pb-image--hover-pulse-grow:hover .pb-image__img{transform:scale(1.08)}.pb-image--hover-shrink:hover .pb-image__img,.pb-image--hover-pulse-shrink:hover .pb-image__img{transform:scale(.94)}.pb-image--hover-rotate:hover .pb-image__img{transform:rotate(6deg)}.pb-image--hover-grow-rotate:hover .pb-image__img{transform:scale(1.06) rotate(6deg)}.pb-image--hover-float:hover .pb-image__img,.pb-image--hover-bob:hover .pb-image__img{transform:translateY(-7px)}.pb-image--hover-sink:hover .pb-image__img,.pb-image--hover-hang:hover .pb-image__img{transform:translateY(7px)}.pb-image--hover-skew:hover .pb-image__img,.pb-image--hover-skew-forward:hover .pb-image__img{transform:skewX(-8deg)}.pb-image--hover-skew-backward:hover .pb-image__img{transform:skewX(8deg)}.pb-image--hover-pulse:hover .pb-image__img,.pb-image--hover-push:hover .pb-image__img,.pb-image--hover-pop:hover .pb-image__img,.pb-image[class*="pb-image--hover-bounce"]:hover .pb-image__img,.pb-image[class*="pb-image--hover-wobble"]:hover .pb-image__img,.pb-image[class*="pb-image--hover-buzz"]:hover .pb-image__img{animation:pb-image-pulse .48s ease both}@keyframes pb-image-pulse{35%{transform:scale(1.08)}70%{transform:scale(.98)}}@media(prefers-reduced-motion:reduce){.pb-image__img{animation:none!important;transition:none!important}}
</style>
