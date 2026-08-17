(function () {
	'use strict';

	const boundRoots = new WeakSet();
	const boundCarouselRoots = new WeakSet();
	const boundBasicGalleryRoots = new WeakSet();
	const boundBasicImageRoots = new WeakSet();
	const boundProductColorSelectorRoots = new WeakSet();
	const heroSliderScriptPromises = new Map();
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
		const isNativeVideo = /\.(?:mp4|webm|ogg)(?:[?#].*)?$/i.test(mediaSource);
		const isEmbedVideo = /^https:\/\/(?:www\.youtube\.com\/embed\/[A-Za-z0-9_-]+|player\.vimeo\.com\/video\/\d+|www\.dailymotion\.com\/embed\/video\/[A-Za-z0-9]+)$/i.test(mediaSource);
		if (mediaType === 'video' && !isNativeVideo && !isEmbedVideo) return;
		const returnFocus = typeof document.activeElement?.focus === 'function' ? document.activeElement : null;
		const bodyStyle = document.body.style || {};
		const previousOverflow = bodyStyle.overflow || '';
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
			const media = document.createElement(isNativeVideo ? 'video' : 'iframe');
			media.src = mediaSource;
			if (isNativeVideo) {
				media.controls = true;
				media.autoplay = true;
				media.playsInline = true;
				media.setAttribute('aria-label', String(alt || 'Media video'));
			} else {
				media.title = String(alt || 'Media video');
				media.allow = 'autoplay; fullscreen; picture-in-picture';
				media.allowFullscreen = true;
				media.style.border = '0';
			}
			media.style.width = String(settings.videoWidth || '75%');
			media.style.maxWidth = '1100px';
			media.style.aspectRatio = '16 / 9';
			overlay.appendChild(media);
		} else {
			const image = document.createElement('img');
			image.src = mediaSource;
			image.alt = String(alt || '');
			overlay.appendChild(image);
		}
		let closed = false;
		const close = function () {
			if (closed) return;
			closed = true;
			overlay.querySelectorAll('iframe,video').forEach(function (media) { media.removeAttribute('src'); media.load?.(); });
			overlay.remove();
			document.removeEventListener('keydown', onKeydown);
			bodyStyle.overflow = previousOverflow;
			if (returnFocus?.isConnected) returnFocus.focus();
		};
		const onKeydown = function (event) { if (event.key === 'Escape') close(); };
		overlay.addEventListener('click', function (event) { if (event.target === overlay || event.target.closest('button')) close(); });
		document.addEventListener('keydown', onKeydown);
		document.body.appendChild(overlay);
		bodyStyle.overflow = 'hidden';
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

	function initHeroBanner(root) {
		if (!markProReady(root, 'hero-banner')) return;
		root.querySelectorAll(':scope [data-hero-media]').forEach(function (trigger) {
			trigger.addEventListener('click', function (event) {
				event.preventDefault();
				const styles = getComputedStyle(root);
				openMediaLightbox(trigger.dataset.mediaSrc, trigger.dataset.mediaType, trigger.dataset.mediaAlt || '', {
					background: styles.getPropertyValue('--hero-modal-background').trim() || 'rgba(0,0,0,.92)',
					uiColor: styles.getPropertyValue('--hero-modal-ui').trim() || '#fff',
					uiHoverColor: styles.getPropertyValue('--hero-modal-ui-hover').trim() || '#6979f8',
					videoWidth: styles.getPropertyValue('--hero-modal-video-width').trim() || '75%',
				});
			});
		});
	}

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

	function parseHeroSliderConfig(root) {
		try { return JSON.parse(root.getAttribute('data-hero-slider-config') || '{}'); }
		catch (_) { return {}; }
	}

	function loadHeroSliderScript(src, ready) {
		const url = String(src || '').trim();
		if (!url) return Promise.reject(new Error('Hero Slider provider SDK URL is empty.'));
		if (typeof ready === 'function' && ready()) return Promise.resolve();
		if (heroSliderScriptPromises.has(url)) return heroSliderScriptPromises.get(url);
		const promise = new Promise(function (resolve, reject) {
			let settled = false;
			let timeout = 0;
			const finish = function (error) {
				if (settled) return;
				settled = true;
				if (timeout) window.clearTimeout(timeout);
				if (error) reject(error); else resolve();
			};
			const onReady = function () {
				if (typeof ready !== 'function' || ready()) finish();
			};
			if (/youtube\.com\/iframe_api/i.test(url)) {
				const previous = window.onYouTubeIframeAPIReady;
				window.onYouTubeIframeAPIReady = function () {
					try { if (typeof previous === 'function') previous(); } finally { onReady(); }
				};
			}
			const script = document.createElement('script');
			script.src = url;
			script.async = true;
			script.onload = onReady;
			script.onerror = function () { finish(new Error('Hero Slider provider SDK failed to load.')); };
			const parent = document.head || document.body || document.documentElement;
			if (!parent || typeof parent.appendChild !== 'function') { finish(new Error('Hero Slider cannot append provider SDK.')); return; }
			parent.appendChild(script);
			timeout = window.setTimeout(function () { finish(new Error('Hero Slider provider SDK timed out.')); }, 10000);
		});
		heroSliderScriptPromises.set(url, promise);
		return promise;
	}

	function heroSliderCall(value, fallback) {
		try {
			const result = typeof value === 'function' ? value() : fallback;
			return result && typeof result.then === 'function' ? result : Promise.resolve(result);
		} catch (error) {
			return Promise.reject(error);
		}
	}

	function heroSliderProviderId(provider, value) {
		const raw = String(value || '').trim();
		if (provider === 'youtube') {
			const match = raw.match(/(?:youtu\.be\/|[?&]v=|embed\/)([^?&/]+)/i);
			return match ? match[1] : raw;
		}
		if (provider === 'vimeo') {
			const match = raw.match(/(?:video\/|vimeo\.com\/)(\d+)/i);
			return match ? match[1] : raw.replace(/\D+/g, '');
		}
		if (provider === 'dailymotion') {
			const match = raw.match(/(?:video\/|dai\.ly\/)([^_?&/]+)/i);
			return match ? match[1] : raw;
		}
		return raw;
	}

	function createHeroNativeAdapter(media, callbacks) {
		const adapter = {
			provider: 'self_hosted', full: true, durationSupported: true, ready: Promise.resolve(),
			play() { return heroSliderCall(() => media.play()); },
			pause() { try { media.pause?.(); } catch (_) {} return Promise.resolve(); },
			mute(value) { media.muted = !!value; return Promise.resolve(); },
			currentTime() { return Number.isFinite(Number(media.currentTime)) ? Number(media.currentTime) : 0; },
			seek(value) { try { if (Number.isFinite(Number(value))) media.currentTime = Number(value); } catch (_) {} return Promise.resolve(); },
		};
		media.addEventListener?.('ended', callbacks.ended);
		media.addEventListener?.('error', callbacks.error);
		media.addEventListener?.('loadedmetadata', callbacks.metadata);
		media.addEventListener?.('playing', callbacks.playing);
		media.addEventListener?.('pause', callbacks.paused);
		return adapter;
	}

	function createHeroIframeAdapter(root, media, provider, slide, config, callbacks) {
		const id = heroSliderProviderId(provider, slide.getAttribute?.('data-video-id') || media.getAttribute?.('data-video-id') || slide.getAttribute?.('data-video-url') || media.getAttribute?.('src') || '');
		let player = null;
		let resolveReady;
		let rejectReady;
		const ready = new Promise(function (resolve, reject) { resolveReady = resolve; rejectReady = reject; });
		const adapter = {
			provider, full: provider !== 'embed', durationSupported: provider !== 'embed', ready,
			play() { return ready.then(function () { if (provider === 'youtube') player.playVideo(); else return player.play?.(); }); },
			pause() { return ready.then(function () { if (provider === 'youtube') player.pauseVideo(); else return player.pause?.(); }).catch(function () {}); },
			mute(value) { return ready.then(function () { if (provider === 'youtube') { value ? player.mute() : player.unMute(); } else if (provider === 'vimeo') return value ? player.setMuted?.(true) : player.setMuted?.(false); else return player.setVolume?.(value ? 0 : 1); }).catch(function () {}); },
			currentTime() {
				try {
					if (!player) return 0;
					if (provider === 'youtube') return Number(player.getCurrentTime?.()) || 0;
					const result = provider === 'vimeo' ? player.getCurrentTime?.() : player.getState?.();
					if (result && typeof result.then === 'function') return result.then(function (value) { return Number(value?.seconds ?? value?.videoTime ?? 0) || 0; });
					return Number(result?.videoTime ?? result) || 0;
				} catch (_) { return 0; }
			},
			seek(value) { return ready.then(function () { if (provider === 'youtube') player.seekTo(Number(value) || 0, true); else if (provider === 'vimeo') return player.setCurrentTime?.(Number(value) || 0); else return player.seek?.(Number(value) || 0); }).catch(function () {}); },
		};
		const resolvePlayer = function (instance) { player = instance; resolveReady(); };
		const rejectPlayer = function (error) { adapter.full = false; adapter.durationSupported = false; rejectReady(error instanceof Error ? error : new Error('Hero Slider provider player failed.')); };
		if (provider === 'youtube') {
			loadHeroSliderScript('https://www.youtube.com/iframe_api', () => !!window.YT?.Player).then(function () {
				if (!window.YT?.Player) throw new Error('YouTube IFrame API unavailable.');
				new window.YT.Player(media, { events: {
					onReady: function (event) { resolvePlayer(event.target); callbacks.metadata(); },
					onStateChange: function (event) { if (event.data === 0) callbacks.ended(); else if (event.data === 1) callbacks.playing(); else if (event.data === 2) callbacks.paused(); },
					onError: rejectPlayer,
				} });
			}).catch(rejectPlayer);
		} else if (provider === 'vimeo') {
			loadHeroSliderScript('https://player.vimeo.com/api/player.js', () => !!window.Vimeo?.Player).then(function () {
				if (!window.Vimeo?.Player) throw new Error('Vimeo Player SDK unavailable.');
				const instance = new window.Vimeo.Player(media);
				instance.on?.('ended', callbacks.ended); instance.on?.('play', callbacks.playing); instance.on?.('pause', callbacks.paused); instance.on?.('error', rejectPlayer);
				resolvePlayer(instance); callbacks.metadata();
			}).catch(rejectPlayer);
		} else if (provider === 'dailymotion') {
			// Dailymotion Web SDK: PLAYER_END, VIDEO_PLAY, and VIDEO_PAUSE are official lifecycle events.
			const playerId = String(config.dailymotionPlayerId || '').trim();
			const sdkUrl = String(config.dailymotionSdkUrl || '').trim() || (playerId ? 'https://geo.dailymotion.com/libs/player/' + encodeURIComponent(playerId) + '.js' : '');
			if (!sdkUrl) { rejectPlayer(new Error('Dailymotion Player ID or SDK URL is required.')); }
			else loadHeroSliderScript(sdkUrl, () => !!(window.dailymotion || window.Dailymotion)).then(function () {
				const api = window.dailymotion || window.Dailymotion;
				if (!api?.createPlayer) throw new Error('Dailymotion Web SDK unavailable.');
				const targetId = media.id || ('pb-hero-slider-dm-' + Math.random().toString(36).slice(2));
				media.id = targetId;
				return Promise.resolve(api.createPlayer(targetId, { video: id })).then(function (instance) {
					instance.on?.('PLAYER_END', callbacks.ended); instance.on?.('VIDEO_PLAY', callbacks.playing); instance.on?.('VIDEO_PAUSE', callbacks.paused); instance.on?.('error', rejectPlayer);
					resolvePlayer(instance); callbacks.metadata();
				});
			}).catch(rejectPlayer);
		} else {
			rejectPlayer(new Error('Generic embed providers do not expose a lifecycle adapter.'));
		}
		return adapter;
	}

	function initHeroSlider(root) {
		if (!markProReady(root, 'hero-slider')) return;
		const config = parseHeroSliderConfig(root);
		const slides = Array.from(root.querySelectorAll(':scope [data-hero-slide]'));
		const track = root.querySelector(':scope [data-hero-slider-track]');
		const previous = root.querySelector(':scope [data-hero-prev]');
		const next = root.querySelector(':scope [data-hero-next]');
		const dots = Array.from(root.querySelectorAll(':scope [data-hero-index]'));
		const progress = root.querySelector(':scope [data-hero-slider-progress]');
		const states = slides.map(function (slide) { return { slide, media: slide.querySelector(':scope [data-hero-video]'), adapter: null, resumeTime: 0, status: 'ready', muted: slide.getAttribute('data-video-muted') !== 'false', durationSupported: slide.getAttribute('data-video-duration-supported') === 'true' }; });
		let active = 0;
		let timer = 0;
		let progressTimer = 0;
		let hovered = false;
		let focused = false;
		let interactionPaused = false;
		let waitingForDuration = false;
		let pointerStart = null;

		function deviceValue(base, fallback = '') {
			const width = Number(window.innerWidth || document.documentElement?.clientWidth || 0);
			const suffix = width <= 767 ? 'Mobile' : (width <= 1024 ? 'Tablet' : '');
			if (suffix && config[base + suffix] !== '' && config[base + suffix] != null) return config[base + suffix];
			return config[base] === '' || config[base] == null ? fallback : config[base];
		}
		function direction() { return deviceValue('direction', 'horizontal') === 'vertical' ? 'vertical' : 'horizontal'; }
		function paginationPosition() {
			const vertical = direction() === 'vertical';
			const modeBase = vertical ? 'paginationPlacementModeVertical' : 'paginationPlacementModeHorizontal';
			const alignmentBase = vertical ? 'paginationAlignmentVertical' : 'paginationAlignmentHorizontal';
			const base = vertical ? 'paginationPositionVertical' : 'paginationPositionHorizontal';
			const fallback = vertical ? 'center-right' : 'bottom-center';
			const mode = String(deviceValue(modeBase, 'basic') || 'basic');
			if (mode !== 'custom') {
				const alignment = String(deviceValue(alignmentBase, 'center') || 'center');
				if (vertical) return { top: 'top-right', bottom: 'bottom-right' }[alignment] || 'center-right';
				return { left: 'bottom-left', right: 'bottom-right' }[alignment] || 'bottom-center';
			}
			const value = String(deviceValue(base, fallback) || fallback);
			return ['top-left', 'top-center', 'top-right', 'center-left', 'center', 'center-right', 'bottom-left', 'bottom-center', 'bottom-right'].includes(value) ? value : fallback;
		}
		function paginationOffset(coordinate) {
			const axis = direction() === 'vertical' ? 'Vertical' : 'Horizontal';
			const value = String(deviceValue('paginationOffset' + coordinate + axis, '0px') || '0px').trim();
			return /^-?\d+(?:\.\d+)?(?:px|%|em|rem)$/.test(value) ? value : '0px';
		}
		function maximum() { return Math.max(0, slides.length - 1); }
		function normalize(index) {
			const value = Number(index) || 0;
			if (config.loop && maximum() > 0) { if (value > maximum()) return 0; if (value < 0) return maximum(); }
			if (config.rewind && value > maximum()) return 0;
			return Math.max(0, Math.min(maximum(), value));
		}
		function stopTimer() {
			if (timer) window.clearTimeout(timer);
			if (progressTimer) window.clearInterval(progressTimer);
			timer = 0; progressTimer = 0;
		}
		function intervalDuration() { return Math.max(100, Number(config.autoplaySpeed) || 5000); }
		function isLooping(state) { return !!config.videoLoop || state?.slide.getAttribute('data-video-loop') === 'true'; }
		function moveStep() { return Math.max(1, Number(config.perMove) || 1); }
		function startProgress() {
			if (!progress) return;
			const bar = progress.querySelector?.('span');
			if (!bar) return;
			const vertical = direction() === 'vertical';
			let elapsed = 0;
			bar.style.width = vertical ? '100%' : '0%'; bar.style.height = vertical ? '0%' : '100%';
			progressTimer = window.setInterval(function () { elapsed += 100; const value = Math.min(100, elapsed / intervalDuration() * 100); if (vertical) bar.style.height = value + '%'; else bar.style.width = value + '%'; }, 100);
		}
		function canSchedule() { return !!config.autoplay && maximum() > 0 && !prefersReducedMotion() && !interactionPaused && !(config.pauseOnHover && hovered) && !(config.pauseOnFocus && focused); }
		function nextIndex() { const target = active + moveStep(); if (target <= maximum()) return target; if (config.loop || config.rewind) return 0; return maximum(); }
		function previousIndex() { const target = active - moveStep(); if (target >= 0) return target; if (config.loop || config.rewind) return maximum(); return 0; }
		function capture(state) {
			if (!state?.media) return;
			if (state.media.tagName === 'VIDEO' || state.media.tagName === 'AUDIO') state.resumeTime = Number(state.media.currentTime) || 0;
			else if (state.adapter) {
				const value = state.adapter.currentTime?.();
				if (typeof value === 'number') state.resumeTime = value;
				else value?.then?.(function (time) { state.resumeTime = Number(time) || state.resumeTime; });
			}
		}
		function hidePoster(state, hidden) { const poster = state?.slide.querySelector?.('[data-hero-slider-poster]'); poster?.classList?.toggle('is-hidden', hidden); }
		function responsivePoster(state) {
			if (!state?.slide) return '';
			const width = Number(window.innerWidth || document.documentElement?.clientWidth || 0);
			const key = width <= 767 ? 'data-video-poster-mobile' : (width <= 1024 ? 'data-video-poster-tablet' : 'data-video-poster');
			return state.slide.getAttribute(key) || state.slide.getAttribute('data-video-poster') || '';
		}
		function syncPoster(state) {
			const value = responsivePoster(state);
			if (state?.media?.tagName === 'VIDEO' && value) state.media.poster = value;
			const poster = state?.slide.querySelector?.('[data-hero-slider-poster]');
			if (poster && value) poster.style.backgroundImage = 'url("' + value.replace(/"/g, '%22') + '")';
		}
		function markStatus(state, status) { if (!state) return; state.status = status; state.slide.dataset.videoStatus = status; }
		function adapterFor(state) {
			if (!state?.media) return Promise.resolve(null);
			if (state.adapter) return Promise.resolve(state.adapter);
			const provider = state.slide.getAttribute('data-video-provider') || state.media.getAttribute?.('data-video-provider') || (state.media.tagName === 'VIDEO' ? 'self_hosted' : 'embed');
			const callbacks = {
				ended: function () { markStatus(state, 'ended'); hidePoster(state, true); if (active === slides.indexOf(state.slide) && config.videoDurationMode === 'duration' && config.autoplay && state.durationSupported && !isLooping(state)) { state.adapter.pause(); goTo(nextIndex(), 'video-ended'); } else if (active === slides.indexOf(state.slide)) scheduleInterval(); },
				playing: function () { markStatus(state, 'playing'); hidePoster(state, true); },
				paused: function () { if (state.status !== 'ended') markStatus(state, 'paused'); },
				error: function () { state.durationSupported = false; markStatus(state, 'error'); scheduleInterval(); },
				metadata: function () { updateHeight(); },
			};
			state.adapter = provider === 'self_hosted' ? createHeroNativeAdapter(state.media, callbacks) : createHeroIframeAdapter(root, state.media, provider, state.slide, config, callbacks);
			state.durationSupported = !!state.adapter.durationSupported;
			return Promise.resolve(state.adapter);
		}
		function pauseState(state) { if (!state?.media) return; capture(state); if (state.adapter) state.adapter.pause?.(); else if (state.media.tagName === 'VIDEO' || state.media.tagName === 'AUDIO') { try { state.media.pause?.(); } catch (_) {} } if (state.status === 'playing') markStatus(state, 'paused'); }
		function playState(state) {
			return adapterFor(state).then(function (adapter) {
				if (!adapter) return false;
				if (state.resumeTime > 0 && config.videoResume !== false && state.slide.getAttribute('data-video-resume') !== 'false') adapter.seek?.(state.resumeTime);
				if ((config.videoMutedAutoplay || state.slide.getAttribute('data-video-muted') === 'true') && state.muted !== false) adapter.mute?.(true);
				return Promise.resolve(adapter.ready).then(function () { return adapter.play(); }).then(function () { markStatus(state, 'playing'); hidePoster(state, true); return true; });
			});
		}
		function shouldVideoAutoplay(state) {
			const value = state.slide.getAttribute('data-video-autoplay');
			return value === 'true' || (value === '' && !!config.videoAutoplay) || (value === 'inherit' && !!config.videoAutoplay);
		}
		function scheduleInterval() {
			stopTimer();
			waitingForDuration = false;
			if (!canSchedule()) return;
			startProgress();
			timer = window.setTimeout(function () { goTo(nextIndex(), 'interval'); }, intervalDuration());
		}
		function prepareActive() {
			const state = states[active];
			if (!state?.media) { scheduleInterval(); updateHeight(); return; }
			syncPoster(state);
			if (state.adapter && state.resumeTime > 0 && config.videoResume !== false && state.slide.getAttribute('data-video-resume') !== 'false') state.adapter.seek?.(state.resumeTime);
			if (interactionPaused) { updateHeight(); return; }
			adapterFor(state).then(function () {
				if (!shouldVideoAutoplay(state)) { scheduleInterval(); return; }
				return playState(state).then(function () {
					if (config.autoplay && config.videoDurationMode === 'duration' && state.durationSupported && !isLooping(state)) waitingForDuration = true;
					if (!waitingForDuration) scheduleInterval(); else { stopTimer(); startProgress(); }
				});
			}).catch(function () { state.durationSupported = false; markStatus(state, 'error'); scheduleInterval(); });
			updateHeight();
		}
		function render(index) {
			active = normalize(index);
			const currentDirection = direction();
			root.setAttribute('data-direction', currentDirection); if (root.dataset) root.dataset.direction = currentDirection;
			root.setAttribute('aria-orientation', currentDirection);
			if (track) { track.style.transitionDuration = (prefersReducedMotion() ? 0 : Math.max(0, Number(config.transitionSpeed) || 600)) + 'ms'; track.style.transform = config.transition === 'fade' ? '' : (currentDirection === 'vertical' ? 'translate3d(0,-' + (active * 100) + '%,0)' : 'translate3d(-' + (active * 100) + '%,0,0)'); }
			slides.forEach(function (slide, slideIndex) { const selected = slideIndex === active; slide.classList.toggle('is-active', selected); slide.setAttribute('aria-hidden', selected ? 'false' : 'true'); syncPoster(states[slideIndex]); });
			dots.forEach(function (dot, dotIndex) { const selected = dotIndex === active; dot.classList.toggle('is-active', selected); dot.setAttribute('aria-current', selected ? 'true' : 'false'); dot.setAttribute('aria-selected', selected ? 'true' : 'false'); });
			const pagination = root.querySelector(':scope [data-hero-pagination]'); if (pagination) { pagination.setAttribute('data-orientation', currentDirection); pagination.setAttribute('data-position', paginationPosition()); pagination.style.setProperty('--hero-slider-pagination-offset-x', paginationOffset('X')); pagination.style.setProperty('--hero-slider-pagination-offset-y', paginationOffset('Y')); }
			if (previous) previous.disabled = !config.loop && !config.rewind && active === 0;
			if (next) next.disabled = !config.loop && !config.rewind && active === maximum();
			updateHeight();
		}
		function goTo(index, reason) {
			if (['arrow', 'pagination', 'keyboard', 'wheel', 'drag'].includes(reason)) interactionPaused = !!config.pauseOnInteraction;
			const target = normalize(index);
			if (target === active && reason !== 'initial') { if (reason === 'interval') stopTimer(); return; }
			stopTimer(); waitingForDuration = false; pauseState(states[active]); active = target; render(active); prepareActive();
		}
		function updateHeight() {
			if (config.heightMode === 'fixed') return;
			const slide = slides[active]; const state = states[active]; if (!slide) return;
			const minimum = Number.parseFloat(String(deviceValue('minHeight', '0'))) || 0;
			const width = Number(root.clientWidth || root.getBoundingClientRect?.().width || 0); if (!width) return;
			const media = state?.media || slide.querySelector?.('img,video,iframe');
			let height = 0;
			if (media?.tagName === 'IMG' && media.naturalWidth && media.naturalHeight) height = width * media.naturalHeight / media.naturalWidth;
			if (media?.tagName === 'VIDEO' && media.videoWidth && media.videoHeight) height = width * media.videoHeight / media.videoWidth;
			if (!height) { const ratio = String(config.slides?.[active]?.videoAspectRatio || '16/9').split('/').map(Number); height = width * ((ratio[1] || 9) / (ratio[0] || 16)); }
			root.style.transition = prefersReducedMotion() ? 'none' : 'height .3s ease'; root.style.height = Math.max(minimum, Math.round(height)) + 'px';
		}
		function bindControls() {
			slides.forEach(function (slide, slideIndex) {
				const state = states[slideIndex];
				slide.querySelectorAll?.('[data-hero-video-control]').forEach(function (button) {
					button.addEventListener('click', function () {
						const action = button.getAttribute('data-hero-video-control');
						adapterFor(state).then(function (adapter) { if (!adapter) return; if (action === 'mute') { state.muted = !state.muted; return adapter.mute(state.muted); } if (state.status === 'playing') { pauseState(state); } else return playState(state); }).catch(function () { state.durationSupported = false; markStatus(state, 'error'); scheduleInterval(); });
					});
				});
			});
		}
		root.querySelectorAll?.('[data-hero-media]').forEach(function (trigger) {
			trigger.addEventListener('click', function () {
				const styles = window.getComputedStyle ? window.getComputedStyle(root) : null;
				openMediaLightbox(
					trigger.getAttribute('data-media-src'),
					trigger.getAttribute('data-media-type'),
					trigger.getAttribute('data-media-alt'),
					{
						background: styles?.getPropertyValue('--hero-slider-modal-background').trim() || config.modalBackground,
						uiColor: styles?.getPropertyValue('--hero-slider-modal-ui').trim() || config.modalUiColor,
						uiHoverColor: styles?.getPropertyValue('--hero-slider-modal-ui-hover').trim() || config.modalUiHoverColor,
						videoWidth: styles?.getPropertyValue('--hero-slider-modal-video-width').trim() || config.modalVideoWidth,
					},
				);
			});
		});
		previous?.addEventListener('click', function () { goTo(previousIndex(), 'arrow'); });
		next?.addEventListener('click', function () { goTo(nextIndex(), 'arrow'); });
		dots.forEach(function (dot) { dot.addEventListener('click', function () { goTo(dot.getAttribute('data-index') ?? dot.dataset.index, 'pagination'); }); });
		root.addEventListener('mouseenter', function () { hovered = true; if (!waitingForDuration) scheduleInterval(); });
		root.addEventListener('mouseleave', function () { hovered = false; if (!waitingForDuration) scheduleInterval(); });
		root.addEventListener('focusin', function () { focused = true; if (!waitingForDuration) stopTimer(); });
		root.addEventListener('focusout', function () { focused = false; if (!waitingForDuration) scheduleInterval(); });
		root.addEventListener('keydown', function (event) {
			if (config.keyboard === false) return;
			const vertical = direction() === 'vertical';
			if ((!vertical && event.key === 'ArrowLeft') || (vertical && event.key === 'ArrowUp')) { event.preventDefault(); goTo(previousIndex(), 'keyboard'); }
			if ((!vertical && event.key === 'ArrowRight') || (vertical && event.key === 'ArrowDown')) { event.preventDefault(); goTo(nextIndex(), 'keyboard'); }
			if (event.key === 'Home') { event.preventDefault(); goTo(0, 'keyboard'); }
			if (event.key === 'End') { event.preventDefault(); goTo(maximum(), 'keyboard'); }
		});
		root.addEventListener('wheel', function (event) {
			if (!config.mouseWheel) return;
			const delta = direction() === 'vertical' ? event.deltaY : event.deltaX || event.deltaY;
			if (Math.abs(delta) < 10) return;
			if (!config.wheelRelease) event.preventDefault?.();
			goTo(delta > 0 ? nextIndex() : previousIndex(), 'wheel');
		}, { passive: !config.wheelRelease });
		root.addEventListener('pointerdown', function (event) { if (config.drag === false) return; pointerStart = { x: event.clientX || 0, y: event.clientY || 0 }; root.setPointerCapture?.(event.pointerId); });
		root.addEventListener('pointerup', function (event) { if (!pointerStart || config.drag === false) return; const dx = (event.clientX || 0) - pointerStart.x; const dy = (event.clientY || 0) - pointerStart.y; const distance = direction() === 'vertical' ? dy : dx; pointerStart = null; if (Math.abs(distance) >= 35) goTo(distance < 0 ? nextIndex() : previousIndex(), 'drag'); });
		slides.forEach(function (slide, slideIndex) { slide.querySelectorAll?.('img').forEach(function (image) { image.addEventListener?.('load', updateHeight); }); slide.querySelectorAll?.('video').forEach(function (video) { video.addEventListener?.('loadedmetadata', updateHeight); }); syncPoster(states[slideIndex]); });
		window.addEventListener('resize', function () { render(active); if (!waitingForDuration) scheduleInterval(); }, { passive: true });
		root.setAttribute('tabindex', root.getAttribute('tabindex') || '0');
		bindControls(); render(0); prepareActive(); if (!waitingForDuration) scheduleInterval();
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
		rootScope.querySelectorAll('[data-hero-banner]').forEach(initHeroBanner);
		rootScope.querySelectorAll('[data-product-color-selector]').forEach(initProductColorSelector);
		rootScope.querySelectorAll('[data-hero-slider]').forEach(initHeroSlider);
	}

	window.PageBuilderElementorV23Runtime = Object.freeze({ init, bindAccordion, bindImageCarousel, bindBasicGallery, bindBasicImage, bindAdvancedWidget, openMediaLightbox, initProSlides, initProCarousel, initProCountdown, initProProgressTracker, initProVideoPlaylist, initProHotspot, initProFlipBox, initProForm, initProAnimatedHeadline, initProCodeHighlight, initProShareButtons, initHeroBanner, initProductColorSelector, initHeroSlider });

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
	} else {
		init(document);
	}
})();
