(function (registry) {
    "use strict";

    if (!registry) throw new Error("Page Builder Elementor widget registry is not loaded.");

    const advanced = () =>
        registry.advancedDefaults();
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
    const safeLink = (value) => {
        const raw = String(value || "").trim();
        return /^(?:https?:\/\/|\/|#)/i.test(raw) ? raw : "";
    };
    const tag = (value, fallback = "h4") =>
        enumValue(String(value || "").toLowerCase(), ["h1", "h2", "h3", "h4", "h5", "h6", "div", "span"], fallback);
    const typographyDefaults = () => ({
        videoPlaylistNameFontFamily: "inherit",
        videoPlaylistNameFontSize: "20px",
        videoPlaylistNameFontWeight: "600",
        videoPlaylistNameLineHeight: "1.3em",
        videoPlaylistNameLetterSpacing: "0px",
        videoPlaylistNameWordSpacing: "0px",
        videoPlaylistNameTextTransform: "none",
        videoPlaylistNameFontStyle: "normal",
        videoPlaylistNameTextDecoration: "none",
        videoPlaylistCountFontFamily: "inherit",
        videoPlaylistCountFontSize: "13px",
        videoPlaylistCountFontWeight: "400",
        videoPlaylistCountLineHeight: "1.4em",
        videoPlaylistCountLetterSpacing: "0px",
        videoPlaylistCountWordSpacing: "0px",
        videoPlaylistCountTextTransform: "none",
        videoPlaylistCountFontStyle: "normal",
        videoPlaylistCountTextDecoration: "none",
        videoPlaylistItemFontFamily: "inherit",
        videoPlaylistItemFontSize: "14px",
        videoPlaylistItemFontWeight: "500",
        videoPlaylistItemLineHeight: "1.3em",
        videoPlaylistItemLetterSpacing: "0px",
        videoPlaylistItemWordSpacing: "0px",
        videoPlaylistItemTextTransform: "none",
        videoPlaylistItemFontStyle: "normal",
        videoPlaylistItemTextDecoration: "none",
        videoPlaylistDurationFontFamily: "inherit",
        videoPlaylistDurationFontSize: "12px",
        videoPlaylistDurationFontWeight: "400",
        videoPlaylistDurationLineHeight: "1.3em",
        videoPlaylistDurationLetterSpacing: "0px",
        videoPlaylistDurationWordSpacing: "0px",
        videoPlaylistDurationTextTransform: "none",
        videoPlaylistDurationFontStyle: "normal",
        videoPlaylistDurationTextDecoration: "none",
        videoPlaylistTabTitleFontFamily: "inherit",
        videoPlaylistTabTitleFontSize: "14px",
        videoPlaylistTabTitleFontWeight: "600",
        videoPlaylistTabTitleLineHeight: "1.3em",
        videoPlaylistTabTitleLetterSpacing: "0px",
        videoPlaylistTabTitleWordSpacing: "0px",
        videoPlaylistTabTitleTextTransform: "none",
        videoPlaylistTabTitleFontStyle: "normal",
        videoPlaylistTabTitleTextDecoration: "none",
        videoPlaylistTabContentFontFamily: "inherit",
        videoPlaylistTabContentFontSize: "14px",
        videoPlaylistTabContentFontWeight: "400",
        videoPlaylistTabContentLineHeight: "1.5em",
        videoPlaylistTabContentLetterSpacing: "0px",
        videoPlaylistTabContentWordSpacing: "0px",
        videoPlaylistTabContentTextTransform: "none",
        videoPlaylistTabContentFontStyle: "normal",
        videoPlaylistTabContentTextDecoration: "none",
        videoPlaylistShowMoreFontFamily: "inherit",
        videoPlaylistShowMoreFontSize: "13px",
        videoPlaylistShowMoreFontWeight: "600",
        videoPlaylistShowMoreLineHeight: "1.3em",
        videoPlaylistShowMoreLetterSpacing: "0px",
        videoPlaylistShowMoreWordSpacing: "0px",
        videoPlaylistShowMoreTextTransform: "none",
        videoPlaylistShowMoreFontStyle: "normal",
        videoPlaylistShowMoreTextDecoration: "none",
    });
    const item = (id, index = 1) => ({
        id,
        type: "youtube",
        link: "https://www.youtube.com/watch?v=dQw4w9WgXcQ",
        title: index === 1 ? "Sample video" : `Sample video ${index}`,
        titleTag: "h4",
        duration: "0:16",
        thumbnailUrl: "",
        sectionContent: "",
        showContentTabs: false,
        contentTabOneTitle: "Overview",
        contentTabOneContent: "",
        contentTabTwoTitle: "Notes",
        contentTabTwoContent: "",
    });
    const defaults = () => ({
        ...advanced(),
        playlistName: "Playlist",
        playlistTitleTag: "h3",
        playlistNameFontSizeMode: "auto",
        playlistNameFontSize: "20px",
        playlistNameFontSizeTablet: "",
        playlistNameFontSizeMobile: "",
        items: [1, 2, 3].map((index) => item(`video-${index}`, index)),
        tabsCollapsible: false,
        readMoreLabel: "Read More",
        readLessLabel: "Read Less",
        tabsHeight: "120px",
        imageOverlay: false,
        overlayImageUrl: "",
        imageResolution: "full",
        autoplayOnLoad: false,
        autoplayNext: false,
        indicateWatched: false,
        showVideoCount: true,
        showDuration: true,
        showThumbnails: true,
        dropdownAlignment: "right",
        playIconSource: "library",
        playIconClass: "fas fa-play",
        playIconName: "play",
        playIconStyle: "solid",
        playIconSvg: "",
        playedIconSource: "library",
        playedIconClass: "fas fa-check",
        playedIconName: "check",
        playedIconStyle: "solid",
        playedIconSvg: "",
        dropdownIconSource: "library",
        dropdownIconClass: "fas fa-chevron-down",
        dropdownIconName: "chevron-down",
        dropdownIconStyle: "solid",
        dropdownIconSvg: "",
        videoPosition: "left",
        videoHeight: "360px",
        videoPlaylistItemFontSizeMode: "auto",
        playlistNameBackground: "#101828",
        playlistNameColor: "#ffffff",
        videoCountColor: "#667085",
        itemBackground: "#ffffff",
        itemBackgroundHover: "#f2f4f7",
        itemBackgroundActive: "#eef2ff",
        itemColor: "#344054",
        itemColorHover: "#101828",
        itemColorActive: "#101828",
        durationColor: "#667085",
        iconColor: "#6979f8",
        iconBackground: "#ffffff",
        iconShadow: "0 2px 6px rgba(16,24,40,.16)",
        iconSize: "18px",
        dropdownIconColor: "#667085",
        dropdownIconColorHover: "#101828",
        dropdownIconColorActive: "#6979f8",
        sectionBackgroundType: "classic",
        sectionBackground: "#f8fafc",
        sectionGradientColorOne: "#f8fafc",
        sectionGradientColorTwo: "#eef2ff",
        sectionGradientAngle: 135,
        sectionBorderType: "solid",
        sectionBorderColor: "#e4e7ec",
        sectionBorderWidth: "1px",
        sectionRadius: "6px",
        sectionBoxShadow: "none",
        sectionPadding: "12px",
        tabsBorderWidth: "1px",
        tabsBorderColor: "#e4e7ec",
        tabsBackground: "#ffffff",
        tabsTitleColor: "#667085",
        tabsTitleActiveColor: "#6979f8",
        tabsContentColor: "#344054",
        tabsContentPadding: "14px",
        showMoreColor: "#6979f8",
        showMoreColorHover: "#5868e8",
        ...typographyDefaults(),
    });

    registry.register({type: "video_playlist",defaults,normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            s.playlistName = String(s.playlistName ?? base.playlistName);
        s.playlistTitleTag = tag(s.playlistTitleTag, "h3");
            const playlistNameSizeBase = base;
            const hasLegacyPlaylistNameSize = ["videoPlaylistNameFontSize", "videoPlaylistNameFontSizeTablet", "videoPlaylistNameFontSizeMobile"].some((key) => String(s[key] ?? "").trim() !== String(playlistNameSizeBase[key] ?? "").trim());
            s.playlistNameFontSizeMode = ["auto", "custom"].includes(s.playlistNameFontSizeMode)
                ? s.playlistNameFontSizeMode
                : (hasLegacyPlaylistNameSize ? "custom" : "auto");
            const hasLegacyPlaylistItemSize = ["videoPlaylistItemFontSize", "videoPlaylistItemFontSizeTablet", "videoPlaylistItemFontSizeMobile"].some((key) => String(s[key] ?? "").trim() !== String(playlistNameSizeBase[key] ?? "").trim());
            s.videoPlaylistItemFontSizeMode = ["auto", "custom"].includes(s.videoPlaylistItemFontSizeMode)
                ? s.videoPlaylistItemFontSizeMode
                : (hasLegacyPlaylistItemSize ? "custom" : "auto");
            s.tabsCollapsible = bool(s.tabsCollapsible);
            s.imageOverlay = bool(s.imageOverlay);
            s.autoplayOnLoad = bool(s.autoplayOnLoad);
            s.autoplayNext = bool(s.autoplayNext);
            s.indicateWatched = bool(s.indicateWatched);
            s.showVideoCount = s.showVideoCount !== false;
            s.showDuration = s.showDuration !== false;
            s.showThumbnails = s.showThumbnails !== false;
            s.dropdownAlignment = enumValue(s.dropdownAlignment, ["left", "center", "right"], "right");
            s.videoPosition = enumValue(s.videoPosition, ["left", "right"], "left");
            s.sectionBackgroundType = enumValue(s.sectionBackgroundType, ["classic", "gradient", "none"], "classic");
            s.sectionGradientAngle = Math.max(0, Math.min(360, Number(s.sectionGradientAngle) || 0));
            s.sectionBorderType = enumValue(s.sectionBorderType, ["none", "solid", "double", "dotted", "dashed"], "solid");
            s.imageResolution = enumValue(s.imageResolution, ["thumbnail", "medium", "medium_large", "large", "1536x1536", "2048x2048", "full", "custom"], "full");
            ["videoHeight", "tabsHeight", "sectionBorderWidth", "sectionRadius", "sectionPadding", "tabsBorderWidth", "tabsContentPadding", "iconSize"].forEach((key) => {
                s[key] = length(s[key], base[key]);
            });
            s.tabsHeight = length(s.tabsHeight, base.tabsHeight);
            const sourceItems = Array.isArray(s.items) && s.items.length ? s.items : base.items;
            s.items = sourceItems.map((entry, index) => {
                const normalized = { ...item(`video-${index + 1}`, index + 1), ...(entry || {}) };
                normalized.id = String(entry?.id || `video-${index + 1}`);
                normalized.type = enumValue(normalized.type, ["youtube", "vimeo", "self_hosted", "section"], "youtube");
                normalized.link = safeLink(normalized.link);
                normalized.title = String(normalized.title ?? "");
                normalized.titleTag = tag(normalized.titleTag, "h4");
                normalized.duration = String(normalized.duration ?? "");
                normalized.thumbnailUrl = safeLink(normalized.thumbnailUrl);
                normalized.sectionContent = String(normalized.sectionContent ?? "");
                normalized.showContentTabs = bool(normalized.showContentTabs);
                normalized.contentTabOneTitle = String(normalized.contentTabOneTitle ?? "");
                normalized.contentTabOneContent = String(normalized.contentTabOneContent ?? "");
                normalized.contentTabTwoTitle = String(normalized.contentTabTwoTitle ?? "");
                normalized.contentTabTwoContent = String(normalized.contentTabTwoContent ?? "");
                return normalized;
            });
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
