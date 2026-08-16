(function (registry) {
    'use strict';

    if (!registry) throw new Error('Page Builder Elementor widget registry is not loaded.');

    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
    const enumValue = (value, allowed, fallback) => allowed.includes(value) ? value : fallback;
    const text = (value, fallback = '') => String(value == null ? fallback : value);
    const length = (value, fallback, allowEmpty = false) => {
        const raw = text(value).trim();
        if (allowEmpty && raw === '') return '';
        return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw) ? raw : fallback;
    };
    const aspectRatio = (value, fallback, allowEmpty = false) => {
        const raw = text(value).trim();
        if (allowEmpty && raw === '') return '';
        return /^\d+(?:\.\d+)?\s*\/\s*\d+(?:\.\d+)?$/.test(raw) ? raw.replace(/\s*\/\s*/, ' / ') : fallback;
    };
    const color = (value, fallback) => {
        const raw = text(value).trim();
        return /^(?:#[0-9a-f]{3,8}|transparent|currentcolor|(?:rgba?|hsla?)\([\d.%,\s/-]+\))$/i.test(raw) ? raw : fallback;
    };
    const safeUrl = (value) => {
        const raw = text(value).trim();
        return /^(?:https?:\/\/|\/)[^\s"'<>]*$/i.test(raw) ? raw : '';
    };
    const imagePosition = (value, fallback, allowEmpty = false) => {
        const raw = text(value).trim().toLowerCase();
        if (allowEmpty && raw === '') return '';
        return /^(?:left|center|right)(?:\s+(?:top|center|bottom))?$/.test(raw) ? raw : fallback;
    };
    const duration = (value, fallback) => {
        const raw = text(value).trim();
        if (/^\d+(?:\.\d+)?$/.test(raw)) return `${Math.max(0, Math.min(2000, Number(raw)))}ms`;
        return /^\d+(?:\.\d+)?(?:ms|s)$/i.test(raw) ? raw : fallback;
    };
    const item = (id, name, swatchColor) => ({
        id,
        name,
        description: '',
        swatchColor,
        imageSource: 'ckfinder',
        imageUrl: '',
        imageAlt: '',
        imageSourceTablet: '',
        imageUrlTablet: '',
        imageAltTablet: '',
        imageSourceMobile: '',
        imageUrlMobile: '',
        imageAltMobile: '',
    });
    const defaultItems = () => [
        item('color-1', 'Black', '#111827'),
        item('color-2', 'Silver', '#98a2b3'),
        item('color-3', 'Red', '#d92d20'),
    ];
    const defaults = () => ({
        ...advanced(),
        title: 'Choose Your Color',
        showTitle: true,
        description: 'Select a color to preview your product.',
        showDescription: true,
        titleTag: 'h2',
        descriptionTag: 'p',
        titleAlignment: 'left',
        titleAlignmentTablet: '',
        titleAlignmentMobile: '',
        items: defaultItems(),
        defaultItemId: 'color-1',
        listPosition: 'bottom',
        listPositionTablet: '',
        listPositionMobile: '',
        listAlignment: 'auto',
        listAlignmentTablet: '',
        listAlignmentMobile: '',
        imageAspectRatio: '16 / 9',
        imageAspectRatioTablet: '',
        imageAspectRatioMobile: '',
        imageFit: 'contain',
        imageFitTablet: '',
        imageFitMobile: '',
        imagePosition: 'center center',
        imagePositionTablet: '',
        imagePositionMobile: '',
        transition: 'fade',
        transitionDuration: '300ms',
        surfaceBackground: '#f7f9fa',
        surfaceRadius: '10px',
        surfacePadding: '16px',
        imageRadius: '20px',
        swatchWidth: '180px',
        swatchHeight: '104px',
        swatchRadius: '0px',
        listGap: '16px',
        itemTextGap: '8px',
        activeIndicatorColor: '#ffffff',
        activeIndicatorSize: '34px',
        titleColor: '#101828',
        descriptionColor: '#475467',
        itemNameColor: '#101828',
        itemDescriptionColor: '#667085',
        titleFontSizeMode: 'auto',
        titleFontSize: '48px',
        titleFontSizeTablet: '',
        titleFontSizeMobile: '',
        descriptionFontSize: '18px',
        descriptionFontSizeTablet: '',
        descriptionFontSizeMobile: '',
        itemNameFontSize: '16px',
        itemNameFontSizeTablet: '',
        itemNameFontSizeMobile: '',
        itemDescriptionFontSize: '13px',
        itemDescriptionFontSizeTablet: '',
        itemDescriptionFontSizeMobile: '',
    });

    registry.register({
        type: 'product_color_selector',
        label: 'Product Color Selector',
        category: 'pro',
        icon: 'fas fa-palette',
        toolbox: true,
        canvas: '/js/pagebuilder_elementor_v23/widgets/pro/product-color-selector/Canvas.vue',
        settings: '/js/pagebuilder_elementor_v23/widgets/pro/product-color-selector/Settings.vue',
        defaults,
        normalize(node) {
            const base = defaults();
            const s = node.settings = { ...base, ...(node.settings || {}) };
            const textTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span'];
            const alignments = ['left', 'center', 'right', 'justify'];
            const positions = ['top', 'bottom', 'left', 'right'];
            const listAlignments = ['auto', 'start', 'center', 'end', 'space-between'];

            s.title = text(s.title, base.title);
            s.showTitle = s.showTitle !== false;
            s.description = text(s.description, base.description);
            s.showDescription = s.showDescription !== false;
            s.titleTag = enumValue(text(s.titleTag).toLowerCase(), textTags, base.titleTag);
            const legacyTitleSize = ['titleFontSize', 'titleFontSizeTablet', 'titleFontSizeMobile'].some((key) => s[key] !== '' && s[key] !== base[key]);
            s.titleFontSizeMode = ['auto', 'custom'].includes(s.titleFontSizeMode) ? s.titleFontSizeMode : (legacyTitleSize ? 'custom' : 'auto');
            s.descriptionTag = enumValue(text(s.descriptionTag).toLowerCase(), textTags, base.descriptionTag);
            s.titleAlignment = enumValue(s.titleAlignment, alignments, base.titleAlignment);
            ['titleAlignmentTablet', 'titleAlignmentMobile'].forEach((key) => {
                s[key] = s[key] === '' ? '' : enumValue(s[key], alignments, '');
            });
            s.listPosition = enumValue(s.listPosition, positions, base.listPosition);
            ['listPositionTablet', 'listPositionMobile'].forEach((key) => {
                s[key] = s[key] === '' ? '' : enumValue(s[key], positions, '');
            });
            s.listAlignment = enumValue(s.listAlignment, listAlignments, base.listAlignment);
            ['listAlignmentTablet', 'listAlignmentMobile'].forEach((key) => {
                s[key] = s[key] === '' ? '' : enumValue(s[key], listAlignments, '');
            });
            s.imageAspectRatio = aspectRatio(s.imageAspectRatio, base.imageAspectRatio);
            ['imageAspectRatioTablet', 'imageAspectRatioMobile'].forEach((key) => {
                s[key] = aspectRatio(s[key], '', true);
            });
            s.imageFit = enumValue(s.imageFit, ['contain', 'cover', 'fill'], base.imageFit);
            ['imageFitTablet', 'imageFitMobile'].forEach((key) => {
                s[key] = s[key] === '' ? '' : enumValue(s[key], ['contain', 'cover', 'fill'], '');
            });
            s.imagePosition = imagePosition(s.imagePosition, base.imagePosition);
            ['imagePositionTablet', 'imagePositionMobile'].forEach((key) => {
                s[key] = imagePosition(s[key], '', true);
            });
            s.transition = enumValue(s.transition, ['none', 'fade', 'slide'], base.transition);
            s.transitionDuration = duration(s.transitionDuration, base.transitionDuration);

            ['surfaceRadius', 'surfacePadding', 'imageRadius', 'swatchWidth', 'swatchHeight', 'swatchRadius', 'listGap', 'itemTextGap', 'activeIndicatorSize', 'titleFontSize', 'descriptionFontSize', 'itemNameFontSize', 'itemDescriptionFontSize'].forEach((key) => {
                s[key] = length(s[key], base[key]);
            });
            ['titleFontSizeTablet', 'titleFontSizeMobile', 'descriptionFontSizeTablet', 'descriptionFontSizeMobile', 'itemNameFontSizeTablet', 'itemNameFontSizeMobile', 'itemDescriptionFontSizeTablet', 'itemDescriptionFontSizeMobile'].forEach((key) => {
                s[key] = length(s[key], '', true);
            });
            ['surfaceBackground', 'activeIndicatorColor', 'titleColor', 'descriptionColor', 'itemNameColor', 'itemDescriptionColor'].forEach((key) => {
                s[key] = color(s[key], base[key]);
            });

            const sourceItems = Array.isArray(s.items) && s.items.length ? s.items : base.items;
            const ids = new Set();
            s.items = sourceItems.map((entry, index) => {
                const fallback = base.items[index] || item(`color-${index + 1}`, `Color ${index + 1}`, base.items[0].swatchColor);
                const normalized = { ...fallback, ...(entry && typeof entry === 'object' ? entry : {}) };
                let id = text(normalized.id).trim() || `color-${index + 1}`;
                while (ids.has(id)) id = `${id}-${index + 1}`;
                ids.add(id);
                normalized.id = id;
                normalized.name = text(normalized.name, fallback.name);
                normalized.description = text(normalized.description);
                normalized.swatchColor = color(normalized.swatchColor, fallback.swatchColor);
                normalized.imageSource = enumValue(normalized.imageSource, ['ckfinder', 'url'], 'ckfinder');
                normalized.imageUrl = safeUrl(normalized.imageUrl);
                normalized.imageAlt = text(normalized.imageAlt);
                ['Tablet', 'Mobile'].forEach((device) => {
                    const deviceUrl = safeUrl(normalized[`imageUrl${device}`]);
                    const deviceSource = enumValue(normalized[`imageSource${device}`], ['ckfinder', 'url'], normalized.imageSource);
                    const deviceAlt = text(normalized[`imageAlt${device}`]).trim();
                    normalized[`imageSource${device}`] = deviceUrl ? deviceSource : normalized.imageSource;
                    normalized[`imageUrl${device}`] = deviceUrl || normalized.imageUrl;
                    normalized[`imageAlt${device}`] = deviceAlt || normalized.imageAlt;
                });
                return normalized;
            });
            s.defaultItemId = s.items.some((entry) => entry.id === text(s.defaultItemId).trim())
                ? text(s.defaultItemId).trim()
                : s.items[0].id;
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
