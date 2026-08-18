(function (registry) {
    'use strict';

    const advanced = () => window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
    const defaultButton = (index = 0) => ({
        id: `hero-button-${index + 1}`,
        text: index ? `Button ${index + 1}` : 'Watch Video',
        actionType: index ? 'link' : 'video_popup',
        linkUrl: '',
        linkTarget: '',
        linkNofollow: false,
        linkCustomAttributes: [],
        videoSource: 'youtube',
        videoUrl: index ? '' : 'https://www.youtube.com/watch?v=h529sg3pEV4',
        imageSource: 'ckfinder',
        imageUrl: '',
        imageAlt: '',
    });
    const position = (anchor, x, y, width, align) => ({ anchor, x, y, width, align });
    const defaults = () => ({
        ...advanced(),
        positioningMode: 'grouped',
        title: 'MG 5 GT',
        subtitle: 'Light Up Desire',
        titleTag: 'h2',
        subtitleTag: 'p',
        showTitle: true,
        showSubtitle: true,
        showButtons: true,
        contentOrder: ['title', 'subtitle', 'buttons'],
        buttons: [defaultButton()],
        imageSource: 'ckfinder',
        imageSourceTablet: '',
        imageSourceMobile: '',
        imageUrl: '',
        imageUrlTablet: '',
        imageUrlMobile: '',
        imageAlt: 'Hero banner image',
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
        minHeight: '500px',
        minHeightTablet: '520px',
        minHeightMobile: '680px',
        ...responsivePositionDefaults(),
        buttonDirection: 'row',
        buttonDirectionTablet: '',
        buttonDirectionMobile: 'column',
        buttonAlign: 'left',
        buttonAlignTablet: '',
        buttonAlignMobile: 'center',
        buttonAlignMode: 'inherit',
        buttonAlignModeTablet: '',
        buttonAlignModeMobile: '',
        buttonGap: '10px',
        buttonGapTablet: '',
        buttonGapMobile: '9px',
        buttonWrap: true,
        buttonWrapTablet: null,
        buttonWrapMobile: true,
        contentGap: '14px',
        contentGapTablet: '',
        contentGapMobile: '10px',
        overlayColor: 'rgba(255,255,255,0)',
        titleColor: '#292d32',
        titleFontSizeMode: 'auto',
        titleFontSize: '48px',
        titleFontSizeTablet: '38px',
        titleFontSizeMobile: '34px',
        titleFontWeight: '700',
        subtitleColor: '#292d32',
        subtitleFontSize: '22px',
        subtitleFontSizeTablet: '18px',
        subtitleFontSizeMobile: '17px',
        subtitleFontWeight: '400',
        buttonTextColor: '#ffffff',
        buttonBackground: '#30343a',
        buttonTextColorHover: '#ffffff',
        buttonBackgroundHover: '#1f2328',
        buttonRadius: '999px',
        buttonPaddingX: '18px',
        buttonPaddingY: '10px',
        modalBackground: 'rgba(0,0,0,.92)',
        modalUiColor: '#ffffff',
        modalUiHoverColor: '#6979f8',
        modalVideoWidth: '75%',
    });

    function responsivePositionDefaults() {
        const out = {};
        const devices = [
            ['', { group: position('center-left', 17, 54, 32, 'left'), title: position('top-left', 17, 52, 30, 'left'), subtitle: position('top-left', 17, 65, 30, 'left'), buttons: position('top-left', 17, 77, 32, 'left') }],
            ['Tablet', { group: position('center-left', 11, 55, 42, 'left'), title: position('top-left', 11, 48, 42, 'left'), subtitle: position('top-left', 11, 62, 42, 'left'), buttons: position('top-left', 11, 75, 42, 'left') }],
            ['Mobile', { group: position('top-center', 50, 13, 84, 'center'), title: position('top-center', 50, 12, 84, 'center'), subtitle: position('top-center', 50, 20, 84, 'center'), buttons: position('top-center', 50, 27, 72, 'center') }],
        ];
        devices.forEach(([suffix, targets]) => Object.entries(targets).forEach(([target, value]) => {
            const prefix = target + suffix;
            out[prefix + 'Anchor'] = value.anchor;
            out[prefix + 'X'] = value.x;
            out[prefix + 'Y'] = value.y;
            out[prefix + 'Width'] = value.width;
            out[prefix + 'Align'] = value.align;
        }));
        return out;
    }

    function normalizeButton(button, index) {
        const source = button && typeof button === 'object' ? button : {};
        const item = { ...defaultButton(index), ...source };
        item.id = String(item.id || `hero-button-${index + 1}`);
        item.actionType = ['link', 'video_popup', 'image_popup'].includes(item.actionType) ? item.actionType : 'link';
        item.videoSource = ['youtube', 'vimeo', 'dailymotion', 'self_hosted'].includes(item.videoSource) ? item.videoSource : 'youtube';
        item.imageSource = item.imageSource === 'url' ? 'url' : 'ckfinder';
        item.linkCustomAttributes = Array.isArray(item.linkCustomAttributes) ? item.linkCustomAttributes : [];
        item.linkNofollow = Boolean(item.linkNofollow);
        return item;
    }

    function normalize(node) {
        const baseline = defaults();
        const settings = node.settings = { ...baseline, ...(node.settings || {}) };
        const sourceButtons = Array.isArray(settings.buttons) && settings.buttons.length ? settings.buttons : baseline.buttons;
        settings.buttons = sourceButtons.slice(0, 3).map(normalizeButton);
        const allowedOrder = ['title', 'subtitle', 'buttons'];
        const suppliedOrder = Array.isArray(settings.contentOrder) ? settings.contentOrder.filter((key, index, values) => allowedOrder.includes(key) && values.indexOf(key) === index) : [];
        settings.contentOrder = [...suppliedOrder, ...allowedOrder.filter((key) => !suppliedOrder.includes(key))];
        settings.positioningMode = settings.positioningMode === 'independent' ? 'independent' : 'grouped';
        settings.titleTag = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div'].includes(settings.titleTag) ? settings.titleTag : 'h2';
        const legacyTitleSize = ['titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile'].some((key) => settings[key] !== '' && settings[key] !== baseline[key]);
        settings.titleFontSizeMode = ['auto', 'custom'].includes(settings.titleFontSizeMode) ? settings.titleFontSizeMode : (legacyTitleSize ? 'custom' : 'auto');
        settings.subtitleTag = ['p', 'div', 'span'].includes(settings.subtitleTag) ? settings.subtitleTag : 'p';
        settings.buttonAlignMode = ['inherit', 'custom'].includes(settings.buttonAlignMode) ? settings.buttonAlignMode : 'inherit';
        ['buttonAlignModeTablet', 'buttonAlignModeMobile'].forEach((key) => {
            if (!['', 'inherit', 'custom'].includes(settings[key])) settings[key] = '';
        });
        ['showTitle', 'showSubtitle', 'showButtons'].forEach((key) => { settings[key] = settings[key] !== false; });
        ['', 'Tablet', 'Mobile'].forEach((suffix) => {
            const key = 'imageLayout' + suffix;
            const value = String(settings[key] ?? '').trim().toLowerCase();
            settings[key] = ['cover', 'natural'].includes(value) ? value : (suffix ? '' : 'cover');
        });
        return node;
    }

    registry.register({
        type: 'hero_banner',
        label: 'Hero Banner',
        category: 'pro',
        icon: 'fas fa-image',
        toolbox: true,
        canvas: '/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue',
        settings: '/js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue',
        defaults,
        normalize,
    });
})(window.PageBuilderElementorV23Widgets);
