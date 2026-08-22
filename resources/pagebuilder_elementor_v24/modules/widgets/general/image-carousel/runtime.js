(function (core) {
	'use strict';
	if (!core) return;

	const boundRoots = new WeakSet();
	const boundCarouselRoots = new WeakSet();
	const boundBasicGalleryRoots = new WeakSet();
	const boundBasicImageRoots = new WeakSet();
	const boundTabsRoots = new WeakSet();
	const boundProductColorSelectorRoots = new WeakSet();
	const heroSliderScriptPromises = new Map();
	const transitionState = new WeakMap();
	const prefersReducedMotion = () => core.prefersReducedMotion();
	const openMediaLightbox = (mediaSource, mediaType, alt, settings = {}) => core.openMediaLightbox(
		mediaSource,
		mediaType,
		alt,
		{ className: 'pb-pro-media-lightbox', ...settings },
	);

function currentDevice() {
		const width = window.innerWidth || document.documentElement.clientWidth || 1280;
		if (width <= 767) return 'mobile';
		if (width <= 1024) return 'tablet';
		return 'desktop';
	}

function openImageLightbox(source, alt = '') {
		openMediaLightbox(source, 'image', alt, { className: 'pb-image-lightbox pb-image-carousel-lightbox' });
	}

function bindImageCarousel(root) {
		if (!root || boundCarouselRoots.has(root)) return;
		boundCarouselRoots.add(root);
		const track = root.querySelector(':scope .pb-image-carousel__track');
		const slides = Array.from(root.querySelectorAll(':scope .pb-image-carousel__slide'));
		if (!track || !slides.length) return;
		let config = {};
		try { config = JSON.parse(root.getAttribute('data-carousel-config') || '{}'); } catch (_) { config = {}; }
		const previous = root.querySelector(':scope .pb-image-carousel__arrow--previous');
		const next = root.querySelector(':scope .pb-image-carousel__arrow--next');
		const pagination = root.querySelector(':scope .pb-image-carousel__pagination');
		const dots = Array.from(root.querySelectorAll(':scope .pb-image-carousel__dot'));
		let activeIndex = 0;
		let autoplayTimer = 0;
		let hovered = false;
		let interactionPaused = false;

		function responsiveConfig(base, fallback) {
			const device = currentDevice();
			const value = device === 'mobile' ? config[base + 'Mobile'] : (device === 'tablet' ? config[base + 'Tablet'] : config[base]);
			const number = Number(value);
			return Number.isFinite(number) && number > 0 ? Math.round(number) : fallback;
		}
		function visibleSlides() { return Math.max(1, Math.min(slides.length, responsiveConfig('slidesToShow', Math.min(3, slides.length)))); }
		function slidesToScroll() { return Math.max(1, Math.min(visibleSlides(), responsiveConfig('slidesToScroll', 1))); }
		function maxIndex() { return Math.max(0, slides.length - visibleSlides()); }
		function pageCount() { return Math.max(1, Math.ceil(maxIndex() / slidesToScroll()) + 1); }
		function normalizedIndex(index) {
			const maximum = maxIndex();
			if (config.infiniteLoop && maximum > 0) {
				if (index > maximum) return 0;
				if (index < 0) return maximum;
			}
			return Math.max(0, Math.min(maximum, index));
		}
		function updateImageCarousel(index) {
			activeIndex = normalizedIndex(Number(index) || 0);
			const visible = visibleSlides();
			root.style.setProperty('--pb-carousel-visible', String(visible));
			track.style.transform = `translate3d(-${activeIndex * (100 / visible)}%,0,0)`;
			const hasOverflow = maxIndex() > 0;
			const pages = pageCount();
			const dotIndex = Math.min(pages - 1, Math.round(activeIndex / slidesToScroll()));
			dots.forEach(function (dot, index) {
				const active = index === dotIndex;
				dot.hidden = index >= pages;
				dot.classList.toggle('is-active', active);
				dot.setAttribute('aria-selected', active ? 'true' : 'false');
			});
			if (previous) {
				previous.hidden = !hasOverflow;
				previous.disabled = !config.infiniteLoop && activeIndex === 0;
			}
			if (next) {
				next.hidden = !hasOverflow;
				next.disabled = !config.infiniteLoop && activeIndex === maxIndex();
			}
			if (pagination) pagination.hidden = !hasOverflow;
		}
		function stopAutoplay() {
			if (!autoplayTimer) return;
			window.clearInterval(autoplayTimer);
			autoplayTimer = 0;
		}
		function startAutoplay() {
			stopAutoplay();
			if (!config.autoplay || maxIndex() <= 0 || prefersReducedMotion() || (config.pauseOnHover && hovered) || interactionPaused) return;
			autoplayTimer = window.setInterval(function () {
				updateImageCarousel(activeIndex + (config.direction === 'right' ? -slidesToScroll() : slidesToScroll()));
			}, Math.max(100, Number(config.autoplaySpeed) || 5000));
		}
		function interact(index) {
			updateImageCarousel(index);
			if (config.pauseOnInteraction) {
				interactionPaused = true;
				stopAutoplay();
			} else startAutoplay();
		}
		if (previous) previous.addEventListener('click', function () { interact(activeIndex - slidesToScroll()); });
		if (next) next.addEventListener('click', function () { interact(activeIndex + slidesToScroll()); });
		dots.forEach(function (dot, index) { dot.addEventListener('click', function () { interact(index * slidesToScroll()); }); });
		root.addEventListener('mouseenter', function () { hovered = true; startAutoplay(); });
		root.addEventListener('mouseleave', function () { hovered = false; startAutoplay(); });
		root.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') { event.preventDefault(); interact(activeIndex - slidesToScroll()); }
			if (event.key === 'ArrowRight') { event.preventDefault(); interact(activeIndex + slidesToScroll()); }
		});
		root.querySelectorAll('[data-carousel-lightbox]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				const image = link.querySelector('img');
				openImageLightbox(image?.getAttribute('src') || link.getAttribute('href'), image?.getAttribute('alt') || '');
			});
		});
		root.setAttribute('tabindex', root.getAttribute('tabindex') || '0');
		window.addEventListener('resize', function () { updateImageCarousel(activeIndex); startAutoplay(); }, { passive: true });
		updateImageCarousel(0);
		startAutoplay();
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-image-carousel]").forEach(bindImageCarousel);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["image_carousel"] = Object.freeze({ init, bindImageCarousel });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
