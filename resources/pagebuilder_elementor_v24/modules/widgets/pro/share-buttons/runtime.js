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

function initProShareButtons(root) {
		if (!markProReady(root, 'share-buttons')) return;
		const status = root.querySelector(':scope [data-share-status]');
		function setStatus(message) {
			if (status) status.textContent = message;
			if (message) window.setTimeout(function () {
				if (status) status.textContent = '';
			}, 1800);
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
		root.querySelectorAll(':scope [data-share-action]').forEach(function (button) {
			const action = button.getAttribute('data-share-action');
			if (!['copy', 'print'].includes(action)) return;
			button.addEventListener('click', async function (event) {
				event.preventDefault();
				if (action === 'print') {
					if (typeof window.print === 'function') window.print();
					return;
				}
				const value = String(button.getAttribute('data-share-url') || '');
				try {
					if (navigator.clipboard?.writeText) await navigator.clipboard.writeText(value);
					else if (!fallbackCopy(value)) throw new Error('Clipboard unavailable');
					setStatus('Copied');
				} catch (_) {
					setStatus('Copy failed');
				}
			});
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-share-buttons]").forEach(initProShareButtons);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["share_buttons"] = Object.freeze({ init, initProShareButtons });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
