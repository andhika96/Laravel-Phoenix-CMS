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

function openImageLightbox(source, alt = '') {
		openMediaLightbox(source, 'image', alt, { className: 'pb-image-lightbox pb-image-carousel-lightbox' });
	}

function bindBasicGallery(root) {
		if (!root || boundBasicGalleryRoots.has(root)) return;
		boundBasicGalleryRoots.add(root);
		root.querySelectorAll('[data-basic-gallery-lightbox]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				const image = link.querySelector('img');
				openImageLightbox(image?.getAttribute('src') || link.getAttribute('href'), image?.getAttribute('alt') || '');
			});
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-basic-gallery]").forEach(bindBasicGallery);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["basic_gallery"] = Object.freeze({ init, bindBasicGallery });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
