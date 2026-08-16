(function (registry) {
    "use strict";
    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() ||
        {};
    const featureDefaults = (feature = {}) => ({
        id: "feature",
        text: "Feature item",
        iconSource: "library",
        iconStyle: "solid",
        iconName: "check",
        iconClass: "fas fa-check",
        iconSvg: "",
        ...feature,
    });
    const normalizeFeature = (feature) => {
        const normalized = featureDefaults(feature);
        const iconClass = String(normalized.iconClass || "").trim();
        if (normalized.iconSource === "svg" && normalized.iconSvg) {
            normalized.iconClass = "";
            return normalized;
        }
        if (/^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(iconClass)) {
            const [prefix, name] = iconClass.split(/\s+/);
            normalized.iconSource = "library";
            normalized.iconStyle =
                { fas: "solid", far: "regular", fab: "brands" }[prefix] ||
                normalized.iconStyle ||
                "solid";
            normalized.iconName = name.replace(/^fa-/, "");
            normalized.iconSvg = "";
            return normalized;
        }
        return featureDefaults({ id: normalized.id, text: normalized.text });
    };
    const defaults = () => ({
        ...advanced(),
        title: "Basic Plan",
        description: "For small teams",
        titleTag: "h3",
        priceTableHeaderFontSizeMode: "auto",
        priceTableHeaderFontSize: "24px",
        priceTableHeaderFontSizeTablet: "",
        priceTableHeaderFontSizeMobile: "",
        currencySymbol: "$",
        price: "39.99",
        currencyFormat: "comma",
        sale: false,
        originalPrice: "49.99",
        period: "Per Month",
        features: [1, 2, 3].map((number) =>
            featureDefaults({ id: `feature-${number}` }),
        ),
        buttonText: "Get Started",
        buttonLink: "",
        additionalInfo: "No hidden fees.",
        showRibbon: true,
        ribbonTitle: "Popular",
        ribbonPosition: "right",
        headerBackground: "#101828",
        headerColor: "#ffffff",
        pricingBackground: "#f2f4f7",
        priceColor: "#101828",
        featuresBackground: "#ffffff",
        featuresColor: "#344054",
        footerBackground: "#ffffff",
        buttonBackground: "#6979f8",
        buttonBackgroundHover: "#5868e8",
        buttonTextColor: "#ffffff",
        ribbonBackground: "#f04438",
        ribbonTextColor: "#ffffff",
    });
    registry.register({
        type: "price_table",
        label: "Price Table",
        category: "pro",
        icon: "fas fa-tags",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const settings = (node.settings = {
                ...defaults(),
                ...(node.settings || {}),
            });
            settings.features =
                Array.isArray(settings.features) && settings.features.length
                    ? settings.features.map(normalizeFeature)
                    : defaults().features;
            settings.titleTag = ["h1", "h2", "h3", "h4", "h5", "h6", "div", "span", "p"].includes(String(settings.titleTag || "").toLowerCase())
                ? String(settings.titleTag).toLowerCase()
                : "h3";
            const base = defaults();
            const hasLegacyHeaderSize = ["priceTableHeaderFontSize", "priceTableHeaderFontSizeTablet", "priceTableHeaderFontSizeMobile"].some((key) => String(settings[key] ?? "").trim() !== String(base[key] ?? "").trim());
            settings.priceTableHeaderFontSizeMode = ["auto", "custom"].includes(settings.priceTableHeaderFontSizeMode)
                ? settings.priceTableHeaderFontSizeMode
                : (hasLegacyHeaderSize ? "custom" : "auto");
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
