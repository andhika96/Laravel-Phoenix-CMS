(function (registry) {
    "use strict";

    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() || {};
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
        skin: "border",
        alignment: "left",
        alignmentTablet: "",
        alignmentMobile: "",
        content: "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        author: "John Doe",
        tweetButton: true,
        tweetView: "icon_text",
        tweetSkin: "classic",
        tweetLabel: "Tweet",
        tweetUsername: "",
        tweetTarget: "current",
        tweetUrl: "",
        contentGap: "16px",
        contentColor: "#344054",
        authorColor: "#101828",
        tweetSize: "14px",
        tweetSizeTablet: "14px",
        tweetSizeMobile: "14px",
        tweetBorderRadius: "4px",
        tweetColorMode: "official",
        tweetPrimaryColor: "#1da1f2",
        tweetSecondaryColor: "#ffffff",
        tweetPrimaryColorHover: "#0d8bd0",
        tweetSecondaryColorHover: "#ffffff",
        tweetTransitionDuration: 0.3,
        tweetGap: "8px",
        borderColor: "#6979f8",
        borderWidthTop: "3px",
        borderWidthRight: "0px",
        borderWidthBottom: "0px",
        borderWidthLeft: "0px",
        borderGap: "16px",
        borderTransitionDuration: 0.3,
        borderVerticalPadding: "8px",
        quoteColor: "#6979f8",
        quoteSize: "48px",
        quoteGap: "12px",
        boxPaddingTop: "24px",
        boxPaddingRight: "24px",
        boxPaddingBottom: "24px",
        boxPaddingLeft: "24px",
        boxBackground: "#f8fafc",
        boxBackgroundHover: "#eef2ff",
        boxBorderType: "none",
        boxBorderWidthTop: "1px",
        boxBorderWidthRight: "1px",
        boxBorderWidthBottom: "1px",
        boxBorderWidthLeft: "1px",
        boxBorderWidthHoverTop: "1px",
        boxBorderWidthHoverRight: "1px",
        boxBorderWidthHoverBottom: "1px",
        boxBorderWidthHoverLeft: "1px",
        boxBorderColor: "#e4e7ec",
        boxBorderColorHover: "#6979f8",
        boxRadiusTop: "8px",
        boxRadiusRight: "8px",
        boxRadiusBottom: "8px",
        boxRadiusLeft: "8px",
        boxRadiusHoverTop: "8px",
        boxRadiusHoverRight: "8px",
        boxRadiusHoverBottom: "8px",
        boxRadiusHoverLeft: "8px",
        boxShadow: "0 4px 12px #10182814",
        boxShadowHover: "0 8px 24px #10182824",
        boxTransitionDuration: 0.3,
        ...typographyDefaults("blockquoteContent", "18px", "400", "1.5em"),
        ...typographyDefaults("blockquoteAuthor", "14px", "600", "1.4em"),
        ...typographyDefaults("blockquoteTweet", "13px", "600", "1.2em"),
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
    const clampDuration = (value, fallback) =>
        Math.max(0, Math.min(10, Number(value) || fallback));

    registry.register({
        type: "blockquote",
        label: "Blockquote",
        category: "pro",
        icon: "fas fa-quote-left",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            s.skin = enumValue(s.skin, ["border", "quotation", "boxed", "clean"], "border");
            ["alignment", "alignmentTablet", "alignmentMobile"].forEach((key) => {
                s[key] = enumValue(s[key], ["", "left", "center", "right"], "");
            });
            s.tweetView = enumValue(s.tweetView, ["icon_text", "icon", "text"], "icon_text");
            s.tweetSkin = enumValue(s.tweetSkin, ["classic", "bubble", "link"], "classic");
            s.tweetTarget = enumValue(s.tweetTarget, ["current", "none", "custom"], "current");
            s.tweetColorMode = enumValue(s.tweetColorMode, ["official", "custom"], "official");
            s.tweetButton = boolValue(s.tweetButton);
            ["content", "author", "tweetLabel", "tweetUsername", "tweetUrl"].forEach(
                (key) => (s[key] = String(s[key] ?? base[key])),
            );
            [
                "contentGap",
                "tweetSize",
                "tweetSizeTablet",
                "tweetSizeMobile",
                "tweetBorderRadius",
                "tweetGap",
                "borderGap",
                "borderVerticalPadding",
                "quoteSize",
                "quoteGap",
                "borderWidthTop",
                "borderWidthRight",
                "borderWidthBottom",
                "borderWidthLeft",
                "boxPaddingTop",
                "boxPaddingRight",
                "boxPaddingBottom",
                "boxPaddingLeft",
                "boxBorderWidthTop",
                "boxBorderWidthRight",
                "boxBorderWidthBottom",
                "boxBorderWidthLeft",
                "boxBorderWidthHoverTop",
                "boxBorderWidthHoverRight",
                "boxBorderWidthHoverBottom",
                "boxBorderWidthHoverLeft",
                "boxRadiusTop",
                "boxRadiusRight",
                "boxRadiusBottom",
                "boxRadiusLeft",
                "boxRadiusHoverTop",
                "boxRadiusHoverRight",
                "boxRadiusHoverBottom",
                "boxRadiusHoverLeft",
            ].forEach((key) => (s[key] = clampLength(s[key], base[key])));
            ["tweetTransitionDuration", "borderTransitionDuration", "boxTransitionDuration"].forEach(
                (key) => (s[key] = clampDuration(s[key], base[key])),
            );
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
