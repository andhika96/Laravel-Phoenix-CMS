(function (registry) {
    "use strict";

    const advanced = () =>
        registry.advancedDefaults();
    const review = (id) => ({
        id,
        imageUrl:
            "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png",
        name: "John Doe",
        title: "@username",
        rating: "",
        review:
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.",
        linkUrl: "",
        linkTarget: "",
        linkNofollow: false,
        linkCustomAttributes: [],
        iconSource: "library",
        iconStyle: "brands",
        iconName: "twitter",
        iconClass: "fab fa-twitter",
        iconSvg: "",
    });
    const defaults = () => ({
        ...advanced(),
        slidesName: "Slides",
        items: [review("review-1"), review("review-2"), review("review-3")],
        slidesToShow: 1,
        slidesToShowTablet: 1,
        slidesToShowMobile: 1,
        slidesToScroll: 1,
        slidesToScrollTablet: 1,
        slidesToScrollMobile: 1,
        reviewsWidth: "100%",
        reviewsWidthTablet: "100%",
        reviewsWidthMobile: "100%",
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
        imageFit: "cover",
        imageBorder: false,
        imageBorderColor: "#e4e7ec",
        imageBorderTop: "0px",
        imageBorderRight: "0px",
        imageBorderBottom: "0px",
        imageBorderLeft: "0px",
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
        slidePaddingTop: "0px",
        slidePaddingRight: "0px",
        slidePaddingBottom: "0px",
        slidePaddingLeft: "0px",
        headerBackground: "#ffffff",
        headerGap: "15px",
        reviewSeparator: true,
        separatorColor: "#e4e7ec",
        separatorSize: "1px",
        nameColor: "#101828",
        titleColor: "#667085",
        reviewColor: "#344054",
        imageSize: "50px",
        imageGap: "15px",
        imageRadiusTop: "50%",
        imageRadiusRight: "50%",
        imageRadiusBottom: "50%",
        imageRadiusLeft: "50%",
        iconColorMode: "official",
        iconColor: "#1da1f2",
        iconSize: "20px",
        ratingIcon: "fontawesome",
        unmarkedStyle: "solid",
        ratingSize: "16px",
        ratingSpacing: "2px",
        ratingColor: "#f0ad4e",
        ratingUnmarkedColor: "#ccd6df",
        arrowsSize: "24px",
        arrowColor: "#344054",
        arrowBackground: "#ffffff",
        navigation: "both",
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
        arrowButtonSize: "24px",
        arrowButtonSizeTablet: "",
        arrowButtonSizeMobile: "",
        arrowIconSize: "10px",
        arrowIconSizeTablet: "",
        arrowIconSizeMobile: "",
        arrowHoverColor: "#344054",
        arrowHoverBackground: "#ffffff",
        arrowRadiusTop: "50%",
        arrowRadiusRight: "50%",
        arrowRadiusBottom: "50%",
        arrowRadiusLeft: "50%",
        paginationPosition: "outside",
        dotsGap: "8px",
        dotsSize: "8px",
        paginationColor: "#d0d5dd",
        paginationActiveColor: "#6979f8",
    });

    registry.register({type: "reviews",editor: { iconTargets: { reviewsItem: { prefix: "icon", collection: "items" } } },defaults,normalize(node) {
            const base = defaults();
            const legacy = node.settings || {};
            const s = (node.settings = { ...base, ...legacy });
            const iconPattern = /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i;
            const lengthPattern = /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i;
            const normalizeLength = (key, fallback) => {
                s[key] = lengthPattern.test(String(s[key] ?? "").trim()) ? String(s[key]).trim() : fallback;
                ["Tablet", "Mobile"].forEach((suffix) => {
                    if (s[key + suffix] !== "") {
                        s[key + suffix] = lengthPattern.test(String(s[key + suffix]).trim()) ? String(s[key + suffix]).trim() : "";
                    }
                });
            };
            const normalizeIcon = (key, fallback) => {
                s[key] = iconPattern.test(String(s[key] || "")) ? String(s[key]).trim() : fallback;
                s[key + "Source"] = s[key + "Source"] === "svg" ? "svg" : "library";
                s[key + "Svg"] = String(s[key + "Svg"] || "");
            };
            const clampSlides = (value) => Math.max(1, Math.min(10, Number(value) || 1));
            [
                "slidesToShow",
                "slidesToShowTablet",
                "slidesToShowMobile",
                "slidesToScroll",
                "slidesToScrollTablet",
                "slidesToScrollMobile",
            ].forEach((key) => (s[key] = clampSlides(s[key])));
            s.pagination = ["dots", "fraction", "progress", "none"].includes(s.pagination)
                ? s.pagination
                : "dots";
            if (!Object.prototype.hasOwnProperty.call(legacy, "navigation")) {
                s.navigation = s.arrows
                    ? s.pagination === "none" ? "arrows" : "both"
                    : s.pagination === "none" ? "none" : "dots";
            }
            s.navigation = ["both", "arrows", "dots", "none"].includes(s.navigation)
                ? s.navigation
                : "both";
            s.paginationPosition = ["inside", "outside"].includes(s.paginationPosition)
                ? s.paginationPosition
                : "outside";
            s.imageResolution = [
                "thumbnail",
                "medium",
                "medium_large",
                "large",
                "1536x1536",
                "2048x2048",
                "full",
                "custom",
            ].includes(s.imageResolution)
                ? s.imageResolution
                : "full";
            s.imageFit = ["cover", "contain", "auto"].includes(s.imageFit)
                ? s.imageFit
                : "cover";
            s.customImageWidth = Math.max(1, Math.min(4096, Number(s.customImageWidth) || 300));
            s.customImageHeight = Math.max(1, Math.min(4096, Number(s.customImageHeight) || 300));
            s.iconColorMode = ["official", "custom"].includes(s.iconColorMode)
                ? s.iconColorMode
                : "official";
            s.ratingIcon = ["fontawesome", "unicode"].includes(s.ratingIcon)
                ? s.ratingIcon
                : "fontawesome";
            s.unmarkedStyle = ["solid", "outline"].includes(s.unmarkedStyle)
                ? s.unmarkedStyle
                : "solid";
            [
                "arrows",
                "autoplay",
                "infiniteLoop",
                "pauseOnHover",
                "pauseOnInteraction",
                "lazyLoad",
                "reviewSeparator",
                "imageBorder",
            ].forEach((key) => (s[key] = Boolean(s[key])));
            if (legacy.arrowButtonSize == null && legacy.arrowsSize != null) s.arrowButtonSize = legacy.arrowsSize;
            if (legacy.arrowIconSize == null && legacy.arrowsSize != null) {
                const old = Number.parseFloat(String(legacy.arrowsSize));
                if (Number.isFinite(old)) s.arrowIconSize = `${Math.max(1, Math.min(80, old / 2))}px`;
            }
            s.arrowPosition = ["inside", "outside"].includes(s.arrowPosition) ? s.arrowPosition : "inside";
            ["Tablet", "Mobile"].forEach((suffix) => {
                if (s["arrowPosition" + suffix] !== "") s["arrowPosition" + suffix] = ["inside", "outside"].includes(s["arrowPosition" + suffix]) ? s["arrowPosition" + suffix] : "";
            });
            normalizeLength("arrowEdgeOffset", "46px");
            normalizeLength("arrowButtonSize", "24px");
            normalizeLength("arrowIconSize", "10px");
            ["imageBorderTop", "imageBorderRight", "imageBorderBottom", "imageBorderLeft"].forEach((key) => normalizeLength(key, "0px"));
            ["arrowRadiusTop", "arrowRadiusRight", "arrowRadiusBottom", "arrowRadiusLeft", "dotsGap", "dotsSize"].forEach((key) => {
                s[key] = lengthPattern.test(String(s[key] ?? "").trim()) ? String(s[key]).trim() : base[key];
            });
            normalizeIcon("previousArrowIcon", "fas fa-chevron-left");
            normalizeIcon("nextArrowIcon", "fas fa-chevron-right");
            s.transitionSpeed = Math.max(0, Number(s.transitionSpeed) || 0);
            s.autoplaySpeed = Math.max(100, Number(s.autoplaySpeed) || 5000);
            const fallbackItems = base.items;
            s.items = (Array.isArray(s.items) && s.items.length ? s.items : fallbackItems).map(
                (item, index) => {
                    const fallback = review(`review-${index + 1}`);
                    const normalized = { ...fallback, ...(item || {}) };
                    normalized.imageAlt = String(normalized.imageAlt || normalized.name || "");
                    const rating = normalized.rating === "" ? "" : Number(normalized.rating);
                    normalized.rating = rating === "" || !Number.isFinite(rating)
                        ? ""
                        : Math.max(0, Math.min(5, rating));
                    normalized.linkCustomAttributes = Array.isArray(normalized.linkCustomAttributes)
                        ? normalized.linkCustomAttributes
                        : [];
                    return normalized;
                },
            );
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
