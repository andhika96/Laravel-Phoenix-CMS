(function (registry) {
    "use strict";
    const advanced = () =>
        window.PageBuilderElementorComplexWidgetRuntime?.image_box?.defaults?.() ||
        {};
    const defaults = () => ({
        ...advanced(),
        frontGraphic: "icon",
        frontIconSource: "library",
        frontIconStyle: "solid",
        frontIconName: "star",
        frontIconClass: "fas fa-star",
        frontIconSvg: "",
        frontImageUrl: "",
        frontImageAlt: "",
        frontTitle: "Front Side",
        frontDescription: "This is the front side",
        frontBackground: "#14b8a6",
        backTitle: "Back Side",
        backDescription: "This is the back side",
        backButtonText: "Click Here",
        backButtonLink: "",
        backBackground: "#6979f8",
        flipEffect: "flip",
        flipDirection: "left",
        height: "300px",
        borderRadius: "8px",
        frontPadding: "30px",
        backPadding: "30px",
        frontBorderWidth: "0px",
        frontBorderColor: "transparent",
        backBorderWidth: "0px",
        backBorderColor: "transparent",
        frontAlignment: "center",
        backAlignment: "center",
        verticalPosition: "middle",
        frontImageWidth: "30%",
        frontImageOpacity: 1,
        frontImageRadius: "0px",
        frontGraphicSpacing: "16px",
        iconColor: "#ffffff",
        iconBackground: "transparent",
        iconSize: "48px",
        iconPadding: "0px",
        iconRadius: "0px",
        iconRotation: 0,
        frontTitleColor: "#ffffff",
        frontDescriptionColor: "#ffffff",
        backTitleColor: "#ffffff",
        backDescriptionColor: "#ffffff",
        buttonTextColor: "#6979f8",
        buttonBackground: "#ffffff",
        buttonTextColorHover: "#ffffff",
        buttonBackgroundHover: "#5868e8",
    });
    registry.register({
        type: "flip_box",
        label: "Flip Box",
        category: "pro",
        icon: "fas fa-sync-alt",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const s = (node.settings = {
                ...defaults(),
                ...(node.settings || {}),
            });
            s.flipEffect = [
                "flip",
                "slide",
                "push",
                "zoom-in",
                "zoom-out",
                "fade",
            ].includes(s.flipEffect)
                ? s.flipEffect
                : "flip";
            s.flipDirection = ["left", "right", "up", "down"].includes(
                s.flipDirection,
            )
                ? s.flipDirection
                : "left";
            const iconClass = String(s.frontIconClass || "").trim();
            if (s.frontIconSource === "svg" && s.frontIconSvg) {
                s.frontIconClass = "";
            } else if (/^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(iconClass)) {
                const [prefix, name] = iconClass.split(/\s+/);
                s.frontIconSource = "library";
                s.frontIconStyle =
                    { fas: "solid", far: "regular", fab: "brands" }[prefix] ||
                    s.frontIconStyle ||
                    "solid";
                s.frontIconName = name.replace(/^fa-/, "");
                s.frontIconSvg = "";
            } else {
                Object.assign(s, {
                    frontIconSource: "library",
                    frontIconStyle: "solid",
                    frontIconName: "star",
                    frontIconClass: "fas fa-star",
                    frontIconSvg: "",
                });
            }
            return node;
        },
    });
})(window.PageBuilderElementorWidgets);
