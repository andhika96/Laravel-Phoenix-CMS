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

function parseProductColorSelectorConfig(root) {
		try { return JSON.parse(root.getAttribute('data-product-color-config') || '{}'); }
		catch (_) { return {}; }
	}

function initProductColorSelector(root) {
		if (!root || root.getAttribute('data-editor-preview') === 'true' || boundProductColorSelectorRoots.has(root)) return;
		boundProductColorSelectorRoots.add(root);
		const config = parseProductColorSelectorConfig(root);
		const tabs = Array.from(root.querySelectorAll(':scope [role="tab"][data-product-color-id]'));
		const panels = Array.from(root.querySelectorAll(':scope [data-product-color-panel][data-product-color-id]'));
		const list = root.querySelector(':scope [data-product-color-list]') || root.querySelector(':scope [role="tablist"]');
		if (!tabs.length) return;

		const allowedPositions = ['top', 'right', 'bottom', 'left'];
		const allowedObjectPositions = ['left top', 'left center', 'left bottom', 'center top', 'center center', 'center bottom', 'right top', 'right center', 'right bottom'];
		const allowedFits = ['contain', 'cover', 'fill'];
		const tabById = new Map(tabs.map((tab) => [String(tab.getAttribute('data-product-color-id') || ''), tab]));
		let activeId = '';

		function device() {
			const width = Number(window.innerWidth || document.documentElement?.clientWidth || 0);
			return width <= 767 ? 'Mobile' : (width <= 1024 ? 'Tablet' : '');
		}
		function responsiveValue(base, fallback = '') {
			const suffix = device();
			const keys = suffix === 'Mobile' ? [base + 'Mobile', base + 'Tablet', base] : (suffix === 'Tablet' ? [base + 'Tablet', base] : [base]);
			for (const key of keys) {
				if (config[key] !== '' && config[key] !== null && config[key] !== undefined) return config[key];
			}
			return fallback;
		}
		function safePosition(value) { return allowedPositions.includes(String(value)) ? String(value) : 'bottom'; }
		function safeRatio(value) {
			const raw = String(value || '').trim();
			return /^\d+(?:\.\d+)?\s*\/\s*\d+(?:\.\d+)?$/.test(raw) ? raw.replace(/\s*\/\s*/, ' / ') : '16 / 9';
		}
		function safeObjectPosition(value) { return allowedObjectPositions.includes(String(value)) ? String(value) : 'center center'; }
		function applyResponsive() {
			const position = safePosition(responsiveValue('listPosition', 'bottom'));
			const vertical = position === 'left' || position === 'right';
			const alignment = String(responsiveValue('listAlignment', 'auto'));
			const alignmentAxis = vertical ? ['top', 'center', 'bottom'] : ['left', 'center', 'right'];
			const effectiveAlignment = alignment === 'auto' || !alignmentAxis.includes(alignment) ? 'center' : alignment;
			const justify = effectiveAlignment === 'center' ? 'center' : (effectiveAlignment === 'right' || effectiveAlignment === 'bottom' ? 'flex-end' : 'flex-start');
			root.setAttribute('data-position', position);
			root.setAttribute('data-orientation', vertical ? 'vertical' : 'horizontal');
			root.classList?.remove?.('is-position-top', 'is-position-right', 'is-position-bottom', 'is-position-left', 'is-horizontal', 'is-vertical');
			root.classList?.add?.('is-position-' + position, vertical ? 'is-vertical' : 'is-horizontal');
			list?.setAttribute('aria-orientation', vertical ? 'vertical' : 'horizontal');
			root.style?.setProperty?.('--pb-pcs-list-justify', justify);
			root.style?.setProperty?.('--pb-pcs-image-aspect-ratio', safeRatio(responsiveValue('imageAspectRatio', '16 / 9')));
			root.style?.setProperty?.('--pb-pcs-image-fit', allowedFits.includes(String(responsiveValue('imageFit', 'contain'))) ? String(responsiveValue('imageFit', 'contain')) : 'contain');
			root.style?.setProperty?.('--pb-pcs-image-position', safeObjectPosition(responsiveValue('imagePosition', 'center center')));
			const titleAlignment = ['left', 'center', 'right'].includes(String(responsiveValue('titleAlignment', 'left'))) ? String(responsiveValue('titleAlignment', 'left')) : 'left';
			root.style?.setProperty?.('--pb-pcs-header-align', titleAlignment);
			['titleFontSize', 'descriptionFontSize', 'itemNameFontSize', 'itemDescriptionFontSize'].forEach(function (key) {
				const value = String(responsiveValue(key, '') || '').trim();
				if (/^-?\d+(?:\.\d+)?(?:px|pt|%|em|rem|vw|vh)?$/i.test(value)) root.style?.setProperty?.('--pb-pcs-' + ({ titleFontSize: 'title-size', descriptionFontSize: 'description-size', itemNameFontSize: 'item-name-size', itemDescriptionFontSize: 'item-description-size' }[key]), value);
			});
		}
		function fallbackFor(panel, image) {
			if (!panel || !image) return;
			image.hidden = true;
			let empty = panel.querySelector(':scope [data-product-color-empty]');
			if (!empty && typeof document.createElement === 'function') {
				empty = document.createElement('div');
				empty.className = 'pb-product-color-selector__empty';
				empty.setAttribute('data-product-color-empty', 'true');
				empty.setAttribute('role', 'img');
				empty.setAttribute('aria-label', 'Product image unavailable');
				empty.innerHTML = '<i class="far fa-image" aria-hidden="true"></i><span>Choose an image</span>';
				panel.appendChild(empty);
			}
			if (empty) empty.hidden = false;
			panel.classList?.add?.('is-error');
		}
		function setActive(id, focus) {
			const candidate = String(id || '');
			activeId = tabById.has(candidate) ? candidate : String(config.defaultItemId || tabs[0].getAttribute('data-product-color-id') || '');
			if (!tabById.has(activeId)) activeId = String(tabs[0].getAttribute('data-product-color-id') || '');
			tabs.forEach(function (tab) {
				const selected = tab.getAttribute('data-product-color-id') === activeId;
				tab.classList?.toggle?.('is-active', selected);
				tab.setAttribute('aria-selected', selected ? 'true' : 'false');
				tab.setAttribute('tabindex', selected ? '0' : '-1');
			});
			panels.forEach(function (panel) {
				const selected = panel.getAttribute('data-product-color-id') === activeId;
				panel.classList?.toggle?.('is-active', selected);
				panel.hidden = !selected;
			});
			root.setAttribute('data-active-item', activeId);
			applyResponsive();
			if (focus) tabById.get(activeId)?.focus?.();
		}
		function moveFrom(tab, offset) {
			const index = tabs.indexOf(tab);
			if (index < 0) return;
			const next = Math.max(0, Math.min(tabs.length - 1, index + offset));
			if (next !== index) { setActive(tabs[next].getAttribute('data-product-color-id'), true); }
		}
		tabs.forEach(function (tab) {
			tab.addEventListener('click', function (event) { event.preventDefault(); setActive(tab.getAttribute('data-product-color-id'), false); });
			tab.addEventListener('keydown', function (event) {
				const vertical = root.getAttribute('data-orientation') === 'vertical';
				if (event.key === 'Home') { event.preventDefault(); setActive(tabs[0].getAttribute('data-product-color-id'), true); return; }
				if (event.key === 'End') { event.preventDefault(); setActive(tabs[tabs.length - 1].getAttribute('data-product-color-id'), true); return; }
				if ((!vertical && event.key === 'ArrowLeft') || (vertical && event.key === 'ArrowUp')) { event.preventDefault(); moveFrom(tab, -1); }
				if ((!vertical && event.key === 'ArrowRight') || (vertical && event.key === 'ArrowDown')) { event.preventDefault(); moveFrom(tab, 1); }
			});
		});
		panels.forEach(function (panel) { panel.querySelectorAll(':scope img').forEach(function (image) { image.addEventListener('error', function () { fallbackFor(panel, image); }); image.addEventListener('load', function () { image.hidden = false; panel.querySelector(':scope [data-product-color-empty]')?.setAttribute('hidden', 'hidden'); panel.classList?.remove?.('is-error'); }); }); });
		window.addEventListener('resize', applyResponsive, { passive: true });
		setActive(root.getAttribute('data-default-item') || config.defaultItemId || tabs[0].getAttribute('data-product-color-id'), false);
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-product-color-selector]").forEach(initProductColorSelector);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["product_color_selector"] = Object.freeze({ init, initProductColorSelector });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
