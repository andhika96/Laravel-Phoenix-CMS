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

function ownedTabElements(root, selector) {
		return Array.from(root.querySelectorAll(selector)).filter(function (element) {
			const owner = element.closest?.('[data-tabs-widget]');
			return !owner || owner === root;
		});
	}

function initTabs(root) {
		if (!root || boundTabsRoots.has(root)) return;
		boundTabsRoots.add(root);
		const buttons = function () { return ownedTabElements(root, ':scope [data-tab-target]'); };
		const navigationTabs = function () { return ownedTabElements(root, ':scope [role="tab"][data-tab-target]'); };
		const panels = function () { return ownedTabElements(root, ':scope [data-tab-panel]'); };
		const activate = function (target) {
			const targetId = String(target || '');
			if (!targetId || !panels().some(function (panel) { return panel.getAttribute('data-tab-panel') === targetId; })) return;
			buttons().forEach(function (button) {
				const active = button.getAttribute('data-tab-target') === targetId;
				button.classList.toggle('is-active', active);
				if (button.getAttribute('role') === 'tab') {
					button.setAttribute('aria-selected', active ? 'true' : 'false');
					button.setAttribute('tabindex', active ? '0' : '-1');
				} else {
					button.setAttribute('aria-expanded', active ? 'true' : 'false');
				}
			});
			panels().forEach(function (panel) {
				const active = panel.getAttribute('data-tab-panel') === targetId;
				panel.classList.toggle('is-active', active);
				panel.hidden = !active;
			});
		};
		const initiallySelected = navigationTabs().find(function (button) { return button.getAttribute('aria-selected') === 'true'; }) || panels().find(function (panel) { return !panel.hidden; });
		activate(initiallySelected?.getAttribute('data-tab-target') || initiallySelected?.getAttribute('data-tab-panel'));
		root.addEventListener('click', function (event) {
			const button = event.target?.closest?.('[data-tab-target]');
			if (!button || !ownedTabElements(root, ':scope [data-tab-target]').includes(button)) return;
			activate(button.getAttribute('data-tab-target'));
		});
		root.addEventListener('keydown', function (event) {
			const button = event.target?.closest?.('[data-tab-target]');
			const tabs = navigationTabs();
			const currentIndex = tabs.indexOf(button);
			if (currentIndex < 0 || !tabs.length) return;
			let nextIndex = null;
			if (event.key === 'ArrowRight' || event.key === 'ArrowDown') nextIndex = (currentIndex + 1) % tabs.length;
			else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;
			else if (event.key === 'Home') nextIndex = 0;
			else if (event.key === 'End') nextIndex = tabs.length - 1;
			if (nextIndex === null) return;
			event.preventDefault();
			const next = tabs[nextIndex];
			activate(next.getAttribute('data-tab-target'));
			next.focus?.();
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-tabs-widget]").forEach(initTabs);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["tabs"] = Object.freeze({ init, initTabs });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
