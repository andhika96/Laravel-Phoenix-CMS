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

function initProCountdown(root) {
		if (!markProReady(root, 'countdown')) return;
		const config = parseProConfig(root);
		const target = config.type === 'evergreen' ? Date.now() + Math.max(0, Number(config.duration) || 0) * 1000 : Date.parse(config.dueDate || '');
		let timer = 0;
		function render() {
			let remaining = Number.isFinite(target) ? Math.max(0, Math.floor((target - Date.now()) / 1000)) : 0;
			const values = { days: Math.floor(remaining / 86400) };
			remaining %= 86400; values.hours = Math.floor(remaining / 3600); remaining %= 3600; values.minutes = Math.floor(remaining / 60); values.seconds = remaining % 60;
			Object.entries(values).forEach(function ([key, value]) { const node = root.querySelector('[data-countdown-' + key + ']'); if (node) node.textContent = String(value).padStart(2, '0'); });
			if (Object.values(values).some(Boolean)) return;
			if (timer) window.clearInterval(timer); timer = 0;
			if (config.action === 'hide') root.hidden = true;
			if (config.action === 'message') { const message = root.querySelector('[data-countdown-message]'); if (message) message.hidden = false; }
			if (config.action === 'redirect' && /^(?:https?:\/\/|\/|#)/i.test(String(config.redirect || ''))) window.location.assign(config.redirect);
		}
		render(); if (!root.hidden) timer = window.setInterval(render, 1000);
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-pro-countdown]").forEach(initProCountdown);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["countdown"] = Object.freeze({ init, initProCountdown });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
