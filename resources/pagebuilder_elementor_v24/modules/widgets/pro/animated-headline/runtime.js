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

function parseProConfig(root) {
		try { return JSON.parse(root.getAttribute('data-pro-config') || '{}'); }
		catch (_) { return {}; }
	}

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function initProAnimatedHeadline(root) {
		if (!markProReady(root, 'animated-headline')) return;
		const config = parseProConfig(root); const target = root.querySelector('.pb-pro-headline__animated'); const words = Array.isArray(config.words) ? config.words.filter(Boolean) : [];
		if (!target) return;
		target.style.setProperty('--headline-duration', Math.max(100, Number(config.duration) || 1200) + 'ms');
		if (words.length < 2 || prefersReducedMotion()) return;
		let index = 0;
		function rotate() {
			if (!config.loop && index >= words.length - 1) return;
			index = (index + 1) % words.length;
			target.classList.remove('is-changing');
			target.textContent = words[index];
			void target.offsetWidth;
			target.classList.add('is-changing');
			window.setTimeout(rotate, Math.max(400, Number(config.delay) || 2500));
		}
		window.setTimeout(rotate, Math.max(400, Number(config.delay) || 2500));
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-pro-headline]").forEach(initProAnimatedHeadline);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["animated_headline"] = Object.freeze({ init, initProAnimatedHeadline });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
