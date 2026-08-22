(function (registry) {
    "use strict";

    const advanced = () =>
        registry.advancedDefaults();
    const languages = [
        "plain-text",
        "markup",
        "html",
        "xml",
        "svg",
        "mathml",
        "ssml",
        "atom",
        "rss",
        "css",
        "less",
        "sass",
        "scss",
        "javascript",
        "typescript",
        "actionscript",
        "c",
        "cpp",
        "csharp",
        "java",
        "kotlin",
        "dart",
        "go",
        "rust",
        "swift",
        "objectivec",
        "php",
        "python",
        "ruby",
        "perl",
        "lua",
        "r",
        "matlab",
        "sql",
        "plsql",
        "json",
        "json5",
        "yaml",
        "toml",
        "markdown",
        "mdx",
        "bash",
        "shell",
        "powershell",
        "batch",
        "docker",
        "git",
        "diff",
        "http",
        "graphql",
        "jsx",
        "tsx",
        "vue",
        "twig",
        "blade",
        "pascal",
        "haskell",
        "scala",
        "groovy",
        "elixir",
        "erlang",
        "clojure",
        "fsharp",
        "fortran",
        "cobol",
        "basic",
        "arduino",
    ];
    const typographyDefaults = () => ({
        codeHighlightFontFamily: "monospace",
        codeHighlightFontSize: "14px",
        codeHighlightFontWeight: "400",
        codeHighlightLineHeight: "1.5em",
        codeHighlightLetterSpacing: "0px",
        codeHighlightWordSpacing: "0px",
        codeHighlightTextTransform: "none",
        codeHighlightFontStyle: "normal",
        codeHighlightTextDecoration: "none",
        codeHighlightCopyButtonFontFamily: "inherit",
        codeHighlightCopyButtonFontSize: "12px",
        codeHighlightCopyButtonFontWeight: "600",
        codeHighlightCopyButtonLineHeight: "1.2em",
        codeHighlightCopyButtonLetterSpacing: "0px",
        codeHighlightCopyButtonWordSpacing: "0px",
        codeHighlightCopyButtonTextTransform: "none",
        codeHighlightCopyButtonFontStyle: "normal",
        codeHighlightCopyButtonTextDecoration: "none",
    });
    const defaults = () => ({
        ...advanced(),
        language: "javascript",
        code: 'const greeting = "Hello, world!";\nconsole.log(greeting);',
        lineNumbers: true,
        copyButton: true,
        highlightLines: "",
        wordWrap: false,
        theme: "dark",
        height: "300px",
        heightTablet: "300px",
        heightMobile: "300px",
        fontSize: "14px",
        fontSizeTablet: "14px",
        fontSizeMobile: "14px",
        codeTextColor: "",
        codeBackground: "",
        codePaddingTop: "20px",
        codePaddingRight: "20px",
        codePaddingBottom: "20px",
        codePaddingLeft: "20px",
        codeRadiusTop: "6px",
        codeRadiusRight: "6px",
        codeRadiusBottom: "6px",
        codeRadiusLeft: "6px",
        lineNumberColor: "",
        lineNumberBackground: "",
        gutterWidth: "34px",
        highlightLineColor: "",
        highlightLineBorderColor: "",
        copyButtonTextColor: "#ffffff",
        copyButtonBackground: "#6979f8",
        copyButtonTextColorHover: "#ffffff",
        copyButtonBackgroundHover: "#5868e8",
        copyButtonPaddingTop: "8px",
        copyButtonPaddingRight: "12px",
        copyButtonPaddingBottom: "8px",
        copyButtonPaddingLeft: "12px",
        copyButtonRadiusTop: "4px",
        copyButtonRadiusRight: "4px",
        copyButtonRadiusBottom: "4px",
        copyButtonRadiusLeft: "4px",
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
    const normalizeHighlightLines = (value) => {
        return String(value ?? "")
            .split(",")
            .map((segment) => {
                const range = segment.trim().match(/^(\d+)\s*-\s*(\d+)$/);
                if (range) {
                    const start = Math.max(1, Number(range[1]));
                    const end = Math.max(1, Number(range[2]));
                    return start <= end ? `${start}-${end}` : `${end}-${start}`;
                }
                return /^\d+$/.test(segment.trim()) && Number(segment.trim()) > 0
                    ? String(Number(segment.trim()))
                    : "";
            })
            .filter(Boolean)
            .join(", ");
    };

    registry.register({type: "code_highlight",defaults,normalize(node) {
            const base = defaults();
            const s = (node.settings = { ...base, ...(node.settings || {}) });
            s.language = enumValue(s.language, languages, "javascript");
            s.code = String(s.code ?? base.code);
            s.highlightLines = normalizeHighlightLines(s.highlightLines);
            s.theme = enumValue(s.theme, ["light", "dark"], "dark");
            ["lineNumbers", "copyButton", "wordWrap"].forEach((key) => {
                s[key] = boolValue(s[key]);
            });
            s.height = clampLength(s.height, base.height);
            s.heightTablet = clampLength(s.heightTablet, base.heightTablet);
            s.heightMobile = clampLength(s.heightMobile, base.heightMobile);
            s.fontSize = clampLength(s.fontSize, base.fontSize);
            s.fontSizeTablet = clampLength(s.fontSizeTablet, base.fontSizeTablet);
            s.fontSizeMobile = clampLength(s.fontSizeMobile, base.fontSizeMobile);
            [
                "gutterWidth",
                "codePaddingTop",
                "codePaddingRight",
                "codePaddingBottom",
                "codePaddingLeft",
                "codeRadiusTop",
                "codeRadiusRight",
                "codeRadiusBottom",
                "codeRadiusLeft",
                "copyButtonPaddingTop",
                "copyButtonPaddingRight",
                "copyButtonPaddingBottom",
                "copyButtonPaddingLeft",
                "copyButtonRadiusTop",
                "copyButtonRadiusRight",
                "copyButtonRadiusBottom",
                "copyButtonRadiusLeft",
            ].forEach((key) => {
                s[key] = clampLength(s[key], base[key]);
            });
            return node;
        }});
})(window.PageBuilderElementorV24Widgets);
