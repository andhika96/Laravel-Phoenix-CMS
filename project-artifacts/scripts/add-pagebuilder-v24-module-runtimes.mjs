import fs from 'node:fs';
import path from 'node:path';

const projectRoot = path.resolve(import.meta.dirname, '..', '..');
const modulesRoot = path.join(projectRoot, 'resources', 'pagebuilder_elementor_v24', 'modules');
const runtimes = {
    accordion: ['widgets/general/accordion', '[data-accordion-root]', 'bindAccordion'],
    image_carousel: ['widgets/general/image-carousel', '[data-image-carousel]', 'bindImageCarousel'],
    basic_gallery: ['widgets/general/basic-gallery', '[data-basic-gallery]', 'bindBasicGallery'],
    image: ['widgets/basic/image', '[data-basic-image]', 'bindBasicImage'],
    tabs: ['widgets/general/tabs', '[data-tabs-widget]', 'initTabs'],
    slides: ['widgets/pro/slides', '[data-pro-slides]', 'initProSlides'],
    carousel: ['widgets/pro/carousel', '[data-pro-carousel]', 'initProCarousel'],
    reviews: ['widgets/pro/reviews', '[data-pro-carousel]', 'initProCarousel'],
    testimonial_carousel: ['widgets/pro/testimonial-carousel', '[data-pro-carousel]', 'initProCarousel'],
    media_carousel: ['widgets/pro/media-carousel', '[data-pro-carousel]', 'initProCarousel'],
    countdown: ['widgets/pro/countdown', '[data-pro-countdown]', 'initProCountdown'],
    progress_tracker: ['widgets/pro/progress-tracker', '[data-progress-tracker]', 'initProProgressTracker'],
    video_playlist: ['widgets/pro/video-playlist', '[data-video-playlist]', 'initProVideoPlaylist'],
    hotspot: ['widgets/pro/hotspot', '[data-pro-hotspot]', 'initProHotspot'],
    flip_box: ['widgets/pro/flip-box', '[data-pro-flip-box]', 'initProFlipBox'],
    form: ['widgets/pro/form', '[data-pro-form]', 'initProForm'],
    animated_headline: ['widgets/pro/animated-headline', '[data-pro-headline]', 'initProAnimatedHeadline'],
    code_highlight: ['widgets/pro/code-highlight', '[data-code-highlight]', 'initProCodeHighlight'],
    share_buttons: ['widgets/pro/share-buttons', '[data-share-buttons]', 'initProShareButtons'],
    hero_banner: ['widgets/pro/hero-banner', '[data-hero-banner]', 'initHeroBanner'],
    product_color_selector: ['widgets/pro/product-color-selector', '[data-product-color-selector]', 'initProductColorSelector'],
    hero_slider: ['widgets/pro/hero-slider', '[data-hero-slider]', 'initHeroSlider'],
};

for (const [type, [relativeDirectory, selector, initializer]] of Object.entries(runtimes)) {
    const directory = path.join(modulesRoot, relativeDirectory);
    const manifestPath = path.join(directory, 'module.json');
    const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'));
    if (manifest.type !== type) throw new Error(`Manifest type mismatch: ${type}`);

    manifest.assets.runtime = 'runtime.js';
    fs.writeFileSync(manifestPath, `${JSON.stringify(manifest, null, 2)}\n`);

    const runtime = `(function (runtime) {
    'use strict';
    if (!runtime || typeof runtime.${initializer} !== 'function') return;
    function init(scope) {
        const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
        root.querySelectorAll('${selector}').forEach(runtime.${initializer});
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
    } else {
        init(document);
    }
})(window.PageBuilderElementorV24Runtime);
`;
    fs.writeFileSync(path.join(directory, 'runtime.js'), runtime);
    process.stdout.write(`${type}\n`);
}
