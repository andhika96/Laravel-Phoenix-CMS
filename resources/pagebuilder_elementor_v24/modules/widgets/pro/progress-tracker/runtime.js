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

function initProProgressTracker(root) {
		if (!markProReady(root, 'progress-tracker')) return;
		const config = parseProConfig(root);
		const indicator = root.querySelector(':scope [data-progress-indicator]');
		const percentage = root.querySelector(':scope [data-progress-percentage]');
		const progressbar = root.querySelector('[role="progressbar"]');
		const circumference = 2 * Math.PI * 52;
		function targetElement() {
			if (config.relativeTo === 'selector') {
				try { return document.querySelector(String(config.selector || '')); } catch (_) { return null; }
			}
			if (config.relativeTo === 'post_content') return document.querySelector('.post-content, .entry-content, article, main') || document.documentElement;
			return document.documentElement;
		}
		function calculate() {
			const viewport = Math.max(1, window.innerHeight || document.documentElement.clientHeight || 1);
			const scrollTop = Math.max(0, window.scrollY || document.documentElement.scrollTop || 0);
			const target = targetElement();
			let value = 0;
			if (target && config.relativeTo === 'page') {
				const scrollHeight = Math.max(document.documentElement.scrollHeight, document.body?.scrollHeight || 0);
				const maximum = Math.max(0, scrollHeight - viewport);
				value = maximum ? (scrollTop / maximum) * 100 : 50;
			} else if (target) {
				const rect = target.getBoundingClientRect();
				const top = rect.top + scrollTop;
				const height = Math.max(1, rect.height || target.scrollHeight || 1);
				value = ((scrollTop - top + viewport) / (height + viewport)) * 100;
			}
			value = Math.max(0, Math.min(100, Math.round(value)));
			root.dataset.progressValue = String(value);
			if (indicator) {
				if (root.classList.contains('pb-pro-progress-tracker--circular')) {
					indicator.style.strokeDasharray = String(circumference);
					indicator.style.strokeDashoffset = String(circumference * (1 - value / 100));
				} else indicator.style.width = value + '%';
			}
			if (percentage) percentage.textContent = value + '%';
			if (progressbar) progressbar.setAttribute('aria-valuenow', String(value));
		}
		root.__pbProgressUpdate = calculate;
		window.addEventListener('scroll', calculate, { passive: true });
		window.addEventListener('resize', calculate, { passive: true });
		calculate();
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-progress-tracker]").forEach(initProProgressTracker);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["progress_tracker"] = Object.freeze({ init, initProProgressTracker });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
