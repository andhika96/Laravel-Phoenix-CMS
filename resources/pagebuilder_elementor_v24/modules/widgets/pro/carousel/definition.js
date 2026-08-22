(function (registry) {
    "use strict";

    const advanced = () =>
        registry.advancedDefaults();
    const iconPattern = /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i;
    const lengthPattern = /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i;
    const sizes = [
        "thumbnail",
        "medium",
        "medium_large",
        "large",
        "1536x1536",
        "2048x2048",
        "full",
        "custom",
    ];
    const enumValue = (value, allowed, fallback) =>
        allowed.includes(value) ? value : fallback;
    const length = (value, fallback) =>
        lengthPattern.test(String(value ?? "").trim()) ? String(value).trim() : fallback;
    const responsiveLength = (settings, base, fallback) => {
        settings[base] = length(settings[base], fallback);
        ["Tablet", "Mobile"].forEach((suffix) => {
            if (settings[base + suffix] !== "") {
                settings[base + suffix] = length(settings[base + suffix], "");
            }
        });
    };
    const normalizeIcon = (settings, key, fallback) => {
        settings[key] = iconPattern.test(String(settings[key] || ""))
            ? String(settings[key]).trim()
            : fallback;
        settings[key + "Source"] = settings[key + "Source"] === "svg" ? "svg" : "library";
        settings[key + "Svg"] = String(settings[key + "Svg"] || "");
    };
    const defaultItem = (id, title) => ({
        id,
        title,
        description: "Carousel item content",
        imageUrl: "",
        imageAlt: "",
        linkUrl: "",
    });
    const defaults = () => ({
        ...advanced(),
        carouselName: "Carousel",
        items: [defaultItem("carousel-1", "Slide #1"), defaultItem("carousel-2", "Slide #2"), defaultItem("carousel-3", "Slide #3")],
        slidesToShow: 3,
        slidesToShowTablet: 2,
        slidesToShowMobile: 1,
        slidesToScroll: 1,
        slidesToScrollTablet: 1,
        slidesToScrollMobile: 1,
        equalHeight: true,
        autoplay: false,
        pauseOnHover: true,
        pauseOnInteraction: true,
        autoplaySpeed: 5000,
        infiniteLoop: true,
        transitionSpeed: 500,
        navigation: "both",
        pagination: "dots",
        paginationPosition: "outside",
        gap: "20px",
        slideBackground: "#ffffff",
        slideBorderColor: "#e4e7ec",
        slideBorderWidth: "1px",
        slideRadius: "8px",
        slidePadding: "20px",
        contentGap: "10px",
        carouselTitleColor: "#101828",
        carouselDescriptionColor: "#344054",
        imageResolution: "full",
        customImageWidth: 300,
        customImageHeight: 300,
        imageFit: "cover",
        imageStretch: false,
        lazyLoad: false,
        previousArrowIcon: "fas fa-chevron-left",
        previousArrowIconSource: "library",
        previousArrowIconSvg: "",
        nextArrowIcon: "fas fa-chevron-right",
        nextArrowIconSource: "library",
        nextArrowIconSvg: "",
        arrowPosition: "inside",
        arrowPositionTablet: "",
        arrowPositionMobile: "",
        arrowEdgeOffset: "46px",
        arrowEdgeOffsetTablet: "",
        arrowEdgeOffsetMobile: "",
        arrowButtonSize: "34px",
        arrowButtonSizeTablet: "",
        arrowButtonSizeMobile: "",
        arrowIconSize: "10px",
        arrowIconSizeTablet: "",
        arrowIconSizeMobile: "",
        arrowColor: "#344054",
        arrowBackground: "#ffffff",
        arrowHoverColor: "#344054",
        arrowHoverBackground: "#ffffff",
        arrowRadiusTop: "50%",
        arrowRadiusRight: "50%",
        arrowRadiusBottom: "50%",
        arrowRadiusLeft: "50%",
        dotsGap: "8px",
        dotsSize: "8px",
        paginationColor: "#d0d5dd",
        paginationActiveColor: "#6979f8",
    });

    registry.register({type: "carousel",defaults,normalize(node) {
            const legacy = node.settings || {};
            const s = (node.settings = { ...defaults(), ...legacy });
            if (legacy.arrowButtonSize == null && legacy.arrowsSize != null) s.arrowButtonSize = legacy.arrowsSize;
            if (legacy.arrowIconSize == null && legacy.arrowsSize != null) {
                const old = Number.parseFloat(String(legacy.arrowsSize));
                if (Number.isFinite(old)) s.arrowIconSize = `${Math.max(1, Math.min(80, old / 2))}px`;
            }
            ["slidesToShow", "slidesToShowTablet", "slidesToShowMobile", "slidesToScroll", "slidesToScrollTablet", "slidesToScrollMobile"].forEach((key) => {
                s[key] = Math.max(1, Math.min(8, Number(s[key]) || 1));
            });
            s.navigation = enumValue(s.navigation, ["both", "arrows", "dots", "none"], "both");
            s.pagination = enumValue(s.pagination, ["dots", "fraction", "progress", "none"], "dots");
            s.paginationPosition = enumValue(s.paginationPosition, ["inside", "outside"], "outside");
            s.imageResolution = enumValue(s.imageResolution, sizes, "full");
            s.imageFit = enumValue(s.imageFit, ["cover", "contain", "auto"], "cover");
            ["autoplay", "pauseOnHover", "pauseOnInteraction", "infiniteLoop", "equalHeight", "imageStretch", "lazyLoad"].forEach((key) => {
                s[key] = Boolean(s[key]);
            });
            s.autoplaySpeed = Math.max(100, Number(s.autoplaySpeed) || 5000);
            s.transitionSpeed = Math.max(0, Number(s.transitionSpeed) || 0);
            s.customImageWidth = Math.max(1, Math.min(4096, Number(s.customImageWidth) || 300));
            s.customImageHeight = Math.max(1, Math.min(4096, Number(s.customImageHeight) || 300));
            responsiveLength(s, "gap", "20px");
            responsiveLength(s, "slidePadding", "20px");
            responsiveLength(s, "slideRadius", "8px");
            responsiveLength(s, "slideBorderWidth", "1px");
            responsiveLength(s, "contentGap", "10px");
            s.arrowPosition = enumValue(s.arrowPosition, ["inside", "outside"], "inside");
            ["Tablet", "Mobile"].forEach((suffix) => {
                if (s["arrowPosition" + suffix] !== "") s["arrowPosition" + suffix] = enumValue(s["arrowPosition" + suffix], ["inside", "outside"], "");
            });
            responsiveLength(s, "arrowEdgeOffset", "46px");
            responsiveLength(s, "arrowButtonSize", "34px");
            responsiveLength(s, "arrowIconSize", "10px");
            ["arrowRadiusTop", "arrowRadiusRight", "arrowRadiusBottom", "arrowRadiusLeft", "dotsGap", "dotsSize"].forEach((key) => {
                s[key] = length(s[key], defaults()[key]);
            });
            normalizeIcon(s, "previousArrowIcon", "fas fa-chevron-left");
            normalizeIcon(s, "nextArrowIcon", "fas fa-chevron-right");
            s.items = (Array.isArray(s.items) && s.items.length ? s.items : defaults().items).map((item, index) => ({
                ...defaultItem(`carousel-${index + 1}`, `Slide #${index + 1}`),
                ...(item || {}),
                id: String(item?.id || `carousel-${index + 1}`),
                title: String(item?.title || ""),
                description: String(item?.description || ""),
                imageUrl: String(item?.imageUrl || ""),
                imageAlt: String(item?.imageAlt || ""),
                linkUrl: String(item?.linkUrl || ""),
            }));
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
