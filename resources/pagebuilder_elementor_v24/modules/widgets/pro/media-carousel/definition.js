(function (registry) {
    "use strict";

    const placeholder =
        "https://playground.elementor.com/wp-content/plugins/elementor/assets/images/placeholder.png";
    const advanced = () =>
        registry.advancedDefaults();
    const media = (id) => ({
        id,
        type: "image",
        imageUrl: placeholder,
        videoUrl: "",
        linkType: "none",
        linkUrl: "",
        linkTarget: "",
        linkNofollow: false,
        linkCustomAttributes: [],
        title: "",
        caption: "",
        description: "",
    });
    const defaults = () => ({
        ...advanced(),
        skins: ["carousel", "slideshow", "coverflow"],
        skin: "carousel",
        slidesName: "Slides",
        items: [1, 2, 3, 4, 5].map((index) => media(`media-${index}`)),
        effect: "slide",
        slidesToShow: 3,
        slidesToShowTablet: 2,
        slidesToShowMobile: 1,
        slidesToScroll: 1,
        slidesToScrollTablet: 1,
        slidesToScrollMobile: 1,
        thumbsSlidesToShow: 5,
        thumbsSlidesToShowTablet: 4,
        thumbsSlidesToShowMobile: 3,
        thumbsRatio: "21:9",
        centeredSlides: false,
        height: "300px",
        heightTablet: "260px",
        heightMobile: "220px",
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
        overlay: "none",
        captionSource: "title",
        overlayIcon: "search-plus",
        overlayAnimation: "fade",
        imageResolution: "full",
        customImageWidth: 300,
        customImageHeight: 300,
        imageFit: "cover",
        lazyLoad: false,
        gap: "10px",
        slideBackground: "#ffffff",
        slideBorderColor: "transparent",
        slideBorderTop: "0px",
        slideBorderRight: "0px",
        slideBorderBottom: "0px",
        slideBorderLeft: "0px",
        slideRadiusTop: "0px",
        slideRadiusRight: "0px",
        slideRadiusBottom: "0px",
        slideRadiusLeft: "0px",
        slidePaddingTop: "0px",
        slidePaddingRight: "0px",
        slidePaddingBottom: "0px",
        slidePaddingLeft: "0px",
        arrowsSize: "20px",
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
        arrowButtonSize: "20px",
        arrowButtonSizeTablet: "",
        arrowButtonSizeMobile: "",
        arrowIconSize: "10px",
        arrowIconSizeTablet: "",
        arrowIconSizeMobile: "",
        arrowColor: "#ffffff",
        arrowBackground: "rgba(16,24,40,.5)",
        arrowHoverColor: "#ffffff",
        arrowHoverBackground: "rgba(16,24,40,.5)",
        arrowRadiusTop: "50%",
        arrowRadiusRight: "50%",
        arrowRadiusBottom: "50%",
        arrowRadiusLeft: "50%",
        paginationPosition: "outside",
        dotsGap: "8px",
        dotsSize: "8px",
        paginationColor: "#d0d5dd",
        paginationActiveColor: "#6979f8",
        playIconColor: "#ffffff",
        playIconSize: "80px",
        playIconShadow: "0 1px 6px rgba(0,0,0,.35)",
        overlayBackground: "rgba(0,0,0,.5)",
        overlayTextColor: "#ffffff",
        overlayIconSize: "32px",
        lightboxBackground: "rgba(0,0,0,.92)",
        lightboxUiColor: "#ffffff",
        lightboxUiHoverColor: "#6979f8",
        lightboxVideoWidth: "75%",
    });

    registry.register({type: "media_carousel",defaults,normalize(node) {
            const base = defaults();
            const existing = node.settings || {};
            const missingArrowContract = {
                button: existing.arrowButtonSize === undefined,
                icon: existing.arrowIconSize === undefined,
                edge: existing.arrowEdgeOffset === undefined,
            };
            const legacyIconSize = (value, fallback = "10px") => {
                const match = String(value || "").trim().match(/^(\d+(?:\.\d+)?)px$/i);
                return match ? `${Math.min(16, Number(match[1]) / 2)}px` : fallback;
            };
            const s = (node.settings = { ...base, ...existing });
            const enumValue = (value, allowed, fallback) =>
                allowed.includes(value) ? value : fallback;
            const clampSlides = (value, fallback = 1) =>
                Math.max(1, Math.min(10, Number(value) || fallback));
            s.skins = [...base.skins];
            s.skin = enumValue(s.skin, s.skins, "carousel");
            s.effect = enumValue(s.effect, ["slide", "fade", "cube"], "slide");
            s.pagination = enumValue(s.pagination, ["none", "dots", "fraction", "progress"], "dots");
            s.overlay = enumValue(s.overlay, ["none", "text", "icon"], "none");
            s.captionSource = enumValue(s.captionSource, ["title", "caption", "description"], "title");
            s.overlayIcon = enumValue(s.overlayIcon, ["search-plus", "plus-circle", "eye", "link"], "search-plus");
            s.overlayAnimation = enumValue(s.overlayAnimation, ["fade", "slide-up", "slide-down", "slide-right", "slide-left", "zoom-in"], "fade");
            s.imageFit = enumValue(s.imageFit, ["cover", "contain", "auto"], "cover");
            s.arrowPosition = enumValue(s.arrowPosition, ["inside", "outside"], "inside");
            ["arrowPositionTablet", "arrowPositionMobile"].forEach((key) => {
                s[key] = s[key] === "" || ["inside", "outside"].includes(s[key]) ? s[key] : "";
            });
            if (missingArrowContract.button) s.arrowButtonSize = String(existing.arrowsSize || "20px");
            if (missingArrowContract.icon) s.arrowIconSize = legacyIconSize(existing.arrowsSize);
            if (missingArrowContract.edge) s.arrowEdgeOffset = "46px";
            [["Tablet", "arrowsSizeTablet"], ["Mobile", "arrowsSizeMobile"]].forEach(([suffix, legacyKey]) => {
                if (existing[legacyKey] && !existing["arrowButtonSize" + suffix]) s["arrowButtonSize" + suffix] = String(existing[legacyKey]);
                if (existing[legacyKey] && !existing["arrowIconSize" + suffix]) s["arrowIconSize" + suffix] = legacyIconSize(existing[legacyKey], "");
            });
            s.imageResolution = enumValue(s.imageResolution, ["thumbnail", "medium", "medium_large", "large", "1536x1536", "2048x2048", "full", "custom"], "full");
            s.thumbsRatio = enumValue(s.thumbsRatio, ["1:1", "4:3", "16:9", "21:9"], "21:9");
            ["slidesToShow", "slidesToShowTablet", "slidesToShowMobile", "slidesToScroll", "slidesToScrollTablet", "slidesToScrollMobile", "thumbsSlidesToShow", "thumbsSlidesToShowTablet", "thumbsSlidesToShowMobile"].forEach((key) => {
                s[key] = clampSlides(s[key], base[key]);
            });
            ["arrows", "autoplay", "infiniteLoop", "pauseOnHover", "pauseOnInteraction", "centeredSlides", "lazyLoad"].forEach((key) => {
                s[key] = Boolean(s[key]);
            });
            s.transitionSpeed = Math.max(0, Number(s.transitionSpeed) || 0);
            s.autoplaySpeed = Math.max(100, Number(s.autoplaySpeed) || 5000);
            s.customImageWidth = Math.max(1, Math.min(4096, Number(s.customImageWidth) || 300));
            s.customImageHeight = Math.max(1, Math.min(4096, Number(s.customImageHeight) || 300));
            ["previousArrowIcon", "nextArrowIcon"].forEach((key) => {
                const fallback = key === "previousArrowIcon" ? "fas fa-chevron-left" : "fas fa-chevron-right";
                s[key] = String(s[key] || fallback).trim() || fallback;
                s[key + "Source"] = s[key + "Source"] === "svg" && String(s[key + "Svg"] || "").trim() ? "svg" : "library";
                s[key + "Svg"] = s[key + "Source"] === "svg" ? String(s[key + "Svg"] || "").trim() : "";
            });
            s.items = (Array.isArray(s.items) && s.items.length ? s.items : base.items).map((item, index) => {
                const normalized = { ...media(`media-${index + 1}`), ...(item || {}) };
                normalized.type = enumValue(normalized.type, ["image", "video"], "image");
                normalized.linkType = normalized.type === "image"
                    ? enumValue(normalized.linkType, ["none", "media", "custom"], "none")
                    : "none";
                normalized.imageUrl = String(normalized.imageUrl || "").trim();
                normalized.videoUrl = String(normalized.videoUrl || "").trim();
                normalized.linkUrl = String(normalized.linkUrl || "").trim();
                normalized.linkCustomAttributes = Array.isArray(normalized.linkCustomAttributes)
                    ? normalized.linkCustomAttributes
                    : [];
                return normalized;
            });
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
