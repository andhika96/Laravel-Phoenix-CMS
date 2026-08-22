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

function initProHotspot(root) {
		if (!markProReady(root, 'hotspot')) return;
		const trigger = root.dataset.trigger || 'hover';
		root.querySelectorAll(':scope .pb-pro-hotspot__marker').forEach(function (marker) {
			const tooltip = marker.querySelector(':scope .pb-pro-hotspot__tooltip');
			if (!tooltip || trigger === 'none') return;
			const show = function () { tooltip.hidden = false; marker.setAttribute('aria-expanded', 'true'); };
			const hide = function () { tooltip.hidden = true; marker.setAttribute('aria-expanded', 'false'); };
			if (trigger === 'click') marker.addEventListener('click', function (event) {
				const linked = typeof marker.matches === 'function' && marker.matches('a[href]');
				if (tooltip.hidden) { if (linked) event.preventDefault(); show(); return; }
				if (!linked) hide();
			});
			else { marker.addEventListener('mouseenter', show); marker.addEventListener('mouseleave', hide); marker.addEventListener('focus', show); marker.addEventListener('blur', hide); }
			marker.addEventListener('keydown', function (event) { if (event.key === 'Escape') hide(); });
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-pro-hotspot]").forEach(initProHotspot);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["hotspot"] = Object.freeze({ init, initProHotspot });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
