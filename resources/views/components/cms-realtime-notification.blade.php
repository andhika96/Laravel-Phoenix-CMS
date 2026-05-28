{{--
	cms-realtime-notification.blade.php

	Komponen real-time notification bell untuk CMS Phoenix.
	Di-include di dalam cms_layout (semua themes).

	Mendengarkan channel: cms-notifications via Pusher.js CDN + Laravel Reverb.
	Event yang didukung:
		- user.registered   → user baru dibuat
		- user.updated      → data user diperbarui
		- article.created   → artikel baru dibuat
		- article.updated   → artikel disunting
--}}

{{-- ====== BELL ICON BUTTON (masuk di topbar) ====== --}}
<div class="dropdown position-relative me-1" id="cms-notif-wrapper">
	<button
		class="ph-btn-action-icon position-relative"
		type="button"
		id="cmsNotifBell"
		data-bs-toggle="dropdown"
		data-bs-auto-close="outside"
		aria-expanded="false"
		title="Notifikasi"
	>
		<i class="fas fa-bell"></i>
		<span
			class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cms-notif-badge d-none"
			id="cmsNotifCount"
			style="font-size: 0.6rem; min-width: 18px; padding: 3px 5px;"
		>0</span>
	</button>

	<div class="dropdown-menu dropdown-menu-end p-0 shadow cms-notif-dropdown" id="cmsNotifDropdown" style="min-width: 340px; max-width: 380px;">
		<div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
			<span class="fw-semibold" style="font-size: 0.9rem;">Notifikasi</span>
			<button class="btn btn-link btn-sm p-0 text-muted text-decoration-none" id="cmsNotifClearAll" style="font-size: 0.75rem;">Hapus semua</button>
		</div>

		<div id="cmsNotifList" style="max-height: 360px; overflow-y: auto;">
			<div class="text-center text-muted py-4" id="cmsNotifEmpty" style="font-size: 0.82rem;">
				<i class="fas fa-bell-slash mb-2 d-block" style="font-size: 1.4rem; opacity: 0.4;"></i>
				Tidak ada notifikasi
			</div>
		</div>
	</div>
</div>

{{-- ====== TOAST CONTAINER ====== --}}
<div
	id="cmsToastContainer"
	class="position-fixed"
	style="top: 70px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none;"
	aria-live="polite"
></div>

{{-- ====== CSS ====== --}}
<style>
.cms-notif-dropdown {
	border-radius: 10px;
	overflow: hidden;
}

.cms-notif-item {
	display: flex;
	align-items: flex-start;
	gap: 10px;
	padding: 10px 14px;
	border-bottom: 1px solid var(--bs-border-color, rgba(0,0,0,.08));
	cursor: default;
	transition: background 0.15s;
}
.cms-notif-item:last-child { border-bottom: none; }
.cms-notif-item:hover { background: var(--ph-hover-bg, rgba(0,0,0,.04)); }

.cms-notif-icon {
	width: 36px;
	height: 36px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-shrink: 0;
	font-size: 0.85rem;
}
.cms-notif-icon.user-registered  { background: rgba(16,185,129,.15);  color: #10b981; }
.cms-notif-icon.user-updated     { background: rgba(59,130,246,.15);  color: #3b82f6; }
.cms-notif-icon.article-created  { background: rgba(168,85,247,.15);  color: #a855f7; }
.cms-notif-icon.article-updated  { background: rgba(245,158,11,.15);  color: #f59e0b; }

.cms-notif-body { flex: 1; min-width: 0; }
.cms-notif-title {
	font-size: 0.8rem;
	font-weight: 600;
	margin-bottom: 2px;
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.cms-notif-msg {
	font-size: 0.76rem;
	color: var(--bs-secondary-color, #6c757d);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
}
.cms-notif-time {
	font-size: 0.68rem;
	color: var(--bs-secondary-color, #6c757d);
	margin-top: 3px;
}

/* Toast */
.cms-toast {
	pointer-events: all;
	min-width: 300px;
	max-width: 360px;
	background: var(--bs-body-bg, #fff);
	border: 1px solid var(--bs-border-color, rgba(0,0,0,.12));
	border-radius: 10px;
	box-shadow: 0 8px 24px rgba(0,0,0,.15);
	padding: 12px 14px;
	display: flex;
	align-items: flex-start;
	gap: 10px;
	animation: cmsToastIn 0.3s ease;
	transition: opacity 0.4s ease, transform 0.4s ease;
}
.cms-toast.hiding {
	opacity: 0;
	transform: translateX(20px);
}
@keyframes cmsToastIn {
	from { opacity: 0; transform: translateX(20px); }
	to   { opacity: 1; transform: translateX(0); }
}
.cms-toast-body { flex: 1; min-width: 0; }
.cms-toast-title {
	font-size: 0.8rem;
	font-weight: 600;
	margin-bottom: 2px;
}
.cms-toast-msg {
	font-size: 0.76rem;
	color: var(--bs-secondary-color, #6c757d);
}
.cms-toast-close {
	background: none;
	border: none;
	cursor: pointer;
	font-size: 0.9rem;
	color: var(--bs-secondary-color, #6c757d);
	padding: 0;
	line-height: 1;
	flex-shrink: 0;
}
</style>

{{-- ====== JS: Pusher.js CDN + Realtime Logic ====== --}}
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script>
(function() {
	// ── CONFIG ─────────────────────────────────────────────
	@php
		$reverbScheme = env('REVERB_SCHEME', 'http');
		$reverbPort   = $reverbScheme === 'https' ? 443 : (int) env('REVERB_SERVER_PORT', 9001);
	@endphp
	const REVERB_KEY    = '{{ env("REVERB_APP_KEY", "cms-main-key") }}';
	const REVERB_HOST   = '{{ env("REVERB_HOST", "laravel-12-phoenix.aruna") }}';
	const REVERB_PORT   = {{ $reverbPort }};
	const REVERB_TLS    = '{{ $reverbScheme }}' === 'https';
	const MAX_NOTIF     = 30;

	// ── STATE ──────────────────────────────────────────────
	let notifications = [];
	let unreadCount   = 0;

	// ── DOM ────────────────────────────────────────────────
	const badge      = document.getElementById('cmsNotifCount');
	const list       = document.getElementById('cmsNotifList');
	const empty      = document.getElementById('cmsNotifEmpty');
	const clearBtn   = document.getElementById('cmsNotifClearAll');
	const toastCont  = document.getElementById('cmsToastContainer');

	// ── ICON MAP ───────────────────────────────────────────
	const iconMap = {
		'user_registered' : { css: 'user-registered',  fa: 'fas fa-user-plus' },
		'user_updated'    : { css: 'user-updated',      fa: 'fas fa-user-edit' },
		'article_created' : { css: 'article-created',   fa: 'fas fa-file-alt' },
		'article_updated' : { css: 'article-updated',   fa: 'fas fa-file-signature' },
	};

	// ── HELPERS ────────────────────────────────────────────
	function timeAgo(dateStr) {
		const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
		if (diff < 60)   return 'Baru saja';
		if (diff < 3600) return Math.floor(diff / 60) + ' mnt lalu';
		if (diff < 86400)return Math.floor(diff / 3600) + ' jam lalu';
		return Math.floor(diff / 86400) + ' hari lalu';
	}

	function updateBadge() {
		if (unreadCount > 0) {
			badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
			badge.classList.remove('d-none');
		} else {
			badge.classList.add('d-none');
		}
	}

	function renderList() {
		if (notifications.length === 0) {
			list.innerHTML = '';
			list.appendChild(empty);
			return;
		}

		list.innerHTML = '';
		notifications.forEach(n => {
			const icon = iconMap[n.type] || { css: 'article-created', fa: 'fas fa-bell' };
			const el   = document.createElement('div');
			el.className = 'cms-notif-item';
			el.innerHTML = `
				<div class="cms-notif-icon ${icon.css}">
					<i class="${icon.fa}"></i>
				</div>
				<div class="cms-notif-body">
					<div class="cms-notif-title">${n.title}</div>
					<div class="cms-notif-msg">${n.message}</div>
					<div class="cms-notif-time">${timeAgo(n.created_at)}</div>
				</div>
			`;
			list.appendChild(el);
		});
	}

	function showToast(n) {
		const icon = iconMap[n.type] || { css: 'article-created', fa: 'fas fa-bell' };
		const toast = document.createElement('div');
		toast.className = 'cms-toast';
		toast.innerHTML = `
			<div class="cms-notif-icon ${icon.css}" style="flex-shrink:0;">
				<i class="${icon.fa}"></i>
			</div>
			<div class="cms-toast-body">
				<div class="cms-toast-title">${n.title}</div>
				<div class="cms-toast-msg">${n.message}</div>
			</div>
			<button class="cms-toast-close" title="Tutup"><i class="fas fa-times"></i></button>
		`;

		toast.querySelector('.cms-toast-close').addEventListener('click', () => dismissToast(toast));
		toastCont.appendChild(toast);

		// Auto-dismiss setelah 5 detik
		setTimeout(() => dismissToast(toast), 5000);
	}

	function dismissToast(toast) {
		if (!toast.parentNode) return;
		toast.classList.add('hiding');
		setTimeout(() => { if (toast.parentNode) toast.remove(); }, 400);
	}

	function addNotification(data) {
		// Simpan ke state
		notifications.unshift(data);
		if (notifications.length > MAX_NOTIF) notifications.pop();

		// Update unread
		const isDropdownOpen = document.getElementById('cmsNotifDropdown').classList.contains('show');
		if (!isDropdownOpen) {
			unreadCount++;
			updateBadge();
		}

		// Render ulang list
		renderList();

		// Tampilkan toast
		showToast(data);
	}

	// Reset unread saat dropdown dibuka
	document.getElementById('cmsNotifBell').addEventListener('click', function() {
		unreadCount = 0;
		updateBadge();
	});

	// Hapus semua
	clearBtn.addEventListener('click', function() {
		notifications = [];
		unreadCount   = 0;
		updateBadge();
		renderList();
	});

	// ── REVERB CONNECTION — mirror config filemanager.blade.php ──
	const cmsReverb = new Pusher(REVERB_KEY, {
		wsHost:            REVERB_HOST,
		wsPort:            REVERB_PORT,
		wssPort:           REVERB_PORT,
		forceTLS:          REVERB_TLS,
		disableStats:      true,
		enabledTransports: REVERB_TLS ? ['wss'] : ['ws'],
		cluster:           '',
	});

	cmsReverb.connection.bind('connected', function() {
		console.log('✅ CMS Notifications: Reverb connected');
	});

	cmsReverb.connection.bind('error', function(err) {
		console.warn('❌ CMS Notifications: Reverb error', err);
	});

	// Subscribe ke public channel
	const channel = cmsReverb.subscribe('cms-notifications');

	// ── EVENT LISTENERS ────────────────────────────────────

	// User baru terdaftar
	channel.bind('user.registered', function(data) {
		addNotification(data);
	});

	// User diperbarui
	channel.bind('user.updated', function(data) {
		addNotification(data);
	});

	// Artikel baru dibuat
	channel.bind('article.created', function(data) {
		addNotification(data);
	});

	// Artikel disunting
	channel.bind('article.updated', function(data) {
		addNotification(data);
	});

	// Expose ke window (opsional, untuk debugging)
	window.cmsReverb = cmsReverb;

})();
</script>
