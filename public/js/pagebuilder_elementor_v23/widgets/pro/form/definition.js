(function (registry) {
    "use strict";
    const advanced = () =>
        window.PageBuilderElementorV23ComplexWidgetRuntime?.image_box?.defaults?.() ||
        {};
    const fieldDefaults = (field = {}) => ({
        id: "field",
        label: "Field",
        type: "text",
        placeholder: "",
        defaultValue: "",
        required: false,
        width: 100,
        autocomplete: "",
        optionsText: "Option 1|option-1\nOption 2|option-2",
        multiple: false,
        inlineList: false,
        rows: 4,
        min: "",
        max: "",
        step: "",
        acceptanceText: "I agree to the terms.",
        html: "HTML content",
        fileTypes: "",
        stepTitle: "Step",
        stepDescription: "",
        nextButton: "Next",
        previousButton: "Previous",
        ...field,
    });
    const defaults = () => ({
        ...advanced(),
        formName: "New Form",
        fields: [
            fieldDefaults({ id: "name", label: "Name" }),
            fieldDefaults({
                id: "email",
                label: "Email",
                type: "email",
                required: true,
            }),
            fieldDefaults({
                id: "message",
                label: "Message",
                type: "textarea",
            }),
        ],
        inputSize: "small",
        showLabels: true,
        markRequired: false,
        buttonSize: "small",
        buttonWidth: "100",
        buttonText: "Send",
        buttonIconSource: "none",
        buttonIconStyle: "solid",
        buttonIconName: "",
        buttonIconClass: "",
        buttonIconSvg: "",
        buttonIconPosition: "before",
        buttonIconSpacing: "5px",
        buttonId: "",
        submitActions: ["message"],
        emailTo: "",
        emailSubject: 'New message from "New Form"',
        emailContent: "[all-fields]",
        emailFrom: "",
        emailFromName: "",
        emailReplyTo: "email",
        emailCc: "",
        emailBcc: "",
        emailContentType: "html",
        email2To: "",
        email2Subject: "New form submission",
        email2Content: "[all-fields]",
        email2From: "",
        email2FromName: "",
        email2ReplyTo: "",
        email2Cc: "",
        email2Bcc: "",
        email2ContentType: "html",
        redirectUrl: "",
        webhookUrl: "",
        stepType: "none",
        stepShape: "circle",
        formId: "new_form",
        validation: "browser",
        customMessages: false,
        successMessage: "The form was sent successfully.",
        errorMessage: "An error occurred.",
        columnGap: "10px",
        rowGap: "10px",
        labelColor: "#344054",
        htmlColor: "#344054",
        fieldTextColor: "#344054",
        fieldBackground: "#ffffff",
        fieldBorderColor: "#d0d5dd",
        fieldBorderWidth: "1px",
        fieldRadius: "4px",
        fieldFocusBorderColor: "#6979f8",
        fieldFocusBackground: "#ffffff",
        buttonAlign: "left",
        buttonBackground: "#6979f8",
        buttonBackgroundHover: "#5868e8",
        buttonTextColor: "#ffffff",
        buttonTextColorHover: "#ffffff",
        buttonRadius: "4px",
        successColor: "#067647",
        errorColor: "#b42318",
        stepActiveColor: "#6979f8",
        stepInactiveColor: "#d0d5dd",
    });
    registry.register({
        type: "form",
        label: "Form",
        category: "pro",
        icon: "fab fa-wpforms",
        toolbox: true,
        canvas: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue",
        settings: "/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue",
        defaults,
        normalize(node) {
            const settings = (node.settings = {
                ...defaults(),
                ...(node.settings || {}),
            });
            settings.fields =
                Array.isArray(settings.fields) && settings.fields.length
                    ? settings.fields.map((field) => fieldDefaults(field))
                    : defaults().fields;
            const allowedActions = [
                "message",
                "email",
                "email2",
                "redirect",
                "webhook",
                "collect",
            ];
            settings.submitActions = Array.isArray(settings.submitActions)
                ? settings.submitActions.filter((action) =>
                      allowedActions.includes(action),
                  )
                : ["message"];
            const iconClass = String(settings.buttonIconClass || "").trim();
            if (settings.buttonIconSource === "svg" && settings.buttonIconSvg) {
                settings.buttonIconClass = "";
            } else if (/^(?:fas|far|fab|fal|fad)\s+fa-[a-z0-9-]+$/i.test(iconClass)) {
                const [prefix, name] = iconClass.split(/\s+/);
                settings.buttonIconSource = "library";
                settings.buttonIconStyle =
                    { fas: "solid", far: "regular", fab: "brands" }[prefix] ||
                    settings.buttonIconStyle ||
                    "solid";
                settings.buttonIconName = name.replace(/^fa-/, "");
                settings.buttonIconSvg = "";
            } else {
                settings.buttonIconSource = "none";
                settings.buttonIconName = "";
                settings.buttonIconClass = "";
                settings.buttonIconSvg = "";
            }
            return node;
        },
    });
})(window.PageBuilderElementorV23Widgets);
