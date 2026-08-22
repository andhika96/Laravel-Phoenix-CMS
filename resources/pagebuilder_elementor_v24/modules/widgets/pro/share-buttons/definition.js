(function (registry) {
    "use strict";

    const advanced = () =>
        registry.advancedDefaults();
    const networks = [
        "facebook",
        "twitter",
        "x",
        "threads",
        "linkedin",
        "pinterest",
        "reddit",
        "whatsapp",
        "telegram",
        "email",
        "print",
        "copy",
        "vk",
        "tumblr",
        "skype",
        "digg",
        "stumbleupon",
        "pocket",
        "flipboard",
        "buffer",
        "weibo",
        "blogger",
        "odnoklassniki",
    ];
    const networkLabels = {
        facebook: "Facebook",
        twitter: "Twitter",
        x: "X",
        threads: "Threads",
        linkedin: "LinkedIn",
        pinterest: "Pinterest",
        reddit: "Reddit",
        whatsapp: "WhatsApp",
        telegram: "Telegram",
        email: "Email",
        print: "Print",
        copy: "Copy Link",
        vk: "VK",
        tumblr: "Tumblr",
        skype: "Skype",
        digg: "Digg",
        stumbleupon: "StumbleUpon",
        pocket: "Pocket",
        flipboard: "Flipboard",
        buffer: "Buffer",
        weibo: "Weibo",
        blogger: "Blogger",
        odnoklassniki: "Odnoklassniki",
    };
    const item = (id, network) => ({
        id,
        network,
        customLabel: networkLabels[network] || network,
    });
    const typographyDefaults = () => ({
        shareButtonsFontFamily: "inherit",
        shareButtonsFontSize: "14px",
        shareButtonsFontWeight: "600",
        shareButtonsLineHeight: "1.2em",
        shareButtonsLetterSpacing: "0px",
        shareButtonsWordSpacing: "0px",
        shareButtonsTextTransform: "none",
        shareButtonsFontStyle: "normal",
        shareButtonsTextDecoration: "none",
    });
    const defaults = () => ({
        ...advanced(),
        items: [item("share-1", "facebook"), item("share-2", "x"), item("share-3", "linkedin")],
        view: "icon_text",
        showLabel: true,
        skin: "flat",
        shape: "rounded",
        columns: "auto",
        alignment: "left",
        targetUrl: "current",
        customUrl: "",
        columnsGap: "8px",
        rowsGap: "8px",
        buttonSize: "40px",
        iconSize: "16px",
        buttonHeight: "40px",
        colorMode: "official",
        primaryColor: "#1877f2",
        secondaryColor: "#ffffff",
        primaryColorHover: "#0d6efd",
        secondaryColorHover: "#ffffff",
        ...typographyDefaults(),
    });
    const enumValue = (value, allowed, fallback) =>
        allowed.includes(value) ? value : fallback;
    const boolValue = (value) =>
        value === true || value === 1 || value === "1" || value === "true";
    const clampLength = (value, fallback) => {
        const raw = String(value ?? "").trim();
        return /^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(raw)
            ? raw
            : fallback;
    };

    registry.register({type: "share_buttons",defaults,normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            s.view = enumValue(s.view, ["icon_text", "icon", "text"], "icon_text");
            s.skin = enumValue(s.skin, ["flat", "gradient", "minimal", "framed", "box", "3d"], "flat");
            s.shape = enumValue(s.shape, ["rounded", "square", "circle", "none"], "rounded");
            s.columns = enumValue(s.columns, ["auto", "1", "2", "3", "4", "5", "6"], "auto");
            s.alignment = enumValue(s.alignment, ["left", "center", "right"], "left");
            s.targetUrl = enumValue(s.targetUrl, ["current", "custom"], "current");
            s.colorMode = enumValue(s.colorMode, ["official", "custom"], "official");
            s.showLabel = boolValue(s.showLabel);
            ["customUrl", "primaryColor", "secondaryColor", "primaryColorHover", "secondaryColorHover"].forEach(
                (key) => (s[key] = String(s[key] ?? base[key])),
            );
            ["columnsGap", "rowsGap", "buttonSize", "iconSize", "buttonHeight"].forEach(
                (key) => (s[key] = clampLength(s[key], base[key])),
            );
            const sourceItems = Array.isArray(s.items) ? s.items : [];
            s.items = sourceItems
                .map((entry, index) => {
                    const network = String(entry?.network || "");
                    if (!networks.includes(network)) return null;
                    return {
                        id: String(entry?.id || "share-" + (index + 1)),
                        network,
                        customLabel: String(entry?.customLabel ?? networkLabels[network] ?? network),
                    };
                })
                .filter(Boolean);
            if (!s.items.length) s.items = base.items.map((entry) => ({ ...entry }));
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
