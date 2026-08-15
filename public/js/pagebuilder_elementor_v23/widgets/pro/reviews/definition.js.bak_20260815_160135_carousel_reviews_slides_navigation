(function (registry) {
    "use strict";

    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
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
        dotsGap: "8px",
        dotsSize: "8px",
        paginationColor: "#d0d5dd",
        paginationActiveColor: "#6979f8",
    });

    registry.register({
        type: "reviews",
        label: "Reviews",
        category: "pro",
        icon: "fas fa-comments",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
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
            ].forEach((key) => (s[key] = Boolean(s[key])));
            s.transitionSpeed = Math.max(0, Number(s.transitionSpeed) || 0);
            s.autoplaySpeed = Math.max(100, Number(s.autoplaySpeed) || 5000);
            const fallbackItems = base.items;
            s.items = (Array.isArray(s.items) && s.items.length ? s.items : fallbackItems).map(
                (item, index) => {
                    const fallback = review(`review-${index + 1}`);
                    const normalized = { ...fallback, ...(item || {}) };
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
        },
    });
})(window.PageBuilderElementorV23Widgets);
