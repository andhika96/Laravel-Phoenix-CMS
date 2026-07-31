(function () {
	'use strict';

	const boundRoots = new WeakSet();
	const boundCarouselRoots = new WeakSet();
	const boundBasicGalleryRoots = new WeakSet();
	const transitionState = new WeakMap();
	const reducedMotionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
	const motionEntries = new Map();
	let motionFrame = 0;
	let motionListenersBound = false;
	let entranceObserver = null;

	function prefersReducedMotion() {
		return !!(reducedMotionQuery && reducedMotionQuery.matches);
	}

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

	function currentDevice() {
		const width = window.innerWidth || document.documentElement.clientWidth || 1280;
		if (width <= 767) return 'mobile';
		if (width <= 1024) return 'tablet';
		return 'desktop';
	}

	function effectAllowed(config) {
		const device = currentDevice();
		if (device === 'mobile') return config.applyMobile !== false;
		if (device === 'tablet') return config.applyTablet !== false;
		return config.applyDesktop !== false;
	}

	function viewportProgress(element) {
		const rect = element.getBoundingClientRect();
		const viewport = Math.max(1, window.innerHeight || document.documentElement.clientHeight || 1);
		return Math.max(0, Math.min(1, (viewport - rect.top) / (viewport + Math.max(1, rect.height))));
	}

	function directionalLevel(direction, progress) {
		if (direction === 'fade-out') return 1 - progress;
		if (direction === 'fade-out-in') return Math.abs(progress - 0.5) * 2;
		if (direction === 'fade-in-out') return 1 - Math.abs(progress - 0.5) * 2;
		return progress;
	}

	function updateMotion() {
		motionFrame = 0;
		motionEntries.forEach(function (config, element) {
			if (!element.isConnected) {
				motionEntries.delete(element);
				return;
			}
			if (prefersReducedMotion() || !effectAllowed(config)) {
				element.style.setProperty('--pb-motion-transform', 'translate3d(0,0,0) rotate(0deg) scale(1)');
				element.style.removeProperty('opacity');
				element.style.removeProperty('filter');
				return;
			}
			const progress = viewportProgress(element);
			const centered = (progress - 0.5) * 2;
			const transforms = [];
			if (config.scrollingEffects && config.verticalScrollEnabled) {
				const sign = config.verticalScrollDirection === 'down' ? 1 : -1;
				transforms.push(`translateY(${centered * sign * (Number(config.verticalScrollSpeed) || 4) * 10}px)`);
			}
			if (config.scrollingEffects && config.horizontalScrollEnabled) {
				const sign = config.horizontalScrollDirection === 'right' ? 1 : -1;
				transforms.push(`translateX(${centered * sign * (Number(config.horizontalScrollSpeed) || 4) * 10}px)`);
			}
			if (config.scrollingEffects && config.rotateEnabled) {
				const sign = config.rotateDirection === 'right' ? 1 : -1;
				transforms.push(`rotate(${centered * sign * (Number(config.rotateSpeed) || 4) * 12}deg)`);
			}
			if (config.scrollingEffects && config.scaleEnabled) {
				const direction = String(config.scaleDirection || 'up');
				const level = direction.includes('down') ? 1 - progress : progress;
				transforms.push(`scale(${0.8 + level * Math.min(0.5, (Number(config.scaleSpeed) || 4) / 20)})`);
			}
			element.style.setProperty('--pb-motion-transform', transforms.join(' ') || 'translate3d(0,0,0)');
			if (config.scrollingEffects && config.transparencyEnabled) {
				const opacity = directionalLevel(config.transparencyDirection, progress);
				element.style.opacity = String(Math.max(0, Math.min(1, opacity)));
			} else element.style.removeProperty('opacity');
			if (config.scrollingEffects && config.blurEnabled) {
				const level = 1 - directionalLevel(config.blurDirection, progress);
				element.style.filter = `blur(${Math.max(0, level * (Number(config.blurLevel) || 5))}px)`;
			} else element.style.removeProperty('filter');
		});
	}

	function scheduleMotion() {
		if (motionFrame) return;
		motionFrame = requestAnimationFrame(updateMotion);
	}

	function updatePointerEffects(event) {
		if (prefersReducedMotion()) return;
		const viewportWidth = Math.max(1, window.innerWidth || 1);
		const viewportHeight = Math.max(1, window.innerHeight || 1);
		const x = (event.clientX / viewportWidth - 0.5) * 2;
		const y = (event.clientY / viewportHeight - 0.5) * 2;
		motionEntries.forEach(function (config, element) {
			if (!config.mouseEffects || !effectAllowed(config)) return;
			const transforms = [];
			if (config.mouseTrackEnabled) {
				const sign = config.mouseTrackDirection === 'opposite' ? -1 : 1;
				const speed = Number(config.mouseTrackSpeed) || 1;
				transforms.push(`translate3d(${x * sign * speed * 12}px,${y * sign * speed * 12}px,0)`);
			}
			if (config.tilt3dEnabled) {
				const sign = config.tilt3dDirection === 'opposite' ? -1 : 1;
				const speed = Number(config.tilt3dSpeed) || 1;
				transforms.push(`perspective(900px) rotateX(${y * sign * speed * -5}deg) rotateY(${x * sign * speed * 5}deg)`);
			}
			element.style.setProperty('--pb-mouse-transform', transforms.join(' ') || 'translate3d(0,0,0)');
		});
	}

	function ensureMotionListeners() {
		if (motionListenersBound) return;
		motionListenersBound = true;
		window.addEventListener('scroll', scheduleMotion, { passive: true });
		window.addEventListener('resize', scheduleMotion, { passive: true });
		window.addEventListener('pointermove', updatePointerEffects, { passive: true });
	}

	function ensureEntranceObserver() {
		if (entranceObserver || typeof IntersectionObserver !== 'function') return entranceObserver;
		entranceObserver = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				const element = entry.target;
				const delay = Math.max(0, Number(element.dataset.entranceDelay) || 0);
				window.setTimeout(function () { element.classList.add('is-visible'); }, prefersReducedMotion() ? 0 : delay);
				entranceObserver.unobserve(element);
			});
		}, { threshold: 0.08 });
		return entranceObserver;
	}

	function bindAdvancedWidget(element) {
		if (!element || motionEntries.has(element)) return;
		let config = {};
		try { config = JSON.parse(element.getAttribute('data-pb-motion') || '{}'); } catch (_) { config = {}; }
		motionEntries.set(element, config);
		if (element.classList.contains('pb-advanced-entrance')) {
			if (prefersReducedMotion()) element.classList.add('is-visible');
			else ensureEntranceObserver()?.observe(element);
		}
		ensureMotionListeners();
		scheduleMotion();
	}

	function openImageLightbox(source, alt = '') {
		const imageSource = String(source || '').trim();
		if (!imageSource || !/^(https?:\/\/|\/)/i.test(imageSource) || imageSource.startsWith('//')) return;
		const overlay = document.createElement('div');
		overlay.className = 'pb-image-lightbox pb-image-carousel-lightbox';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.innerHTML = '<button type="button" aria-label="Close lightbox">&times;</button><img alt="">';
		const image = overlay.querySelector('img');
		image.src = imageSource;
		image.alt = String(alt || '');
		const close = function () { overlay.remove(); document.removeEventListener('keydown', onKeydown); };
		const onKeydown = function (event) { if (event.key === 'Escape') close(); };
		overlay.addEventListener('click', function (event) { if (event.target === overlay || event.target.closest('button')) close(); });
		document.addEventListener('keydown', onKeydown);
		document.body.appendChild(overlay);
		overlay.querySelector('button')?.focus();
	}

	function bindImageCarousel(root) {
		if (!root || boundCarouselRoots.has(root)) return;
		boundCarouselRoots.add(root);
		const track = root.querySelector(':scope .pb-image-carousel__track');
		const slides = Array.from(root.querySelectorAll(':scope .pb-image-carousel__slide'));
		if (!track || !slides.length) return;
		let config = {};
		try { config = JSON.parse(root.getAttribute('data-carousel-config') || '{}'); } catch (_) { config = {}; }
		const previous = root.querySelector(':scope .pb-image-carousel__arrow--previous');
		const next = root.querySelector(':scope .pb-image-carousel__arrow--next');
		const pagination = root.querySelector(':scope .pb-image-carousel__pagination');
		const dots = Array.from(root.querySelectorAll(':scope .pb-image-carousel__dot'));
		let activeIndex = 0;
		let autoplayTimer = 0;
		let hovered = false;
		let interactionPaused = false;

		function responsiveConfig(base, fallback) {
			const device = currentDevice();
			const value = device === 'mobile' ? config[base + 'Mobile'] : (device === 'tablet' ? config[base + 'Tablet'] : config[base]);
			const number = Number(value);
			return Number.isFinite(number) && number > 0 ? Math.round(number) : fallback;
		}
		function visibleSlides() { return Math.max(1, Math.min(slides.length, responsiveConfig('slidesToShow', Math.min(3, slides.length)))); }
		function slidesToScroll() { return Math.max(1, Math.min(visibleSlides(), responsiveConfig('slidesToScroll', 1))); }
		function maxIndex() { return Math.max(0, slides.length - visibleSlides()); }
		function pageCount() { return Math.max(1, Math.ceil(maxIndex() / slidesToScroll()) + 1); }
		function normalizedIndex(index) {
			const maximum = maxIndex();
			if (config.infiniteLoop && maximum > 0) {
				if (index > maximum) return 0;
				if (index < 0) return maximum;
			}
			return Math.max(0, Math.min(maximum, index));
		}
		function updateImageCarousel(index) {
			activeIndex = normalizedIndex(Number(index) || 0);
			const visible = visibleSlides();
			root.style.setProperty('--pb-carousel-visible', String(visible));
			track.style.transform = `translate3d(-${activeIndex * (100 / visible)}%,0,0)`;
			const hasOverflow = maxIndex() > 0;
			const pages = pageCount();
			const dotIndex = Math.min(pages - 1, Math.round(activeIndex / slidesToScroll()));
			dots.forEach(function (dot, index) {
				const active = index === dotIndex;
				dot.hidden = index >= pages;
				dot.classList.toggle('is-active', active);
				dot.setAttribute('aria-selected', active ? 'true' : 'false');
			});
			if (previous) {
				previous.hidden = !hasOverflow;
				previous.disabled = !config.infiniteLoop && activeIndex === 0;
			}
			if (next) {
				next.hidden = !hasOverflow;
				next.disabled = !config.infiniteLoop && activeIndex === maxIndex();
			}
			if (pagination) pagination.hidden = !hasOverflow;
		}
		function stopAutoplay() {
			if (!autoplayTimer) return;
			window.clearInterval(autoplayTimer);
			autoplayTimer = 0;
		}
		function startAutoplay() {
			stopAutoplay();
			if (!config.autoplay || maxIndex() <= 0 || prefersReducedMotion() || (config.pauseOnHover && hovered) || interactionPaused) return;
			autoplayTimer = window.setInterval(function () {
				updateImageCarousel(activeIndex + (config.direction === 'right' ? -slidesToScroll() : slidesToScroll()));
			}, Math.max(100, Number(config.autoplaySpeed) || 5000));
		}
		function interact(index) {
			updateImageCarousel(index);
			if (config.pauseOnInteraction) {
				interactionPaused = true;
				stopAutoplay();
			} else startAutoplay();
		}
		if (previous) previous.addEventListener('click', function () { interact(activeIndex - slidesToScroll()); });
		if (next) next.addEventListener('click', function () { interact(activeIndex + slidesToScroll()); });
		dots.forEach(function (dot, index) { dot.addEventListener('click', function () { interact(index * slidesToScroll()); }); });
		root.addEventListener('mouseenter', function () { hovered = true; startAutoplay(); });
		root.addEventListener('mouseleave', function () { hovered = false; startAutoplay(); });
		root.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowLeft') { event.preventDefault(); interact(activeIndex - slidesToScroll()); }
			if (event.key === 'ArrowRight') { event.preventDefault(); interact(activeIndex + slidesToScroll()); }
		});
		root.querySelectorAll('[data-carousel-lightbox]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				const image = link.querySelector('img');
				openImageLightbox(image?.getAttribute('src') || link.getAttribute('href'), image?.getAttribute('alt') || '');
			});
		});
		root.setAttribute('tabindex', root.getAttribute('tabindex') || '0');
		window.addEventListener('resize', function () { updateImageCarousel(activeIndex); startAutoplay(); }, { passive: true });
		updateImageCarousel(0);
		startAutoplay();
	}

	function bindBasicGallery(root) {
		if (!root || boundBasicGalleryRoots.has(root)) return;
		boundBasicGalleryRoots.add(root);
		root.querySelectorAll('[data-basic-gallery-lightbox]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				const image = link.querySelector('img');
				openImageLightbox(image?.getAttribute('src') || link.getAttribute('href'), image?.getAttribute('alt') || '');
			});
		});
	}

	function init(scope) {
		const rootScope = scope && scope.querySelectorAll ? scope : document;
		rootScope.querySelectorAll('[data-accordion-root]').forEach(bindAccordion);
		rootScope.querySelectorAll('[data-image-carousel]').forEach(bindImageCarousel);
		rootScope.querySelectorAll('[data-basic-gallery]').forEach(bindBasicGallery);
		rootScope.querySelectorAll('[data-pb-motion]').forEach(bindAdvancedWidget);
	}

	window.PageBuilderElementorRuntime = Object.freeze({ init, bindAccordion, bindImageCarousel, bindBasicGallery, bindAdvancedWidget });

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
	} else {
		init(document);
	}
})();
