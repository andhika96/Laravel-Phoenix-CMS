(function (registry) {
    "use strict";

    const advanced = () =>
        registry.advancedDefaults();
    const defaults = () => ({
        ...advanced(),
        imageUrl: "",
        imageAlt: "Hotspot image",
        imageResolution: "full",
        hotspots: [
            {
                id: "hotspot-1",
                label: "1",
                tooltip: "Hotspot item",
                x: 50,
                y: 50,
                linkUrl: "",
            },
        ],
        hotspotAnimation: "soft-beat",
        sequencedAnimation: false,
        tooltipPosition: "top",
        tooltipTrigger: "hover",
        tooltipAnimation: "fade",
        tooltipDuration: 300,
        imageAlignment: "center",
        imageWidth: "100%",
        imageMaxWidth: "100%",
        imageHeight: "auto",
        imageObjectFit: "cover",
        imageObjectPosition: "center center",
        imageOpacity: 1,
        imageBrightness: 100,
        imageContrast: 100,
        imageSaturation: 100,
        imageBlur: 0,
        imageHue: 0,
        imageTransitionDuration: 0.3,
        imageBorderType: "none",
        imageBorderWidth: "0px",
        imageBorderColor: "transparent",
        imageRadius: "0px",
        imageShadowColor: "transparent",
        imageShadowHorizontal: "0px",
        imageShadowVertical: "0px",
        imageShadowBlur: "0px",
        imageShadowSpread: "0px",
        hotspotColor: "#ffffff",
        hotspotMinWidth: "32px",
        hotspotMinHeight: "32px",
        hotspotBoxColor: "#6979f8",
        hotspotPadding: "8px",
        hotspotRadius: "50%",
        tooltipTextColor: "#ffffff",
        tooltipAlign: "center",
        tooltipMinWidth: "120px",
        tooltipMaxWidth: "240px",
        tooltipPadding: "10px",
        tooltipColor: "#101828",
        tooltipRadius: "4px",
    });

    registry.register({type: "hotspot",defaults,normalize(node) {
            const settings = (node.settings = {
                ...defaults(),
                ...(node.settings || {}),
            });
            settings.hotspots =
                Array.isArray(settings.hotspots) && settings.hotspots.length
                    ? settings.hotspots
                    : defaults().hotspots;
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
