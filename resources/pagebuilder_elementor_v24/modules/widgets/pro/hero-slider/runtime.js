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
		function slideResponsiveValue(slideConfig, base, fallback = '') {
			const width = Number(window.innerWidth || document.documentElement?.clientWidth || 0);
			const suffix = width <= 767 ? 'Mobile' : (width <= 1024 ? 'Tablet' : '');
			const keys = suffix === 'Mobile' ? [base + 'Mobile', base + 'Tablet', base] : (suffix === 'Tablet' ? [base + 'Tablet', base] : [base]);
			for (const key of keys) if (slideConfig?.[key] !== '' && slideConfig?.[key] != null) return slideConfig[key];
			return fallback;
		}
		function imageLayoutFor(index) {
			const slideConfig = config.slides?.[index] || {};
			if (String(slideConfig.mediaType || 'image') !== 'image') return 'cover';
			return slideResponsiveValue(slideConfig, 'imageLayout', 'cover') === 'natural' ? 'natural' : 'cover';
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
			const imageLayout = imageLayoutFor(active);
			root.classList?.toggle('is-natural-image', imageLayout === 'natural');
			root.setAttribute('data-hero-image-layout', imageLayout);
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
			const slide = slides[active]; const state = states[active]; if (!slide) return;
			const naturalImage = imageLayoutFor(active) === 'natural';
			if (config.heightMode === 'fixed' && !naturalImage) {
				root.style.height = deviceValue('fixedHeight', '520px');
				root.style.minHeight = '';
				return;
			}
			const minimum = naturalImage ? 0 : (Number.parseFloat(String(deviceValue('minHeight', '0'))) || 0);
			const width = Number(root.clientWidth || root.getBoundingClientRect?.().width || 0); if (!width) return;
			const media = state?.media || slide.querySelector?.('img,video,iframe');
			let height = 0;
			if (media?.tagName === 'IMG' && media.naturalWidth && media.naturalHeight) height = width * media.naturalHeight / media.naturalWidth;
			if (media?.tagName === 'VIDEO' && media.videoWidth && media.videoHeight) height = width * media.videoHeight / media.videoWidth;
			if (!height) { const ratio = String(config.slides?.[active]?.videoAspectRatio || '16/9').split('/').map(Number); height = width * ((ratio[1] || 9) / (ratio[0] || 16)); }
			root.style.transition = prefersReducedMotion() ? 'none' : 'height .3s ease'; root.style.minHeight = naturalImage ? '0px' : ''; root.style.height = Math.max(minimum, Math.round(height)) + 'px';
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
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-hero-slider]").forEach(initHeroSlider);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["hero_slider"] = Object.freeze({ init, initHeroSlider });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
