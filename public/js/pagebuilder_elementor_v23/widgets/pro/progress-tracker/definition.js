(function (registry) {
    "use strict";

    if (!registry) throw new Error("Page Builder Elementor widget registry is not loaded.");

    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
    const length = (value, fallback) => {
        const raw = String(value ?? "").trim();
        return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw)
            ? raw
            : fallback;
    };
    const bool = (value, fallback = false) => {
        if (value === true || value === 1 || value === "1" || value === "true") return true;
        if (value === false || value === 0 || value === "0" || value === "false") return false;
        return fallback;
    };
    const enumValue = (value, allowed, fallback) =>
        allowed.includes(value) ? value : fallback;
    const typographyDefaults = () => ({
        progressTrackerPercentageFontFamily: "inherit",
        progressTrackerPercentageFontSize: "14px",
        progressTrackerPercentageFontWeight: "600",
        progressTrackerPercentageLineHeight: "1.2em",
        progressTrackerPercentageLetterSpacing: "0px",
        progressTrackerPercentageWordSpacing: "0px",
        progressTrackerPercentageTextTransform: "none",
        progressTrackerPercentageFontStyle: "normal",
        progressTrackerPercentageTextDecoration: "none",
    });
    const defaults = () => ({
        ...advanced(),
        trackerType: "horizontal",
        relativeTo: "page",
        selector: "",
        direction: "left",
        showPercentage: true,
        trackerSize: "6px",
        trackerSizeTablet: "",
        trackerSizeMobile: "",
        circleSize: "140px",
        circleSizeTablet: "",
        circleSizeMobile: "",
        indicatorColor: "#6979f8",
        indicatorWidth: "4px",
        indicatorWidthTablet: "",
        indicatorWidthMobile: "",
        indicatorAlignment: "left",
        backgroundColor: "#e4e7ec",
        backgroundWidth: "4px",
        backgroundWidthTablet: "",
        backgroundWidthMobile: "",
        percentageColor: "#101828",
        progressTrackerPercentageTextShadow: "none",
        ...typographyDefaults(),
    });

    registry.register({
        type: "progress_tracker",
        label: "Progress Tracker",
        category: "pro",
        icon: "fas fa-tasks",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            s.trackerType = enumValue(s.trackerType, ["horizontal", "circular"], "horizontal");
            s.relativeTo = enumValue(s.relativeTo, ["page", "post_content", "selector"], "page");
            s.direction = enumValue(s.direction, ["left", "center", "right"], "left");
            s.indicatorAlignment = enumValue(s.indicatorAlignment, ["left", "center", "right"], "left");
            s.selector = String(s.selector || "").trim();
            s.showPercentage = bool(s.showPercentage, true);
            ["trackerSize", "trackerSizeTablet", "trackerSizeMobile"].forEach((key) => {
                s[key] = s[key] === "" ? "" : length(s[key], base[key] || base.trackerSize);
            });
            ["circleSize", "circleSizeTablet", "circleSizeMobile"].forEach((key) => {
                s[key] = s[key] === "" ? "" : length(s[key], base[key] || base.circleSize);
            });
            ["indicatorWidth", "indicatorWidthTablet", "indicatorWidthMobile", "backgroundWidth", "backgroundWidthTablet", "backgroundWidthMobile"].forEach((key) => {
                s[key] = s[key] === "" ? "" : length(s[key], base[key] || "4px");
            });
            s.indicatorColor = String(s.indicatorColor || base.indicatorColor);
            s.backgroundColor = String(s.backgroundColor || base.backgroundColor);
            s.percentageColor = String(s.percentageColor || base.percentageColor);
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
