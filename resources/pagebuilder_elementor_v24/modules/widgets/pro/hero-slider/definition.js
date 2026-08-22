(function (registry) {
    'use strict';

    if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');

    const providers = new Set(['self_hosted', 'youtube', 'vimeo', 'dailymotion', 'embed']);
    const mediaTypes = new Set(['image', 'video']);
    const imageLayouts = new Set(['cover', 'natural']);
    const directions = new Set(['horizontal', 'vertical']);
    const transitions = new Set(['slide', 'fade']);
    const anchors = new Set([
        'top-left', 'top-center', 'top-right',
        'center-left', 'center', 'center-right',
        'bottom-left', 'bottom-center', 'bottom-right',
    ]);
    const paginationPositions = new Set([
        'top-left', 'top-center', 'top-right',
        'center-left', 'center', 'center-right',
        'bottom-left', 'bottom-center', 'bottom-right',
    ]);
    const ratios = new Set(['16/9', '4/3', '1/1', '3/2', '21/9', '9/16', '4/5']);
    const lengthPattern = /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i;
    const advanced = () => registry.advancedDefaults();

    const cleanString = (value, fallback = '') => String(value ?? fallback).trim();
    const positiveNumber = (value, fallback) => {
        const number = Number(value);
        return Number.isFinite(number) && number >= 0 ? number : fallback;
    };
    const booleanValue = (value, fallback = false) => value === undefined || value === null ? fallback : !!value;
    const lengthValue = (value, fallback) => lengthPattern.test(cleanString(value)) ? cleanString(value) : fallback;
    const ratioValue = (value, fallback = '16/9') => ratios.has(cleanString(value)) ? cleanString(value) : fallback;
    const imageLayoutValue = (value, fallback = '') => {
        const layout = cleanString(value).toLowerCase();
        return imageLayouts.has(layout) ? layout : fallback;
    };
    const paginationPositionValue = (value, fallback) => {
        const candidate = cleanString(value).toLowerCase();
        return paginationPositions.has(candidate) ? candidate : fallback;
    };
    const cleanAttributes = (value) => Array.isArray(value)
        ? value.filter((item) => item && typeof item === 'object').map((item) => ({
            key: cleanString(item.key || item.name),
            value: cleanString(item.value),
        })).filter((item) => item.key)
        : [];

    const defaultButton = (index = 0) => ({
        id: 'hero-slider-button-' + (index + 1),
        text: index ? 'Button ' + (index + 1) : 'Learn More',
        cssClass: '',
        actionType: 'link',
        linkUrl: '',
        linkTarget: '',
        linkNofollow: false,
        linkCustomAttributes: [],
        videoSource: 'youtube',
        videoUrl: '',
        imageSource: 'ckfinder',
        imageUrl: '',
        imageAlt: '',
    });

    const positionDefaults = () => {
        const output = {};
        const targets = {
            group: ['bottom-left', '8%', '86%', '70%', 'left'],
            title: ['top-left', '8%', '66%', '70%', 'left'],
            subtitle: ['top-left', '8%', '76%', '70%', 'left'],
            buttons: ['top-left', '8%', '86%', '70%', 'left'],
        };
        Object.entries(targets).forEach(([target, values]) => {
            const [anchor, x, y, width, align] = values;
            Object.assign(output, {
                [target + 'Anchor']: anchor,
                [target + 'X']: x,
                [target + 'Y']: y,
                [target + 'Width']: width,
                [target + 'Align']: align,
                [target + 'AnchorTablet']: '',
                [target + 'XTablet']: '',
                [target + 'YTablet']: '',
                [target + 'WidthTablet']: '',
                [target + 'AlignTablet']: '',
                [target + 'AnchorMobile']: target === 'group' ? 'bottom-center' : 'top-center',
                [target + 'XMobile']: '50%',
                [target + 'YMobile']: target === 'group' ? '90%' : ({ title: '66%', subtitle: '76%', buttons: '86%' }[target] || '90%'),
                [target + 'WidthMobile']: '84%',
                [target + 'AlignMobile']: 'center',
            });
        });
        return output;
    };

    function inferProvider(value) {
        const url = cleanString(value).toLowerCase();
        if (/youtu\.be\/|youtube\.com\//.test(url)) return 'youtube';
        if (/vimeo\.com\//.test(url)) return 'vimeo';
        if (/dailymotion\.com\/|dai\.ly\//.test(url)) return 'dailymotion';
        return '';
    }

    function normalizeProvider(value, url = '') {
        const raw = cleanString(value).toLowerCase();
        const aliases = { file: 'self_hosted', html5: 'self_hosted', native: 'self_hosted', direct: 'self_hosted', iframe: 'embed' };
        const candidate = aliases[raw] || raw;
        if (providers.has(candidate)) return candidate;
        if (!candidate || candidate === 'auto') return inferProvider(url) || 'self_hosted';
        return 'embed';
    }

    function defaultSlide(index) {
        return {
            id: 'hero-slider-slide-' + index,
            mediaType: 'image',
            imageSource: 'ckfinder',
            imageUrl: '',
            imageUrlTablet: '',
            imageUrlMobile: '',
            imageAlt: '',
            imageAltTablet: '',
            imageAltMobile: '',
            imageLayout: 'cover',
            imageLayoutTablet: '',
            imageLayoutMobile: '',
            objectFit: 'cover',
            objectFitTablet: '',
            objectFitMobile: '',
            objectPosition: 'center center',
            objectPositionTablet: '',
            objectPositionMobile: '',
            videoProvider: 'self_hosted',
            videoUrl: '',
            videoPoster: '',
            videoPosterTablet: '',
            videoPosterMobile: '',
            videoAutoplay: 'inherit',
            videoLoop: false,
            videoControls: true,
            videoMuted: true,
            videoResume: true,
            videoAspectRatio: '16/9',
            title: '',
            subtitle: '',
            titleTag: 'h2',
            subtitleTag: 'p',
            showTitle: true,
            showSubtitle: true,
            showButtons: true,
            contentOrder: ['title', 'subtitle', 'buttons'],
            positioningMode: 'grouped',
            ...positionDefaults(),
            buttons: [],
            buttonDirection: 'row',
            buttonDirectionTablet: '',
            buttonDirectionMobile: 'column',
            buttonAlign: 'left',
            buttonAlignTablet: '',
            buttonAlignMobile: 'center',
            buttonGap: '10px',
            buttonGapTablet: '',
            buttonGapMobile: '9px',
            buttonWrap: true,
            buttonWrapTablet: '',
            buttonWrapMobile: true,
            styleOverride: false,
            slideOverlayColor: '',
            slideTitleColor: '',
            slideSubtitleColor: '',
            slideButtonTextColor: '',
            slideButtonBackground: '',
            slideButtonTextColorHover: '',
            slideButtonBackgroundHover: '',
            slideTitleFontSizeMode: 'auto',
            slideTitleFontSize: '',
            slideTitleFontSizeTablet: '',
            slideTitleFontSizeMobile: '',
            slideTitleFontWeight: '',
            slideSubtitleFontSize: '',
            slideSubtitleFontSizeTablet: '',
            slideSubtitleFontSizeMobile: '',
            slideSubtitleFontWeight: '',
            slideContentGap: '',
            slideContentGapTablet: '',
            slideContentGapMobile: '',
        };
    }

    const defaults = () => ({
        ...advanced(),
        slides: [defaultSlide(1), defaultSlide(2)],
        direction: 'horizontal', directionTablet: '', directionMobile: '',
        transition: 'slide', transitionSpeed: 600,
        autoplay: true, autoplaySpeed: 5000,
        pauseOnHover: true, pauseOnFocus: true, pauseOnInteraction: false,
        loop: true, rewind: false, perMove: 1,
        arrows: true, pagination: true, keyboard: true, drag: true,
        previousArrowIcon: 'fas fa-chevron-left', previousArrowIconSource: 'library', previousArrowIconSvg: '',
        nextArrowIcon: 'fas fa-chevron-right', nextArrowIconSource: 'library', nextArrowIconSvg: '',
        arrowPosition: 'inside', arrowPositionTablet: '', arrowPositionMobile: '',
        arrowEdgeOffset: '16px', arrowEdgeOffsetTablet: '', arrowEdgeOffsetMobile: '',
        arrowButtonSize: '38px', arrowButtonSizeTablet: '', arrowButtonSizeMobile: '',
        arrowIconSize: '16px', arrowIconSizeTablet: '', arrowIconSizeMobile: '',
        arrowColor: '#fff', arrowBackground: 'rgba(0,0,0,.45)', arrowHoverColor: '#fff', arrowHoverBackground: 'rgba(0,0,0,.65)',
        arrowRadiusTop: '999px', arrowRadiusRight: '999px', arrowRadiusBottom: '999px', arrowRadiusLeft: '999px',
        mouseWheel: false, wheelRelease: false, progress: true,
        lazyLoad: true, gap: '0px', padding: '0px',
        paginationPositionHorizontal: 'bottom-center', paginationPositionHorizontalTablet: '', paginationPositionHorizontalMobile: '',
        paginationPositionVertical: 'center-right', paginationPositionVerticalTablet: '', paginationPositionVerticalMobile: '',
        paginationPlacementModeHorizontal: 'basic', paginationPlacementModeHorizontalTablet: '', paginationPlacementModeHorizontalMobile: '',
        paginationPlacementModeVertical: 'basic', paginationPlacementModeVerticalTablet: '', paginationPlacementModeVerticalMobile: '',
        paginationAlignmentHorizontal: 'center', paginationAlignmentHorizontalTablet: '', paginationAlignmentHorizontalMobile: '',
        paginationAlignmentVertical: 'center', paginationAlignmentVerticalTablet: '', paginationAlignmentVerticalMobile: '',
        paginationOffsetXHorizontal: '0px', paginationOffsetXHorizontalTablet: '', paginationOffsetXHorizontalMobile: '',
        paginationOffsetYHorizontal: '0px', paginationOffsetYHorizontalTablet: '', paginationOffsetYHorizontalMobile: '',
        paginationOffsetXVertical: '0px', paginationOffsetXVerticalTablet: '', paginationOffsetXVerticalMobile: '',
        paginationOffsetYVertical: '0px', paginationOffsetYVerticalTablet: '', paginationOffsetYVerticalMobile: '',
        videoAutoplay: false, videoDurationMode: 'interval', videoAutoplayFallback: 'interval',
        videoMutedAutoplay: true, videoControls: 'custom', videoLoop: false, videoResume: true, videoPrivacyMode: false,
        dailymotionPlayerId: '', dailymotionSdkUrl: '',
        heightMode: 'adaptive', fixedHeight: '520px', fixedHeightTablet: '', fixedHeightMobile: '',
        minHeight: '420px', minHeightTablet: '360px', minHeightMobile: '280px',
        overlayColor: 'rgba(0,0,0,.2)',
        titleColor: '#fff', subtitleColor: '#fff', buttonTextColor: '#fff',
        buttonBackground: '#30343a', buttonTextColorHover: '#fff', buttonBackgroundHover: '#1f2328',
        titleFontSizeMode: 'auto', titleFontSize: '52px', titleFontSizeTablet: '42px', titleFontSizeMobile: '34px', titleFontWeight: '700',
        subtitleFontSize: '22px', subtitleFontSizeTablet: '18px', subtitleFontSizeMobile: '16px', subtitleFontWeight: '400',
        contentGap: '12px', contentGapTablet: '10px', contentGapMobile: '9px',
        buttonRadius: '999px', buttonPaddingX: '18px', buttonPaddingY: '10px',
        buttonRadiusTop: '999px', buttonRadiusRight: '999px', buttonRadiusBottom: '999px', buttonRadiusLeft: '999px',
        buttonPaddingTop: '10px', buttonPaddingRight: '18px', buttonPaddingBottom: '10px', buttonPaddingLeft: '18px',
        modalBackground: 'rgba(0,0,0,.92)', modalUiColor: '#fff', modalUiHoverColor: '#6979f8', modalVideoWidth: '75%',
        cssClass: '',
    });

    function normalizeButton(button, index) {
        const source = button && typeof button === 'object' ? button : {};
        const item = { ...defaultButton(index), ...source };
        item.id = cleanString(item.id, 'hero-slider-button-' + (index + 1));
        item.text = cleanString(item.text, 'Learn More');
        item.cssClass = cleanString(item.cssClass);
        item.actionType = ['link', 'video_popup', 'image_popup'].includes(cleanString(item.actionType)) ? cleanString(item.actionType) : 'link';
        item.linkUrl = cleanString(item.linkUrl || item.url);
        item.linkTarget = item.linkTarget === '_blank' ? '_blank' : '';
        item.linkNofollow = !!item.linkNofollow;
        item.linkCustomAttributes = cleanAttributes(item.linkCustomAttributes);
        item.videoSource = ['youtube', 'vimeo', 'dailymotion', 'self_hosted'].includes(cleanString(item.videoSource)) ? cleanString(item.videoSource) : 'youtube';
        item.videoUrl = cleanString(item.videoUrl);
        item.imageSource = cleanString(item.imageSource) === 'url' ? 'url' : 'ckfinder';
        item.imageUrl = cleanString(item.imageUrl);
        item.imageAlt = cleanString(item.imageAlt);
        return item;
    }

    function normalizeSlide(raw, index) {
        const input = raw && typeof raw === 'object' ? { ...raw } : {};
        const nestedMedia = input.media && typeof input.media === 'object' ? input.media : {};
        const nestedVideo = input.video && typeof input.video === 'object' ? input.video : {};
        const merged = {
            ...defaultSlide(index + 1),
            ...input,
            ...nestedMedia,
            ...nestedVideo,
        };
        const videoUrl = cleanString(merged.videoUrl || merged.url || merged.fileUrl || nestedMedia.url || '');
        const explicitProvider = input.videoProvider ?? input.provider ?? nestedMedia.provider ?? nestedVideo.provider;
        const providerInput = explicitProvider ?? (Object.prototype.hasOwnProperty.call(input, 'videoProvider') ? merged.videoProvider : '');
        const poster = cleanString(merged.videoPoster || merged.poster || nestedVideo.poster || '');
        const mediaType = mediaTypes.has(cleanString(merged.mediaType).toLowerCase())
            ? cleanString(merged.mediaType).toLowerCase()
            : (videoUrl ? 'video' : 'image');
        const buttons = Array.isArray(merged.buttons) ? merged.buttons.slice(0, 3).map(normalizeButton) : [];
        const allowedOrder = ['title', 'subtitle', 'buttons'];
        const suppliedOrder = Array.isArray(merged.contentOrder)
            ? merged.contentOrder.filter((key, orderIndex, values) => allowedOrder.includes(key) && values.indexOf(key) === orderIndex)
            : [];
        const output = {
            ...merged,
            positioningMode: merged.positioningMode === 'independent' ? 'independent' : 'grouped',
            titleTag: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'].includes(merged.titleTag) ? merged.titleTag : 'h2',
            subtitleTag: ['p', 'div', 'span'].includes(merged.subtitleTag) ? merged.subtitleTag : 'p',
            showTitle: merged.showTitle !== false,
            showSubtitle: merged.showSubtitle !== false,
            showButtons: merged.showButtons !== false,
            contentOrder: [...suppliedOrder, ...allowedOrder.filter((key) => !suppliedOrder.includes(key))],
            styleOverride: !!merged.styleOverride,
        };
        const hasCustomSlideTitleSize = ['slideTitleFontSize', 'slideTitleFontSizeTablet', 'slideTitleFontSizeMobile'].some((key) => cleanString(merged[key]) !== '');
        output.slideTitleFontSizeMode = ['auto', 'custom'].includes(cleanString(merged.slideTitleFontSizeMode).toLowerCase())
            ? cleanString(merged.slideTitleFontSizeMode).toLowerCase()
            : (hasCustomSlideTitleSize ? 'custom' : 'auto');
        ['group', 'title', 'subtitle', 'buttons'].forEach((target) => ['', 'Tablet', 'Mobile'].forEach((suffix) => {
            const anchorKey = target + 'Anchor' + suffix;
            const alignKey = target + 'Align' + suffix;
            const fallbackAnchor = suffix ? '' : defaultSlide(index + 1)[anchorKey];
            const fallbackAlign = suffix ? '' : defaultSlide(index + 1)[alignKey];
            output[anchorKey] = anchors.has(cleanString(merged[anchorKey])) ? cleanString(merged[anchorKey]) : fallbackAnchor;
            output[alignKey] = ['left', 'center', 'right'].includes(cleanString(merged[alignKey])) ? cleanString(merged[alignKey]) : fallbackAlign;
            ['X', 'Y', 'Width'].forEach((part) => {
                const key = target + part + suffix;
                output[key] = lengthValue(merged[key], suffix ? '' : defaultSlide(index + 1)[key]);
            });
        }));
        ['', 'Tablet', 'Mobile'].forEach((suffix) => {
            const directionKey = 'buttonDirection' + suffix;
            const alignKey = 'buttonAlign' + suffix;
            const gapKey = 'buttonGap' + suffix;
            const wrapKey = 'buttonWrap' + suffix;
            output[directionKey] = ['row', 'column'].includes(cleanString(merged[directionKey])) ? cleanString(merged[directionKey]) : (suffix ? '' : 'row');
            output[alignKey] = ['left', 'center', 'right'].includes(cleanString(merged[alignKey])) ? cleanString(merged[alignKey]) : (suffix ? '' : 'left');
            output[gapKey] = lengthValue(merged[gapKey], suffix ? '' : '10px');
            output[wrapKey] = suffix && merged[wrapKey] === '' ? '' : booleanValue(merged[wrapKey], true);
            ['slideTitleFontSize', 'slideSubtitleFontSize', 'slideContentGap'].forEach((base) => {
                const key = base + suffix;
                output[key] = lengthValue(merged[key], '');
            });
        });
        return {
            ...output,
            id: cleanString(merged.id, 'hero-slider-slide-' + (index + 1)) || 'hero-slider-slide-' + (index + 1),
            mediaType,
            imageSource: cleanString(merged.imageSource, 'ckfinder') === 'url' ? 'url' : 'ckfinder',
            imageUrl: cleanString(merged.imageUrl || (mediaType === 'image' ? merged.url : '')),
            imageUrlTablet: cleanString(merged.imageUrlTablet),
            imageUrlMobile: cleanString(merged.imageUrlMobile),
            imageAlt: cleanString(merged.imageAlt),
            imageAltTablet: cleanString(merged.imageAltTablet),
            imageAltMobile: cleanString(merged.imageAltMobile),
            imageLayout: imageLayoutValue(merged.imageLayout, 'cover'),
            imageLayoutTablet: imageLayoutValue(merged.imageLayoutTablet),
            imageLayoutMobile: imageLayoutValue(merged.imageLayoutMobile),
            objectFit: ['cover', 'contain', 'fill'].includes(cleanString(merged.objectFit)) ? cleanString(merged.objectFit) : 'cover',
            objectFitTablet: ['cover', 'contain', 'fill'].includes(cleanString(merged.objectFitTablet)) ? cleanString(merged.objectFitTablet) : '',
            objectFitMobile: ['cover', 'contain', 'fill'].includes(cleanString(merged.objectFitMobile)) ? cleanString(merged.objectFitMobile) : '',
            objectPosition: cleanString(merged.objectPosition, 'center center'),
            objectPositionTablet: cleanString(merged.objectPositionTablet),
            objectPositionMobile: cleanString(merged.objectPositionMobile),
            videoProvider: normalizeProvider(providerInput, videoUrl),
            videoUrl,
            videoPoster: poster,
            videoPosterTablet: cleanString(merged.videoPosterTablet),
            videoPosterMobile: cleanString(merged.videoPosterMobile),
            videoAutoplay: ['inherit', 'on', 'off'].includes(cleanString(merged.videoAutoplay).toLowerCase()) ? cleanString(merged.videoAutoplay).toLowerCase() : 'inherit',
            videoLoop: booleanValue(merged.videoLoop),
            videoControls: merged.videoControls !== false,
            videoMuted: merged.videoMuted !== false,
            videoResume: merged.videoResume !== false,
            videoAspectRatio: ratioValue(merged.videoAspectRatio),
            title: cleanString(merged.title),
            subtitle: cleanString(merged.subtitle),
            buttons,
        };
    }

    function normalize(node) {
        const normalized = node && typeof node === 'object' ? node : {};
        const incomingSettings = normalized.settings && typeof normalized.settings === 'object' ? normalized.settings : {};
        const defaultSettings = defaults();
        const settings = normalized.settings = { ...defaultSettings, ...incomingSettings };
        const hasLegacyTitleSize = ['titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile'].some((key) => settings[key] !== defaultSettings[key]);
        settings.titleFontSizeMode = ['auto', 'custom'].includes(cleanString(settings.titleFontSizeMode).toLowerCase())
            ? cleanString(settings.titleFontSizeMode).toLowerCase()
            : (hasLegacyTitleSize ? 'custom' : 'auto');
        settings.slides = (Array.isArray(settings.slides) ? settings.slides : [])
            .slice(0, 30)
            .map(normalizeSlide);
        if (!settings.slides.length) settings.slides = [defaultSlide(1)];
        settings.direction = directions.has(cleanString(settings.direction).toLowerCase()) ? cleanString(settings.direction).toLowerCase() : 'horizontal';
        ['directionTablet', 'directionMobile'].forEach((key) => {
            const value = cleanString(settings[key]).toLowerCase();
            settings[key] = directions.has(value) ? value : '';
        });
        settings.transition = transitions.has(cleanString(settings.transition).toLowerCase()) ? cleanString(settings.transition).toLowerCase() : 'slide';
        settings.arrowPosition = ['inside', 'outside'].includes(cleanString(settings.arrowPosition).toLowerCase()) ? cleanString(settings.arrowPosition).toLowerCase() : 'inside';
        ['arrowPositionTablet', 'arrowPositionMobile'].forEach((key) => {
            const value = cleanString(settings[key]).toLowerCase();
            settings[key] = ['inside', 'outside'].includes(value) ? value : '';
        });
        settings.videoDurationMode = cleanString(settings.videoDurationMode).toLowerCase() === 'duration' ? 'duration' : 'interval';
        settings.videoAutoplayFallback = 'interval';
        settings.videoControls = cleanString(settings.videoControls).toLowerCase() === 'provider' ? 'provider' : 'custom';
        settings.heightMode = cleanString(settings.heightMode).toLowerCase() === 'fixed' ? 'fixed' : 'adaptive';
        settings.paginationPositionHorizontal = paginationPositionValue(settings.paginationPositionHorizontal, defaults().paginationPositionHorizontal);
        settings.paginationPositionVertical = paginationPositionValue(settings.paginationPositionVertical, defaults().paginationPositionVertical);
        ['paginationPositionHorizontalTablet', 'paginationPositionHorizontalMobile', 'paginationPositionVerticalTablet', 'paginationPositionVerticalMobile'].forEach((key) => {
            settings[key] = paginationPositionValue(settings[key], '');
        });
        ['', 'Tablet', 'Mobile'].forEach((suffix) => {
            ['Horizontal', 'Vertical'].forEach((axis) => {
                const modeKey = 'paginationPlacementMode' + axis + suffix;
                const alignmentKey = 'paginationAlignment' + axis + suffix;
                const positionKey = 'paginationPosition' + axis + suffix;
                const rawPosition = settings[positionKey];
                const incomingMode = cleanString(incomingSettings[modeKey]).toLowerCase();
                const basicPositions = axis === 'Horizontal'
                    ? ['bottom-left', 'bottom-center', 'bottom-right', 'center']
                    : ['top-right', 'center-right', 'bottom-right', 'center'];
                const inferredMode = Object.prototype.hasOwnProperty.call(incomingSettings, positionKey) && rawPosition && !basicPositions.includes(rawPosition) ? 'custom' : 'basic';
                settings[modeKey] = ['basic', 'custom'].includes(incomingMode) ? incomingMode : (suffix && !Object.prototype.hasOwnProperty.call(incomingSettings, positionKey) ? '' : inferredMode);
                const incomingAlignment = cleanString(incomingSettings[alignmentKey]).toLowerCase();
                const inferredAlignment = axis === 'Horizontal'
                    ? (rawPosition === 'bottom-left' ? 'left' : (rawPosition === 'bottom-right' ? 'right' : 'center'))
                    : (rawPosition === 'top-right' ? 'top' : (rawPosition === 'bottom-right' ? 'bottom' : 'center'));
                const allowedAlignment = axis === 'Horizontal' ? ['left', 'center', 'right'] : ['top', 'center', 'bottom'];
                settings[alignmentKey] = allowedAlignment.includes(incomingAlignment) ? incomingAlignment : (suffix && !rawPosition ? '' : inferredAlignment);
            });
        });
        settings.autoplaySpeed = Math.min(60000, Math.max(100, Math.round(positiveNumber(settings.autoplaySpeed, 5000))));
        settings.transitionSpeed = Math.min(10000, Math.max(0, Math.round(positiveNumber(settings.transitionSpeed, 600))));
        settings.perMove = Math.min(10, Math.max(1, Math.round(positiveNumber(settings.perMove, 1))));
        settings.position = ['default', 'absolute', 'fixed'].includes(cleanString(settings.position).toLowerCase()) ? cleanString(settings.position).toLowerCase() : 'default';
        settings.horizontalOrientation = ['left', 'right'].includes(cleanString(settings.horizontalOrientation).toLowerCase()) ? cleanString(settings.horizontalOrientation).toLowerCase() : 'left';
        settings.verticalOrientation = ['top', 'bottom'].includes(cleanString(settings.verticalOrientation).toLowerCase()) ? cleanString(settings.verticalOrientation).toLowerCase() : 'top';
        ['autoplay', 'pauseOnHover', 'pauseOnFocus', 'pauseOnInteraction', 'loop', 'rewind', 'arrows', 'pagination', 'keyboard', 'drag', 'mouseWheel', 'wheelRelease', 'progress', 'lazyLoad', 'videoAutoplay', 'videoMutedAutoplay', 'videoLoop', 'videoResume', 'videoPrivacyMode'].forEach((key) => {
            settings[key] = booleanValue(settings[key], defaults()[key]);
        });
        ['gap', 'padding', 'fixedHeight', 'fixedHeightTablet', 'fixedHeightMobile', 'minHeight', 'minHeightTablet', 'minHeightMobile', 'titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile', 'subtitleFontSize', 'subtitleFontSizeTablet', 'subtitleFontSizeMobile', 'contentGap', 'contentGapTablet', 'contentGapMobile', 'arrowEdgeOffset', 'arrowEdgeOffsetTablet', 'arrowEdgeOffsetMobile', 'arrowButtonSize', 'arrowButtonSizeTablet', 'arrowButtonSizeMobile', 'arrowIconSize', 'arrowIconSizeTablet', 'arrowIconSizeMobile', 'paginationOffsetXHorizontal', 'paginationOffsetXHorizontalTablet', 'paginationOffsetXHorizontalMobile', 'paginationOffsetYHorizontal', 'paginationOffsetYHorizontalTablet', 'paginationOffsetYHorizontalMobile', 'paginationOffsetXVertical', 'paginationOffsetXVerticalTablet', 'paginationOffsetXVerticalMobile', 'paginationOffsetYVertical', 'paginationOffsetYVerticalTablet', 'paginationOffsetYVerticalMobile', 'modalVideoWidth'].forEach((key) => {
            settings[key] = lengthValue(settings[key], defaultSettings[key]);
        });
        ['', 'Tablet', 'Mobile'].forEach((deviceSuffix) => {
            const responsiveFallback = deviceSuffix === '' ? null : '';
            const legacyRadius = lengthValue(incomingSettings['buttonRadius' + deviceSuffix], '');
            const legacyPaddingX = lengthValue(incomingSettings['buttonPaddingX' + deviceSuffix], '');
            const legacyPaddingY = lengthValue(incomingSettings['buttonPaddingY' + deviceSuffix], '');
            ['Top', 'Right', 'Bottom', 'Left'].forEach((side) => {
                const radiusKey = 'buttonRadius' + side + deviceSuffix;
                const paddingKey = 'buttonPadding' + side + deviceSuffix;
                const radiusFallback = responsiveFallback ?? defaultSettings['buttonRadius' + side];
                const paddingFallback = responsiveFallback ?? defaultSettings['buttonPadding' + side];
                settings[radiusKey] = Object.prototype.hasOwnProperty.call(incomingSettings, radiusKey)
                    ? lengthValue(incomingSettings[radiusKey], radiusFallback)
                    : (legacyRadius || lengthValue(settings[radiusKey], radiusFallback));
                const legacyPadding = side === 'Top' || side === 'Bottom' ? legacyPaddingY : legacyPaddingX;
                settings[paddingKey] = Object.prototype.hasOwnProperty.call(incomingSettings, paddingKey)
                    ? lengthValue(incomingSettings[paddingKey], paddingFallback)
                    : (legacyPadding || lengthValue(settings[paddingKey], paddingFallback));
            });
        });
        ['', 'Tablet', 'Mobile'].forEach((deviceSuffix) => {
            ['Top', 'Right', 'Bottom', 'Left'].forEach((side) => {
                const key = 'arrowRadius' + side + deviceSuffix;
                settings[key] = lengthValue(settings[key], deviceSuffix ? '' : defaultSettings['arrowRadius' + side]);
            });
        });
        ['previousArrowIcon', 'nextArrowIcon'].forEach((key) => {
            const fallback = key === 'previousArrowIcon' ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
            settings[key] = cleanString(settings[key], fallback) || fallback;
            settings[key + 'Source'] = settings[key + 'Source'] === 'svg' ? 'svg' : 'library';
            settings[key + 'Svg'] = cleanString(settings[key + 'Svg']);
        });
        ['dailymotionPlayerId', 'dailymotionSdkUrl', 'cssId', 'cssClass'].forEach((key) => { settings[key] = cleanString(settings[key]); });
        ['overlayColor', 'titleColor', 'subtitleColor', 'buttonTextColor', 'buttonBackground', 'buttonTextColorHover', 'buttonBackgroundHover', 'arrowColor', 'arrowBackground', 'arrowHoverColor', 'arrowHoverBackground', 'titleFontWeight', 'subtitleFontWeight', 'modalBackground', 'modalUiColor', 'modalUiHoverColor', 'buttonRadius', 'buttonPaddingX', 'buttonPaddingY'].forEach((key) => { settings[key] = cleanString(settings[key], defaultSettings[key]); });
        return normalized;
    }

    registry.register({type: 'hero_slider',defaults,normalize});
})(window.PageBuilderElementorV24Widgets);
