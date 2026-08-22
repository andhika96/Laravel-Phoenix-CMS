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

function directSummary(details) {
		return details ? details.querySelector(':scope > .el-widget-accordion__summary') : null;
	}

function directContent(details) {
		return details ? details.querySelector(':scope > .el-widget-accordion__content-wrap') : null;
	}

function clearTransition(details) {
		const previous = transitionState.get(details);
		if (!previous) return;
		if (previous.frame) cancelAnimationFrame(previous.frame);
		if (previous.timer) window.clearTimeout(previous.timer);
		if (previous.content && previous.handler) previous.content.removeEventListener('transitionend', previous.handler);
		transitionState.delete(details);
	}

function finishState(details, expanded) {
		const content = directContent(details);
		const summary = directSummary(details);
		if (!content || !summary) return;
		clearTransition(details);
		details.open = expanded;
		details.classList.toggle('is-active', expanded);
		summary.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		content.style.height = expanded ? 'auto' : '0px';
		content.style.visibility = expanded ? '' : 'hidden';
	}

function animateState(details, expanded, duration) {
		const content = directContent(details);
		const summary = directSummary(details);
		if (!content || !summary) return;
		clearTransition(details);
		summary.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		details.classList.toggle('is-active', expanded);

		if (prefersReducedMotion() || duration <= 0) {
			finishState(details, expanded);
			return;
		}

		if (expanded) {
			details.open = true;
			content.style.visibility = '';
			content.style.height = '0px';
			void content.offsetHeight;
		} else {
			content.style.visibility = '';
			content.style.height = content.getBoundingClientRect().height + 'px';
			void content.offsetHeight;
		}

		const handler = function (event) {
			if (event.target !== content || event.propertyName !== 'height') return;
			finishState(details, expanded);
		};
		content.addEventListener('transitionend', handler);
		const frame = requestAnimationFrame(function () {
			content.style.height = expanded ? content.scrollHeight + 'px' : '0px';
		});
		const timer = window.setTimeout(function () {
			finishState(details, expanded);
		}, duration + 80);
		transitionState.set(details, { content, handler, frame, timer });
	}

function accordionItems(root) {
		return Array.from(root.querySelectorAll(':scope > .el-widget-accordion__item'));
	}

function toggleItem(root, details) {
		const duration = Math.max(0, Math.min(5000, Number(root.dataset.animationDuration) || 400));
		const willExpand = !details.open || directSummary(details)?.getAttribute('aria-expanded') !== 'true';
		if (willExpand && root.dataset.maxExpanded !== 'multiple') {
			accordionItems(root).forEach(function (other) {
				if (other !== details && other.open) animateState(other, false, duration);
			});
		}
		animateState(details, willExpand, duration);
	}

function focusSummaryAt(root, current, offset) {
		const summaries = accordionItems(root).map(directSummary).filter(Boolean);
		const index = summaries.indexOf(current);
		if (index < 0 || !summaries.length) return;
		const target = summaries[(index + offset + summaries.length) % summaries.length];
		target.focus();
	}

function onSummaryKeydown(root, summary, event) {
		switch (event.key) {
			case 'ArrowDown':
				event.preventDefault();
				focusSummaryAt(root, summary, 1);
				break;
			case 'ArrowUp':
				event.preventDefault();
				focusSummaryAt(root, summary, -1);
				break;
			case 'Home': {
				event.preventDefault();
				const first = accordionItems(root).map(directSummary).find(Boolean);
				if (first) first.focus();
				break;
			}
			case 'End': {
				event.preventDefault();
				const summaries = accordionItems(root).map(directSummary).filter(Boolean);
				if (summaries.length) summaries[summaries.length - 1].focus();
				break;
			}
		}
	}

function bindAccordion(root) {
		if (!root || boundRoots.has(root)) return;
		boundRoots.add(root);
		accordionItems(root).forEach(function (details) {
			const summary = directSummary(details);
			const content = directContent(details);
			if (!summary || !content) return;
			finishState(details, details.open);
			summary.addEventListener('click', function (event) {
				event.preventDefault();
				toggleItem(root, details);
			});
			summary.addEventListener('keydown', function (event) {
				onSummaryKeydown(root, summary, event);
			});
		});
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-accordion-root]").forEach(bindAccordion);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["accordion"] = Object.freeze({ init, bindAccordion });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
