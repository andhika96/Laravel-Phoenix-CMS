(function (registry) {
    "use strict";

    const placeholder =
        "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png";
    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
    const testimonial = (id) => ({
        id,
        content:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.",
        imageUrl: placeholder,
        name: "John Doe",
        title: "CEO",
    });
    const typographyDefaults = (prefix, fontSize, fontWeight, lineHeight) => ({
        [prefix + "FontFamily"]: "inherit",
        [prefix + "FontSize"]: fontSize,
        [prefix + "FontWeight"]: fontWeight,
        [prefix + "LineHeight"]: lineHeight,
        [prefix + "LetterSpacing"]: "0px",
        [prefix + "WordSpacing"]: "0px",
        [prefix + "TextTransform"]: "none",
        [prefix + "FontStyle"]: "normal",
        [prefix + "TextDecoration"]: "none",
    });
    const defaults = () => ({
        ...advanced(),
        slidesName: "Slides",
        items: [
            testimonial("testimonial-1"),
            testimonial("testimonial-2"),
            testimonial("testimonial-3"),
        ],
        skin: "default",
        layout: "image_inline",
        alignment: "center",
        alignmentTablet: "",
        alignmentMobile: "",
        slidesToShow: 1,
        slidesToShowTablet: 1,
        slidesToShowMobile: 1,
        slidesToScroll: 1,
        slidesToScrollTablet: 1,
        slidesToScrollMobile: 1,
        width: "100%",
        widthTablet: "100%",
        widthMobile: "100%",
        arrows: true,
        pagination: "dots",
        transitionSpeed: 500,
        autoplay: true,
        autoplaySpeed: 5000,
        infiniteLoop: true,
        pauseOnHover: true,
        pauseOnInteraction: true,
        imageResolution: "full",
        customImageWidth: 300,
        customImageHeight: 300,
        lazyLoad: false,
        gap: "10px",
        slideBackground: "#ffffff",
        slideBorderColor: "#e4e7ec",
        slideBorderTop: "1px",
        slideBorderRight: "1px",
        slideBorderBottom: "1px",
        slideBorderLeft: "1px",
        slideRadiusTop: "0px",
        slideRadiusRight: "0px",
        slideRadiusBottom: "0px",
        slideRadiusLeft: "0px",
        slidePaddingTop: "20px",
        slidePaddingRight: "20px",
        slidePaddingBottom: "20px",
        slidePaddingLeft: "20px",
        contentGap: "10px",
        contentColor: "#344054",
        nameColor: "#101828",
        titleColor: "#667085",
        imageSize: "50px",
        imageGap: "10px",
        imageBorder: false,
        imageBorderColor: "#e4e7ec",
        imageBorderTop: "1px",
        imageBorderRight: "1px",
        imageBorderBottom: "1px",
        imageBorderLeft: "1px",
        imageRadiusTop: "50%",
        imageRadiusRight: "50%",
        imageRadiusBottom: "50%",
        imageRadiusLeft: "50%",
        arrowsSize: "20px",
        arrowColor: "#344054",
        dotsGap: "8px",
        dotsSize: "8px",
        paginationColor: "#d0d5dd",
        paginationActiveColor: "#6979f8",
        testimonialCarouselContentTextStrokeWidth: "0px",
        testimonialCarouselContentTextStrokeColor: "#000000",
        testimonialCarouselContentTextShadow: "none",
        ...typographyDefaults("testimonialCarouselContent", "16px", "400", "1.5em"),
        ...typographyDefaults("testimonialCarouselName", "18px", "600", "1.3em"),
        ...typographyDefaults("testimonialCarouselTitle", "14px", "400", "1.4em"),
    });

    const enumValue = (value, allowed, fallback) =>
        allowed.includes(value) ? value : fallback;
    const clampSlides = (value) => Math.max(1, Math.min(10, Number(value) || 1));
    const clampLength = (value, fallback) => {
        const raw = String(value ?? "").trim();
        return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw)
            ? raw
            : fallback;
    };

    registry.register({
        type: "testimonial_carousel",
        label: "Testimonial Carousel",
        category: "pro",
        icon: "fas fa-quote-right",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            [
                "slidesToShow",
                "slidesToShowTablet",
                "slidesToShowMobile",
                "slidesToScroll",
                "slidesToScrollTablet",
                "slidesToScrollMobile",
            ].forEach((key) => (s[key] = clampSlides(s[key])));
            s.skin = enumValue(s.skin, ["default", "bubble"], "default");
            s.layout = enumValue(
                s.layout,
                ["image_inline", "image_stacked", "image_above", "image_left", "image_right"],
                "image_inline",
            );
            ["alignment", "alignmentTablet", "alignmentMobile"].forEach((key) => {
                s[key] = enumValue(s[key], ["", "left", "center", "right"], "");
            });
            s.pagination = enumValue(
                s.pagination,
                ["dots", "fraction", "progress", "none"],
                "dots",
            );
            s.imageResolution = enumValue(
                s.imageResolution,
                [
                    "thumbnail",
                    "medium",
                    "medium_large",
                    "large",
                    "1536x1536",
                    "2048x2048",
                    "full",
                    "custom",
                ],
                "full",
            );
            s.customImageWidth = Math.max(
                1,
                Math.min(4096, Number(s.customImageWidth) || 300),
            );
            s.customImageHeight = Math.max(
                1,
                Math.min(4096, Number(s.customImageHeight) || 300),
            );
            [
                "arrows",
                "autoplay",
                "infiniteLoop",
                "pauseOnHover",
                "pauseOnInteraction",
                "lazyLoad",
                "imageBorder",
            ].forEach((key) => (s[key] = Boolean(s[key])));
            s.transitionSpeed = Math.max(0, Number(s.transitionSpeed) || 0);
            s.autoplaySpeed = Math.max(100, Number(s.autoplaySpeed) || 5000);
            [
                "gap",
                "contentGap",
                "imageSize",
                "imageGap",
                "arrowsSize",
                "dotsGap",
                "dotsSize",
            ].forEach((key) => {
                s[key] = clampLength(s[key], base[key]);
            });
            const fallbackItems = base.items;
            s.items = (Array.isArray(s.items) && s.items.length ? s.items : fallbackItems).map(
                (item, index) => ({
                    ...testimonial(`testimonial-${index + 1}`),
                    ...(item || {}),
                    id: String(item?.id || `testimonial-${index + 1}`),
                    content: String(item?.content ?? testimonial(`testimonial-${index + 1}`).content),
                    imageUrl: String(item?.imageUrl ?? ""),
                    name: String(item?.name ?? ""),
                    title: String(item?.title ?? ""),
                }),
            );
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
