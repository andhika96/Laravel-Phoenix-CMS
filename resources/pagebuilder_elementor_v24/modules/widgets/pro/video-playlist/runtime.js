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

function parseProConfig(root) {
		try { return JSON.parse(root.getAttribute('data-pro-config') || '{}'); }
		catch (_) { return {}; }
	}

function markProReady(root, kind) {
		if (!root || root.getAttribute('data-pb-pro-ready')) return false;
		root.setAttribute('data-pb-pro-ready', kind);
		return true;
	}

function initProVideoPlaylist(root) {
		if (!markProReady(root, 'video-playlist')) return;
		const config = parseProConfig(root);
		const items = Array.isArray(config.items) ? config.items : [];
		const player = root.querySelector(':scope [data-playlist-player]');
		const list = Array.from(root.querySelectorAll(':scope [data-playlist-index]'));
		const playlistList = root.querySelector(':scope .pb-pro-video-playlist__items');
		const dropdownToggle = root.querySelector(':scope [data-playlist-dropdown-toggle]');
		const tabs = root.querySelector(':scope [data-playlist-tabs]');
		let active = 0;
		let activeTab = 0;
		let tabsExpanded = true;
		let dropdownExpanded = true;
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
		function iconSvg(index) {
			return config.indicateWatched && watched.has(index) ? String(config.playedIconSvg || '') : String(config.playIconSvg || '');
		}
		function renderItemIcon(container, index) {
			if (!container) return;
			container.replaceChildren();
			const svg = iconSvg(index);
			if (svg) {
				const span = document.createElement('span');
				span.className = 'pb-pro-icon-svg';
				span.innerHTML = svg;
				container.append(span);
				return;
			}
			const className = iconClass(index);
			if (!className) return;
			const icon = document.createElement('i');
			icon.className = className;
			container.append(icon);
		}
		function renderDropdown() {
			if (playlistList) playlistList.hidden = !dropdownExpanded;
			if (!dropdownToggle) return;
			dropdownToggle.classList.toggle('is-active', dropdownExpanded);
			dropdownToggle.setAttribute('aria-expanded', dropdownExpanded ? 'true' : 'false');
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
				button.querySelectorAll(':scope [data-playlist-item-icon]').forEach(function (container) { renderItemIcon(container, index); });
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
		dropdownToggle?.addEventListener('click', function () { dropdownExpanded = !dropdownExpanded; renderDropdown(); });
		renderDropdown();
		select(0);
	}

	function init(scope) {
		const root = scope && typeof scope.querySelectorAll === 'function' ? scope : document;
		root.querySelectorAll("[data-video-playlist]").forEach(initProVideoPlaylist);
	}

	const runtimes = window.PageBuilderElementorV24ModuleRuntimes ||= {};
	runtimes["video_playlist"] = Object.freeze({ init, initProVideoPlaylist });

	if (typeof document !== 'undefined') {
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function () { init(document); }, { once: true });
		} else {
			init(document);
		}
	}
})(window.PageBuilderElementorV24Runtime);
