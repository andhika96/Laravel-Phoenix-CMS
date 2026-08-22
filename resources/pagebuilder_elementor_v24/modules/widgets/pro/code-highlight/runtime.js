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

function initProCodeHighlight(root) {
		if (!markProReady(root, 'code-highlight')) return;
		const button = root.querySelector(':scope [data-code-copy]');
		const source = root.querySelector(':scope [data-code-source]');
		const status = root.querySelector(':scope [data-code-copy-status]');
		if (!button || !source) return;
		let resetTimer = 0;
		function setStatus(message) {
			if (status) status.textContent = message;
			button.setAttribute('aria-label', message === 'Copied' ? 'Code copied' : message === 'Copy failed' ? 'Copy code failed' : 'Copy code');
			const label = button.querySelector('span');
			if (label) label.textContent = message || 'Copy';
			window.clearTimeout(resetTimer);
			if (message) resetTimer = window.setTimeout(function () { setStatus(''); }, 1800);
		}
		function fallbackCopy(value) {
			if (!document.body) return false;
			const textarea = document.createElement('textarea');
			textarea.value = value;
			textarea.setAttribute('readonly', '');
			textarea.style.position = 'fixed';
			textarea.style.opacity = '0';
			document.body.appendChild(textarea);
			textarea.select();
			let copied = false;
			try { copied = Boolean(document.execCommand('copy')); } catch (_) { copied = false; }
			textarea.remove();
			return copied;
		}
		button.addEventListener('click', async function () {
			const value = String(source.value || source.textContent || '');
			try {
				if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(value);
				else if (!fallbackCopy(value)) throw new Error('Clipboard unavailable');
				setStatus('Copied');
			} catch (_) {
				setStatus('Copy failed');
			}
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-code-highlight]").forEach(initProCodeHighlight);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["code_highlight"] = Object.freeze({ init, initProCodeHighlight });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
