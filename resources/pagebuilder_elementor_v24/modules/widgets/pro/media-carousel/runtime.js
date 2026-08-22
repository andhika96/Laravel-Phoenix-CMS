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

function currentDevice() {
		const width = window.innerWidth || document.documentElement.clientWidth || 1280;
		if (width <= 767) return 'mobile';
		if (width <= 1024) return 'tablet';
		return 'desktop';
	}

function parseProConfig(root) {
		try { return JSON.parse(root.getAttribute('data-pro-config') || '{}'); }
		catch (_) { return {}; }
	}

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function bindProSlider(root, carousel) {
		if (!markProReady(root, carousel ? 'carousel' : 'slides')) return;
		const config = parseProConfig(root);
		const items = Array.from(root.querySelectorAll(carousel ? ':scope .pb-pro-carousel__slide' : ':scope .pb-pro-slides__slide'));
		const track = carousel ? root.querySelector(':scope .pb-pro-carousel__track') : null;
		const previous = root.querySelector(':scope [data-pro-prev]');
		const next = root.querySelector(':scope [data-pro-next]');
		const dots = Array.from(root.querySelectorAll(':scope [data-pro-index]'));
		const thumbnailViewport = root.querySelector(':scope .pb-pro-media-carousel__thumbnails');
		const thumbnailTrack = root.querySelector(':scope [data-media-thumbnail-track]');
		const thumbnails = Array.from(root.querySelectorAll(':scope .pb-pro-media-carousel__thumbnail'));
		const progress = root.querySelector(':scope [data-pro-progress]');
		const current = root.querySelector(':scope [data-pro-current]');
		const total = root.querySelector(':scope [data-pro-total]');
		let active = 0;
		let timer = 0;
		let interactionPaused = false;
		let hovered = false;
		function visible() {
			if (!carousel) return 1;
			if (config.skin === 'slideshow' || ['fade', 'cube'].includes(config.effect)) return 1;
			const device = currentDevice();
			const key = device === 'mobile' ? 'slidesToShowMobile' : (device === 'tablet' ? 'slidesToShowTablet' : 'slidesToShow');
			return Math.max(1, Math.min(items.length || 1, Number(config[key]) || (device === 'desktop' ? 3 : 1)));
		}
		function maximum() { return carousel ? Math.max(0, items.length - visible()) : Math.max(0, items.length - 1); }
		function step() {
			if (!carousel) return 1;
			const device = currentDevice();
			const key = device === 'mobile' ? 'slidesToScrollMobile' : (device === 'tablet' ? 'slidesToScrollTablet' : 'slidesToScroll');
			return Math.max(1, Math.min(visible(), Number(config[key]) || Number(config.slidesToScroll) || 1));
		}
		function thumbsVisible() {
			const device = currentDevice();
			const key = device === 'mobile' ? 'thumbsSlidesToShowMobile' : (device === 'tablet' ? 'thumbsSlidesToShowTablet' : 'thumbsSlidesToShow');
			return Math.max(1, Math.min(10, Number(config[key]) || Number(config.thumbsSlidesToShow) || 5));
		}
		function gapOffset(count) {
			const raw = String(getComputedStyle(root).getPropertyValue('--carousel-gap') || '20px').trim();
			const match = raw.match(/^(-?\d+(?:\.\d+)?)([a-z%]*)$/i);
			if (!match || count <= 1) return '0px';
			return Number((Number(match[1]) * (count - 1) / count).toFixed(4)) + (match[2] || 'px');
		}
		function normalize(index) {
			const max = maximum();
			if (config.infiniteLoop && max > 0) { if (index > max) return 0; if (index < 0) return max; }
			return Math.max(0, Math.min(max, index));
		}
		function render(index) {
			active = normalize(Number(index) || 0);
			if (carousel && track) {
				const count = visible();
				root.style.setProperty('--pb-pro-visible', String(count));
				track.style.transitionDuration = Math.max(0, Number(config.transitionSpeed) || 0) + 'ms';
				track.style.alignItems = config.equalHeight ? 'stretch' : 'flex-start';
				items.forEach(function (item, itemIndex) {
					item.style.flexBasis = `calc(${100 / count}% - ${gapOffset(count)})`;
					item.style.height = config.equalHeight ? 'auto' : 'max-content';
					item.classList?.toggle('is-active', itemIndex === active);
					item.classList?.toggle('is-before', itemIndex < active);
					item.classList?.toggle('is-after', itemIndex > active);
				});
				const offset = Math.max(0, Number(items[active]?.offsetLeft) || 0);
				track.style.transform = `translate3d(-${offset}px,0,0)`;
			} else items.forEach(function (item, itemIndex) { item.hidden = itemIndex !== active; item.classList.toggle('is-active', itemIndex === active); });
			const pageCount = carousel ? Math.max(1, Math.ceil(maximum() / step()) + 1) : items.length;
			const activePage = carousel ? Math.min(pageCount - 1, Math.round(active / step())) : active;
			dots.forEach(function (dot, dotIndex) { dot.hidden = carousel && dotIndex >= pageCount; const selected = dotIndex === activePage; dot.classList.toggle('active', selected); dot.setAttribute('aria-selected', selected ? 'true' : 'false'); });
			if (thumbnailViewport && thumbnailTrack && thumbnails.length) {
				const count = thumbsVisible();
				root.style.setProperty('--media-thumbs-per-view', String(count));
				thumbnails.forEach(function (thumbnail) {
					thumbnail.style.flexBasis = `calc((100% - ${count - 1} * var(--carousel-dot-gap, 8px)) / ${count})`;
				});
				const selected = thumbnails[active];
				const offset = config.centeredSlides && selected
					? (Number(selected.offsetLeft) || 0) + (Number(selected.offsetWidth) || 0) / 2 - (Number(thumbnailViewport.clientWidth) || 0) / 2
					: 0;
				thumbnailTrack.style.transform = `translate3d(${-offset}px,0,0)`;
			}
			if (current) current.textContent = String(activePage + 1);
			if (total) total.textContent = String(pageCount);
			if (progress) progress.style.width = ((activePage + 1) / pageCount * 100) + '%';
			if (carousel && dots[0]?.parentElement) dots[0].parentElement.hidden = maximum() <= 0;
			if (carousel && previous) previous.hidden = maximum() <= 0;
			if (carousel && next) next.hidden = maximum() <= 0;
			if (previous) previous.disabled = !config.infiniteLoop && active === 0;
			if (next) next.disabled = !config.infiniteLoop && active === maximum();
		}
		function stop() { if (timer) window.clearInterval(timer); timer = 0; }
		function start() { stop(); if (!config.autoplay || prefersReducedMotion() || interactionPaused || (config.pauseOnHover && hovered) || maximum() <= 0) return; timer = window.setInterval(function () { render(active + step()); }, Math.max(100, Number(config.autoplaySpeed) || 5000)); }
		function interact(index) { render(index); if (config.pauseOnInteraction) { interactionPaused = true; stop(); } else start(); }
		previous?.addEventListener('click', function () { interact(active - step()); });
		next?.addEventListener('click', function () { interact(active + step()); });
		dots.forEach(function (dot) { dot.addEventListener('click', function () { const pageIndex = Number(dot.dataset.proIndex) || 0; interact(pageIndex * step()); }); });
		root.addEventListener('mouseenter', function () { hovered = true; start(); });
		root.addEventListener('mouseleave', function () { hovered = false; start(); });
		root.addEventListener('keydown', function (event) { if (event.key === 'ArrowLeft') { event.preventDefault(); interact(active - step()); } if (event.key === 'ArrowRight') { event.preventDefault(); interact(active + step()); } });
		root.querySelectorAll(':scope [data-pro-media-lightbox]').forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				const styles = getComputedStyle(root);
				openMediaLightbox(trigger.dataset.mediaSrc, trigger.dataset.mediaType, trigger.dataset.mediaAlt || '', {
					background: styles.getPropertyValue('--media-lightbox-background').trim() || 'rgba(0,0,0,.92)',
					uiColor: styles.getPropertyValue('--media-lightbox-ui').trim() || '#fff',
					uiHoverColor: styles.getPropertyValue('--media-lightbox-ui-hover').trim() || '#6979f8',
					videoWidth: styles.getPropertyValue('--media-lightbox-video-width').trim() || '75%',
				});
			});
		});
		window.addEventListener('resize', function () { render(active); }, { passive: true });
		render(0); start();
	}

function initProCarousel(root) { bindProSlider(root, true); }

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-pro-carousel].pb-pro-media-carousel").forEach(initProCarousel);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["media_carousel"] = Object.freeze({ init, initProCarousel });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
