(function () {
	'use strict';

	const reducedMotionQuery = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : null;
	const motionEntries = new Map();
	let motionFrame = 0;
	let motionListenersBound = false;
	let entranceObserver = null;

function prefersReducedMotion() {
		return !!(reducedMotionQuery && reducedMotionQuery.matches);
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
		overlay.className = ['pb-media-lightbox', String(settings.className || '').trim()].filter(Boolean).join(' ');
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

function init(scope) {
		const rootScope = scope && scope.querySelectorAll ? scope : document;
		rootScope.querySelectorAll('[data-pb-motion]').forEach(bindAdvancedWidget);
	}

	window.PageBuilderElementorV24Runtime = Object.freeze({
		init,
		bindAdvancedWidget,
		openMediaLightbox,
		prefersReducedMotion,
	});

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})();
