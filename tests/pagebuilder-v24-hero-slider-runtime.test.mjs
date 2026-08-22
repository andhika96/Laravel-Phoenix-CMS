import assert from 'node:assert/strict';
import test from 'node:test';

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
    innerWidth: 1280,
};
globalThis.document = {
    addEventListener() {},
    documentElement: { clientWidth: 1280 },
    readyState: 'loading',
    querySelector: () => null,
};
globalThis.getComputedStyle = () => ({ getPropertyValue: () => '' });
globalThis.CustomEvent = class CustomEvent {
    constructor(type, options = {}) {
        this.type = type;
        this.detail = options.detail;
    }
};

await import('../public/js/pagebuilder_elementor_v24/frontend-runtime.js');
await import('../resources/pagebuilder_elementor_v24/modules/widgets/pro/hero-slider/runtime.js');
const runtime = window.PageBuilderElementorV24ModuleRuntimes.hero_slider;

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
        dispatch(type, event = {}) {
            handlers.get(type)?.(event);
        },
    };
}

function node(attributes = {}) {
    const target = eventTarget();
    const attrs = new Map(Object.entries(attributes));
    return {
        ...target,
        dataset: {},
        hidden: false,
        classList: classList(),
        style: { setProperty(name, value) { this[name] = value; } },
        getAttribute: (name) => attrs.get(name) || '',
        setAttribute: (name, value) => attrs.set(name, String(value)),
        querySelector: () => null,
        querySelectorAll: () => [],
    };
}

function sliderRoot(config, slides) {
    const root = node({
        'data-hero-slider-config': JSON.stringify(config),
    });
    const track = node();
    const previous = node();
    const next = node();
    const progress = node();
    const pagination = node();
    const dots = slides.map((_, index) => {
        const dot = node();
        dot.dataset.heroIndex = String(index);
        return dot;
    });
    root.querySelector = (selector) => {
        if (selector.includes('data-hero-slider-track')) return track;
        if (selector.includes('data-hero-prev')) return previous;
        if (selector.includes('data-hero-next')) return next;
        if (selector.includes('data-hero-slider-progress')) return progress;
        if (selector.includes('data-hero-pagination')) return pagination;
        return null;
    };
    root.querySelectorAll = (selector) => {
        if (selector.includes('data-hero-slide')) return slides;
        if (selector.includes('data-hero-index')) return dots;
        return [];
    };
    return { root, track, previous, next, progress, pagination, dots };
}

test('Hero Slider changes to vertical direction and responds to vertical keyboard controls', () => {
    const slides = [node(), node(), node()];
    const fixture = sliderRoot({
        direction: 'vertical',
        autoplay: false,
        loop: false,
        transition: 'slide',
        transitionSpeed: 200,
        pagination: true,
        arrows: true,
    }, slides);

    runtime.initHeroSlider(fixture.root);
    assert.equal(fixture.root.dataset.direction, 'vertical');
    assert.equal(fixture.track.style.transform, 'translate3d(0,-0%,0)');
    fixture.root.dispatch('keydown', { key: 'ArrowDown', preventDefault() {} });
    assert.equal(fixture.track.style.transform, 'translate3d(0,-100%,0)');
    fixture.root.dispatch('keydown', { key: 'ArrowUp', preventDefault() {} });
    assert.equal(fixture.track.style.transform, 'translate3d(0,-0%,0)');
});

test('Hero Slider honors perMove and rewind for manual navigation', () => {
    const slides = [node(), node(), node(), node()];
    const fixture = sliderRoot({
        direction: 'horizontal',
        autoplay: false,
        loop: false,
        rewind: true,
        perMove: 2,
        transition: 'slide',
        transitionSpeed: 200,
        pagination: true,
        arrows: true,
    }, slides);

    runtime.initHeroSlider(fixture.root);
    fixture.next.handlers.get('click')();
    assert.equal(fixture.track.style.transform, 'translate3d(-200%,0,0)');
    fixture.next.handlers.get('click')();
    assert.equal(fixture.track.style.transform, 'translate3d(-0%,0,0)');
    fixture.previous.handlers.get('click')();
    assert.equal(fixture.track.style.transform, 'translate3d(-300%,0,0)');
});

test('Hero Slider applies direction-specific pagination positions and responsive overrides', () => {
    const slides = [node(), node()];
    const fixture = sliderRoot({
        direction: 'horizontal',
        directionTablet: 'vertical',
        paginationPlacementModeHorizontal: 'basic',
        paginationAlignmentHorizontal: 'center',
        paginationPlacementModeVertical: 'basic',
        paginationAlignmentVertical: 'center',
        paginationPlacementModeVerticalTablet: 'custom',
        paginationPositionHorizontal: 'bottom-center',
        paginationPositionHorizontalTablet: 'top-left',
        paginationPositionVertical: 'center-right',
        paginationPositionVerticalTablet: 'center-left',
        paginationOffsetXHorizontal: '4px',
        paginationOffsetYHorizontal: '-6px',
        paginationOffsetXVertical: '10px',
        paginationOffsetYVertical: '12px',
        paginationOffsetXVerticalTablet: '-14px',
        paginationOffsetYVerticalTablet: '18px',
        autoplay: false,
        loop: false,
        pagination: true,
    }, slides);

    runtime.initHeroSlider(fixture.root);
    assert.equal(fixture.pagination.getAttribute('data-orientation'), 'horizontal');
    assert.equal(fixture.pagination.getAttribute('data-position'), 'bottom-center');
    assert.equal(fixture.pagination.style['--hero-slider-pagination-offset-x'], '4px');
    assert.equal(fixture.pagination.style['--hero-slider-pagination-offset-y'], '-6px');

    window.innerWidth = 800;
    windowListeners.get('resize')?.();
    assert.equal(fixture.pagination.getAttribute('data-orientation'), 'vertical');
    assert.equal(fixture.pagination.getAttribute('data-position'), 'center-left');
    assert.equal(fixture.pagination.style['--hero-slider-pagination-offset-x'], '-14px');
    assert.equal(fixture.pagination.style['--hero-slider-pagination-offset-y'], '18px');
    window.innerWidth = 1280;
});

test('Hero Slider maps basic pagination alignment to the direction edge', () => {
    const horizontalSlides = [node(), node()];
    const horizontal = sliderRoot({
        direction: 'horizontal',
        paginationPlacementModeHorizontal: 'basic',
        paginationAlignmentHorizontal: 'right',
        autoplay: false,
        pagination: true,
    }, horizontalSlides);
    runtime.initHeroSlider(horizontal.root);
    assert.equal(horizontal.pagination.getAttribute('data-position'), 'bottom-right');

    const verticalSlides = [node(), node()];
    const vertical = sliderRoot({
        direction: 'vertical',
        paginationPlacementModeVertical: 'basic',
        paginationAlignmentVertical: 'bottom',
        autoplay: false,
        pagination: true,
    }, verticalSlides);
    runtime.initHeroSlider(vertical.root);
    assert.equal(vertical.pagination.getAttribute('data-position'), 'bottom-right');
});

test('Hero Slider lets a Natural Image slide use its intrinsic ratio below the configured minimum height', () => {
    const image = node();
    Object.assign(image, { tagName: 'IMG', naturalWidth: 1200, naturalHeight: 500 });
    const slide = node({ 'data-hero-image-layout': 'natural' });
    slide.querySelector = (selector) => selector.includes('data-hero-video') ? null : (selector.includes('img,video,iframe') ? image : null);
    const fixture = sliderRoot({
        direction: 'horizontal',
        autoplay: false,
        heightMode: 'adaptive',
        minHeight: '420px',
        slides: [{ imageLayout: 'natural', videoAspectRatio: '16/9' }],
    }, [slide]);
    fixture.root.clientWidth = 600;
    fixture.root.getBoundingClientRect = () => ({ width: 600 });

    runtime.initHeroSlider(fixture.root);

    assert.equal(fixture.root.style.height, '250px');
    assert.equal(fixture.root.style.minHeight, '0px');
});

test('Hero Slider pauses inactive native video and resumes the preserved currentTime', async () => {
    const firstVideo = node();
    const secondVideo = node();
    Object.assign(firstVideo, {
        tagName: 'VIDEO',
        currentTime: 12,
        paused: false,
        pauseCalls: 0,
        play() { this.paused = false; return Promise.resolve(); },
        pause() { this.pauseCalls++; this.paused = true; },
        load() {},
    });
    Object.assign(secondVideo, {
        tagName: 'VIDEO',
        currentTime: 0,
        paused: true,
        play() { this.paused = false; return Promise.resolve(); },
        pause() { this.paused = true; },
        load() {},
    });
    const slides = [node(), node()];
    slides[0].querySelector = (selector) => selector.includes('data-hero-video') ? firstVideo : null;
    slides[1].querySelector = (selector) => selector.includes('data-hero-video') ? secondVideo : null;
    const fixture = sliderRoot({
        direction: 'horizontal',
        autoplay: false,
        videoAutoplay: true,
        videoDurationMode: 'duration',
        videoMutedAutoplay: true,
        videoResume: true,
        loop: false,
    }, slides);
    runtime.initHeroSlider(fixture.root);

    fixture.next.handlers.get('click')();
    assert.equal(firstVideo.pauseCalls, 1);
    assert.equal(fixture.track.style.transform, 'translate3d(-100%,0,0)');
    fixture.previous.handlers.get('click')();
    assert.equal(firstVideo.currentTime, 12);
    await Promise.resolve();
});
