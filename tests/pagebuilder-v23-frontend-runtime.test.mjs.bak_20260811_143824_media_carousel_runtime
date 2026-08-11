import assert from "node:assert/strict";
import test from "node:test";

const windowListeners = new Map();
globalThis.window = {
    addEventListener(type, handler) {
        windowListeners.set(type, handler);
    },
    clearInterval,
    clearTimeout,
    matchMedia: () => ({ matches: false }),
    setInterval,
    setTimeout,
};
globalThis.document = {
    addEventListener() {},
    documentElement: { clientWidth: 1280 },
    readyState: "loading",
};
globalThis.getComputedStyle = () => ({
    getPropertyValue: () => "20px",
});
globalThis.CustomEvent = class CustomEvent {
    constructor(type, options = {}) {
        this.type = type;
        this.detail = options.detail;
    }
};

await import("../public/js/pagebuilder_elementor_v23/frontend-runtime.js");
const runtime = window.PageBuilderElementorV23Runtime;

function classList() {
    const values = new Set();
    return {
        add: (name) => values.add(name),
        contains: (name) => values.has(name),
        remove: (name) => values.delete(name),
        toggle(name, force) {
            const enabled = force === undefined ? !values.has(name) : force;
            if (enabled) values.add(name);
            else values.delete(name);
            return enabled;
        },
    };
}

function eventTarget() {
    const handlers = new Map();
    return {
        handlers,
        addEventListener(type, handler) {
            handlers.set(type, handler);
        },
    };
}

function rootBase(attributes = {}) {
    const target = eventTarget();
    const attrs = new Map(Object.entries(attributes));
    return {
        ...target,
        classList: classList(),
        dataset: {},
        style: { setProperty() {} },
        getAttribute: (name) => attrs.get(name) || "",
        setAttribute: (name, value) => attrs.set(name, String(value)),
    };
}

test("actual Pro Carousel runtime uses the rendered slide offset, including gap", () => {
    const items = [0, 120, 240].map((offsetLeft) => ({
        offsetLeft,
        style: {},
    }));
    const track = { style: {} };
    const next = eventTarget();
    const root = rootBase({
        "data-pro-config": JSON.stringify({
            slidesToShow: 2,
            slidesToScroll: 1,
            transitionSpeed: 400,
        }),
    });
    root.querySelectorAll = (selector) =>
        selector.includes("pb-pro-carousel__slide") ? items : [];
    root.querySelector = (selector) => {
        if (selector.includes("pb-pro-carousel__track")) return track;
        if (selector.includes("data-pro-next")) return next;
        return null;
    };

    runtime.initProCarousel(root);
    assert.equal(track.style.transform, "translate3d(-0px,0,0)");
    next.handlers.get("click")();
    assert.equal(track.style.transform, "translate3d(-120px,0,0)");
});

test("actual Pro Hotspot runtime opens a linked tooltip before navigation", () => {
    const tooltip = { hidden: true };
    const marker = eventTarget();
    marker.matches = (selector) => selector === "a[href]";
    marker.querySelector = () => tooltip;
    marker.setAttribute = () => {};
    const root = rootBase();
    root.dataset.trigger = "click";
    root.querySelectorAll = () => [marker];

    runtime.initProHotspot(root);
    let prevented = false;
    marker.handlers.get("click")({ preventDefault: () => (prevented = true) });
    assert.equal(prevented, true);
    assert.equal(tooltip.hidden, false);

    prevented = false;
    marker.handlers.get("click")({ preventDefault: () => (prevented = true) });
    assert.equal(prevented, false);
    assert.equal(tooltip.hidden, false);
});

test("actual Pro Flip Box runtime ignores nested interactive controls", () => {
    const root = rootBase();
    runtime.initProFlipBox(root);

    root.handlers.get("click")({ target: { closest: () => ({}) } });
    assert.equal(root.classList.contains("is-flipped"), false);

    root.handlers.get("click")({ target: { closest: () => null } });
    assert.equal(root.classList.contains("is-flipped"), true);
});

function invalidForm(validation) {
    const root = rootBase({ "data-pro-config": "{}" });
    root.dataset.validation = validation;
    root.dataset.errorMessage = "Please check the form fields.";
    const message = { classList: classList(), textContent: "" };
    const invalid = {
        focusCalls: 0,
        focus() {
            this.focusCalls++;
        },
    };
    root.querySelectorAll = () => [];
    root.querySelector = (selector) =>
        selector === "[data-pro-form-message]" ? message : invalid;
    root.checkValidity = () => false;
    root.reportCalls = 0;
    root.reportValidity = () => root.reportCalls++;
    return { invalid, message, root };
}

test("actual Pro Form runtime separates custom and browser validation", () => {
    const custom = invalidForm("custom");
    runtime.initProForm(custom.root);
    custom.root.handlers.get("submit")({ preventDefault() {} });
    assert.equal(custom.root.reportCalls, 0);
    assert.equal(custom.invalid.focusCalls, 1);
    assert.equal(custom.message.classList.contains("is-error"), true);

    const browser = invalidForm("browser");
    runtime.initProForm(browser.root);
    browser.root.handlers.get("submit")({ preventDefault() {} });
    assert.equal(browser.root.reportCalls, 1);
    assert.equal(browser.invalid.focusCalls, 0);
});

test("actual Pro Form runtime waits for the server before success and redirect", async () => {
    const message = { classList: classList(), textContent: "" };
    const submit = { disabled: false };
    const root = rootBase({
        "data-pro-config": JSON.stringify({
            actions: ["message", "email", "redirect"],
            submitUrl: "/pagebuilder-elementor/v2.3/form/contact-page/form-contact",
        }),
    });
    root.dataset.validation = "browser";
    root.dataset.successMessage = "Fallback success";
    root.dataset.errorMessage = "Fallback error";
    root.querySelectorAll = () => [];
    root.querySelector = (selector) => {
        if (selector === "[data-pro-form-message]") return message;
        if (selector === 'button[type="submit"]') return submit;
        return null;
    };
    root.checkValidity = () => true;
    root.dispatchEvent = () => {};

    const originalFormData = globalThis.FormData;
    const originalFetch = globalThis.fetch;
    const originalLocation = window.location;
    const fetchCalls = [];
    const redirects = [];
    globalThis.FormData = class FormData {
        constructor(form) {
            this.form = form;
        }
    };
    globalThis.fetch = async (url, options) => {
        fetchCalls.push({ url, options });
        return {
            ok: true,
            async json() {
                return { success: true, message: "Server success", redirect: "/thank-you" };
            },
        };
    };
    window.location = { assign: (url) => redirects.push(url) };

    try {
        runtime.initProForm(root);
        await root.handlers.get("submit")({ preventDefault() {} });
    } finally {
        globalThis.FormData = originalFormData;
        globalThis.fetch = originalFetch;
        window.location = originalLocation;
    }

    assert.equal(fetchCalls.length, 1);
    assert.equal(fetchCalls[0].url, "/pagebuilder-elementor/v2.3/form/contact-page/form-contact");
    assert.equal(fetchCalls[0].options.method, "POST");
    assert.equal(message.textContent, "Server success");
    assert.deepEqual(redirects, ["/thank-you"]);
    assert.equal(submit.disabled, false);
});

test("actual Pro Form runtime keeps the form usable after a server error", async () => {
    const message = { classList: classList(), textContent: "" };
    const submit = { disabled: false };
    const root = rootBase({
        "data-pro-config": JSON.stringify({
            actions: ["email", "redirect"],
            submitUrl: "/pagebuilder-elementor/v2.3/form/contact-page/form-contact",
        }),
    });
    root.dataset.validation = "browser";
    root.dataset.errorMessage = "Fallback error";
    root.querySelectorAll = () => [];
    root.querySelector = (selector) => {
        if (selector === "[data-pro-form-message]") return message;
        if (selector === 'button[type="submit"]') return submit;
        return null;
    };
    root.checkValidity = () => true;
    root.dispatchEvent = () => {};

    const originalFormData = globalThis.FormData;
    const originalFetch = globalThis.fetch;
    const originalLocation = window.location;
    const redirects = [];
    globalThis.FormData = class FormData {};
    globalThis.fetch = async () => ({
        ok: false,
        async json() {
            return { message: "Server rejected the submission." };
        },
    });
    window.location = { assign: (url) => redirects.push(url) };

    try {
        runtime.initProForm(root);
        await root.handlers.get("submit")({ preventDefault() {} });
    } finally {
        globalThis.FormData = originalFormData;
        globalThis.fetch = originalFetch;
        window.location = originalLocation;
    }

    assert.equal(message.textContent, "Server rejected the submission.");
    assert.equal(message.classList.contains("is-error"), true);
    assert.deepEqual(redirects, []);
    assert.equal(submit.disabled, false);
    assert.equal(root.getAttribute("aria-busy"), "false");
});

test("Animated Headline initializer is part of the public runtime contract", () => {
    assert.equal(typeof runtime.initProAnimatedHeadline, "function");
});



test("v2.3 public runtime retains Slides and Countdown initializers", () => {
    assert.equal(typeof runtime.initProSlides, "function");
    assert.equal(typeof runtime.initProCountdown, "function");
});
