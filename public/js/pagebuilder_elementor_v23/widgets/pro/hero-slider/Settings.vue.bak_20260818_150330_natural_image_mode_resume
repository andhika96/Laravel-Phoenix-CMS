<template>
    <div class="pb-widget-settings pb-widget-settings--general-new pb-widget-settings--pro pb-hero-slider-settings">
        <div v-if="editor.settingsTab==='content'" class="pb-tab-content">
            <details class="pb-collapsible" open>
                <summary>Slides</summary>
                <div class="pb-collapsible-body">
                    <div class="pb-label-row"><label class="pb-form-label mb-0">Media Slides</label><span class="pb-form-hint">{{ slides.length }} / 30</span></div>
                    <div class="pb-hero-slider-slides">
                        <div v-for="(slide,index) in slides" :key="slide.id" class="pb-hero-slider-slide" :class="{'is-open':expandedSlideId===slide.id}">
                            <div class="pb-hero-slider-slide__header" role="button" tabindex="0" :aria-expanded="expandedSlideId===slide.id?'true':'false'" @click="toggleSlide(slide.id)" @keydown.enter.prevent="toggleSlide(slide.id)" @keydown.space.prevent="toggleSlide(slide.id)">
                                <i class="fas pb-hero-slider-slide__disclosure" :class="expandedSlideId===slide.id?'fa-chevron-up':'fa-chevron-down'" aria-hidden="true"></i>
                                <span class="pb-hero-slider-slide__title"><i class="fas fa-grip-vertical"></i><strong>{{ slide.title || (slide.mediaType==='video' ? 'Video' : 'Image') }} {{ index+1 }}</strong></span>
                                <button type="button" title="Move slide up" :disabled="index===0" @click.stop="moveSlide(index,-1)"><i class="fas fa-arrow-up"></i></button>
                                <button type="button" title="Move slide down" :disabled="index===slides.length-1" @click.stop="moveSlide(index,1)"><i class="fas fa-arrow-down"></i></button>
                                <button type="button" title="Duplicate slide" :disabled="slides.length>=30" @click.stop="duplicateSlide(index)"><i class="far fa-copy"></i></button>
                                <button type="button" title="Remove slide" :disabled="slides.length<=1" @click.stop="removeSlide(index)"><i class="fas fa-times"></i></button>
                            </div>
                            <div v-if="expandedSlideId===slide.id" class="pb-hero-slider-slide__body">
                                <details class="pb-hero-slider-subsection" open>
                                    <summary>Media</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <div class="pb-form-group"><label class="pb-form-label">Media Type</label><div class="pb-btn-group"><button type="button" class="pb-seg-btn" :class="{active:slide.mediaType==='image'}" @click="slide.mediaType='image'"><i class="far fa-image"></i> Image</button><button type="button" class="pb-seg-btn" :class="{active:slide.mediaType==='video'}" @click="slide.mediaType='video'"><i class="fas fa-video"></i> Video</button></div></div>
                                        <template v-if="slide.mediaType==='image'">
                                            <div class="pb-form-group"><label class="pb-form-label">Image Source</label><select class="pb-select" v-model="slide.imageSource"><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select></div>
                                            <MediaField label="Image URL" :value="slide.imageUrl" :has-picker="slide.imageSource!=='url'" @input="slide.imageUrl=$event" @choose="chooseMedia(slide,'imageUrl','Choose image')" />
                                            <MediaField label="Tablet Image URL" :value="slide.imageUrlTablet" :has-picker="slide.imageSource!=='url'" @input="slide.imageUrlTablet=$event" @choose="chooseMedia(slide,'imageUrlTablet','Choose tablet image')" />
                                            <MediaField label="Mobile Image URL" :value="slide.imageUrlMobile" :has-picker="slide.imageSource!=='url'" @input="slide.imageUrlMobile=$event" @choose="chooseMedia(slide,'imageUrlMobile','Choose mobile image')" />
                                            <div class="pb-form-group"><label class="pb-form-label">Alt Text</label><input class="pb-input" v-model="slide.imageAlt"></div>
                                            <responsive-select label="Image Layout" :control-id="'hero-slider-image-layout-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'imageLayout','cover')" :options="imageLayoutOptions" @update:model-value="setSlideResponsive(slide,'imageLayout',$event)" />
                                            <template v-if="slideResponsiveValue(slide,'imageLayout','cover')==='cover'"><responsive-select label="Object Fit" :control-id="'hero-slider-object-fit-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'objectFit','cover')" :options="objectFitOptions" @update:model-value="setSlideResponsive(slide,'objectFit',$event)" />
                                            <responsive-select label="Object Position" :control-id="'hero-slider-object-position-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'objectPosition','center center')" :options="objectPositionOptions" @update:model-value="setSlideResponsive(slide,'objectPosition',$event)" /></template>
                                            <p v-else class="pb-form-note">Uses the selected image's original ratio and ignores Minimum Height.</p>
                                        </template>
                                        <template v-else>
                                            <div class="pb-form-group"><label class="pb-form-label">Video Provider</label><select class="pb-select" v-model="slide.videoProvider"><option value="self_hosted">Local / Direct HTML5</option><option value="youtube">YouTube</option><option value="vimeo">Vimeo</option><option value="dailymotion">Dailymotion</option><option value="embed">Generic Embed</option></select></div>
                                            <MediaField label="Video URL" :value="slide.videoUrl" :has-picker="slide.videoProvider==='self_hosted'" @input="slide.videoUrl=$event" @choose="chooseMedia(slide,'videoUrl','Choose video')" />
                                            <MediaField label="Poster Image" :value="slide.videoPoster" :has-picker="true" @input="slide.videoPoster=$event" @choose="chooseMedia(slide,'videoPoster','Choose poster image')" />
                                            <MediaField label="Tablet Poster URL" :value="slide.videoPosterTablet" :has-picker="true" @input="slide.videoPosterTablet=$event" @choose="chooseMedia(slide,'videoPosterTablet','Choose tablet poster')" />
                                            <MediaField label="Mobile Poster URL" :value="slide.videoPosterMobile" :has-picker="true" @input="slide.videoPosterMobile=$event" @choose="chooseMedia(slide,'videoPosterMobile','Choose mobile poster')" />
                                            <div class="pb-form-group"><label class="pb-form-label">Video Autoplay</label><select class="pb-select" v-model="slide.videoAutoplay"><option value="inherit">Inherit global</option><option value="on">On</option><option value="off">Off</option></select></div>
                                            <div class="pb-form-group"><label class="pb-form-label">Aspect Ratio</label><select class="pb-select" v-model="slide.videoAspectRatio"><option v-for="ratio in ratios" :key="ratio" :value="ratio">{{ratio}}</option></select></div>
                                            <ToggleField :id="'hero-slider-loop-'+slide.id" label="Loop Video" v-model="slide.videoLoop" />
                                            <ToggleField :id="'hero-slider-muted-'+slide.id" label="Muted by default" v-model="slide.videoMuted" />
                                            <ToggleField :id="'hero-slider-resume-'+slide.id" label="Resume position" v-model="slide.videoResume" />
                                        </template>
                                    </div>
                                </details>

                                <details class="pb-hero-slider-subsection" open>
                                    <summary>Content Behavior</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <div class="pb-btn-group pb-hero-slider-mode"><button type="button" class="pb-seg-btn" :class="{active:slide.positioningMode==='grouped'}" @click="slide.positioningMode='grouped'"><i class="fas fa-object-group"></i><span>Grouped</span></button><button type="button" class="pb-seg-btn" :class="{active:slide.positioningMode==='independent'}" @click="slide.positioningMode='independent'"><i class="fas fa-layer-group"></i><span>Independent</span></button></div>
                                        <div class="pb-form-note pb-hero-slider-behavior-note">{{slide.positioningMode==='independent'?'Title, Subtitle, and Button Group have separate responsive positions.':'Title, Subtitle, and Button Group follow one content flow.'}}</div>
                                        <div class="pb-form-group"><label class="pb-form-label">Slide Title</label><input class="pb-input" v-model="slide.title"></div>
                                        <div class="pb-form-group"><label class="pb-form-label">Title HTML Tag</label><select class="pb-select" v-model="slide.titleTag"><option v-for="tag in ['h1','h2','h3','h4','h5','h6','div']" :key="tag" :value="tag">{{tag.toUpperCase()}}</option></select></div>
                                        <ToggleField :id="'hero-slider-show-title-'+slide.id" label="Show Title" v-model="slide.showTitle" />
                                        <div class="pb-form-group"><label class="pb-form-label">Slide Subtitle</label><textarea class="pb-textarea" rows="2" v-model="slide.subtitle"></textarea></div>
                                        <div class="pb-form-group"><label class="pb-form-label">Subtitle HTML Tag</label><select class="pb-select" v-model="slide.subtitleTag"><option v-for="tag in ['p','div','span']" :key="tag" :value="tag">{{tag.toUpperCase()}}</option></select></div>
                                        <ToggleField :id="'hero-slider-show-subtitle-'+slide.id" label="Show Subtitle" v-model="slide.showSubtitle" />
                                        <ToggleField :id="'hero-slider-show-buttons-'+slide.id" label="Show Button Group" v-model="slide.showButtons" />
                                        <div v-if="slide.positioningMode==='grouped'" class="pb-hero-slider-content-order"><div class="pb-label-row"><label class="pb-form-label mb-0">Content Order</label></div><div v-for="(key,orderIndex) in slide.contentOrder" :key="key" class="pb-hero-slider-order-row"><i class="fas fa-grip-vertical"></i><strong>{{contentLabel(key)}}</strong><button type="button" :class="{'is-visible':contentVisible(slide,key)}" :aria-label="(contentVisible(slide,key)?'Hide ':'Show ')+contentLabel(key)" @click="toggleContentVisibility(slide,key)"><i :class="contentVisible(slide,key)?'fas fa-eye':'fas fa-eye-slash'"></i></button><button type="button" :disabled="orderIndex===0" @click="moveContent(slide,orderIndex,-1)"><i class="fas fa-arrow-up"></i></button><button type="button" :disabled="orderIndex===slide.contentOrder.length-1" @click="moveContent(slide,orderIndex,1)"><i class="fas fa-arrow-down"></i></button></div></div>
                                    </div>
                                </details>

                                <details class="pb-hero-slider-subsection" open>
                                    <summary>Buttons</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <div class="pb-label-row"><label class="pb-form-label mb-0">Button Items</label><span class="pb-form-hint">{{ (slide.buttons||[]).length }} / 3</span></div>
                                        <div class="pb-hero-slider-buttons-editor">
                                            <div v-for="(button,buttonIndex) in (slide.buttons||[])" :key="button.id||buttonIndex" class="pb-hero-slider-button-item" :class="{'is-open':expandedButtonId===button.id}">
                                                <div class="pb-hero-slider-button-item__header" role="button" tabindex="0" :aria-expanded="expandedButtonId===button.id?'true':'false'" @click="toggleButton(button.id)" @keydown.enter.prevent="toggleButton(button.id)" @keydown.space.prevent="toggleButton(button.id)"><i class="fas fa-grip-vertical"></i><strong>{{button.text||'Button '+(buttonIndex+1)}}</strong><button type="button" title="Duplicate Button" :disabled="slide.buttons.length>=3" @click.stop="duplicateButton(slide,buttonIndex)"><i class="far fa-copy"></i></button><button type="button" title="Remove Button" @click.stop="removeButton(slide,buttonIndex)"><i class="fas fa-times"></i></button></div>
                                                <div v-if="expandedButtonId===button.id" class="pb-hero-slider-button-item__body">
                                                    <div class="pb-form-group"><label class="pb-form-label">Button Text</label><input class="pb-input" v-model="button.text"></div>
                                                    <div class="pb-form-group"><label class="pb-form-label">CSS Class</label><input class="pb-input" v-model="button.cssClass" placeholder="my-button"></div>
                                                    <div class="pb-form-group"><label class="pb-form-label">Action Type</label><select class="pb-select" v-model="button.actionType"><option value="link">Link</option><option value="video_popup">Video Popup</option><option value="image_popup">Image Popup</option></select></div>
                                                    <div v-if="button.actionType==='link'" class="pb-form-group"><label class="pb-form-label">Link</label><component :is="editor.linkControl" :url="button.linkUrl||''" :target="button.linkTarget||''" :nofollow="Boolean(button.linkNofollow)" :custom-attributes="button.linkCustomAttributes||[]" @update:url="button.linkUrl=$event" @update:target="button.linkTarget=$event" @update:nofollow="button.linkNofollow=$event" @update:customAttributes="button.linkCustomAttributes=$event" /></div>
                                                    <template v-if="button.actionType==='video_popup'"><div class="pb-form-group"><label class="pb-form-label">Video Source</label><select class="pb-select" v-model="button.videoSource"><option value="youtube">YouTube</option><option value="vimeo">Vimeo</option><option value="dailymotion">Dailymotion</option><option value="self_hosted">Self Hosted</option></select></div><MediaField label="Video URL" :value="button.videoUrl" :has-picker="button.videoSource==='self_hosted'" @input="button.videoUrl=$event" @choose="editor.chooseMedia(button,'videoUrl','Choose video')" /></template>
                                                    <template v-if="button.actionType==='image_popup'"><div class="pb-form-group"><label class="pb-form-label">Image Source</label><select class="pb-select" v-model="button.imageSource"><option value="ckfinder">CKFinder / Media Library</option><option value="url">External URL</option></select></div><MediaField label="Image URL" :value="button.imageUrl" :has-picker="button.imageSource!=='url'" @input="button.imageUrl=$event" @choose="editor.chooseMedia(button,'imageUrl','Choose image')" /><div class="pb-form-group"><label class="pb-form-label">Image Alt</label><input class="pb-input" v-model="button.imageAlt"></div></template>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="pb-hero-slider-add-button" :disabled="(slide.buttons||[]).length>=3" @click="addButton(slide)"><i class="fas fa-plus"></i> Add Button</button>
                                    </div>
                                </details>

                                <details class="pb-hero-slider-subsection" open>
                                    <summary>Responsive Position</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <div v-if="slide.positioningMode==='independent'" class="pb-btn-group pb-hero-slider-position-targets"><button v-for="target in ['title','subtitle','buttons']" :key="target" type="button" class="pb-seg-btn" :class="{active:selectedPositionTarget(slide)===target}" @click="setSelectedPositionTarget(slide,target)">{{contentLabel(target)}}</button></div>
                                        <position-editor :slide="slide" :editor="editor" :target="slide.positioningMode==='grouped'?'group':selectedPositionTarget(slide)" :label="contentLabel(slide.positioningMode==='grouped'?'group':selectedPositionTarget(slide))" />
                                    </div>
                                </details>

                                <details class="pb-hero-slider-subsection" open>
                                    <summary>Button Group Layout</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <responsive-choice label="Direction" :control-id="'hero-slider-button-direction-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'buttonDirection','row')" :options="buttonDirectionOptions" @update:model-value="setSlideResponsive(slide,'buttonDirection',$event)" />
                                        <responsive-choice label="Alignment" :control-id="'hero-slider-button-align-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'buttonAlign','left')" :options="alignmentOptions" @update:model-value="setSlideResponsive(slide,'buttonAlign',$event)" />
                                        <size-control label="Gap" base="buttonGap" :control-id="'hero-slider-button-gap-'+slide.id" fallback="10px" :target="slide" :node="node" :editor="editor" :min="0" :max="100" :allowed-units="['px','em','rem']" />
                                        <responsive-choice label="Wrap Buttons" :control-id="'hero-slider-button-wrap-'+slide.id" :editor="editor" :model-value="slideResponsiveValue(slide,'buttonWrap',true)" :options="booleanOptions" @update:model-value="setSlideResponsive(slide,'buttonWrap',$event)" />
                                    </div>
                                </details>

                                <details class="pb-hero-slider-subsection">
                                    <summary>Slide Style Override</summary>
                                    <div class="pb-hero-slider-subsection__body">
                                        <ToggleField :id="'hero-slider-style-override-'+slide.id" label="Override Slide Style" v-model="slide.styleOverride" />
                                        <template v-if="slide.styleOverride">
                                            <ColorField label="Overlay Color" v-model="slide.slideOverlayColor" /><ColorField label="Title Color" v-model="slide.slideTitleColor" /><ColorField label="Subtitle Color" v-model="slide.slideSubtitleColor" /><ColorField label="Button Text Color" v-model="slide.slideButtonTextColor" /><ColorField label="Button Background" v-model="slide.slideButtonBackground" /><ColorField label="Button Hover Text Color" v-model="slide.slideButtonTextColorHover" /><ColorField label="Button Hover Background" v-model="slide.slideButtonBackgroundHover" />
                                            <div class="pb-form-group"><label class="pb-form-label">Title Size Mode</label><select class="pb-select" v-model="slide.slideTitleFontSizeMode"><option value="auto">Auto by HTML tag</option><option value="custom">Custom</option></select></div>
                                            <size-control label="Title Size" base="slideTitleFontSize" mode-key="slideTitleFontSizeMode" :control-id="'hero-slider-slide-title-size-'+slide.id" fallback="52px" :target="slide" :node="node" :editor="editor" :min="8" :max="160" />
                                            <div class="pb-form-group"><label class="pb-form-label">Title Weight</label><input class="pb-input" v-model="slide.slideTitleFontWeight"></div>
                                            <size-control label="Subtitle Size" base="slideSubtitleFontSize" :control-id="'hero-slider-slide-subtitle-size-'+slide.id" fallback="22px" :target="slide" :node="node" :editor="editor" :min="8" :max="100" />
                                            <div class="pb-form-group"><label class="pb-form-label">Subtitle Weight</label><input class="pb-input" v-model="slide.slideSubtitleFontWeight"></div>
                                            <size-control label="Content Gap" base="slideContentGap" :control-id="'hero-slider-slide-content-gap-'+slide.id" fallback="12px" :target="slide" :node="node" :editor="editor" :min="0" :max="100" />
                                        </template>
                                        <div class="pb-form-note">Disabled fields inherit the global Hero Slider style.</div>
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="pb-hero-add" :disabled="slides.length>=30" @click="addSlide"><i class="fas fa-plus"></i> Add Slide</button>
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Slider Behavior</summary>
                <div class="pb-collapsible-body">
                    <responsive-choice label="Direction" control-id="hero-slider-direction" :editor="editor" :model-value="responsiveValue('direction','horizontal')" :options="directionOptions" @update:model-value="setResponsive('direction',$event)" />
                    <div class="pb-form-group"><label class="pb-form-label">Transition</label><select class="pb-select" v-model="settings.transition"><option value="slide">Slide</option><option value="fade">Fade</option></select></div>
                    <div class="pb-hero-slider-grid pb-hero-slider-timing-controls"><scalar-control v-if="settings.autoplay" label="Interval (ms)" setting-key="autoplaySpeed" :node="node" :min="100" :max="60000" :step="100" /><scalar-control label="Transition Speed" setting-key="transitionSpeed" :node="node" :min="0" :max="10000" :step="50" /><scalar-control label="Slides per move" setting-key="perMove" :node="node" :min="1" :max="10" :step="1" /></div>
                    <ToggleField :id="'hero-slider-autoplay-'+node.id" label="Slider Autoplay" v-model="settings.autoplay" />
                    <ToggleField :id="'hero-slider-loop-'+node.id" label="Loop Slides" v-model="settings.loop" />
                    <ToggleField :id="'hero-slider-rewind-'+node.id" label="Rewind at end" v-model="settings.rewind" />
                    <ToggleField :id="'hero-slider-hover-'+node.id" label="Pause on Hover" v-model="settings.pauseOnHover" />
                    <ToggleField :id="'hero-slider-focus-'+node.id" label="Pause on Focus" v-model="settings.pauseOnFocus" />
                    <ToggleField :id="'hero-slider-interaction-'+node.id" label="Pause on Interaction" v-model="settings.pauseOnInteraction" />
                    <div class="pb-form-group"><label class="pb-form-label">Navigation</label><select class="pb-select" v-model="navigationMode"><option value="arrows_dots">Arrows and Dots</option><option value="arrows">Arrows Only</option><option value="dots">Dots Only</option><option value="none">None</option></select></div>
                    <div v-if="settings.arrows" class="pb-hero-slider-arrow-icon-settings">
                        <IconPicker label="Previous Arrow Icon" setting-key="previousArrowIcon" :node="node" :editor="editor" fallback="fas fa-chevron-left" />
                        <IconPicker label="Next Arrow Icon" setting-key="nextArrowIcon" :node="node" :editor="editor" fallback="fas fa-chevron-right" />
                    </div>
                    <div v-if="settings.pagination" class="pb-hero-slider-pagination-settings--stacked">
                        <pagination-placement-control label="Horizontal Slider Position" direction="horizontal" control-id="hero-slider-pagination-horizontal" :editor="editor" :settings="settings" />
                        <pagination-placement-control label="Vertical Slider Position" direction="vertical" control-id="hero-slider-pagination-vertical" :editor="editor" :settings="settings" />
                        <div class="pb-form-note">Basic Center keeps horizontal dots at Bottom Center and the vertical rail at Center Right. Enable Custom Placement for the full 3×3 grid.</div>
                    </div>
                    <ToggleField :id="'hero-slider-progress-'+node.id" label="Progress Bar" v-model="settings.progress" />
                    <ToggleField :id="'hero-slider-keyboard-'+node.id" label="Keyboard Navigation" v-model="settings.keyboard" />
                    <ToggleField :id="'hero-slider-drag-'+node.id" label="Drag" v-model="settings.drag" />
                    <ToggleField :id="'hero-slider-wheel-'+node.id" label="Mouse Wheel" v-model="settings.mouseWheel" />
                    <ToggleField :id="'hero-slider-wheel-release-'+node.id" label="Wheel release" v-model="settings.wheelRelease" />
                    <ToggleField :id="'hero-slider-lazy-'+node.id" label="Lazy-load media" v-model="settings.lazyLoad" />
                </div>
            </details>

            <details class="pb-collapsible" open>
                <summary>Video Playback</summary>
                <div class="pb-collapsible-body">
                    <ToggleField :id="'hero-slider-video-autoplay-'+node.id" label="Global Video Autoplay" v-model="settings.videoAutoplay" />
                    <div class="pb-form-group"><label class="pb-form-label">Video Duration Mode</label><select class="pb-select" v-model="settings.videoDurationMode"><option value="interval">Fixed interval</option><option value="duration">Follow video duration</option></select></div>
                    <div class="pb-form-note pb-hero-slider-fallback-note"><strong>Video Autoplay Fallback:</strong> Unsupported provider or SDK errors use the slider interval automatically.</div>
                    <ToggleField :id="'hero-slider-video-muted-'+node.id" label="Muted autoplay" v-model="settings.videoMutedAutoplay" />
                    <ToggleField :id="'hero-slider-video-loop-'+node.id" label="Loop Video by default" v-model="settings.videoLoop" />
                    <ToggleField :id="'hero-slider-video-resume-'+node.id" label="Resume on re-entry" v-model="settings.videoResume" />
                    <ToggleField :id="'hero-slider-video-privacy-'+node.id" label="Provider privacy mode" v-model="settings.videoPrivacyMode" />
                    <div class="pb-form-group"><label class="pb-form-label">Video Controls</label><select class="pb-select" v-model="settings.videoControls"><option value="custom">Custom minimal controls</option><option value="provider">Provider controls</option></select></div>
                    <div class="pb-form-group"><label class="pb-form-label">Dailymotion Player ID (optional)</label><input class="pb-input" v-model="settings.dailymotionPlayerId"><div class="pb-form-note">Needed when the official Dailymotion Web SDK library requires a configured player.</div></div>
                    <div class="pb-form-group"><label class="pb-form-label">Dailymotion SDK URL (optional)</label><input class="pb-input" v-model="settings.dailymotionSdkUrl"></div>
                </div>
            </details>
        </div>

        <div v-if="editor.settingsTab==='style'" class="pb-tab-content">
            <details class="pb-collapsible" open>
                <summary>Height</summary>
                <div class="pb-collapsible-body">
                    <div class="pb-form-group"><label class="pb-form-label">Height Mode</label><div class="pb-btn-group"><button type="button" class="pb-seg-btn" :class="{active:settings.heightMode==='adaptive'}" @click="settings.heightMode='adaptive'">Adaptive Height</button><button type="button" class="pb-seg-btn" :class="{active:settings.heightMode==='fixed'}" @click="settings.heightMode='fixed'">Fixed Height</button></div></div>
                    <size-control v-if="settings.heightMode==='fixed'" label="Fixed Height" base="fixedHeight" control-id="hero-slider-fixed-height" fallback="520px" :node="node" :editor="editor" :min="0" :max="2000" :allowed-units="['px','vh','vw']" />
                    <size-control label="Minimum Height" base="minHeight" control-id="hero-slider-min-height" fallback="420px" :node="node" :editor="editor" :min="0" :max="2000" :allowed-units="['px','vh','vw']" />
                    <div class="pb-form-note">Adaptive mode uses image/native video dimensions and the configured aspect-ratio fallback for external providers.</div>
                </div>
            </details>
            <details class="pb-collapsible" open>
                <summary>Overlay & Content</summary>
                <div class="pb-collapsible-body">
                    <ColorField label="Overlay Color" v-model="settings.overlayColor" />
                    <ColorField label="Title Color" v-model="settings.titleColor" />
                    <div class="pb-form-group"><label class="pb-form-label">Title Size Mode</label><select class="pb-select" v-model="settings.titleFontSizeMode"><option value="auto">Auto by HTML tag</option><option value="custom">Custom</option></select></div>
                    <size-control label="Title Size" base="titleFontSize" mode-key="titleFontSizeMode" control-id="hero-slider-title-size" fallback="52px" :node="node" :editor="editor" :min="8" :max="160" />
                    <div class="pb-form-group"><label class="pb-form-label">Title Weight</label><input class="pb-input" v-model="settings.titleFontWeight"></div>
                    <ColorField label="Subtitle Color" v-model="settings.subtitleColor" />
                    <size-control label="Subtitle Size" base="subtitleFontSize" control-id="hero-slider-subtitle-size" fallback="22px" :node="node" :editor="editor" :min="8" :max="100" />
                    <div class="pb-form-group"><label class="pb-form-label">Subtitle Weight</label><input class="pb-input" v-model="settings.subtitleFontWeight"></div>
                    <size-control label="Content Gap" base="contentGap" control-id="hero-slider-content-gap" fallback="12px" :node="node" :editor="editor" :min="0" :max="100" />
                    <ColorField label="Button Text Color" v-model="settings.buttonTextColor" />
                    <ColorField label="Button Background" v-model="settings.buttonBackground" />
                    <ColorField label="Button Hover Text Color" v-model="settings.buttonTextColorHover" />
                    <ColorField label="Button Hover Background" v-model="settings.buttonBackgroundHover" />
                    <sides-control label="Button Radius" base="buttonRadius" control-id="hero-slider-button-radius" kind="corners" :fallback-values="['999px','999px','999px','999px']" :node="node" :editor="editor" />
                    <sides-control label="Button Padding" base="buttonPadding" control-id="hero-slider-button-padding" :fallback-values="['10px','18px','10px','18px']" :node="node" :editor="editor" />
                </div>
            </details>
            <details v-if="settings.arrows" class="pb-collapsible" open>
                <summary>Arrow Buttons</summary>
                <div class="pb-collapsible-body">
                    <responsive-select label="Position" control-id="hero-slider-arrow-position" :editor="editor" :model-value="responsiveValue('arrowPosition','inside')" :options="arrowPositionOptions" @update:model-value="setResponsive('arrowPosition',$event)" />
                    <size-control label="Edge Offset" base="arrowEdgeOffset" control-id="hero-slider-arrow-edge-offset" fallback="16px" :node="node" :editor="editor" :min="0" :max="200" />
                    <size-control label="Button Size" base="arrowButtonSize" control-id="hero-slider-arrow-button-size" fallback="38px" :node="node" :editor="editor" :min="20" :max="120" />
                    <size-control label="Icon Size" base="arrowIconSize" control-id="hero-slider-arrow-icon-size" fallback="16px" :node="node" :editor="editor" :min="8" :max="80" />
                    <ColorField label="Icon Color" v-model="settings.arrowColor" />
                    <ColorField label="Button Background" v-model="settings.arrowBackground" />
                    <ColorField label="Hover Icon Color" v-model="settings.arrowHoverColor" />
                    <ColorField label="Hover Background" v-model="settings.arrowHoverBackground" />
                    <sides-control label="Button Radius" base="arrowRadius" control-id="hero-slider-arrow-radius" kind="corners" :fallback-values="['999px','999px','999px','999px']" :node="node" :editor="editor" />
                </div>
            </details>
            <details class="pb-collapsible">
                <summary>Popup</summary>
                <div class="pb-collapsible-body"><ColorField label="Modal Background" v-model="settings.modalBackground" /><ColorField label="UI Color" v-model="settings.modalUiColor" /><ColorField label="UI Hover Color" v-model="settings.modalUiHoverColor" /><size-control label="Video Width" base="modalVideoWidth" control-id="hero-slider-modal-video-width" fallback="75%" :node="node" :editor="editor" :min="20" :max="100" :allowed-units="['%','px','vw']" /></div>
            </details>
        </div>

        <div v-if="editor.settingsTab==='advanced'" class="pb-tab-content"><component :is="editor.widgetAdvancedControls" :node="node" :responsive-device="editor.responsiveDevice" :show-display-conditions="false" :show-cache-settings="false" :elementor-choices="true" @responsive-device="editor.setResponsiveDevice" @choose-media="editor.chooseMedia(node.settings,$event)" @clear-media="editor.clearMedia(node.settings,$event)" /></div>
    </div>
</template>

<script>
const ResponsiveMenu = { props: ['editor', 'id'], template: `<div class="pb-control-device-wrap"><button type="button" class="pb-control-device-btn" :aria-label="'Responsive: '+editor.responsiveDeviceLabel()" :title="'Responsive: '+editor.responsiveDeviceLabel()" @click.stop="editor.openControlResponsiveMenu(id)"><i :class="editor.responsiveDeviceIcon()"></i></button><div v-if="editor.isControlResponsiveMenuOpen(id)" class="pb-control-device-menu"><button v-for="device in editor.responsiveDevices" :key="id+'-'+device.value" type="button" class="pb-control-device-item" :class="{active:editor.responsiveDevice===device.value}" @click.stop="editor.applyResponsiveDevice(id,device.value)"><i :class="device.icon"></i><span>{{editor.deviceOptionLabel(device)}}</span></button></div></div>` };
const ToggleField = { props: { id: String, label: String, modelValue: Boolean }, emits: ['update:modelValue'], template: `<div class="pb-form-group pb-toggle-label-row pb-widget-settings__compact-toggle"><label class="pb-form-label mb-0" :for="id">{{label}}</label><div class="pb-toggle-switch-wrap"><div class="pb-toggle-wrap"><input :id="id" class="pb-toggle" type="checkbox" :checked="modelValue" @change="$emit('update:modelValue',$event.target.checked)"><label :for="id"></label></div><span class="pb-toggle-state">{{modelValue?'On':'Off'}}</span></div></div>` };
const ColorField = { props: ['label', 'modelValue'], emits: ['update:modelValue'], template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><input class="pb-input coloris pb-coloris-input" :value="modelValue" @input="$emit('update:modelValue',$event.target.value)"></div>` };
const SizeControl = { components: { ResponsiveMenu }, props: { label: String, base: String, modeKey: String, controlId: String, fallback: String, node: Object, target: Object, editor: Object, min: { type: Number, default: 0 }, max: { type: Number, default: null }, allowedUnits: { type: Array, default: () => [] } }, computed: { controlNode() { return this.target ? { settings: this.target } : this.node; }, options() { const units = this.allowedUnits.length ? this.allowedUnits : this.editor.sizeControlUnits; return { fallback: this.fallback, min: this.min, max: this.max, allowedUnits: units, fallbackUnit: units[0] || 'px' }; }, maxValue() { const unitMax = this.editor.sizeControlMax(this.controlNode, this.base, this.fallback, this.options); return this.max === null ? unitMax : Math.min(this.max, unitMax); } }, methods: { markCustom() { if (this.modeKey && this.controlNode?.settings) this.controlNode.settings[this.modeKey] = 'custom'; } }, template: `<div class="pb-form-group pb-hero-slider-size-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="maxValue" :step="editor.sizeControlStep(controlNode,base,fallback,options)" :value="editor.sizeControlDisplayValue(controlNode,base,fallback,options)" @input="editor.onSizeControlInput(controlNode,base,$event,options);markCustom()"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :min="min" :max="maxValue" :value="editor.sizeControlDisplayValue(controlNode,base,fallback,options)" @input="editor.onSizeControlInput(controlNode,base,$event,options);markCustom()"><select class="pb-mini-unit" :value="editor.sizeControlUnit(controlNode,base,fallback,options)" @change="editor.setSizeControlUnit(controlNode,base,$event.target.value,options);markCustom()"><option v-for="unit in options.allowedUnits" :key="unit" :value="unit">{{unit}}</option></select></div></div></div>` };
const OffsetControl = {
    components: { ResponsiveMenu },
    props: { label: String, base: String, controlId: String, target: Object, editor: Object },
    data() { return { units: ['px', '%', 'em', 'rem'], min: -200, max: 200 }; },
    methods: {
        keys() { const device = this.editor.responsiveDevice || 'desktop'; return device === 'mobile' ? [this.base + 'Mobile', this.base + 'Tablet', this.base] : (device === 'tablet' ? [this.base + 'Tablet', this.base] : [this.base]); },
        raw() { for (const key of this.keys()) { if (this.target[key] !== '' && this.target[key] != null) return String(this.target[key]); } return '0px'; },
        unit() { const match = this.raw().trim().match(/(px|%|em|rem)$/i); return match && this.units.includes(match[1].toLowerCase()) ? match[1].toLowerCase() : 'px'; },
        value() { const parsed = Number.parseFloat(this.raw()); return Number.isFinite(parsed) ? Math.max(this.min, Math.min(this.max, parsed)) : 0; },
        setValue(event) { const parsed = Number(event.target.value); const safe = Number.isFinite(parsed) ? Math.max(this.min, Math.min(this.max, parsed)) : 0; this.editor.setResponsiveSetting(this.target, this.base, `${safe}${this.unit()}`); },
        setUnit(event) { const next = this.units.includes(event.target.value) ? event.target.value : 'px'; this.editor.setResponsiveSetting(this.target, this.base, `${this.value()}${next}`); },
    },
    template: `<div class="pb-form-group pb-hero-slider-size-control pb-hero-slider-offset-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="max" step="1" :value="value()" @input="setValue($event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" :min="min" :max="max" step="1" :value="value()" @input="setValue($event)"><select class="pb-mini-unit" :value="unit()" @change="setUnit($event)"><option v-for="option in units" :key="option" :value="option">{{option}}</option></select></div></div></div>`,
};
const ScalarControl = { props: { label: String, settingKey: String, node: Object, min: Number, max: Number, step: Number }, template: `<div class="pb-form-group pb-hero-slider-scalar-control"><label class="pb-form-label">{{label}}</label><div class="pb-range-value-row"><input class="pb-range" type="range" :min="min" :max="max" :step="step" v-model.number="node.settings[settingKey]"><input class="pb-input pb-input-compact" type="number" :min="min" :max="max" :step="step" v-model.number="node.settings[settingKey]"></div></div>` };
const MediaField = { props: { label: String, value: String, hasPicker: Boolean }, emits: ['input', 'choose'], template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><div class="pb-media-field" :class="{'has-action':hasPicker}"><input class="pb-input" :aria-label="label" :value="value" @input="$emit('input',$event.target.value)"><button v-if="hasPicker" type="button" :aria-label="'Choose '+label" :title="'Choose '+label" @click="$emit('choose')"><i class="fas fa-folder-open" aria-hidden="true"></i></button></div></div>` };
const IconPicker = { props: ['label', 'settingKey', 'node', 'editor', 'fallback'], computed: { value() { return String(this.node.settings?.[this.settingKey] || this.fallback || 'fas fa-chevron-left'); }, source() { return this.node.settings?.[this.settingKey + 'Source'] === 'svg' ? 'svg' : 'library'; }, svgMarkup() { return String(this.node.settings?.[this.settingKey + 'Svg'] || '').trim(); }, svgDataUri() { return this.svgMarkup.startsWith('<svg') ? 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(this.svgMarkup) : ''; } }, methods: { setDefault() { this.node.settings[this.settingKey] = this.fallback; this.node.settings[this.settingKey + 'Source'] = 'library'; this.node.settings[this.settingKey + 'Svg'] = ''; }, openLibrary() { this.editor.openImageCarouselArrowIconLibrary(this.settingKey, this.node); }, chooseSvg() { this.editor.chooseImageCarouselArrowSvg(this.settingKey, this.node); } }, template: `<div class="pb-form-group"><label class="pb-form-label">{{label}}</label><div class="pb-image-carousel-icon-picker"><button type="button" class="pb-image-carousel-icon-picker__button" :class="{'is-current':source==='library'&&value===fallback}" title="Default" aria-label="Use default icon" @click="setDefault"><i :class="fallback"></i></button><button type="button" class="pb-image-carousel-icon-picker__button" title="Upload SVG" aria-label="Upload SVG" @click="chooseSvg"><i class="fas fa-upload"></i></button><button type="button" class="pb-image-carousel-icon-picker__button" :class="{'is-current':source==='library'&&value!==fallback}" title="Icon Library" aria-label="Open Font Awesome icon library" @click="openLibrary"><img v-if="source==='svg'&&svgDataUri" :src="svgDataUri" alt=""><i v-else :class="value"></i></button></div></div>` };
const ResponsiveChoice = { components: { ResponsiveMenu }, props: { label: String, controlId: String, editor: Object, modelValue: [String, Boolean], options: Array }, emits: ['update:modelValue'], template: `<div class="pb-form-group pb-hero-slider-responsive-choice"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-btn-group"><button v-for="option in options" :key="String(option.value)" type="button" class="pb-seg-btn" :class="{active:modelValue===option.value}" @click="$emit('update:modelValue',option.value)"><i v-if="option.icon" :class="option.icon"></i><span>{{option.label}}</span></button></div></div>` };
const ResponsiveSelect = { components: { ResponsiveMenu }, props: { label: String, controlId: String, editor: Object, modelValue: String, options: Array }, emits: ['update:modelValue'], template: `<div class="pb-form-group pb-hero-slider-responsive-select"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><select class="pb-select" :value="modelValue" @change="$emit('update:modelValue',$event.target.value)"><option v-for="option in options" :key="option.value" :value="option.value">{{option.label}}</option></select></div>` };
const SidesControl = {
    components: { ResponsiveMenu },
    props: { label: String, base: String, controlId: String, kind: { type: String, default: 'edges' }, fallbackValues: { type: Array, default: () => ['0px', '0px', '0px', '0px'] }, node: Object, editor: Object },
    data() { return { linked: true, units: ['px', '%', 'em', 'rem'] }; },
    computed: { sides() { const labels = this.kind === 'corners' ? ['Top Left', 'Top Right', 'Bottom Right', 'Bottom Left'] : ['Top', 'Right', 'Bottom', 'Left']; return ['Top', 'Right', 'Bottom', 'Left'].map((key, index) => ({ key, label: labels[index] })); } },
    methods: {
        fallback(index) { return this.fallbackValues[index] || this.fallbackValues[0] || '0px'; },
        unit() { return this.editor.sizeControlUnit(this.node, this.base + this.sides[0].key, this.fallback(0), { allowedUnits: this.units }); },
        value(index) { const side = this.sides[index]; return this.editor.sizeControlDisplayValue(this.node, this.base + side.key, this.fallback(index), { allowedUnits: this.units }); },
        setSide(index, event) { const number = Number(event.target.value); const safe = Number.isFinite(number) ? Math.max(0, number) : 0; const targets = this.linked ? this.sides.map((side, targetIndex) => ({ side, targetIndex })) : [{ side: this.sides[index], targetIndex: index }]; targets.forEach(({ side }) => this.editor.setResponsiveSetting(this.node.settings, this.base + side.key, `${safe}${this.unit()}`)); },
        setUnit(event) { const next = this.units.includes(event.target.value) ? event.target.value : 'px'; this.sides.forEach((side, index) => this.editor.setResponsiveSetting(this.node.settings, this.base + side.key, `${this.value(index)}${next}`)); },
    },
    template: `<div class="pb-form-group pb-hero-slider-sides-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><div class="pb-label-tools"><responsive-menu :editor="editor" :id="controlId"/><select class="pb-mini-unit" :value="unit()" :aria-label="label+' unit'" @change="setUnit($event)"><option v-for="option in units" :key="base+'-'+option" :value="option">{{option}}</option></select></div></div><div class="pb-four-sides pb-four-sides-with-link"><label v-for="(side,index) in sides" :key="side.key" class="pb-side-input"><input class="pb-input" type="number" min="0" :value="value(index)" :aria-label="label+' '+side.label" @input="setSide(index,$event)"><span>{{side.label}}</span></label><div class="pb-side-link-cell"><button type="button" class="pb-link-btn" :class="{active:linked}" :title="linked?'Unlink values':'Link values together'" @click="linked=!linked"><i class="fas" :class="linked?'fa-link':'fa-unlink'"></i></button></div></div></div>`,
};

const PositionEditor = {
    components: { ResponsiveMenu },
    props: { slide: Object, editor: Object, target: String, label: String },
    data() { return { anchors: ['top-left', 'top-center', 'top-right', 'center-left', 'center', 'center-right', 'bottom-left', 'bottom-center', 'bottom-right'] }; },
    computed: { currentDevice() { return this.editor.responsiveDevice || 'desktop'; }, controlId() { return 'hero-slider-position-' + this.slide.id + '-' + this.target; } },
    methods: {
        suffix() { return this.currentDevice === 'mobile' ? 'Mobile' : (this.currentDevice === 'tablet' ? 'Tablet' : ''); },
        key(base) { return this.target + base + this.suffix(); },
        value(base, fallback) { const device = this.currentDevice; const root = this.target + base; const keys = device === 'mobile' ? [root + 'Mobile', root + 'Tablet', root] : (device === 'tablet' ? [root + 'Tablet', root] : [root]); for (const key of keys) { if (this.slide[key] !== '' && this.slide[key] != null) return this.slide[key]; } return fallback; },
        set(base, value) { this.slide[this.key(base)] = value; },
        setAnchor(anchor) { const coordinates = { 'top-left': [0, 0], 'top-center': [50, 0], 'top-right': [100, 0], 'center-left': [0, 50], center: [50, 50], 'center-right': [100, 50], 'bottom-left': [0, 100], 'bottom-center': [50, 100], 'bottom-right': [100, 100] }[anchor]; if (!coordinates) return; this.set('Anchor', anchor); this.set('X', coordinates[0] + '%'); this.set('Y', coordinates[1] + '%'); this.set('Width', (coordinates[0] === 50 ? 70 : 50) + '%'); this.set('Align', coordinates[0] === 0 ? 'left' : (coordinates[0] === 100 ? 'right' : 'center')); },
        number(base, fallback) { const parsed = Number.parseFloat(this.value(base, fallback)); return Number.isFinite(parsed) ? parsed : fallback; },
        setNumber(base, event, min = 0) { const parsed = Number(event.target.value); this.set(base, Math.min(100, Math.max(min, Number.isFinite(parsed) ? parsed : min)) + '%'); },
    },
    template: `<div class="pb-hero-slider-position-control"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">Editing: {{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-form-group"><label class="pb-form-label">Anchor Point</label><div class="pb-hero-slider-anchor"><button v-for="anchor in anchors" :key="anchor" type="button" :class="{active:value('Anchor','bottom-left')===anchor}" :title="anchor" :aria-label="anchor" @click="setAnchor(anchor)"><span></span></button></div></div><div class="pb-hero-slider-grid"><div class="pb-form-group"><label class="pb-form-label">Horizontal (X)</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="100" :value="number('X',8)" @input="setNumber('X',$event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="100" :value="number('X',8)" @input="setNumber('X',$event)"><span>%</span></div></div></div><div class="pb-form-group"><label class="pb-form-label">Vertical (Y)</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="0" max="100" :value="number('Y',86)" @input="setNumber('Y',$event)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="0" max="100" :value="number('Y',86)" @input="setNumber('Y',$event)"><span>%</span></div></div></div></div><div class="pb-form-group"><label class="pb-form-label">Content Width</label><div class="pb-range-value-row"><input class="pb-range" type="range" min="10" max="100" :value="number('Width',70)" @input="setNumber('Width',$event,10)"><div class="pb-value-with-unit"><input class="pb-input pb-input-compact" type="number" min="10" max="100" :value="number('Width',70)" @input="setNumber('Width',$event,10)"><span>%</span></div></div></div><div class="pb-form-group"><label class="pb-form-label">Alignment</label><div class="pb-btn-group"><button v-for="align in ['left','center','right']" :key="align" type="button" class="pb-seg-btn" :class="{active:value('Align','left')===align}" @click="set('Align',align)">{{align}}</button></div></div></div>`,
};

const PaginationPlacementControl = {
    components: { ResponsiveMenu, OffsetControl },
    props: { label: String, direction: String, controlId: String, editor: Object, settings: Object },
    data() { return { anchors: ['top-left', 'top-center', 'top-right', 'center-left', 'center', 'center-right', 'bottom-left', 'bottom-center', 'bottom-right'] }; },
    computed: {
        axis() { return this.direction === 'vertical' ? 'Vertical' : 'Horizontal'; },
        basicOptions() { return this.direction === 'vertical' ? [{ value: 'top', label: 'Top' }, { value: 'center', label: 'Center' }, { value: 'bottom', label: 'Bottom' }] : [{ value: 'left', label: 'Left' }, { value: 'center', label: 'Center' }, { value: 'right', label: 'Right' }]; },
        currentDevice() { return this.editor.responsiveDevice || 'desktop'; },
        custom() { return this.value('paginationPlacementMode' + this.axis, 'basic') === 'custom'; },
    },
    methods: {
        suffix() { return this.currentDevice === 'mobile' ? 'Mobile' : (this.currentDevice === 'tablet' ? 'Tablet' : ''); },
        keys(base) { return this.currentDevice === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (this.currentDevice === 'tablet' ? [base + 'Tablet', base] : [base]); },
        value(base, fallback) { for (const key of this.keys(base)) { if (this.settings[key] !== '' && this.settings[key] != null) return this.settings[key]; } return fallback; },
        set(base, value) { this.settings[base + this.suffix()] = value; },
        setCustom(enabled) { this.set('paginationPlacementMode' + this.axis, enabled ? 'custom' : 'basic'); },
    },
    template: `<div class="pb-form-group pb-hero-slider-pagination-placement"><div class="pb-label-row pb-label-row-device"><label class="pb-form-label mb-0">{{label}}</label><responsive-menu :editor="editor" :id="controlId"/></div><div class="pb-btn-group"><button v-for="option in basicOptions" :key="option.value" type="button" class="pb-seg-btn" :class="{active:!custom&&value('paginationAlignment'+axis,'center')===option.value}" @click="set('paginationAlignment'+axis,option.value);setCustom(false)"><span>{{option.label}}</span></button></div><div class="pb-toggle-label-row pb-hero-slider-custom-placement"><label class="pb-form-label mb-0">Custom Placement</label><div class="pb-toggle-wrap"><input :id="controlId+'-custom'" class="pb-toggle" type="checkbox" :checked="custom" @change="setCustom($event.target.checked)"><label :for="controlId+'-custom'"></label></div></div><template v-if="custom"><div class="pb-hero-slider-pagination-anchor"><button v-for="anchor in anchors" :key="anchor" type="button" :class="{active:value('paginationPosition'+axis,direction==='vertical'?'center-right':'bottom-center')===anchor}" :title="anchor==='center'?'Exact Center':anchor" :aria-label="anchor==='center'?'Exact Center':anchor" @click="set('paginationPosition'+axis,anchor)"><span></span></button></div><offset-control label="Horizontal Offset" :base="'paginationOffsetX'+axis" :control-id="controlId+'-offset-x'" :target="settings" :editor="editor" /><offset-control label="Vertical Offset" :base="'paginationOffsetY'+axis" :control-id="controlId+'-offset-y'" :target="settings" :editor="editor" /></template></div>`,
};

export default {
    name: 'HeroSliderSettings',
    components: { ToggleField, ColorField, SizeControl, ScalarControl, MediaField, IconPicker, ResponsiveChoice, ResponsiveSelect, SidesControl, PositionEditor, PaginationPlacementControl },
    props: { node: { type: Object, required: true }, editor: { type: Object, required: true } },
    data() { return {
        expandedSlideId: '', expandedButtonId: '', selectedPositionTargets: {},
        ratios: ['16/9', '4/3', '1/1', '3/2', '21/9', '9/16', '4/5'],
        directionOptions: [{ value: 'horizontal', label: 'Horizontal', icon: 'fas fa-arrows-alt-h' }, { value: 'vertical', label: 'Vertical', icon: 'fas fa-arrows-alt-v' }],
        arrowPositionOptions: [{ value: 'inside', label: 'Inside' }, { value: 'outside', label: 'Outside' }],
        buttonDirectionOptions: [{ value: 'row', label: 'Horizontal', icon: 'fas fa-arrows-alt-h' }, { value: 'column', label: 'Vertical', icon: 'fas fa-arrows-alt-v' }],
        alignmentOptions: [{ value: 'left', label: 'Left' }, { value: 'center', label: 'Center' }, { value: 'right', label: 'Right' }],
        booleanOptions: [{ value: true, label: 'Wrap' }, { value: false, label: 'No Wrap' }],
        imageLayoutOptions: [{ value: 'cover', label: 'Cover (fixed height)' }, { value: 'natural', label: 'Natural Image Ratio' }],
        objectFitOptions: ['cover', 'contain', 'fill'].map((value) => ({ value, label: value.charAt(0).toUpperCase() + value.slice(1) })),
        objectPositionOptions: ['left top', 'left center', 'left bottom', 'center top', 'center center', 'center bottom', 'right top', 'right center', 'right bottom'].map((value) => ({ value, label: value.replace(/\b\w/g, (letter) => letter.toUpperCase()) })),
    }; },
    computed: {
        settings() { return this.node.settings || (this.node.settings = {}); },
        slides() { return Array.isArray(this.settings.slides) ? this.settings.slides : (this.settings.slides = []); },
        currentDevice() { return this.editor.responsiveDevice || 'desktop'; },
        navigationMode: {
            get() { if (this.settings.arrows && this.settings.pagination) return 'arrows_dots'; if (this.settings.arrows) return 'arrows'; if (this.settings.pagination) return 'dots'; return 'none'; },
            set(value) { this.settings.arrows = value === 'arrows_dots' || value === 'arrows'; this.settings.pagination = value === 'arrows_dots' || value === 'dots'; },
        },
    },
    mounted() { if (!this.slides.length) this.addSlide(); if (!this.expandedSlideId && this.slides[0]) this.expandedSlideId = this.slides[0].id; },
    methods: {
        toggleSlide(id) { this.expandedSlideId = this.expandedSlideId === id ? '' : id; },
        newSlide(index) { const source = this.slides[0] ? JSON.parse(JSON.stringify(this.slides[0])) : { positioningMode: 'grouped', contentOrder: ['title', 'subtitle', 'buttons'], showTitle: true, showSubtitle: true, showButtons: true, titleTag: 'h2', subtitleTag: 'p', groupAnchor: 'bottom-left', groupX: '8%', groupY: '86%', groupWidth: '70%', groupAlign: 'left', buttonDirection: 'row', buttonAlign: 'left', buttonGap: '10px', buttonWrap: true, styleOverride: false }; return { ...source, id: 'hero-slider-slide-' + Date.now() + '-' + index, mediaType: 'image', imageSource: 'ckfinder', imageUrl: '', imageUrlTablet: '', imageUrlMobile: '', imageAlt: '', imageLayout: 'cover', imageLayoutTablet: '', imageLayoutMobile: '', videoProvider: 'self_hosted', videoUrl: '', videoPoster: '', videoPosterTablet: '', videoPosterMobile: '', videoAutoplay: 'inherit', videoLoop: false, videoControls: true, videoMuted: true, videoResume: true, videoAspectRatio: '16/9', title: '', subtitle: '', buttons: [], styleOverride: false }; },
        addSlide() { if (this.slides.length >= 30) return; const slide = this.newSlide(this.slides.length + 1); this.slides.push(slide); this.expandedSlideId = slide.id; },
        duplicateSlide(index) { if (this.slides.length >= 30) return; const copy = JSON.parse(JSON.stringify(this.slides[index])); copy.id = 'hero-slider-slide-' + Date.now(); this.slides.splice(index + 1, 0, copy); this.expandedSlideId = copy.id; },
        removeSlide(index) { if (this.slides.length <= 1) return; const removed = this.slides.splice(index, 1)[0]; if (this.expandedSlideId === removed?.id) this.expandedSlideId = this.slides[Math.max(0, index - 1)]?.id || ''; },
        moveSlide(index, direction) { const target = index + direction; if (target < 0 || target >= this.slides.length) return; const [slide] = this.slides.splice(index, 1); this.slides.splice(target, 0, slide); },
        newButton(index, source = {}) { return { id: 'hero-slider-button-' + Date.now() + '-' + index, text: index ? 'Button ' + (index + 1) : 'Learn More', cssClass: '', actionType: 'link', linkUrl: '', linkTarget: '', linkNofollow: false, linkCustomAttributes: [], videoSource: 'youtube', videoUrl: '', imageSource: 'ckfinder', imageUrl: '', imageAlt: '', ...source }; },
        addButton(slide) { if (!Array.isArray(slide.buttons)) slide.buttons = []; if (slide.buttons.length >= 3) return; const button = this.newButton(slide.buttons.length); slide.buttons.push(button); this.expandedButtonId = button.id; },
        duplicateButton(slide, index) { if (!Array.isArray(slide.buttons) || slide.buttons.length >= 3) return; const source = JSON.parse(JSON.stringify(slide.buttons[index] || {})); const button = this.newButton(slide.buttons.length, { ...source, id: '', text: (source.text || 'Button') + ' Copy' }); button.id = 'hero-slider-button-' + Date.now() + '-' + slide.buttons.length; slide.buttons.splice(index + 1, 0, button); this.expandedButtonId = button.id; },
        removeButton(slide, index) { if (Array.isArray(slide.buttons)) slide.buttons.splice(index, 1); this.expandedButtonId = slide.buttons?.[0]?.id || ''; },
        toggleButton(id) { this.expandedButtonId = this.expandedButtonId === id ? '' : id; },
        contentLabel(key) { return { group: 'Content Group', title: 'Title', subtitle: 'Subtitle', buttons: 'Button Group' }[key] || key; },
        visibilityKey(key) { return { title: 'showTitle', subtitle: 'showSubtitle', buttons: 'showButtons' }[key]; },
        contentVisible(slide, key) { return slide[this.visibilityKey(key)] !== false; },
        toggleContentVisibility(slide, key) { const setting = this.visibilityKey(key); if (setting) slide[setting] = !this.contentVisible(slide, key); },
        moveContent(slide, index, direction) { const target = index + direction; if (!Array.isArray(slide.contentOrder) || target < 0 || target >= slide.contentOrder.length) return; const next = [...slide.contentOrder]; [next[index], next[target]] = [next[target], next[index]]; slide.contentOrder = next; },
        selectedPositionTarget(slide) { return this.selectedPositionTargets[slide.id] || 'title'; },
        setSelectedPositionTarget(slide, target) { this.selectedPositionTargets[slide.id] = target; },
        chooseMedia(target, key, label) { if (this.editor.chooseMedia) this.editor.chooseMedia(target, key, label); },
        responsiveKey(base) { return this.currentDevice === 'mobile' ? base + 'Mobile' : (this.currentDevice === 'tablet' ? base + 'Tablet' : base); },
        responsiveValue(base, fallback = '') { const values = this.currentDevice === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (this.currentDevice === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of values) { if (this.settings[key] !== '' && this.settings[key] != null) return this.settings[key]; } return fallback; },
        setResponsive(base, value) { this.settings[this.responsiveKey(base)] = value; },
        slideResponsiveValue(slide, base, fallback = '') { const values = this.currentDevice === 'mobile' ? [base + 'Mobile', base + 'Tablet', base] : (this.currentDevice === 'tablet' ? [base + 'Tablet', base] : [base]); for (const key of values) { if (slide[key] !== '' && slide[key] != null) return slide[key]; } return fallback; },
        setSlideResponsive(slide, base, value) { slide[this.responsiveKey(base)] = value; },
    },
};
</script>

<style scoped>
.pb-hero-slider-settings .pb-hero-slider-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.pb-hero-slider-settings .pb-hero-slider-slides{display:grid;gap:8px}
.pb-hero-slider-settings .pb-hero-slider-slide{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff}
.pb-hero-slider-settings .pb-hero-slider-slide__header{display:flex;min-height:36px;align-items:center;gap:4px;padding:4px 6px;cursor:pointer}
.pb-hero-slider-settings .pb-hero-slider-slide__disclosure{display:grid;flex:0 0 16px;width:16px;height:26px;place-items:center;color:#8795ad;font-size:9px}
.pb-hero-slider-settings .pb-hero-slider-slide__title{display:flex;flex:1;min-width:0;align-items:center;gap:6px}
.pb-hero-slider-settings .pb-hero-slider-slide__title strong{overflow:hidden;min-width:0;text-overflow:ellipsis;white-space:nowrap}
.pb-hero-slider-settings .pb-hero-slider-slide__header button{width:26px;height:26px;padding:0;border:0;background:transparent;color:var(--muted);cursor:pointer}
.pb-hero-slider-settings .pb-hero-slider-slide__header button:disabled{cursor:not-allowed;opacity:.35}
.pb-hero-slider-settings .pb-hero-slider-slide__body{padding:10px;border-top:1px solid var(--line);background:var(--soft)}
.pb-hero-slider-settings .pb-hero-slider-subsection{margin:0 0 8px;border:1px solid var(--line);border-radius:7px;background:#fff}.pb-hero-slider-settings .pb-hero-slider-subsection>summary{padding:8px 9px;color:#344054;font-size:10px;font-weight:700;cursor:pointer}.pb-hero-slider-settings .pb-hero-slider-subsection__body{padding:9px;border-top:1px solid var(--line)}
.pb-hero-slider-settings .pb-hero-slider-mode,.pb-hero-slider-settings .pb-hero-slider-position-targets{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:9px}.pb-hero-slider-settings .pb-hero-slider-position-targets{grid-template-columns:repeat(3,minmax(0,1fr))}
.pb-hero-slider-settings .pb-hero-slider-content-order,.pb-hero-slider-settings .pb-hero-slider-buttons-editor{display:grid;gap:6px;margin-top:9px}.pb-hero-slider-settings .pb-hero-slider-order-row,.pb-hero-slider-settings .pb-hero-slider-button-item__header{display:grid;grid-template-columns:16px minmax(0,1fr) repeat(3,25px);align-items:center;gap:3px;min-height:34px;padding:4px 5px;border:1px solid var(--line);border-radius:6px;background:#fff}.pb-hero-slider-settings .pb-hero-slider-button-item__header{grid-template-columns:16px minmax(0,1fr) repeat(2,25px);cursor:pointer}.pb-hero-slider-settings .pb-hero-slider-order-row strong,.pb-hero-slider-settings .pb-hero-slider-button-item__header strong{overflow:hidden;font-size:10px;text-overflow:ellipsis;white-space:nowrap}.pb-hero-slider-settings .pb-hero-slider-order-row button,.pb-hero-slider-settings .pb-hero-slider-button-item__header button{width:25px;height:25px;padding:0;border:0;border-radius:4px;color:#667085;background:#f2f4f7}.pb-hero-slider-settings .pb-hero-slider-order-row button.is-visible{color:#5b5ce2;background:#eef0ff}.pb-hero-slider-settings .pb-hero-slider-order-row button:disabled,.pb-hero-slider-settings .pb-hero-slider-button-item__header button:disabled{opacity:.35}.pb-hero-slider-settings .pb-hero-slider-button-item.is-open{border:1px solid #b8c0ff;border-radius:7px}.pb-hero-slider-settings .pb-hero-slider-button-item__body{padding:9px;border-top:1px solid var(--line)}
.pb-hero-slider-settings .pb-hero-slider-position-control{display:grid;gap:8px}.pb-hero-slider-settings .pb-hero-slider-anchor,.pb-hero-slider-settings .pb-hero-slider-pagination-anchor{display:grid;grid-template-columns:repeat(3,32px);width:max-content;margin:0 auto;overflow:hidden;border:1px solid var(--line);border-radius:7px}.pb-hero-slider-settings .pb-hero-slider-anchor button,.pb-hero-slider-settings .pb-hero-slider-pagination-anchor button{width:32px;height:29px;padding:0;border:0;border-right:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff}.pb-hero-slider-settings .pb-hero-slider-anchor button:nth-child(3n),.pb-hero-slider-settings .pb-hero-slider-pagination-anchor button:nth-child(3n){border-right:0}.pb-hero-slider-settings .pb-hero-slider-anchor button:nth-last-child(-n+3),.pb-hero-slider-settings .pb-hero-slider-pagination-anchor button:nth-last-child(-n+3){border-bottom:0}.pb-hero-slider-settings .pb-hero-slider-anchor span,.pb-hero-slider-settings .pb-hero-slider-pagination-anchor span{display:block;width:5px;height:5px;margin:auto;border-radius:50%;background:#a8b1c1}.pb-hero-slider-settings .pb-hero-slider-anchor button.active,.pb-hero-slider-settings .pb-hero-slider-pagination-anchor button.active{background:#eef0ff}.pb-hero-slider-settings .pb-hero-slider-anchor button.active span,.pb-hero-slider-settings .pb-hero-slider-pagination-anchor button.active span{width:8px;height:8px;background:#5b6cff}
.pb-hero-slider-settings .pb-hero-slider-pagination-placement{display:grid;gap:8px}.pb-hero-slider-settings .pb-hero-slider-custom-placement{display:flex;align-items:center;justify-content:space-between;margin:0;padding:4px 0}.pb-hero-slider-settings .pb-value-with-unit>span{display:grid;min-width:30px;place-items:center;border:1px solid var(--line);border-left:0;border-radius:0 6px 6px 0;color:var(--muted);font-size:9px;background:#fff}
.pb-hero-slider-settings .pb-hero-add{width:100%;margin-top:10px;padding:9px;border:1px dashed var(--line);border-radius:7px;background:transparent;color:var(--primary,#6979f8);cursor:pointer}
.pb-hero-slider-settings .pb-hero-add:disabled{cursor:not-allowed;opacity:.45}
.pb-hero-slider-settings .pb-media-field.has-action{display:flex}
.pb-hero-slider-settings .pb-media-field.has-action input{border-radius:6px 0 0 6px!important}
.pb-hero-slider-settings .pb-media-field.has-action button{width:46px;border:1px solid var(--line);border-left:0;border-radius:0 6px 6px 0;background:#fff;color:var(--primary,#6979f8);cursor:pointer}
.pb-hero-slider-settings .pb-hero-slider-add-button{margin-top:8px;border:0;background:transparent;color:var(--primary,#6979f8);cursor:pointer}
.pb-hero-slider-settings .pb-hero-slider-add-button:disabled{cursor:not-allowed;opacity:.45}
.pb-hero-slider-settings .pb-form-note{font-size:12px;line-height:1.4;color:var(--muted)}
.pb-hero-slider-settings .pb-hero-slider-behavior-note{margin-bottom:12px}
</style>
