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

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function initHeroBanner(root) {
		if (!markProReady(root, 'hero-banner')) return;
		root.querySelectorAll(':scope [data-hero-media]').forEach(function (trigger) {
			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				const styles = getComputedStyle(root);
				openMediaLightbox(trigger.dataset.mediaSrc, trigger.dataset.mediaType, trigger.dataset.mediaAlt || '', {
					background: styles.getPropertyValue('--hero-modal-background').trim() || 'rgba(0,0,0,.92)',
					uiColor: styles.getPropertyValue('--hero-modal-ui').trim() || '#fff',
					uiHoverColor: styles.getPropertyValue('--hero-modal-ui-hover').trim() || '#6979f8',
					videoWidth: styles.getPropertyValue('--hero-modal-video-width').trim() || '75%',
				});
			});
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-hero-banner]").forEach(initHeroBanner);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["hero_banner"] = Object.freeze({ init, initHeroBanner });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
