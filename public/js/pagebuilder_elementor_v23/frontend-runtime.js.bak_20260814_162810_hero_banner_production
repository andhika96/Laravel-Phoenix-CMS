(function () {
	'use strict';

	const boundRoots = new WeakSet();
	const boundCarouselRoots = new WeakSet();
	const boundBasicGalleryRoots = new WeakSet();
	const boundBasicImageRoots = new WeakSet();
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

	function openMediaLightbox(source, type = 'image', alt = '', settings = {}) {
		const mediaSource = String(source || '').trim();
		const mediaType = type === 'video' ? 'video' : 'image';
		if (!mediaSource || !/^(https?:\/\/|\/)/i.test(mediaSource) || mediaSource.startsWith('//')) return;
		if (mediaType === 'video' && !/^https:\/\/(?:www\.youtube\.com\/embed\/[A-Za-z0-9_-]+|player\.vimeo\.com\/video\/\d+)$/i.test(mediaSource)) return;
		const overlay = document.createElement('div');
		overlay.className = 'pb-image-lightbox pb-image-carousel-lightbox pb-pro-media-lightbox';
		overlay.setAttribute('role', 'dialog');
		overlay.setAttribute('aria-modal', 'true');
		overlay.style.background = String(settings.background || 'rgba(0,0,0,.92)');
		const closeButton = document.createElement('button');
		closeButton.type = 'button';
		closeButton.className = 'pb-pro-media-lightbox__close';
		closeButton.setAttribute('aria-label', 'Close lightbox');
		closeButton.textContent = '×';
		const uiColor = String(settings.uiColor || '#fff');
		const uiHoverColor = String(settings.uiHoverColor || '#6979f8');
		let closeHovered = false;
		let closeFocused = false;
		const renderCloseColor = function () {
			closeButton.style.color = closeHovered || closeFocused ? uiHoverColor : uiColor;
		};
		closeButton.addEventListener('mouseenter', function () { closeHovered = true; renderCloseColor(); });
		closeButton.addEventListener('mouseleave', function () { closeHovered = false; renderCloseColor(); });
		closeButton.addEventListener('focus', function () { closeFocused = true; renderCloseColor(); });
		closeButton.addEventListener('blur', function () { closeFocused = false; renderCloseColor(); });
		renderCloseColor();
		overlay.appendChild(closeButton);
		if (mediaType === 'video') {
			const frame = document.createElement('iframe');
			frame.src = mediaSource;
			frame.title = String(alt || 'Media video');
			frame.allow = 'autoplay; fullscreen; picture-in-picture';
			frame.allowFullscreen = true;
			frame.style.width = String(settings.videoWidth || '75%');
			frame.style.maxWidth = '1100px';
			frame.style.aspectRatio = '16 / 9';
			frame.style.border = '0';
			overlay.appendChild(frame);
		} else {
			const image = document.createElement('img');
			image.src = mediaSource;
			image.alt = String(alt || '');
			overlay.appendChild(image);
		}
		const close = function () { overlay.remove(); document.removeEventListener('keydown', onKeydown); };
		const onKeydown = function (event) { if (event.key === 'Escape') close(); };
		overlay.addEventListener('click', function (event) { if (event.target === overlay || event.target.closest('button')) close(); });
		document.addEventListener('keydown', onKeydown);
		document.body.appendChild(overlay);
		closeButton.focus();
	}

	function openImageLightbox(source, alt = '') {
		openMediaLightbox(source, 'image', alt);
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

	function bindBasicImage(root) {
		if (!root || boundBasicImageRoots.has(root)) return;
		boundBasicImageRoots.add(root);
		root.querySelectorAll('[data-basic-image-lightbox]').forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				const image = link.querySelector('img');
				openImageLightbox(image?.getAttribute('src') || link.getAttribute('href'), image?.getAttribute('alt') || '');
			});
		});
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

	function initProSlides(root) { bindProSlider(root, false); }
	function initProCarousel(root) { bindProSlider(root, true); }

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

	function initProVideoPlaylist(root) {
		if (!markProReady(root, 'video-playlist')) return;
		const config = parseProConfig(root);
		const items = Array.isArray(config.items) ? config.items : [];
		const player = root.querySelector(':scope [data-playlist-player]');
		const list = Array.from(root.querySelectorAll(':scope [data-playlist-index]'));
		const tabs = root.querySelector(':scope [data-playlist-tabs]');
		let active = 0;
		let activeTab = 0;
		let tabsExpanded = true;
		const watched = new Set();
		function safeMedia(value) {
			const raw = String(value || '').trim();
			return /^(?:https?:\/\/|\/)[^\u0000-\u001f"'()\\]*$/i.test(raw) ? raw : '';
		}
		function embed(value) {
			const raw = String(value || '').trim();
			if (!/^https?:\/\//i.test(raw)) return '';
			try {
				const url = new URL(raw);
				const host = url.hostname.toLowerCase().replace(/^www\./, '');
				if (host === 'youtu.be') {
					const id = url.pathname.slice(1);
					return /^[A-Za-z0-9_-]{6,}$/.test(id) ? 'https://www.youtube.com/embed/' + encodeURIComponent(id) : '';
				}
				if (host === 'youtube.com' || host === 'm.youtube.com') {
					const id = url.searchParams.get('v') || url.pathname.split('/').filter(Boolean).pop();
					return /^[A-Za-z0-9_-]{6,}$/.test(id || '') ? 'https://www.youtube.com/embed/' + encodeURIComponent(id) : '';
				}
				if (host === 'vimeo.com' || host === 'player.vimeo.com') {
					const id = url.pathname.split('/').filter(Boolean).pop();
					return /^\d+$/.test(id || '') ? 'https://player.vimeo.com/video/' + id : '';
				}
			} catch (_) {}
			return '';
		}
		function tag(value) {
			return ['h1','h2','h3','h4','h5','h6','div','span'].includes(String(value || '').toLowerCase()) ? String(value).toLowerCase() : 'h4';
		}
		function thumbnail(entry) {
			const custom = safeMedia(entry?.thumbnailUrl);
			if (custom) return custom;
			const link = String(entry?.link || '');
			const match = link.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{6,})/i);
			return match ? 'https://img.youtube.com/vi/' + encodeURIComponent(match[1]) + '/hqdefault.jpg' : '';
		}
		function iconClass(index) {
			return config.indicateWatched && watched.has(index) ? String(config.playedIcon || 'fas fa-check') : String(config.playIcon || 'fas fa-play');
		}
		function renderPlayer(entry) {
			if (!player) return;
			player.replaceChildren();
			if (!entry) return;
			if (entry.type === 'section') {
				const section = document.createElement('div');
				section.className = 'pb-pro-video-playlist__section';
				const heading = document.createElement(tag(entry.titleTag));
				heading.textContent = String(entry.title || '');
				const paragraph = document.createElement('p');
				paragraph.textContent = String(entry.sectionContent || '');
				section.append(heading, paragraph);
				player.append(section);
				return;
			}
			const media = document.createElement('div');
			media.className = 'pb-pro-video-playlist__media';
			const overlay = config.imageOverlay ? safeMedia(config.overlayImageUrl) || thumbnail(entry) : '';
			if (overlay) {
				const image = document.createElement('img');
				image.className = 'pb-pro-video-playlist__overlay-image';
				image.src = overlay;
				image.alt = '';
				media.append(image);
			}
			const source = entry.type === 'self_hosted' ? safeMedia(entry.link) : embed(entry.link);
			if (entry.type === 'self_hosted' && source) {
				const video = document.createElement('video');
				video.src = source;
				video.controls = true;
				if (config.autoplayOnLoad) video.autoplay = true;
				video.addEventListener('ended', function () { if (config.autoplayNext) select(active + 1); });
				media.append(video);
			} else if (source) {
				const frame = document.createElement('iframe');
				frame.src = config.autoplayOnLoad ? source + (source.includes('?') ? '&' : '?') + 'autoplay=1' : source;
				frame.title = String(entry.title || 'Video');
				frame.allow = 'autoplay; fullscreen; picture-in-picture';
				frame.allowFullscreen = true;
				media.append(frame);
			} else {
				const placeholder = document.createElement('div');
				placeholder.className = 'pb-pro-video-playlist__placeholder';
				placeholder.textContent = 'Select a video';
				media.append(placeholder);
			}
			player.append(media);
		}
		function renderTabs(entry) {
			if (!tabs) return;
			tabs.replaceChildren();
			if (!entry || entry.type === 'section' || !entry.showContentTabs) { tabs.hidden = true; return; }
			tabs.hidden = false;
			const buttons = document.createElement('div');
			buttons.className = 'pb-pro-video-playlist__tab-buttons';
			const content = document.createElement('div');
			content.className = 'pb-pro-video-playlist__tab-content';
			const tabData = [
				{ title: entry.contentTabOneTitle || 'Overview', content: entry.contentTabOneContent || '' },
				{ title: entry.contentTabTwoTitle || 'Notes', content: entry.contentTabTwoContent || '' },
			];
			tabData.forEach(function (tab, index) {
				const button = document.createElement('button');
				button.type = 'button';
				button.textContent = String(tab.title);
				button.classList.toggle('is-active', index === activeTab);
				button.addEventListener('click', function () { activeTab = index; renderTabs(entry); });
				buttons.append(button);
			});
			content.textContent = String(tabData[activeTab]?.content || '');
			if (config.tabsCollapsible) {
				content.style.maxHeight = String(config.tabsHeight || '120px');
				content.style.overflow = 'auto';
				if (!tabsExpanded) content.hidden = true;
			}
			tabs.append(buttons, content);
			if (config.tabsCollapsible) {
				const toggle = document.createElement('button');
				toggle.type = 'button';
				toggle.className = 'pb-pro-video-playlist__show-more';
				toggle.textContent = tabsExpanded ? String(config.readLessLabel || 'Read Less') : String(config.readMoreLabel || 'Read More');
				toggle.addEventListener('click', function () { tabsExpanded = !tabsExpanded; renderTabs(entry); });
				tabs.append(toggle);
			}
		}
		function renderList() {
			list.forEach(function (button, index) {
				button.classList.toggle('is-active', index === active);
				button.classList.toggle('is-watched', config.indicateWatched && watched.has(index));
				const icon = button.querySelector(':scope .pb-pro-video-playlist__item-icon i');
				if (icon) icon.className = iconClass(index);
			});
		}
		function select(index) {
			if (!items.length) return;
			const next = Math.max(0, Math.min(items.length - 1, Number(index) || 0));
			if (config.indicateWatched && next !== active) watched.add(active);
			active = next;
			activeTab = 0;
			tabsExpanded = true;
			renderPlayer(items[active]);
			renderTabs(items[active]);
			renderList();
		}
		list.forEach(function (button) { button.addEventListener('click', function () { select(button.dataset.playlistIndex); }); });
		select(0);
	}

	function initProHotspot(root) {
		if (!markProReady(root, 'hotspot')) return;
		const trigger = root.dataset.trigger || 'hover';
		root.querySelectorAll(':scope .pb-pro-hotspot__marker').forEach(function (marker) {
			const tooltip = marker.querySelector(':scope .pb-pro-hotspot__tooltip');
			if (!tooltip || trigger === 'none') return;
			const show = function () { tooltip.hidden = false; marker.setAttribute('aria-expanded', 'true'); };
			const hide = function () { tooltip.hidden = true; marker.setAttribute('aria-expanded', 'false'); };
			if (trigger === 'click') marker.addEventListener('click', function (event) {
				const linked = typeof marker.matches === 'function' && marker.matches('a[href]');
				if (tooltip.hidden) { if (linked) event.preventDefault(); show(); return; }
				if (!linked) hide();
			});
			else { marker.addEventListener('mouseenter', show); marker.addEventListener('mouseleave', hide); marker.addEventListener('focus', show); marker.addEventListener('blur', hide); }
			marker.addEventListener('keydown', function (event) { if (event.key === 'Escape') hide(); });
		});
	}

	function initProFlipBox(root) {
		if (!markProReady(root, 'flip-box')) return;
		function toggle() { root.classList.toggle('is-flipped'); }
		root.addEventListener('click', function (event) {
			if (event.target.closest('a,button,input,select,textarea')) return;
			toggle();
		});
		root.addEventListener('keydown', function (event) {
			if (event.target !== root) return;
			if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggle(); }
			if (event.key === 'Escape') root.classList.remove('is-flipped');
		});
	}

	function initProForm(root) {
		if (!markProReady(root, 'form')) return;
		const config = parseProConfig(root);
		const validation = root.dataset.validation === 'custom' ? 'custom' : 'browser';
		const steps = Array.from(root.querySelectorAll(':scope [data-pro-form-step]'));
		const indicators = Array.from(root.querySelectorAll('[data-pro-step-indicator]'));
		let activeStep = 0;
		function renderStep(index) {
			activeStep = Math.max(0, Math.min(steps.length - 1, Number(index) || 0));
			steps.forEach(function (step, stepIndex) { step.hidden = stepIndex !== activeStep; });
			indicators.forEach(function (indicator, stepIndex) { indicator.classList.toggle('active', stepIndex <= activeStep); });
		}
		function validateStep() {
			const fields = steps[activeStep]?.querySelectorAll('input,textarea,select') || [];
			for (const field of fields) {
				if (field.checkValidity()) continue;
				if (validation === 'browser') field.reportValidity();
				else field.focus?.();
				return false;
			}
			return true;
		}
		root.querySelectorAll('[data-pro-next]').forEach(function (button) { button.addEventListener('click', function () { if (validateStep()) renderStep(activeStep + 1); }); });
		root.querySelectorAll('[data-pro-previous]').forEach(function (button) { button.addEventListener('click', function () { renderStep(activeStep - 1); }); });
		root.addEventListener('submit', async function (event) {
			event.preventDefault();
			const message = root.querySelector('[data-pro-form-message]');
			if (!root.checkValidity()) { if (validation === 'browser') root.reportValidity(); else root.querySelector(':invalid')?.focus?.(); if (message) { message.classList.add('is-error'); message.textContent = root.dataset.errorMessage || 'Please check the form fields.'; } return; }
			const actions = config.actions || [];
			const data = new FormData(root);
			const submit = root.querySelector('button[type="submit"]');
			if (!config.submitUrl) {
				if (message) { message.classList.toggle('is-error', actions.some(function (action) { return !['message', 'redirect'].includes(action); })); message.textContent = actions.every(function (action) { return ['message', 'redirect'].includes(action); }) ? root.dataset.successMessage || 'The form was sent successfully.' : root.dataset.errorMessage || 'This form is not connected yet.'; }
				return;
			}
			if (submit) submit.disabled = true;
			root.setAttribute('aria-busy', 'true');
			if (message) { message.classList.remove('is-error'); message.textContent = 'Sending...'; }
			try {
				const response = await fetch(config.submitUrl, { method: 'POST', headers: { Accept: 'application/json' }, body: data });
				const payload = await response.json().catch(function () { return {}; });
				if (!response.ok) throw payload;
				if (message) message.textContent = payload.message || ((actions.includes('message')) ? root.dataset.successMessage || 'The form was sent successfully.' : '');
				root.dispatchEvent(new CustomEvent('pagebuilder:form-submit', { bubbles: true, detail: { form: root, data, actions, response: payload } }));
				if (payload.redirect && /^(?:https?:\/\/|\/|#)/i.test(String(payload.redirect))) window.location.assign(payload.redirect);
			} catch (error) {
				if (message) { message.classList.add('is-error'); message.textContent = error?.message || root.dataset.errorMessage || 'An error occurred.'; }
			} finally {
				if (submit) submit.disabled = false;
				root.setAttribute('aria-busy', 'false');
			}
		});
		if (steps.length) renderStep(0);
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
		const rootScope = scope && scope.querySelectorAll ? scope : document;
		rootScope.querySelectorAll('[data-accordion-root]').forEach(bindAccordion);
		rootScope.querySelectorAll('[data-image-carousel]').forEach(bindImageCarousel);
		rootScope.querySelectorAll('[data-basic-gallery]').forEach(bindBasicGallery);
		rootScope.querySelectorAll('[data-basic-image]').forEach(bindBasicImage);
		rootScope.querySelectorAll('[data-pb-motion]').forEach(bindAdvancedWidget);
		rootScope.querySelectorAll('[data-pro-slides]').forEach(initProSlides);
		rootScope.querySelectorAll('[data-pro-carousel]').forEach(initProCarousel);
		rootScope.querySelectorAll('[data-pro-countdown]').forEach(initProCountdown);
		rootScope.querySelectorAll('[data-progress-tracker]').forEach(initProProgressTracker);
		rootScope.querySelectorAll('[data-video-playlist]').forEach(initProVideoPlaylist);
		rootScope.querySelectorAll('[data-pro-hotspot]').forEach(initProHotspot);
		rootScope.querySelectorAll('[data-pro-flip-box]').forEach(initProFlipBox);
		rootScope.querySelectorAll('[data-pro-form]').forEach(initProForm);
		rootScope.querySelectorAll('[data-pro-headline]').forEach(initProAnimatedHeadline);
		rootScope.querySelectorAll('[data-code-highlight]').forEach(initProCodeHighlight);
		rootScope.querySelectorAll('[data-share-buttons]').forEach(initProShareButtons);
	}

	window.PageBuilderElementorV23Runtime = Object.freeze({ init, bindAccordion, bindImageCarousel, bindBasicGallery, bindBasicImage, bindAdvancedWidget, initProSlides, initProCarousel, initProCountdown, initProProgressTracker, initProVideoPlaylist, initProHotspot, initProFlipBox, initProForm, initProAnimatedHeadline, initProCodeHighlight, initProShareButtons });

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
	} else {
		init(document);
	}
})();
