(function (registry) {
    "use strict";
    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() ||
        {};
    const slideDefaults = (index = 1) => ({
        id: `slide-${index}`,
        title: `Slide ${index} Heading`,
        description: "Click edit button to change this text.",
        buttonText: "Click Here",
        linkUrl: "",
        applyLinkOn: "button",
        backgroundColor: "#6979f8",
        backgroundImage: "",
        backgroundOverlay: "#00000055",
        backgroundPosition: "center center",
        backgroundSize: "cover",
        titleColor: "",
        descriptionColor: "",
    });
    const defaults = () => ({
        ...advanced(),
        slidesName: "Slides",
        slides: [slideDefaults(1), slideDefaults(2), slideDefaults(3)],
        height: "400px",
        titleTag: "h2",
        slideTitleFontSizeMode: "auto",
        slideTitleFontSize: "32px",
        slideTitleFontSizeTablet: "",
        slideTitleFontSizeMobile: "",
        descriptionTag: "div",
        navigation: "both",
        autoplay: true,
        pauseOnHover: true,
        pauseOnInteraction: true,
        autoplaySpeed: 5000,
        infiniteLoop: true,
        transition: "slide",
        transitionSpeed: 500,
        contentAnimation: "up",
        contentWidth: "66%",
        slidesPadding: "30px",
        horizontalPosition: "center",
        verticalPosition: "middle",
        textAlign: "center",
        titleColor: "#ffffff",
        descriptionColor: "#ffffff",
        buttonTextColor: "#ffffff",
        buttonBackground: "#6979f8",
        buttonTextColorHover: "#ffffff",
        buttonBackgroundHover: "#5868e8",
        buttonRadius: "4px",
        arrowsPosition: "inside",
        arrowsPositionTablet: "",
        arrowsPositionMobile: "",
        arrowsSize: "24px",
        arrowsColor: "#ffffff",
        previousArrowIcon: "fas fa-chevron-left",
        previousArrowIconSource: "library",
        previousArrowIconSvg: "",
        nextArrowIcon: "fas fa-chevron-right",
        nextArrowIconSource: "library",
        nextArrowIconSvg: "",
        arrowEdgeOffset: "10px",
        arrowEdgeOffsetTablet: "",
        arrowEdgeOffsetMobile: "",
        arrowButtonSize: "34px",
        arrowButtonSizeTablet: "",
        arrowButtonSizeMobile: "",
        arrowIconSize: "10px",
        arrowIconSizeTablet: "",
        arrowIconSizeMobile: "",
        arrowColor: "#ffffff",
        arrowBackground: "rgba(16,24,40,.5)",
        arrowHoverColor: "#ffffff",
        arrowHoverBackground: "rgba(16,24,40,.7)",
        arrowRadiusTop: "50%",
        arrowRadiusRight: "50%",
        arrowRadiusBottom: "50%",
        arrowRadiusLeft: "50%",
        dotsPosition: "inside",
        dotsPositionTablet: "",
        dotsPositionMobile: "",
        dotsGap: "8px",
        dotsSize: "8px",
        dotsColor: "#ffffff80",
        dotsActiveColor: "#ffffff",
    });
    registry.register({
        type: "slides",
        label: "Slides",
        category: "pro",
        icon: "fas fa-sliders-h",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const legacy = node.settings || {};
            const settings = (node.settings = {
                ...defaults(),
                ...legacy,
            });
            const iconPattern = /^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i;
            const lengthPattern = /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i;
            const normalizeLength = (key, fallback) => {
                settings[key] = lengthPattern.test(String(settings[key] ?? "").trim()) ? String(settings[key]).trim() : fallback;
                ["Tablet", "Mobile"].forEach((suffix) => {
                    if (settings[key + suffix] !== "") settings[key + suffix] = lengthPattern.test(String(settings[key + suffix]).trim()) ? String(settings[key + suffix]).trim() : "";
                });
            };
            const normalizeIcon = (key, fallback) => {
                settings[key] = iconPattern.test(String(settings[key] || "")) ? String(settings[key]).trim() : fallback;
                settings[key + "Source"] = settings[key + "Source"] === "svg" ? "svg" : "library";
                settings[key + "Svg"] = String(settings[key + "Svg"] || "");
            };
            settings.slides =
                Array.isArray(settings.slides) && settings.slides.length
                    ? settings.slides.map((slide, index) => ({
                          ...slideDefaults(index + 1),
                          ...slide,
                      }))
                    : defaults().slides;
            settings.autoplaySpeed = Math.max(
                100,
                Number(settings.autoplaySpeed) || 5000,
            );
            settings.contentAnimation = [
                "none",
                "down",
                "up",
                "right",
                "left",
                "zoom",
            ].includes(settings.contentAnimation)
                ? settings.contentAnimation
                : "up";
            settings.navigation = ["both", "arrows", "dots", "none"].includes(settings.navigation)
                ? settings.navigation
                : "both";
            settings.titleTag = ["h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p"].includes(String(settings.titleTag || "").toLowerCase())
                ? String(settings.titleTag).toLowerCase()
                : "h2";
            const slideTitleSizeDefaults = defaults();
            const hasLegacySlideTitleSize = ["slideTitleFontSize", "slideTitleFontSizeTablet", "slideTitleFontSizeMobile"].some((key) => String(settings[key] ?? "").trim() !== String(slideTitleSizeDefaults[key] ?? "").trim());
            settings.slideTitleFontSizeMode = ["auto", "custom"].includes(settings.slideTitleFontSizeMode)
                ? settings.slideTitleFontSizeMode
                : (hasLegacySlideTitleSize ? "custom" : "auto");
            settings.arrowsPosition = ["inside", "outside"].includes(settings.arrowsPosition)
                ? settings.arrowsPosition
                : "inside";
            settings.dotsPosition = ["inside", "outside"].includes(settings.dotsPosition)
                ? settings.dotsPosition
                : "inside";
            ["Tablet", "Mobile"].forEach((suffix) => {
                if (settings["arrowsPosition" + suffix] !== "") settings["arrowsPosition" + suffix] = ["inside", "outside"].includes(settings["arrowsPosition" + suffix]) ? settings["arrowsPosition" + suffix] : "";
                if (settings["dotsPosition" + suffix] !== "") settings["dotsPosition" + suffix] = ["inside", "outside"].includes(settings["dotsPosition" + suffix]) ? settings["dotsPosition" + suffix] : "";
            });
            if (legacy.arrowColor == null && legacy.arrowsColor != null) settings.arrowColor = legacy.arrowsColor;
            normalizeIcon("previousArrowIcon", "fas fa-chevron-left");
            normalizeIcon("nextArrowIcon", "fas fa-chevron-right");
            normalizeLength("arrowEdgeOffset", "10px");
            normalizeLength("arrowButtonSize", "34px");
            normalizeLength("arrowIconSize", "10px");
            ["arrowRadiusTop", "arrowRadiusRight", "arrowRadiusBottom", "arrowRadiusLeft", "dotsGap", "dotsSize"].forEach((key) => {
                settings[key] = lengthPattern.test(String(settings[key] ?? "").trim()) ? String(settings[key]).trim() : defaults()[key];
            });
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
