import assert from "node:assert/strict";
import { readFile } from "node:fs/promises";
import { dirname, join, resolve } from "node:path";
import test from "node:test";
import { fileURLToPath } from "node:url";
import vm from "node:vm";
import { compile } from "@vue/compiler-dom";
import { parse } from "@vue/compiler-sfc";
import { renderToString } from "@vue/server-renderer";
import * as Vue from "vue";

globalThis.window ??= globalThis;
globalThis.window.matchMedia ??= () => ({
    matches: false,
    addEventListener() {},
    removeEventListener() {},
});

const testDir = dirname(fileURLToPath(import.meta.url));
const rootDir = resolve(testDir, "..");

async function source(relativePath) {
    return readFile(join(rootDir, relativePath), "utf8");
}

async function loadSfc(relativePath) {
    const filename = join(rootDir, relativePath);
    const contents = await readFile(filename, "utf8");
    const { descriptor, errors } = parse(contents, { filename });
    assert.deepEqual(errors, []);
    const component = Function(descriptor.script.content.replace(/export\s+default/, "return"))();
    component.render = Function("Vue", compile(descriptor.template.content, { mode: "function", prefixIdentifiers: true }).code)(Vue);
    return component;
}

async function definition(type) {
    const context = { window: {} };
    vm.runInNewContext(await source("public/js/pagebuilder_elementor_v23/widget-registry.js"), context);
    vm.runInNewContext(await source(`public/js/pagebuilder_elementor_v23/widgets/pro/${type}/definition.js`), context);
    return context.window.PageBuilderElementorV23Widgets.get(type);
}

test("Canvas SSR render is warning-free", async () => {
    const component = await loadSfc("public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue");
    const app = Vue.createSSRApp(component, {
        item: {
            id: "carousel-warning-probe",
            type: "carousel",
            settings: {
                items: [{ id: "one" }, { id: "two" }],
                slidesToShow: 1,
                navigation: "arrows",
                pagination: "dots",
            },
        },
        responsiveDevice: "desktop",
    });
    const warnings = [];
    app.config.warnHandler = (message) => warnings.push(message);

    await renderToString(app);

    assert.deepEqual(warnings, []);
});

test("Carousel, Reviews, and Slides normalize the shared navigation contract", async () => {
    const carousel = await definition("carousel");
    const reviews = await definition("reviews");
    const slides = await definition("slides");

    const carouselNode = { settings: {
        navigation: "invalid",
        pagination: "invalid",
        previousArrowIcon: "broken",
        arrowPositionTablet: "invalid",
        slidesToScrollMobile: 99,
    } };
    carousel.normalize(carouselNode);
    assert.equal(carouselNode.settings.navigation, "both");
    assert.equal(carouselNode.settings.pagination, "dots");
    assert.equal(carouselNode.settings.previousArrowIcon, "fas fa-chevron-left");
    assert.equal(carouselNode.settings.arrowPositionTablet, "");
    assert.equal(carouselNode.settings.slidesToScrollMobile, 8);

    const reviewsNode = { settings: {
        arrows: false,
        pagination: "none",
        items: [{ id: "review-1", name: "Ada" }],
        nextArrowIcon: "broken",
    } };
    reviews.normalize(reviewsNode);
    assert.equal(reviewsNode.settings.navigation, "none");
    assert.equal(reviewsNode.settings.nextArrowIcon, "fas fa-chevron-right");
    assert.equal(reviewsNode.settings.items[0].imageAlt, "Ada");

    const slidesNode = { settings: {
        navigation: "dots",
        previousArrowIcon: "broken",
        dotsPositionMobile: "outside",
        arrowButtonSize: "44px",
    } };
    slides.normalize(slidesNode);
    assert.equal(slidesNode.settings.navigation, "dots");
    assert.equal(slidesNode.settings.previousArrowIcon, "fas fa-chevron-left");
    assert.equal(slidesNode.settings.dotsPositionMobile, "outside");
    assert.equal(slidesNode.settings.arrowButtonSize, "44px");
});

test("Canvas renders navigation visibility, pagination mode, and custom arrow icons", async () => {
    const component = await loadSfc("public/js/pagebuilder_elementor_v23/widgets/pro/shared/Canvas.vue");

    const arrowsOnly = await renderToString(Vue.createSSRApp(component, {
        item: {
            id: "carousel-arrows",
            type: "carousel",
            settings: {
                items: [
                    { id: "one", title: "One", description: "First" },
                    { id: "two", title: "Two", description: "Second" },
                ],
                slidesToShow: 1,
                slidesToScroll: 1,
                navigation: "arrows",
                pagination: "dots",
                previousArrowIcon: "fas fa-angle-left",
                nextArrowIcon: "fas fa-angle-right",
                arrowPosition: "outside",
                arrowButtonSize: "44px",
                arrowIconSize: "19px",
                arrowEdgeOffset: "12px",
            },
        },
        responsiveDevice: "desktop",
    }));
    assert.match(arrowsOnly, /Previous slide/);
    assert.match(arrowsOnly, /fa-angle-left/);
    assert.match(arrowsOnly, /fa-angle-right/);
    assert.match(arrowsOnly, /arrow-position-outside/);
    assert.match(arrowsOnly, /--carousel-arrow-button-size:44px/);
    assert.equal(arrowsOnly.includes("pb-pro-dots"), false);

    const fraction = await renderToString(Vue.createSSRApp(component, {
        item: {
            id: "carousel-fraction",
            type: "carousel",
            settings: {
                items: [{ id: "one" }, { id: "two" }],
                slidesToShow: 1,
                navigation: "dots",
                pagination: "fraction",
            },
        },
        responsiveDevice: "desktop",
    }));
    assert.equal(fraction.includes("Previous slide"), false);
    assert.match(fraction, /pb-pro-carousel__fraction/);
    assert.equal(fraction.includes("pb-pro-dots"), false);

    const reviewsNone = await renderToString(Vue.createSSRApp(component, {
        item: {
            id: "reviews-none",
            type: "reviews",
            settings: {
                items: [{ id: "one", name: "Ada", review: "Good" }, { id: "two", name: "Lin", review: "Great" }],
                slidesToShow: 1,
                navigation: "none",
                pagination: "dots",
            },
        },
        responsiveDevice: "desktop",
    }));
    assert.equal(reviewsNone.includes("Previous slide"), false);
    assert.equal(reviewsNone.includes("pb-pro-dots"), false);

    const slidesDotsOnly = await renderToString(Vue.createSSRApp(component, {
        item: {
            id: "slides-dots",
            type: "slides",
            settings: {
                slides: [{ id: "one", title: "One" }, { id: "two", title: "Two" }],
                navigation: "dots",
                previousArrowIcon: "fas fa-angle-left",
                nextArrowIcon: "fas fa-angle-right",
                arrowButtonSize: "42px",
                dotsPosition: "outside",
            },
        },
        responsiveDevice: "desktop",
    }));
    assert.equal(slidesDotsOnly.includes("Previous slide"), false);
    assert.match(slidesDotsOnly, /pb-pro-dots/);
    assert.match(slidesDotsOnly, /--slides-arrow-button-size:42px/);
    assert.match(slidesDotsOnly, /--slides-dot-offset:-24px/);
});

test("Settings and Blade renderer expose the same navigation controls", async () => {
    const settingsSource = await source("public/js/pagebuilder_elementor_v23/widgets/pro/shared/Settings.vue");
    const rendererSource = await source("resources/views/pagebuilder_elementor_v23/partials/render_pro_widget.blade.php");

    for (const type of ["carousel", "reviews", "slides"]) {
        assert.match(settingsSource, new RegExp(`type === '${type}'`));
    }
    for (const label of ["Navigation", "Previous Arrow Icon", "Next Arrow Icon", "Position", "Edge Offset", "Button Size", "Icon Size", "Hover Icon Color", "Hover Background", "Button Radius"]) {
        assert.match(settingsSource, new RegExp(label));
    }
    assert.match(settingsSource, /paginationPosition/);
    assert.match(rendererSource, /reviewsNavigation/);
    assert.match(rendererSource, /in_array\(\$reviewsNavigation,\['both','arrows'\],true\)/);
    assert.match(rendererSource, /in_array\(\$reviewsNavigation,\['both','dots'\],true\)/);
    assert.match(rendererSource, /Navigation parity: keep server-rendered Carousel, Reviews, and Slides aligned/);
});
