@extends('themes.'.custom_theme('cms'))

@push('css')
{{-- Override layout padding agar chat bisa full-height tanpa scroll --}}
<style>
.ph-scrollable-content {
	padding: 0 !important;
	overflow: hidden !important;
	display: flex;
	flex-direction: column;
}
</style>
@endpush

@section('content')
<div id="chat-app" class="ph-chat-app" v-cloak>

	{{-- ════════════════════════════════════════════
		SIDEBAR KIRI — Daftar Percakapan
	════════════════════════════════════════════ --}}
	<div class="ph-chat-sidebar" :class="{ 'ph-chat-sidebar--hidden': activeConversation && isMobile }">

		{{-- Header --}}
		<div class="ph-chat-sidebar-header">
			<h5 class="ph-chat-sidebar-title">Messages</h5>
			<button class="ph-chat-icon-btn" @click="openNewChat" title="New Chat">
				<i class="fas fa-edit"></i>
			</button>
		</div>

		{{-- Search --}}
		<div class="ph-chat-search-wrapper">
			<i class="fas fa-search ph-chat-search-icon"></i>
			<input
				v-model="searchQuery"
				@input="onSearchInput"
				class="ph-chat-search-input"
				placeholder="Search messages..."
				type="text"
			/>
		</div>

		{{-- Tab filter --}}
		<div class="ph-chat-tabs">
			<button
				v-for="tab in tabs"
				:key="tab.key"
				class="ph-chat-tab"
				:class="{ active: activeTab === tab.key }"
				@click="activeTab = tab.key"
			>@{{ tab.label }}</button>
		</div>

		{{-- Conversation list --}}
		<div class="ph-chat-conv-list" ref="convListRef">

			{{-- Loading skeleton --}}
			<template v-if="loadingConversations">
				<div v-for="i in 5" :key="i" class="ph-chat-conv-item ph-chat-skeleton">
					<div class="ph-chat-avatar-skeleton"></div>
					<div class="ph-chat-conv-body">
						<div class="ph-skel-line w-60"></div>
						<div class="ph-skel-line w-40 mt-1"></div>
					</div>
				</div>
			</template>

			{{-- Empty state --}}
			<div v-else-if="filteredConversations.length === 0 && !showUserSearch" class="ph-chat-empty-list">
				<i class="fas fa-comment-slash"></i>
				<p>Belum ada percakapan</p>
				<button class="ph-chat-btn-primary" @click="openNewChat">Mulai Chat Baru</button>
			</div>

			{{-- User search results --}}
			<template v-if="showUserSearch">
				<div class="ph-chat-section-label">Pengguna</div>
				<div v-if="loadingUsers" class="ph-chat-empty-list" style="padding:16px">
					<i class="fas fa-spinner fa-spin"></i>
				</div>
				<div
					v-for="user in searchedUsers"
					:key="'u'+user.id"
					class="ph-chat-conv-item"
					@click="startChatWith(user)"
				>
					<div class="ph-chat-avatar" :style="user.avatar_url ? '' : 'background:'+getColor(user.fullname)">
						<img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.fullname" />
						<span v-else>@{{ user.initials }}</span>
					</div>
					<div class="ph-chat-conv-body">
						<div class="ph-chat-conv-name">@{{ user.fullname }}</div>
						<div class="ph-chat-conv-preview">@{{ user.email }}</div>
					</div>
				</div>
				<div class="ph-chat-section-label mt-2" v-if="filteredConversations.length">Percakapan</div>
			</template>

			{{-- Conversation list --}}
			<div
				v-for="conv in filteredConversations"
				:key="conv.id"
				class="ph-chat-conv-item"
				:class="{ active: activeConversation && activeConversation.id === conv.id }"
				@click="selectConversation(conv)"
			>
				<div class="ph-chat-avatar-wrapper">
					<div class="ph-chat-avatar" :style="conv.other_user.avatar_url ? '' : 'background:'+getColor(conv.other_user.fullname)">
						<img v-if="conv.other_user.avatar_url" :src="conv.other_user.avatar_url" :alt="conv.other_user.fullname" />
						<span v-else>@{{ conv.other_user.initials }}</span>
					</div>
					<span v-if="onlineUsers.includes(conv.other_user.id)" class="ph-chat-online-dot"></span>
				</div>
				<div class="ph-chat-conv-body">
					<div class="ph-chat-conv-row">
						<span class="ph-chat-conv-name">@{{ conv.other_user.fullname }}</span>
						<span class="ph-chat-conv-time" v-if="conv.last_message">@{{ conv.last_message.created_at }}</span>
					</div>
					<div class="ph-chat-conv-row">
						<span class="ph-chat-conv-preview">
							<template v-if="conv.last_message">
								<span v-if="conv.last_message.sender_id === currentUserId" class="text-muted">You: </span>
								@{{ conv.last_message.body }}
							</template>
							<span v-else class="text-muted fst-italic">Belum ada pesan</span>
						</span>
						<span v-if="conv.unread_count > 0" class="ph-chat-unread-badge">@{{ conv.unread_count }}</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- ════════════════════════════════════════════
		PANEL KANAN — Area Chat
	════════════════════════════════════════════ --}}
	<div class="ph-chat-panel" :class="{ 'ph-chat-panel--active': activeConversation || isMobile }">

		{{-- Empty state: belum pilih conversation --}}
		<div v-if="!activeConversation" class="ph-chat-panel-empty">
			<div class="ph-chat-panel-empty-inner">
				<div class="ph-chat-panel-empty-icon">
					<i class="fas fa-comments"></i>
				</div>
				<h5>Pilih percakapan</h5>
				<p>Pilih kontak di kiri untuk mulai chat, atau mulai percakapan baru.</p>
				<button class="ph-chat-btn-primary" @click="openNewChat">
					<i class="fas fa-plus me-1"></i> Chat Baru
				</button>
			</div>
		</div>

		{{-- Chat area aktif --}}
		<template v-if="activeConversation">

			{{-- Topbar panel --}}
			<div class="ph-chat-panel-header">
				<button class="ph-chat-icon-btn me-2 d-md-none" @click="activeConversation = null">
					<i class="fas fa-arrow-left"></i>
				</button>
				<div class="ph-chat-avatar sm" :style="activeConversation.other_user.avatar_url ? '' : 'background:'+getColor(activeConversation.other_user.fullname)">
					<img v-if="activeConversation.other_user.avatar_url" :src="activeConversation.other_user.avatar_url" />
					<span v-else>@{{ activeConversation.other_user.initials }}</span>
				</div>
				<div class="ms-2">
					<div class="ph-chat-panel-name">@{{ activeConversation.other_user.fullname }}</div>
					<div class="ph-chat-panel-status">
						<span v-if="typingUsers[activeConversation.id]" class="ph-typing-indicator">
							<span></span><span></span><span></span> mengetik...
						</span>
						<span v-else-if="onlineUsers.includes(activeConversation.other_user.id)" class="text-success" style="font-size:0.72rem">● Online</span>
						<span v-else style="font-size:0.72rem; color: var(--ph-text-muted)">@{{ activeConversation.other_user.email }}</span>
					</div>
				</div>
				<div class="ms-auto d-flex gap-2">
					<button class="ph-chat-icon-btn" title="Info"><i class="fas fa-info-circle"></i></button>
				</div>
			</div>

			{{-- Message list --}}
			<div class="ph-chat-messages" ref="messagesRef" @scroll="onMessagesScroll">

				{{-- Load more --}}
				<div v-if="hasMoreMessages" class="text-center py-2">
					<button class="ph-chat-load-more-btn" @click="loadMoreMessages" :disabled="loadingMessages">
						<i class="fas fa-spinner fa-spin me-1" v-if="loadingMessages"></i>
						Muat pesan lama
					</button>
				</div>

				{{-- Loading --}}
				<div v-if="loadingMessages && messages.length === 0" class="ph-chat-messages-loading">
					<i class="fas fa-spinner fa-spin"></i>
				</div>

				{{-- Messages grouped by date --}}
				<template v-for="(group, date) in groupedMessages" :key="date">
					<div class="ph-chat-date-divider">
						<span>@{{ formatDateLabel(date) }}</span>
					</div>

					<div
						v-for="msg in group"
						:key="msg.id"
						class="ph-chat-msg-row"
						:class="msg.is_mine ? 'ph-chat-msg-row--mine' : 'ph-chat-msg-row--theirs'"
					>
						{{-- Avatar (hanya pesan lawan) --}}
						<div
							v-if="!msg.is_mine"
							class="ph-chat-avatar xs"
							:style="msg.sender_avatar ? '' : 'background:'+getColor(msg.sender_name)"
						>
							<img v-if="msg.sender_avatar" :src="msg.sender_avatar" />
							<span v-else>@{{ getInitials(msg.sender_name) }}</span>
						</div>

						<div class="ph-chat-bubble-wrapper">
							<div class="ph-chat-bubble" :class="msg.is_mine ? 'ph-chat-bubble--mine' : 'ph-chat-bubble--theirs'">
								@{{ msg.body }}
							</div>
							<div class="ph-chat-bubble-meta">
								<span>@{{ msg.created_at }}</span>
								<i v-if="msg.is_mine" class="fas fa-check-double ms-1" :class="msg.read_at ? 'text-primary' : 'text-muted'" style="font-size:0.7rem"></i>
							</div>
						</div>
					</div>
				</template>

				{{-- Typing indicator bubble --}}
				<div v-if="typingUsers[activeConversation.id]" class="ph-chat-msg-row ph-chat-msg-row--theirs">
					<div class="ph-chat-avatar xs" :style="'background:'+getColor(activeConversation.other_user.fullname)">
						<img v-if="activeConversation.other_user.avatar_url" :src="activeConversation.other_user.avatar_url" />
						<span v-else>@{{ activeConversation.other_user.initials }}</span>
					</div>
					<div class="ph-chat-bubble ph-chat-bubble--theirs ph-typing-bubble">
						<span></span><span></span><span></span>
					</div>
				</div>
			</div>

			{{-- Input area --}}
			<div class="ph-chat-input-area">
				<button class="ph-chat-icon-btn" title="Emoji" style="opacity:0.5">
					<i class="fas fa-smile"></i>
				</button>
				<div
					class="ph-chat-input"
					contenteditable="true"
					ref="inputRef"
					:data-placeholder="'Tulis pesan ke ' + activeConversation.other_user.fullname + '...'"
					@keydown.enter.exact.prevent="sendMessage"
					@input="onInputChange"
					@paste.prevent="onPaste"
				></div>
				<button class="ph-chat-icon-btn" title="Mic" style="opacity:0.5">
					<i class="fas fa-microphone"></i>
				</button>
				<button
					class="ph-chat-send-btn"
					@click="sendMessage"
					:disabled="!canSend || sending"
					:class="{ active: canSend }"
				>
					<i class="fas fa-paper-plane" v-if="!sending"></i>
					<i class="fas fa-spinner fa-spin" v-else></i>
				</button>
			</div>

		</template>
	</div>

</div>
@endsection

@push('css')
<style>
/* ═══════════════════════════════════════════════════════
   CMS CHAT — Phoenix Style
═══════════════════════════════════════════════════════ */
[v-cloak] { display: none !important; }

.ph-chat-app {
	display: flex;
	height: calc(100vh - var(--ph-topbar-height, 60px));
	overflow: hidden;
	background: var(--bs-body-bg);
	border-radius: 0;
}

/* ── Sidebar ─────────────────────────────────────── */
.ph-chat-sidebar {
	width: 280px;
	min-width: 260px;
	max-width: 300px;
	display: flex;
	flex-direction: column;
	border-right: 1px solid var(--bs-border-color, rgba(0,0,0,.1));
	background: var(--bs-body-bg);
	overflow: hidden;
	flex-shrink: 0;
}

.ph-chat-sidebar-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 18px 16px 10px;
}

.ph-chat-sidebar-title {
	font-size: 1.05rem;
	font-weight: 700;
	margin: 0;
}

.ph-chat-search-wrapper {
	position: relative;
	padding: 0 12px 10px;
}
.ph-chat-search-icon {
	position: absolute;
	left: 22px;
	top: 50%;
	transform: translateY(-60%);
	color: var(--bs-secondary-color, #888);
	font-size: 0.8rem;
}
.ph-chat-search-input {
	width: 100%;
	background: var(--ph-input-bg, var(--bs-secondary-bg));
	border: 1px solid transparent;
	border-radius: 20px;
	padding: 7px 12px 7px 32px;
	font-size: 0.82rem;
	outline: none;
	color: var(--bs-body-color);
	transition: border-color .2s;
}
.ph-chat-search-input:focus {
	border-color: var(--ph-theme-primary, #f97316);
}

.ph-chat-tabs {
	display: flex;
	gap: 4px;
	padding: 0 12px 8px;
	overflow-x: auto;
}
.ph-chat-tab {
	background: none;
	border: none;
	font-size: 0.78rem;
	color: var(--bs-secondary-color, #888);
	padding: 4px 10px;
	border-radius: 20px;
	cursor: pointer;
	white-space: nowrap;
	transition: all .15s;
}
.ph-chat-tab.active {
	background: var(--ph-theme-primary, #f97316);
	color: #fff;
	font-weight: 600;
}

.ph-chat-conv-list {
	flex: 1;
	overflow-y: auto;
}
.ph-chat-conv-list::-webkit-scrollbar { width: 4px; }
.ph-chat-conv-list::-webkit-scrollbar-thumb { background: var(--bs-border-color); border-radius: 4px; }

.ph-chat-section-label {
	font-size: 0.7rem;
	font-weight: 700;
	color: var(--bs-secondary-color);
	text-transform: uppercase;
	letter-spacing: .06em;
	padding: 6px 16px 2px;
}

.ph-chat-conv-item {
	display: flex;
	align-items: center;
	gap: 10px;
	padding: 10px 14px;
	cursor: pointer;
	transition: background .15s;
	position: relative;
}
.ph-chat-conv-item:hover,
.ph-chat-conv-item.active {
	background: var(--ph-hover-bg, rgba(0,0,0,.05));
}
[data-bs-theme="dark"] .ph-chat-conv-item.active {
	background: rgba(255,255,255,.07);
}

.ph-chat-avatar-wrapper { position: relative; flex-shrink: 0; }

.ph-chat-avatar {
	width: 42px; height: 42px;
	border-radius: 50%;
	display: flex; align-items: center; justify-content: center;
	font-size: 0.8rem;
	font-weight: 700;
	color: #fff;
	overflow: hidden;
	flex-shrink: 0;
}
.ph-chat-avatar.sm { width: 36px; height: 36px; font-size: 0.72rem; }
.ph-chat-avatar.xs { width: 30px; height: 30px; font-size: 0.68rem; flex-shrink: 0; align-self: flex-end; }
.ph-chat-avatar img { width: 100%; height: 100%; object-fit: cover; }

.ph-chat-online-dot {
	position: absolute;
	bottom: 1px; right: 1px;
	width: 10px; height: 10px;
	background: #22c55e;
	border-radius: 50%;
	border: 2px solid var(--bs-body-bg);
}

.ph-chat-conv-body { flex: 1; min-width: 0; }
.ph-chat-conv-row { display: flex; justify-content: space-between; align-items: baseline; gap: 4px; }
.ph-chat-conv-name { font-size: 0.84rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ph-chat-conv-time { font-size: 0.68rem; color: var(--bs-secondary-color); flex-shrink: 0; }
.ph-chat-conv-preview {
	font-size: 0.75rem;
	color: var(--bs-secondary-color);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	flex: 1;
}
.ph-chat-unread-badge {
	background: var(--ph-theme-primary, #f97316);
	color: #fff;
	border-radius: 10px;
	padding: 1px 6px;
	font-size: 0.68rem;
	font-weight: 700;
	flex-shrink: 0;
}

.ph-chat-empty-list {
	display: flex; flex-direction: column; align-items: center;
	padding: 40px 20px; gap: 10px;
	color: var(--bs-secondary-color);
}
.ph-chat-empty-list i { font-size: 2rem; opacity: .4; }
.ph-chat-empty-list p { font-size: 0.82rem; margin: 0; text-align: center; }

/* Skeleton */
.ph-chat-skeleton .ph-chat-avatar-skeleton {
	width: 42px; height: 42px; border-radius: 50%;
	background: var(--bs-secondary-bg);
	animation: shimmer 1.2s infinite;
	flex-shrink: 0;
}
.ph-skel-line {
	height: 10px; border-radius: 6px;
	background: var(--bs-secondary-bg);
	animation: shimmer 1.2s infinite;
}
.ph-skel-line.w-60 { width: 60%; }
.ph-skel-line.w-40 { width: 40%; }
@keyframes shimmer {
	0%   { opacity: 1; }
	50%  { opacity: .4; }
	100% { opacity: 1; }
}

/* ── Right Panel ─────────────────────────────────── */
.ph-chat-panel {
	flex: 1;
	display: flex;
	flex-direction: column;
	overflow: hidden;
	min-width: 0;
}

.ph-chat-panel-empty {
	flex: 1;
	display: flex;
	align-items: center;
	justify-content: center;
}
.ph-chat-panel-empty-inner {
	text-align: center;
	padding: 40px;
}
.ph-chat-panel-empty-icon {
	width: 80px; height: 80px;
	border-radius: 50%;
	background: var(--bs-secondary-bg);
	display: flex; align-items: center; justify-content: center;
	margin: 0 auto 16px;
	font-size: 2rem;
	color: var(--ph-theme-primary, #f97316);
}
.ph-chat-panel-empty-inner h5 { font-weight: 700; margin-bottom: 6px; }
.ph-chat-panel-empty-inner p { font-size: 0.82rem; color: var(--bs-secondary-color); margin-bottom: 16px; }

.ph-chat-panel-header {
	display: flex;
	align-items: center;
	padding: 10px 16px;
	border-bottom: 1px solid var(--bs-border-color, rgba(0,0,0,.1));
	gap: 4px;
	flex-shrink: 0;
}
.ph-chat-panel-name { font-size: 0.9rem; font-weight: 700; line-height: 1.2; }
.ph-chat-panel-status { font-size: 0.72rem; color: var(--bs-secondary-color); }

/* ── Messages ─────────────────────────────────────── */
.ph-chat-messages {
	flex: 1;
	overflow-y: auto;
	padding: 16px 20px;
	display: flex;
	flex-direction: column;
	gap: 4px;
}
.ph-chat-messages::-webkit-scrollbar { width: 4px; }
.ph-chat-messages::-webkit-scrollbar-thumb { background: var(--bs-border-color); border-radius: 4px; }

.ph-chat-date-divider {
	display: flex; align-items: center; gap: 10px;
	text-align: center;
	margin: 12px 0 8px;
}
.ph-chat-date-divider::before,
.ph-chat-date-divider::after {
	content: ''; flex: 1;
	border-top: 1px solid var(--bs-border-color, rgba(0,0,0,.1));
}
.ph-chat-date-divider span {
	font-size: 0.7rem;
	color: var(--bs-secondary-color);
	white-space: nowrap;
	background: var(--bs-body-bg);
	padding: 0 8px;
}

.ph-chat-messages-loading {
	flex: 1; display: flex; align-items: center; justify-content: center;
	color: var(--bs-secondary-color); font-size: 1.2rem;
}

.ph-chat-load-more-btn {
	background: none;
	border: 1px solid var(--bs-border-color);
	border-radius: 20px;
	font-size: 0.75rem;
	padding: 4px 14px;
	color: var(--bs-secondary-color);
	cursor: pointer;
}

.ph-chat-msg-row {
	display: flex;
	align-items: flex-end;
	gap: 6px;
	margin-bottom: 2px;
}
.ph-chat-msg-row--mine  { flex-direction: row-reverse; }
.ph-chat-msg-row--theirs { flex-direction: row; }

.ph-chat-bubble-wrapper {
	display: flex;
	flex-direction: column;
	max-width: min(70%, 480px);
}
.ph-chat-msg-row--mine .ph-chat-bubble-wrapper { align-items: flex-end; }
.ph-chat-msg-row--theirs .ph-chat-bubble-wrapper { align-items: flex-start; }

.ph-chat-bubble {
	padding: 9px 13px;
	border-radius: 18px;
	font-size: 0.85rem;
	line-height: 1.5;
	word-break: break-word;
	white-space: pre-wrap;
}
.ph-chat-bubble--mine {
	background: var(--ph-theme-primary, #f97316);
	color: #fff;
	border-bottom-right-radius: 4px;
}
.ph-chat-bubble--theirs {
	background: var(--bs-secondary-bg);
	color: var(--bs-body-color);
	border-bottom-left-radius: 4px;
}

.ph-chat-bubble-meta {
	font-size: 0.68rem;
	color: var(--bs-secondary-color);
	margin-top: 2px;
	padding: 0 4px;
}

/* Typing bubble */
.ph-typing-bubble {
	display: flex;
	align-items: center;
	gap: 4px;
	padding: 10px 14px;
	min-width: 52px;
}
.ph-typing-bubble span {
	width: 7px; height: 7px;
	background: var(--bs-secondary-color);
	border-radius: 50%;
	animation: typingBounce 1s infinite;
}
.ph-typing-bubble span:nth-child(2) { animation-delay: .15s; }
.ph-typing-bubble span:nth-child(3) { animation-delay: .3s; }
@keyframes typingBounce {
	0%, 80%, 100% { transform: translateY(0); opacity: .5; }
	40%            { transform: translateY(-6px); opacity: 1; }
}

/* Typing indicator inline */
.ph-typing-indicator span {
	display: inline-block;
	width: 5px; height: 5px;
	background: currentColor;
	border-radius: 50%;
	margin: 0 1px;
	animation: typingBounce .8s infinite;
}
.ph-typing-indicator span:nth-child(2) { animation-delay: .12s; }
.ph-typing-indicator span:nth-child(3) { animation-delay: .24s; }

/* ── Input area ───────────────────────────────────── */
.ph-chat-input-area {
	display: flex;
	align-items: center;
	gap: 8px;
	padding: 10px 14px;
	border-top: 1px solid var(--bs-border-color, rgba(0,0,0,.1));
	flex-shrink: 0;
}

.ph-chat-input {
	flex: 1;
	background: var(--bs-secondary-bg);
	border: 1px solid transparent;
	border-radius: 22px;
	padding: 9px 16px;
	font-size: 0.85rem;
	min-height: 40px;
	max-height: 120px;
	overflow-y: auto;
	outline: none;
	color: var(--bs-body-color);
	transition: border-color .2s;
	line-height: 1.4;
}
.ph-chat-input:empty::before {
	content: attr(data-placeholder);
	color: var(--bs-secondary-color);
	pointer-events: none;
}
.ph-chat-input:focus {
	border-color: var(--ph-theme-primary, #f97316);
}

.ph-chat-icon-btn {
	width: 36px; height: 36px;
	border-radius: 50%;
	border: none;
	background: none;
	color: var(--bs-body-color);
	display: flex; align-items: center; justify-content: center;
	cursor: pointer;
	font-size: 0.9rem;
	transition: background .15s;
	flex-shrink: 0;
}
.ph-chat-icon-btn:hover { background: var(--bs-secondary-bg); }

.ph-chat-send-btn {
	width: 40px; height: 40px;
	border-radius: 50%;
	border: none;
	background: var(--bs-secondary-bg);
	color: var(--bs-secondary-color);
	display: flex; align-items: center; justify-content: center;
	cursor: pointer;
	font-size: 0.9rem;
	transition: all .2s;
	flex-shrink: 0;
}
.ph-chat-send-btn.active {
	background: var(--ph-theme-primary, #f97316);
	color: #fff;
	transform: scale(1.05);
}
.ph-chat-send-btn:disabled { cursor: not-allowed; transform: none; }

/* Shared btn */
.ph-chat-btn-primary {
	background: var(--ph-theme-primary, #f97316);
	color: #fff;
	border: none;
	border-radius: 20px;
	padding: 8px 20px;
	font-size: 0.82rem;
	font-weight: 600;
	cursor: pointer;
}
.ph-chat-btn-primary:hover { opacity: .9; }

/* ── Mobile responsive ────────────────────────────── */
@media (max-width: 768px) {
	.ph-chat-app { position: relative; }
	.ph-chat-sidebar {
		position: absolute; top: 0; left: 0; bottom: 0;
		width: 100%; max-width: 100%;
		z-index: 10;
		transition: transform .25s;
	}
	.ph-chat-sidebar--hidden { transform: translateX(-100%); }
	.ph-chat-panel { position: absolute; top: 0; left: 0; right: 0; bottom: 0; }
	.ph-chat-panel--active { z-index: 11; }
}
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
<script>
(function() {
'use strict';

const { createApp, ref, computed, reactive, watch, nextTick, onMounted, onBeforeUnmount } = Vue;

// ── Config dari Blade ─────────────────────────────────
@php
	$reverbScheme = env('REVERB_SCHEME', 'http');
	$reverbPort   = $reverbScheme === 'https' ? 443 : (int) env('REVERB_SERVER_PORT', 9001);
@endphp
const REVERB_KEY    = '{{ env("REVERB_APP_KEY", "cms-main-key") }}';
const REVERB_HOST   = '{{ env("REVERB_HOST", "laravel-12-phoenix.aruna") }}';
const REVERB_PORT   = {{ $reverbPort }};
const REVERB_TLS    = '{{ $reverbScheme }}' === 'https';
const API_BASE      = '{{ url("chat/api") }}';
const CSRF_TOKEN    = document.querySelector('meta[name="csrf-token"]')?.content || '';
const CURRENT_USER  = {
	id:       {{ auth()->user()->id }},
	name:     '{{ addslashes(auth()->user()->fullname ?? auth()->user()->username) }}',
	avatar:   '',
};

// ── Axios helper ──────────────────────────────────────
async function api(method, path, data = null) {
	const opts = {
		method,
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN':  CSRF_TOKEN,
			'X-Requested-With': 'XMLHttpRequest',
		},
	};
	if (data) opts.body = JSON.stringify(data);
	const res = await fetch(API_BASE + path, opts);
	return res.json();
}

// ── Avatar color helper ──────────────────────────────
const COLORS = ['#7c3aed','#0891b2','#059669','#d97706','#db2777','#9333ea','#2563eb','#16a34a'];
function getColor(name = '') {
	let hash = 0;
	for (let c of name) hash = (hash << 5) - hash + c.charCodeAt(0);
	return COLORS[Math.abs(hash) % COLORS.length];
}
function getInitials(name = '') {
	const parts = name.trim().split(' ');
	if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
	return name.slice(0, 2).toUpperCase();
}

// ── Vue App ───────────────────────────────────────────
createApp({
	setup() {
		// state
		const tabs = [
			{ key: 'all',      label: 'All messages' },
			{ key: 'unread',   label: 'Unread' },
			{ key: 'favorites',label: 'Favorites' },
		];
		const activeTab           = ref('all');
		const conversations       = ref([]);
		const activeConversation  = ref(null);
		const messages            = ref([]);
		const onlineUsers         = ref([]);
		const typingUsers         = reactive({});   // { convId: true/false }
		const searchQuery         = ref('');
		const searchedUsers       = ref([]);
		const showUserSearch      = ref(false);
		const loadingConversations= ref(false);
		const loadingMessages     = ref(false);
		const loadingUsers        = ref(false);
		const sending             = ref(false);
		const hasMoreMessages     = ref(false);
		const isMobile            = ref(window.innerWidth < 768);
		const currentUserId       = CURRENT_USER.id;

		// refs
		const messagesRef = ref(null);
		const inputRef    = ref(null);
		const convListRef = ref(null);

		// reactive input text tracker (untuk canSend computed)
		const inputText   = ref('');

		// typing debounce
		let typingTimer   = null;
		let typingConvId  = null;
		let currentChannel= null;
		let searchDebounce= null;

		// reverb — mirror config dari filemanager.blade.php (forceTLS + wss)
		const reverb = new Pusher(REVERB_KEY, {
			wsHost:            REVERB_HOST,
			wsPort:            REVERB_PORT,
			wssPort:           REVERB_PORT,
			forceTLS:          REVERB_TLS,
			disableStats:      true,
			enabledTransports: REVERB_TLS ? ['wss'] : ['ws'],
			cluster:           '',
		});

		reverb.connection.bind('connected', () => console.log('✅ Chat Reverb connected'));
		reverb.connection.bind('error', err  => console.warn('❌ Chat Reverb error', err));

		// ── Computed ──────────────────────────────────────
		const filteredConversations = computed(() => {
			let list = conversations.value;
			if (activeTab.value === 'unread') list = list.filter(c => c.unread_count > 0);
			if (searchQuery.value && !showUserSearch.value) {
				const q = searchQuery.value.toLowerCase();
				list = list.filter(c => c.other_user.fullname.toLowerCase().includes(q));
			}
			return list;
		});

		const canSend = computed(() => inputText.value.trim().length > 0);

		const groupedMessages = computed(() => {
			const groups = {};
			for (const m of messages.value) {
				const date = m.created_date || 'today';
				if (!groups[date]) groups[date] = [];
				groups[date].push(m);
			}
			return groups;
		});

		// ── Methods ───────────────────────────────────────
		async function loadConversations() {
			loadingConversations.value = true;
			try {
				const res = await api('GET', '/conversations');
				if (res.success) conversations.value = res.data;
			} finally {
				loadingConversations.value = false;
			}
		}

		async function selectConversation(conv) {
			// Jika sudah aktif, skip
			if (activeConversation.value?.id === conv.id) return;

			// Unsubscribe dari channel lama
			if (currentChannel) {
				reverb.unsubscribe('chat.' + (activeConversation.value?.id));
				currentChannel = null;
			}

			activeConversation.value = conv;
			messages.value = [];
			hasMoreMessages.value = false;
			typingUsers[conv.id] = false;

			await loadMessages(conv.id);
			subscribeToConversation(conv.id);

			// Reset unread counter
			const idx = conversations.value.findIndex(c => c.id === conv.id);
			if (idx !== -1) conversations.value[idx].unread_count = 0;

			await nextTick();
			scrollToBottom();

			if (inputRef.value) inputRef.value.focus();
		}

		async function loadMessages(convId, beforeId = null) {
			loadingMessages.value = true;
			try {
				let path = `/conversations/${convId}/messages`;
				if (beforeId) path += `?before_id=${beforeId}`;
				const res = await api('GET', path);
				if (res.success) {
					if (beforeId) {
						messages.value = [...res.data, ...messages.value];
					} else {
						messages.value = res.data;
					}
					hasMoreMessages.value = res.data.length >= 50;
				}
			} finally {
				loadingMessages.value = false;
			}
		}

		async function loadMoreMessages() {
			if (!activeConversation.value || loadingMessages.value) return;
			const firstId = messages.value[0]?.id;
			if (!firstId) return;
			const scrollEl = messagesRef.value;
			const prevH = scrollEl?.scrollHeight || 0;
			await loadMessages(activeConversation.value.id, firstId);
			await nextTick();
			if (scrollEl) scrollEl.scrollTop = scrollEl.scrollHeight - prevH;
		}

		function subscribeToConversation(convId) {
			currentChannel = reverb.subscribe('chat.' + convId);

			currentChannel.bind('message.sent', (data) => {
				// Tambah pesan jika bukan milik sendiri (yang sendiri sudah di-push optimistically)
				if (data.sender_id !== currentUserId) {
					messages.value.push({
						id:              data.id,
						conversation_id: data.conversation_id,
						sender_id:       data.sender_id,
						sender_name:     data.sender_name,
						sender_avatar:   data.sender_avatar,
						body:            data.body,
						type:            data.type,
						is_mine:         false,
						read_at:         null,
						created_at:      formatTime(data.created_at),
						created_date:    formatDate(data.created_at),
					});
					nextTick(() => scrollToBottom());

					// Update conversation last message
					updateConvLastMsg(convId, data);
				}
			});

			currentChannel.bind('user.typing', (data) => {
				if (data.user_id !== currentUserId) {
					typingUsers[convId] = data.is_typing;
					if (data.is_typing) {
						setTimeout(() => { typingUsers[convId] = false; }, 3000);
					}
					nextTick(() => scrollToBottom());
				}
			});
		}

		async function sendMessage() {
			if (!canSend.value || !activeConversation.value || sending.value) return;

			const body = inputRef.value.innerText.trim();
			if (!body) return;

			// Clear input immediately + reset reactive tracker
			inputRef.value.innerText = '';
			inputText.value = '';

			// Optimistic UI
			const tempId = Date.now();
			const now    = new Date();
			messages.value.push({
				id:              tempId,
				conversation_id: activeConversation.value.id,
				sender_id:       currentUserId,
				sender_name:     CURRENT_USER.name,
				sender_avatar:   '',
				body:            body,
				type:            'text',
				is_mine:         true,
				read_at:         null,
				created_at:      formatTime(now.toISOString()),
				created_date:    formatDate(now.toISOString()),
			});

			await nextTick();
			scrollToBottom();

			sending.value = true;
			try {
				const res = await api('POST', `/conversations/${activeConversation.value.id}/send`, { body });
				if (res.success) {
					// Ganti temp message dengan data asli
					const idx = messages.value.findIndex(m => m.id === tempId);
					if (idx !== -1) messages.value.splice(idx, 1, res.data);

					// Update conversation list
					updateConvLastMsg(activeConversation.value.id, res.data);
				}
			} finally {
				sending.value = false;
			}

			// Stop typing indicator
			sendTyping(false);
		}

		function onInputChange() {
			// Sync reactive inputText agar canSend computed bisa track
			inputText.value = inputRef.value?.innerText || '';
			sendTyping(true);
			clearTimeout(typingTimer);
			typingTimer = setTimeout(() => sendTyping(false), 2000);
		}

		async function sendTyping(isTyping) {
			if (!activeConversation.value) return;
			try {
				await api('POST', `/conversations/${activeConversation.value.id}/typing`, { is_typing: isTyping });
			} catch (e) {}
		}

		async function onSearchInput() {
			clearTimeout(searchDebounce);
			const q = searchQuery.value.trim();

			if (q.length === 0) {
				showUserSearch.value = false;
				searchedUsers.value  = [];
				return;
			}

			showUserSearch.value = true;
			searchDebounce = setTimeout(async () => {
				loadingUsers.value = true;
				try {
					const res = await api('GET', `/users?search=${encodeURIComponent(q)}`);
					if (res.success) searchedUsers.value = res.data;
				} finally {
					loadingUsers.value = false;
				}
			}, 350);
		}

		async function openNewChat() {
			searchQuery.value    = '';
			showUserSearch.value = true;
			searchedUsers.value  = [];
			loadingUsers.value   = true;
			try {
				const res = await api('GET', '/users');
				if (res.success) searchedUsers.value = res.data;
			} finally {
				loadingUsers.value = false;
			}
		}

		async function startChatWith(user) {
			showUserSearch.value = false;
			searchQuery.value    = '';
			searchedUsers.value  = [];

			const res = await api('POST', '/conversations/open', { user_id: user.id });
			if (res.success) {
				const { conversation_id, other_user } = res.data;

				// Cek apakah sudah ada di list
				let conv = conversations.value.find(c => c.id === conversation_id);
				if (!conv) {
					conv = {
						id: conversation_id,
						other_user,
						last_message: null,
						unread_count: 0,
						last_activity_at: null,
					};
					conversations.value.unshift(conv);
				}

				await selectConversation(conv);
			}
		}

		function updateConvLastMsg(convId, msg) {
			const idx = conversations.value.findIndex(c => c.id === convId);
			if (idx === -1) return;
			conversations.value[idx].last_message = {
				body:      msg.body,
				type:      msg.type || 'text',
				sender_id: msg.sender_id,
				created_at: msg.created_at,
			};
			conversations.value[idx].last_activity_at = new Date().toISOString();
			// Pindah ke atas
			const conv = conversations.value.splice(idx, 1)[0];
			conversations.value.unshift(conv);
		}

		function onPaste(e) {
			const text = e.clipboardData.getData('text/plain');
			document.execCommand('insertText', false, text);
		}

		function onMessagesScroll(e) {
			// Auto-load old messages on scroll to top
			if (e.target.scrollTop < 40 && hasMoreMessages.value && !loadingMessages.value) {
				loadMoreMessages();
			}
		}

		function scrollToBottom(smooth = false) {
			const el = messagesRef.value;
			if (!el) return;
			el.scrollTo({ top: el.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
		}

		function formatTime(isoStr) {
			if (!isoStr) return '';
			const d = new Date(isoStr);
			return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
		}

		function formatDate(isoStr) {
			if (!isoStr) return 'today';
			return new Date(isoStr).toISOString().split('T')[0];
		}

		function formatDateLabel(dateStr) {
			if (!dateStr || dateStr === 'today') return 'Hari ini';
			const d    = new Date(dateStr);
			const now  = new Date();
			const diff = Math.floor((now - d) / 86400000);
			if (diff === 0) return 'Hari ini';
			if (diff === 1) return 'Kemarin';
			return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
		}

		// ── Lifecycle ─────────────────────────────────────
		onMounted(async () => {
			await loadConversations();
			window.addEventListener('resize', () => { isMobile.value = window.innerWidth < 768; });
		});

		onBeforeUnmount(() => {
			reverb.disconnect();
		});

		return {
			tabs, activeTab,
			conversations, activeConversation,
			messages, groupedMessages,
			onlineUsers, typingUsers,
			searchQuery, searchedUsers, showUserSearch,
			loadingConversations, loadingMessages, loadingUsers,
			sending, canSend, hasMoreMessages, inputText,
			isMobile, currentUserId,
			messagesRef, inputRef, convListRef,
			filteredConversations,
			// methods
			selectConversation, openNewChat, startChatWith,
			sendMessage, onInputChange, onPaste,
			onMessagesScroll, loadMoreMessages,
			onSearchInput,
			getColor, getInitials, formatDateLabel,
		};
	},
}).mount('#chat-app');

})();
</script>
@endpush
