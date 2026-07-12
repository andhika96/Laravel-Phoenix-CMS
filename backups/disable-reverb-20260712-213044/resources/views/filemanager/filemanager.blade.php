<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!-- Bootstrap -->
	<link href="{{ url('assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css') }}" rel="stylesheet">

	<!-- Fontawesome -->
	<link href="{{ url('assets/plugins/fontawesome/5.15.3/css/all.min.css') }}" rel="stylesheet">

	<!-- Font -->
	<link href="{{ asset('storage/fonts/nunito/fonts.css?v=').time() }}" rel="stylesheet">	

	<!-- Custom CSS -->
	<link href="{{ asset('assets/css/phoenix-cms.css?v=').time() }}" rel="stylesheet">
	<link href="{{ asset('assets/css/themes/arunika_v1/arunika_v1.css?v=').time() }}" rel="stylesheet">

	{{-- Bootstrap Icons via CDN (tidak ada di asset lokal) --}}
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

	<title>File Manager — {{ config('app.name', 'Arunika CMS') }}</title>

	<style>
		:root { --fm-sidebar: 220px; --fm-toolbar: 50px; --fm-statusbar: 30px; }
		*, *::before, *::after { box-sizing: border-box; }
		html, body { height: 100%; margin: 0; font-size: 14px; background: #f0f2f5; overflow: hidden; }

		/* ── App shell ── */
		#fm-app { display: flex; flex-direction: column; height: 100vh; }

		/* ── Toolbar ── */
		#fm-toolbar {
			height: var(--fm-toolbar); background: #1e293b; color: #e2e8f0;
			display: flex; align-items: center; gap: 6px; padding: 0 14px;
			flex-shrink: 0; z-index: 10;
		}
		.fm-brand { font-weight: 700; font-size: 1.5rem; color: #fff; margin-right: 8px; white-space: nowrap; }
		.fm-sep   { width: 1px; height: 22px; background: #FFFFFF; margin: 0 .75rem; flex-shrink: 0; }
		.fm-badge { font-size: 12px; padding: .35rem .75rem; border-radius: 12px; font-weight: 600; border: 1px solid #334155; white-space: nowrap; }
		.fm-badge.internal  { color: #4ade80; }
		.fm-badge.standalone{ color: #38bdf8; }
		.fm-badge.embed     { color: #a78bfa; }
		.btn-fm { font-size: 14px; }

		/* ── Filter panel ── */
		#fm-filter-panel {
			background: #fff; border-bottom: 1px solid #e2e8f0;
			padding: 8px 14px; flex-shrink: 0;
			display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
			font-size: 12px;
		}

		/* ── Notice bar ── */
		.fm-notice {
			display: flex; align-items: center; gap: 8px;
			padding: 7px 14px; font-size: 12px; flex-shrink: 0;
			border-bottom: 1px solid transparent;
		}
		.fm-notice.success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
		.fm-notice.danger  { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
		.fm-notice.warning { background: #fffbeb; border-color: #fde68a; color: #92400e; }
		.fm-notice.info    { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }

		/* ── Body ── */
		#fm-body { display: flex; flex: 1; overflow: hidden; }

		/* ── Sidebar ── */
		#fm-sidebar {
			width: var(--fm-sidebar); background: #fff;
			border-right: 1px solid #e2e8f0;
			display: flex; flex-direction: column; overflow: hidden; flex-shrink: 0;
		}
		.fm-sidebar-head {
			padding: 10px 12px; 
			font-weight: 600;
			text-transform: uppercase; letter-spacing: .4px; color: #64748b;
			border-bottom: 1px solid #f1f5f9;
		}
		.fm-tree { overflow-y: auto; flex: 1; padding: 4px 0; }
		.fm-tree-item {
			display: flex; align-items: center; gap: 5px;
			padding: 5px 12px; cursor: pointer; user-select: none;
			color: #374151; border-left: 3px solid transparent;
			transition: background .1s;
		}
		.fm-tree-item:hover  { background: #f8fafc; }
		.fm-tree-item.active { background: #eff6ff; color: #2563eb; border-left-color: #2563eb; font-weight: 600; }
		.fm-tree-children    { padding-left: 16px; }
		#fm-quota { padding: 8px 12px; border-top: 1px solid #f1f5f9; font-size: 14px; color: #64748b; }

		/* ── Main ── */
		#fm-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

		/* ── Breadcrumb ── */
		#fm-breadcrumb {
			padding: 8px 16px;
			background: #fff;
			border-bottom: 1px solid #e2e8f0;
			display: flex; 
			align-items: center; 
			gap: 4px;
			flex-shrink: 0;
		}
		#fm-breadcrumb .bc-item { color: #2563eb; cursor: pointer; }
		#fm-breadcrumb .bc-item:hover { text-decoration: underline; }
		#fm-breadcrumb .bc-sep  { color: #94a3b8; font-size: 10px; }

		/* ── Upload progress ── */
		#fm-uploads { padding: 6px 14px; background: #fff; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
		.up-item { margin-bottom: 5px; font-size: 11px; }
		.up-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; display: block; }

		/* ── Content grid/list ── */
		#fm-content {
			flex: 1; overflow-y: auto; padding: 14px;
			display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
			gap: 8px; align-content: start;
		}
		#fm-content.list-view { display: block; }

		/* Grid item */
		.fm-item {
			border: 2px solid transparent; border-radius: 8px;
			background: #fff; padding: 9px 7px 7px;
			cursor: pointer; text-align: center; user-select: none;
			transition: border-color .12s, box-shadow .12s; position: relative;
		}
		.fm-item:hover    { border-color: #93c5fd; box-shadow: 0 1px 5px rgba(0,0,0,.08); }
		.fm-item.selected { border-color: #2563eb; background: #eff6ff; }
		.fm-thumb {
			width: 60px; height: 60px; object-fit: cover;
			border-radius: 5px; margin: 0 auto 5px; display: block;
		}
		.fm-icon-wrap {
			width: 60px; height: 60px; margin: 0 auto 5px;
			display: flex; align-items: center; justify-content: center;
			border-radius: 5px; font-size: 30px;
		}
		.fm-item-name {
			font-size: 14px; overflow: hidden; text-overflow: ellipsis;
			white-space: nowrap; color: #374151;
		}
		.fm-item-check { position: absolute; top: 4px; left: 5px; display: none; }
		.fm-item.selected .fm-item-check { display: block; }

		/* List item */
		.fm-list-row {
			display: flex; align-items: center; gap: 9px;
			padding: 5px 9px; border-radius: 5px; cursor: pointer; user-select: none;
			transition: background .1s;
		}
		.fm-list-row:hover    { background: #f8fafc; }
		.fm-list-row.selected { background: #eff6ff; }
		.fm-list-icon { font-size: 18px; flex-shrink: 0; }
		.fm-list-name { flex: 1; font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
		.fm-list-meta { font-size: 14px; color: #94a3b8; white-space: nowrap; flex-shrink: 0; }

		/* ── Status bar ── */
		#fm-statusbar {
			height: var(--fm-statusbar); 
			background: #f8fafc;
			border-top: 1px solid #e2e8f0;
			display: flex; 
			align-items: center; 
			padding: 10px 12px;
			font-size: 14px; 
			color: #64748b; 
			gap: 10px; 
			flex-shrink: 0;
		}
		.fm-stat-count  { white-space: nowrap; flex-shrink: 0; }
		.fm-stat-path   { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px; flex-shrink: 1; }
		.fm-info-panel  { display: flex; align-items: center; gap: 4px; overflow: hidden; flex: 1; }
		.fm-info-name   { font-weight: 600; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
		.fm-info-sep    { color: #cbd5e1; flex-shrink: 0; }
		.fm-info-item   { white-space: nowrap; flex-shrink: 0; }
		.fm-info-link   { color: #2563eb; text-decoration: none; flex-shrink: 0; }
		.fm-info-link:hover { color: #1d4ed8; }
		.fm-info-loading { color: #94a3b8; white-space: nowrap; }
		.fm-multi-info  { color: #2563eb; font-weight: 600; white-space: nowrap; flex: 1; }

		/* ── Insert bar ── */
		#fm-insert-bar {
			background: #eff6ff; border-top: 1px solid #bfdbfe;
			padding: 7px 14px; display: flex; align-items: center;
			gap: 9px; font-size: 12px; flex-shrink: 0;
		}

		/* ── Drop overlay ── */
		#fm-dropzone {
			position: fixed; inset: 0; background: rgba(37,99,235,.14);
			border: 4px dashed #2563eb; z-index: 9999;
			display: flex; align-items: center; justify-content: center;
			font-size: 20px; color: #1d4ed8; font-weight: 700; pointer-events: none;
		}

		/* ── Context menu ── */
		#fm-ctx {
			position: fixed; z-index: 5000; background: #fff;
			border: 1px solid #e2e8f0; border-radius: 7px;
			box-shadow: 0 6px 20px rgba(0,0,0,.12);
			min-width: 170px; padding: 3px 0;
		}
		.ctx-item {
			display: flex; align-items: center; gap: 7px;
			padding: 7px 12px; cursor: pointer; font-size: 12px; color: #374151;
			transition: background .1s;
		}
		.ctx-item:hover  { background: #f1f5f9; }
		.ctx-item.danger { color: #dc2626; }
		.ctx-sep         { height: 1px; background: #f1f5f9; margin: 3px 0; }

		/* ── Lightbox ── */
		#fm-lightbox {
			position: fixed; inset: 0; background: rgba(0,0,0,.88);
			z-index: 9998; display: flex; align-items: center; justify-content: center;
		}
		#fm-lightbox img { max-width: 92vw; max-height: 88vh; border-radius: 5px; box-shadow: 0 4px 40px rgba(0,0,0,.5); }
		.lb-close { position: absolute; top: 14px; right: 18px; font-size: 26px; color: #fff; cursor: pointer; }
		.lb-info {
			position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%);
			background: rgba(0,0,0,.6); color: #fff; padding: 5px 14px;
			border-radius: 18px; font-size: 12px; white-space: nowrap;
		}

		/* Colors */
		.ic-folder   { color: #f59e0b; }
		.ic-image    { color: #10b981; }
		.ic-video    { color: #8b5cf6; }
		.ic-audio    { color: #ec4899; }
		.ic-document { color: #3b82f6; }
		.ic-archive  { color: #f97316; }
		.ic-other    { color: #94a3b8; }

		/* Drag & Drop move visual feedback */
		.drag-over {
			border-color: #2563eb !important;
			background: #dbeafe !important;
			box-shadow: 0 0 0 2px #93c5fd !important;
		}
		[draggable="true"] { cursor: grab; }
		[draggable="true"]:active { cursor: grabbing; }

		/* Sembunyikan native file input browser */
		#fm-upload-input { display: none !important; }

		/* ── FM Modal — Arunika Manage Article Style ── */
		.fm-modal-arunika .modal-dialog  { max-width: 500px; }
		.fm-modal-arunika .modal-content { border-radius: 1rem  }
		.fm-modal-arunika .modal-header  { border-bottom: 0; padding: 1rem; }
		.fm-modal-arunika .modal-title   { font-size: 1.1rem; font-weight: 600; }
		.fm-modal-arunika .modal-body    { padding: 0.75rem 1.5rem 1rem; }
		.fm-modal-arunika .modal-footer  { border-top: 0; padding: 0 1.5rem 1.5rem; }
		.fm-modal-arunika .fm-modal-form-area {
			background: var(--bs-secondary-bg, #f0f2f5);
			border-radius: 0.5rem;
			padding: 1rem;
		}
		.fm-modal-arunika .fm-modal-form-area .form-label { font-weight: 500; }

		/* Delete modal */
		.fm-modal-delete-arunika .modal-body   { padding-top: 2.5rem; padding-bottom: 0.25rem; }
		.fm-modal-delete-arunika .modal-footer { padding-bottom: 2.5rem; border-top: 0; }
	</style>
</head>
<body>

<div id="fm-app" v-cloak @dragover.prevent="isDragging = true" @dragleave="isDragging = false" @drop.prevent="handleDrop">

	{{-- Drop overlay --}}
	<div id="fm-dropzone" v-if="isDragging">
		<i class="fad fa-cloud-upload me-2"></i> {{ t('Drop file to upload') }}
	</div>

	{{-- Lightbox --}}
	<div id="fm-lightbox" v-if="lightbox.show" @click.self="lightbox.show=false">
		<span class="lb-close" @click="lightbox.show=false"><i class="far fa-times fa-lg"></i></span>
		<img :src="lightbox.url" :alt="lightbox.name">
		<div class="lb-info">@{{ lightbox.name }} &nbsp;·&nbsp; @{{ lightbox.size }}</div>
	</div>

	{{-- Context menu --}}
	<div id="fm-ctx" v-if="ctxMenu.show" :style="{top:ctxMenu.y+'px', left:ctxMenu.x+'px'}" @click.stop>
		<template v-if="ctxMenu.target">
			<div class="ctx-item" v-if="ctxMenu.target.type==='folder'" @click="navigateTo(ctxMenu.target.path); ctxMenu.show=false">
				<i class="fad fa-folder-open"></i> {{ t('Open') }}
			</div>

			<div class="ctx-item" v-if="ctxMenu.target.type==='file' && ctxMenu.target.file_type === 'image'"
				 @click="openLightbox(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-eye fa-fw"></i> {{ t('Preview') }}
			</div>

			<div class="ctx-item" v-if="ctxMenu.target.type === 'file' && ctxMenu.target.file_type === 'image'"
				 @click="openImageEditor(ctxMenu.target); ctxMenu.show=false">
				<i class="bi bi-pencil-square"></i> {{ t('Edit Image') }}
			</div>

			<div class="ctx-item" v-if="ctxMenu.target.type === 'file' && hasCallback" @click="insertToEditor(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-arrow-square-right fa-fw"></i> {{ t('Select / Insert') }}
			</div>

			<div class="ctx-item" v-if="ctxMenu.target.type==='file'" @click="copyUrl(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-link fa-fw"></i> {{ t('Copy URL') }}
			</div>

			<div class="ctx-item" @click="startRename(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-pencil fa-fw"></i> {{ t('Change Name') }}
			</div>

			<div class="ctx-item" @click="startMove(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-arrows fa-fw"></i> {{ t('Move') }}
			</div>

			<div class="ctx-item" v-if="ctxMenu.target.type==='file'" @click="doCopy(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-copy fa-fw"></i> {{ t('Copy to here') }}
			</div>

			<div class="ctx-sep"></div>

			<div class="ctx-item" @click="openPermission(ctxMenu.target); ctxMenu.show=false">
				<i class="bi bi-shield-lock fa-fw"></i> {{ t('Permission (ACL)') }}
			</div>

			<div class="ctx-item" @click="openMetadata(ctxMenu.target); ctxMenu.show=false">
				<i class="bi bi-tags fa-fw"></i> {{ t('Metadata') }}
			</div>

			<div class="ctx-sep"></div>

			<div class="ctx-item danger" @click="confirmDelete(ctxMenu.target); ctxMenu.show=false">
				<i class="fad fa-trash fa-fw"></i> {{ t('Delete') }}
			</div>
		</template>
	</div>

	{{-- ══ TOOLBAR ══ --}}
	<div id="fm-toolbar">
		<span class="fm-brand"><i class="fad fa-folder-open fa-fw me-1"></i> File Manager</span>
		<span class="fm-badge" :class="fmMode">@{{ fmMode }}</span>
		<div class="fm-sep"></div>

		<button class="btn btn-light btn-fm" @click="triggerUpload">
			<i class="far fa-upload fa-fw me-1"></i> {{ t('Upload') }}
		</button>

		<input type="file" id="fm-upload-input" multiple @change="handleFileInput">

		<button class="btn btn-light btn-fm" @click="showNewFolder">
			<i class="far fa-folder-plus fa-fw me-1"></i> {{ t('New Folder') }}
		</button>

		{{-- Select All / Deselect All --}}
		<button class="btn btn-fm ms-1" :class="allSelected ? 'btn-warning' : 'btn-light'" @click="toggleSelectAll" :title="allSelected ? 'Deselect All' : 'Select All'" v-if="folders.length + files.length > 0">
			<i class="far" :class="allSelected ? 'fa-check-square fa-fw' : 'fa-square fa-fw'"></i>
			@{{ allSelected ? 'Deselect All' : 'Select All' }}
		</button>

		<div class="fm-sep"></div>

		{{-- Search + tombol X --}}
		<div class="input-group" style="width: 250px">
			<input type="text" class="form-control" placeholder="{{ t('Search file and folder') }}..." v-model="search" @input="debounceSearch" @keyup.escape="clearSearch">
			
			<button v-if="search" class="btn btn-outline-secondary" @click="clearSearch" style="border-left:0" title="{{ t('Remove search') }}">
				<i class="far fa-times"></i>
			</button>

			<span v-else class="input-group-text bg-white"><i class="far fa-search fa-fw"></i></span>
		</div>

		{{-- Tombol filter --}}
		<button class="btn btn-fm ms-1" :class="showFilter ? 'btn-primary' : 'btn-light'" @click="showFilter = !showFilter" title="Filter & Sort">
			<i class="far fa-filter fa-fw me-1"></i> {{ t('Filter') }}
			<span v-if="filterType || sortBy !== 'date' || sortDir !== 'desc' || sizeMin || sizeMax" class="badge bg-warning text-dark ms-1" style="font-size: 9px; padding:1px 4px">●</span>
		</button>

		<div class="ms-auto d-flex align-items-center gap-2">
			{{-- Disk selector dinamis (support multi-S3) --}}
			<select class="form-select" style="width: 150px" v-model="activeDisk" @change="onDiskChange">
				<option v-for="d in disks" :key="d.key" :value="d.key" :disabled="! d.available">
					@{{ d.label }} @{{ ! d.available ? ' ⚠' : '' }}
				</option>
			</select>

			<button class="btn btn-fm" :class="viewMode === 'grid' ? 'btn-primary' : 'btn-light'" @click="viewMode='grid'" title="Grid view">
				<i class="far fa-th"></i>
			</button>

			<button class="btn btn-fm" :class="viewMode === 'list' ? 'btn-primary' : 'btn-light'" @click="viewMode='list'" title="List view">
				<i class="far fa-list"></i>
			</button>
		</div>
	</div>

	{{-- ══ FILTER PANEL ══ --}}
	<div class="row g-1" id="fm-filter-panel" v-if="showFilter">
		{{-- Filter by type --}}
		<div class="col-auto">
			<div class="d-flex align-items-center gap-2">
				<label class="text-dark text-nowrap mb-0">{{ t('Type') }}:</label>
				
				<select class="form-select" v-model="filterType">
					<option value="">{{ t('All') }}</option>
					<option value="image">{{ t('Image') }}</option>
					<option value="video">{{ t('Video') }}</option>
					<option value="audio">{{ t('Audio') }}</option>
					<option value="document">{{ t('Document') }}</option>
					<option value="archive">{{ t('Archive') }}</option>
					<option value="other">{{ t('Other') }}</option>
				</select>
			</div>
		</div>

		{{-- Sort by --}}
		<div class="col-auto">
			<div class="d-flex align-items-center gap-2">
				<label class="text-dark text-nowrap mb-0">{{ t('Sort by') }}:</label>
				
				<select class="form-select" v-model="sortBy">
					<option value="date">{{ t('Date') }}</option>
					<option value="name">{{ t('Name A-Z') }}</option>
					<option value="size">{{ t('Size') }}</option>
				</select>

				<button class="btn btn-secondary btn-fm w-100" @click="sortDir = sortDir === 'asc' ? 'desc' : 'asc'" :title="sortDir === 'asc' ? 'Ascending' : 'Descending'">
					<i class="fad" :class="sortDir === 'asc' ? 'fa-sort-amount-up fa-fw' : 'fa-sort-amount-down fa-fw'"></i>
					@{{ sortDir === 'asc' ? 'ASC' : 'DESC' }}
				</button>
			</div>
		</div>

		{{-- Filter by size --}}
		<div class="col-auto">
			<div class="d-flex align-items-center gap-2">
				<label class="text-dark text-nowrap mb-0">{{ t('Size') }}:</label>
				
				<select class="form-select"v-model="sizePreset" @change="onSizePreset">
					<option value="">{{ t('All Size') }}</option>
					<option value="tiny">{{ t('Small') }} (&lt; 100 KB)</option>
					<option value="small">{{ t('Medium') }} (100KB – 1MB)</option>
					<option value="medium">{{ t('Large') }} (1MB – 10MB)</option>
					<option value="large">{{ t('Very Large') }} (&gt; 10MB)</option>
					<option value="custom">{{ t('Custom') }}...</option>
				</select>
			</div>
		</div>
		
		<template v-if="sizePreset==='custom'">
			<div class="col-auto">
				<input type="number" class="form-control" style="width: 120px" placeholder="Min bytes" v-model="sizeMin">
				<span class="text-muted">–</span>	
				<input type="number" class="form-control" style="width: 120px" placeholder="Max bytes" v-model="sizeMax">
			</div>
		</template>
		
		<div class="col-auto">
			<button class="btn btn-primary btn-fm" @click="applyFilter">
				<i class="far fa-check fa-fw"></i> {{ t('Submit') }}
			</button>
		</div>

		<div class="col-auto">
			<button class="btn btn-outline-secondary btn-fm" @click="resetFilter">
				<i class="far fa-times fa-fw"></i> {{ t('Reset') }}
			</button>
		</div>
	</div>

	{{-- ══ NOTICES ══ --}}
	<div v-for="n in notices" :key="n.id" :class="'fm-notice ' + n.type" style="flex-shrink:0">
		<i class="bi" :class="{
			'bi-check-circle-fill':n.type==='success',
			'bi-exclamation-triangle-fill':n.type==='warning',
			'bi-x-circle-fill':n.type==='danger',
			'bi-info-circle-fill':n.type==='info'
		}"></i>
		<span style="flex:1">@{{ n.msg }}</span>
		<button type="button" class="btn-close" style="font-size:10px; padding:0" @click="removeNotice(n.id)"></button>
	</div>

	{{-- ══ BODY ══ --}}
	<div id="fm-body">

		{{-- Sidebar --}}
		<div id="fm-sidebar">
			<div class="fm-sidebar-head"><i class="fad fa-folder fa-fw me-1"></i> {{ t('Folder') }}</div>
			
			<div class="fm-tree">
				<div class="fm-tree-item" :class="{active: currentPath === '', 'drag-over': dragOverFolder==='_root_'}" @click="navigateTo('')" @dragover.prevent="onDragOverFolder($event, {path:'_root_', name:'Root'})" @dragleave="onDragLeaveFolder" @drop.stop="onDropToFolder($event, {path:'', name:'Root'})">
					<i class="fad fa-hdd fa-fw ic-folder"></i>
					<span>{{ t('Root') }}</span>
				</div>
				
				<folder-tree-node v-for="f in rootFolders" :key="f.path" :folder="f" :active-path="currentPath" :disk="activeDisk" @navigate="navigateTo" @ctx="showCtx"></folder-tree-node>
			</div>

			<div id="fm-quota" v-if="quota">
				<div class="d-flex justify-content-between mb-1">
					<span>{{ t('Storage') }}</span>
					<span>@{{ quota.used_storage_human }} / @{{ quota.is_storage_unlimited ? '∞' : quota.max_storage_human }}</span>
				</div>

				<div class="progress" style="height: 10px">
					<div class="progress-bar":class="quota.used_percent>85?'bg-danger':quota.used_percent>60?'bg-warning':'bg-success'" :style="{width:quota.used_percent+'%'}"></div>
				</div>
			</div>
		</div>

		{{-- Main content --}}
		<div id="fm-main" @click="deselectAll" @contextmenu.prevent="ctxMenu.show=false">

			{{-- Breadcrumb --}}
			<div id="fm-breadcrumb"
				 @dragover.prevent
				 @drop.prevent="onDropToCurrentFolder">
				<i class="fad fa-hdd fa-fw me-1 text-muted"></i>
				<span class="bc-item" @click="navigateTo('')"
					  @dragover.prevent="onDragOverFolder($event, {path:'', name:'Root'})"
					  @dragleave="onDragLeaveFolder"
					  @drop.stop="onDropToFolder($event, {path:'', name:'Root'})">
					{{ t('Root') }}
				</span>

				<template v-for="crumb in breadcrumb" :key="crumb.path">
					<span class="bc-sep mx-1"><i class="bi bi-chevron-right"></i></span>
					<span class="bc-item" @click="navigateTo(crumb.path)"
						  @dragover.prevent="onDragOverFolder($event, crumb)"
						  @dragleave="onDragLeaveFolder"
						  @drop.stop="onDropToFolder($event, crumb)">
						@{{ crumb.name }}
					</span>
				</template>

				<span v-if="dragItem" class="ms-auto text-muted" style="font-size: 11px; font-style: italic">
					<i class="bi bi-info-circle me-1"></i> Drop ke folder tujuan atau ke breadcrumb
				</span>
			</div>

			{{-- Upload progress --}}
			<div id="fm-uploads" v-if="uploads.length">
				<div v-for="u in uploads" :key="u.uid" class="up-item">
					<div class="d-flex justify-content-between">
						<span class="up-name">@{{ u.name }}</span>
						<span class="text-muted ms-2">@{{ u.progress }}%</span>
					</div>
					<div class="progress mt-1" style="height:3px">
						<div class="progress-bar" :class="u.error?'bg-danger':'bg-primary'"
							 :style="{width:u.progress+'%'}"></div>
					</div>
					<div v-if="u.error" class="text-danger" style="font-size:10px">@{{ u.error }}</div>
				</div>
			</div>

			{{-- Content area --}}
			<div id="fm-content" :class="viewMode==='list'?'list-view':''">

				{{-- Loading --}}
				<div v-if="loading"
					 style="grid-column:1/-1; text-align:center; padding:50px; color:#94a3b8">
					<div class="spinner-border spinner-border-sm me-2"></div> Memuat...
				</div>

				{{-- Empty --}}
				<div v-else-if="!folders.length && !files.length"
					 style="grid-column:1/-1; text-align:center; padding:60px; color:#94a3b8">
					<i class="bi bi-folder2-open" style="font-size:38px; display:block; margin-bottom:10px"></i>
					@{{ search ? 'Tidak ada hasil untuk "' + search + '"' : 'Folder kosong.' }}
				</div>

				{{-- GRID VIEW --}}
				<template v-if="viewMode==='grid' && !loading">
					{{-- Folders --}}
					<div v-for="f in folders" :key="f.path"
						 class="fm-item"
						 :class="{selected: selectedItems.has('folder_'+f.path), 'drag-over': dragOverFolder===f.path}"
						 draggable="true"
						 @dragstart="onDragStart($event, f)"
						 @dragend="onDragEnd"
						 @dragover.prevent="onDragOverFolder($event, f)"
						 @dragleave="onDragLeaveFolder"
						 @drop.stop="onDropToFolder($event, f)"
						 @click.stop="toggleSelect('folder_'+f.path, f)"
						 @dblclick.stop="navigateTo(f.path)"
						 @contextmenu.prevent.stop="showCtx($event, f)"
						 @mouseenter="prefetchFolder(f)">
						<div class="fm-item-check"><i class="bi bi-check-circle-fill text-primary" style="font-size:13px"></i></div>
						<div class="fm-icon-wrap"><i class="bi bi-folder-fill ic-folder"></i></div>
						<div class="fm-item-name" :title="f.name">@{{ f.name }}</div>
					</div>
					{{-- Files --}}
					<div v-for="file in files" :key="file.path"
						 class="fm-item" :class="{selected: selectedItems.has('file_'+file.path)}"
						 draggable="true"
						 @dragstart="onDragStart($event, file)"
						 @dragend="onDragEnd"
						 @click.stop="toggleSelect('file_'+file.path, file)"
						 @dblclick.stop="onItemDblClick(file)"
						 @contextmenu.prevent.stop="showCtx($event, file)">
						<div class="fm-item-check"><i class="bi bi-check-circle-fill text-primary" style="font-size:13px"></i></div>
						{{-- [PERUBAHAN] Thumbnail via imagePreview endpoint (resize 220px + browser-cached 24 jam)
							 bukan file.url langsung (ukuran asli S3 = lambat) --}}
						<img v-if="file.file_type==='image'" class="fm-thumb"
							 :src="thumbUrl(file)" :alt="file.name" loading="lazy">
						<div v-else class="fm-icon-wrap">
							<i :class="['fad', fileIcon(file.file_type, file.extension), iconColor(file.file_type)]"></i>
						</div>
						<div class="fm-item-name" :title="file.name">@{{ file.name }}</div>
					</div>
				</template>

				{{-- LIST VIEW --}}
				<template v-if="viewMode==='list' && !loading">
					<div v-for="f in folders" :key="f.path"
						 class="fm-list-row"
						 :class="{selected: selectedItems.has('folder_'+f.path), 'drag-over': dragOverFolder===f.path}"
						 draggable="true"
						 @dragstart="onDragStart($event, f)"
						 @dragend="onDragEnd"
						 @dragover.prevent="onDragOverFolder($event, f)"
						 @dragleave="onDragLeaveFolder"
						 @drop.stop="onDropToFolder($event, f)"
						 @click.stop="toggleSelect('folder_'+f.path, f)"
						 @dblclick.stop="navigateTo(f.path)"
						 @contextmenu.prevent.stop="showCtx($event, f)"
						 @mouseenter="prefetchFolder(f)">
						<i class="bi bi-folder-fill fm-list-icon ic-folder"></i>
						<span class="fm-list-name">@{{ f.name }}</span>
						<span class="fm-list-meta">Folder</span>
					</div>
					<div v-for="file in files" :key="file.path"
						 class="fm-list-row" :class="{selected: selectedItems.has('file_'+file.path)}"
						 draggable="true"
						 @dragstart="onDragStart($event, file)"
						 @dragend="onDragEnd"
						 @click.stop="toggleSelect('file_'+file.path, file)"
						 @dblclick.stop="onItemDblClick(file)"
						 @contextmenu.prevent.stop="showCtx($event, file)">
						<i :class="['fad', fileIcon(file.file_type, file.extension), 'fm-list-icon', iconColor(file.file_type)]"></i>
						<span class="fm-list-name">@{{ file.name }}</span>
						<span class="fm-list-meta">@{{ file.size_human }}</span>
						<span class="fm-list-meta ms-3">@{{ file.date }}</span>
					</div>
				</template>

				{{-- [PERUBAHAN] Infinite scroll sentinel + load more indicator
					 IntersectionObserver di vueV3-filemanager.js memantau elemen ini.
					 Saat sentinel masuk viewport → loadMore() dipanggil otomatis. --}}
				<div id="fm-scroll-sentinel"
					 style="grid-column:1/-1; height:1px; margin-top:8px">
				</div>
				<div v-if="loadingMore"
					 style="grid-column:1/-1; text-align:center; padding:16px; color:#94a3b8">
					<div class="spinner-border spinner-border-sm me-2"></div>
					Memuat lebih banyak...
				</div>
				<div v-else-if="!loading && hasMore"
					 style="grid-column:1/-1; text-align:center; padding:10px">
					<button class="btn btn-sm btn-outline-secondary" @click="loadMore">
						<i class="bi bi-arrow-down-circle me-1"></i>
						Muat lebih banyak (@{{ totalFiles - files.length }} tersisa)
					</button>
				</div>

			</div>
		</div>
	</div>

	{{-- Insert bar --}}
	<div id="fm-insert-bar" v-if="hasCallback">
		<i class="bi bi-info-circle text-primary"></i>
		<span style="flex:1; color:#1d4ed8">Double-klik file untuk insert, atau pilih lalu klik tombol Insert</span>
		<button class="btn btn-sm btn-primary btn-fm"
				:disabled="!lastSelectedFile"
				@click="insertToEditor(lastSelectedFile)">
			<i class="bi bi-box-arrow-in-right me-1"></i> Insert
		</button>
	</div>

	{{-- Status bar --}}
	<div id="fm-statusbar">
		{{-- Kiri: count folder & file di current dir --}}
		<span class="fm-stat-count">
			@{{ folders.length }} folder · @{{ files.length }} file
		</span>

		{{-- Tengah: Info detail item yang dipilih (hanya saat 1 item) --}}
		<div class="fm-info-panel" v-if="selectedItems.size === 1">
			{{-- Loading --}}
			<span v-if="selectedInfoLoading" class="fm-info-loading">
				<span class="spinner-border spinner-border-sm me-1" style="width:.75rem;height:.75rem"></span>
				Memuat info...
			</span>

			{{-- Info folder --}}
			<template v-else-if="selectedInfo && selectedInfo.type === 'folder'">
				<i class="bi bi-folder-fill ic-folder me-1"></i>
				<span class="fm-info-name">@{{ selectedInfo.name }}</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item">
					<i class="bi bi-files me-1"></i>@{{ selectedInfo.total_files }} file
				</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item">
					<i class="bi bi-folder me-1"></i>@{{ selectedInfo.total_dirs }} subfolder
				</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item">
					<i class="bi bi-hdd me-1"></i>@{{ selectedInfo.total_size_human }}
				</span>
			</template>

			{{-- Info file --}}
			<template v-else-if="selectedInfo && selectedInfo.type === 'file'">
				<i :class="['fad', fileIcon(selectedInfo.file_type, selectedInfo.extension), iconColor(selectedInfo.file_type), 'me-1']"></i>
				<span class="fm-info-name">@{{ selectedInfo.name }}</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item">
					<i class="bi bi-hdd me-1"></i>@{{ selectedInfo.size_human }}
				</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item">
					<i class="bi bi-calendar3 me-1"></i>@{{ selectedInfo.date }}
				</span>
				<span class="fm-info-sep">·</span>
				<span class="fm-info-item text-muted">@{{ selectedInfo.extension.toUpperCase() }}</span>
				<a v-if="selectedInfo.url" :href="selectedInfo.url" target="_blank"
				   class="fm-info-link ms-1" title="Buka di tab baru">
					<i class="bi bi-box-arrow-up-right"></i>
				</a>
			</template>
		</div>

		{{-- Multi-select info --}}
		<span v-else-if="selectedItems.size > 1" class="fm-multi-info">
			<i class="bi bi-check2-square me-1"></i>
			@{{ selectedItems.size }} item dipilih
		</span>

		{{-- Kanan: current path --}}
		<span v-if="currentPath" class="fm-stat-path ms-auto text-truncate">
			<i class="bi bi-hdd me-1"></i>@{{ currentPath }}
		</span>
	</div>

	{{-- ══ MODALS ══ --}}

	{{-- New Folder --}}
	<div class="modal fade fm-modal-arunika" id="modalNewFolder" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ t('New Folder') }}</h5>
				</div>

				<div class="modal-body">
					<div class="fm-modal-form-area">
						<div class="mb-3">
							<label class="form-label">{{ t('Folder Name') }}</label>
							<input type="text" class="form-control" v-model="newFolderName" placeholder="{{ t('Folder Name') }}" @keyup.enter="doCreateFolder" ref="inputFolder">
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Cancel') }}</button>
					
					<button class="btn btn-primary" @click="doCreateFolder" :disabled="modalLoading">
						<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span> {{ t('Create') }}
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Rename --}}
	<div class="modal fade fm-modal-arunika" id="modalRename" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ t('Change Name') }}</h5>
				</div>

				<div class="modal-body">
					<div class="fm-modal-form-area">
						<div class="mb-3">
							<label class="form-label">{{ t('New Name') }}</label>
							<input type="text" class="form-control" v-model="renameValue" @keyup.enter="doRename" ref="inputRename">
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button class="btn btn-secondary" data-bs-dismiss="modal">{{ t('Cancel') }}</button>
				
					<button class="btn btn-primary" @click="doRename" :disabled="modalLoading">
						<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span> {{ t('Save') }}
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Move --}}
	<div class="modal fade" id="modalMove" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header py-2">
					<h6 class="modal-title"><i class="bi bi-arrows-move me-1"></i> Pindahkan</h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" style="max-height:320px; overflow-y:auto">
					<p class="text-muted mb-2" style="font-size:11px">Pilih folder tujuan:</p>
					<div class="fm-tree-item" @click="moveDestPath=''" :class="{active: moveDestPath===''}">
						<i class="bi bi-hdd"></i> Root
					</div>
					<move-picker v-for="f in rootFolders" :key="f.path"
						:folder="f" :selected-path="moveDestPath" :disk="activeDisk"
						@select="p => moveDestPath = p"></move-picker>
				</div>
				<div class="modal-footer py-2">
					<button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button class="btn btn-sm btn-primary" @click="doMove" :disabled="modalLoading">
						<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span>Pindahkan
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- Delete confirm --}}
	<div class="modal fade fm-modal-arunika fm-modal-delete-arunika" id="modalDelete" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header d-none">
					<h5 class="modal-title">{{ t('Delete Confirmation') }}</h5>
				</div>

				<div class="modal-body px-5 text-center">
					<div class="mb-4">
						<i class="fad fa-trash-alt fs-1 text-danger"></i>
					</div>

					<div class="h5 mb-3">{{ t('Delete Data') }}</div>
					<p class="mb-0">@{{ deleteMsg }} Tindakan ini tidak dapat dibatalkan.</p>
				</div>

				<div class="modal-footer d-block border-0">
					<div class="row gx-2 justify-content-center">
						<div class="col-auto">
							<button class="btn btn-secondary" data-bs-dismiss="modal">{{ t('No, keep it') }}</button>
						</div>

						<div class="col-auto">
							<button class="btn btn-danger" @click="doDelete" :disabled="modalLoading">
								<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span> {{ t('Yes, Delete') }}
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- ══ PERMISSION (ACL) MODAL ══ --}}
	<div class="modal fade fm-modal-arunika" id="modalPermission" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered" style="max-width:560px">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Permission (ACL)</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					{{-- Loading spinner saat fetch ACL background --}}
					<div v-if="aclFetching" class="d-flex align-items-center gap-2 mb-3 text-muted" style="font-size:13px">
						<span class="spinner-border spinner-border-sm"></span>
						Memuat informasi ACL...
					</div>

					{{-- Notifikasi disk tidak support ACL --}}
					<div v-if="!aclFetching && !aclSupported" class="alert alert-warning d-flex gap-2 align-items-start mb-3" style="font-size:13px">
						<i class="bi bi-exclamation-triangle-fill mt-1"></i>
						<div>
							<strong>ACL tidak tersedia</strong><br>
							Disk yang aktif tidak mendukung per-object ACL. Akses file dikontrol melalui pengaturan bucket.
						</div>
					</div>

					<div v-if="!aclFetching && aclSupported" class="fm-modal-form-area">
						<div class="mb-3">
							<label class="form-label">Target</label>
							<div class="d-flex gap-2 align-items-center p-2 bg-white rounded border" style="font-size:13px">
								<i :class="permissionTarget && permissionTarget.type==='folder' ? 'bi bi-folder-fill ic-folder' : 'bi bi-file-earmark'"></i>
								<span class="text-truncate">@{{ permissionTarget ? permissionTarget.path || permissionTarget.name : '-' }}</span>
								<span class="badge bg-secondary ms-auto">@{{ permissionTarget ? permissionTarget.type : '' }}</span>
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">ACL Permission</label>
							<select class="form-select" v-model="permissionForm.acl">
								<option value="public-read">public-read — Siapapun bisa membaca</option>
								<option value="private">private — Hanya pemilik yang bisa akses</option>
								<option value="authenticated-read">authenticated-read — User AWS terautentikasi</option>
								<option value="public-read-write">public-read-write — Siapapun bisa baca &amp; tulis</option>
							</select>
						</div>

						<div v-if="permissionTarget && permissionTarget.type === 'folder'" class="mb-3">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" id="permRecursive" v-model="permissionForm.recursive">
								<label class="form-check-label" for="permRecursive" style="font-size:13px">
									Terapkan ke semua file dalam subfolder (recursive)
								</label>
							</div>
						</div>

						<div v-if="permissionTarget && permissionTarget.type === 'folder'" class="mb-3">
							<label class="form-label">Filter ekstensi <span class="text-muted">(opsional)</span></label>
							<div class="input-group input-group-sm">
								<span class="input-group-text">.</span>
								<input type="text" class="form-control" placeholder="mp4, jpg, pdf..." v-model="permissionForm.extensionFilter">
							</div>
							<div class="form-text">Kosongkan untuk semua tipe file</div>
						</div>

						<div v-if="permissionTarget && permissionTarget.type === 'folder'" class="mb-0">
							<label class="form-label">Concurrency <span class="text-muted">(file diproses paralel)</span></label>
							<div class="d-flex align-items-center gap-3">
								<input type="range" class="form-range" min="1" max="100" v-model.number="permissionForm.concurrency" style="flex:1">
								<span class="badge bg-primary" style="min-width:40px">@{{ permissionForm.concurrency }}</span>
							</div>
							<div class="form-text">Min: 1 — Max: 100</div>
						</div>
					</div>

					{{-- Progress bar bulk --}}
					<div v-if="bulkProgress.active" class="mt-3">
						<div class="d-flex justify-content-between mb-1" style="font-size:12px">
							<span>@{{ bulkProgress.message }}</span>
							<span>@{{ bulkProgress.current }} / @{{ bulkProgress.total }}</span>
						</div>
						<div class="progress" style="height:8px">
							<div class="progress-bar progress-bar-striped progress-bar-animated"
								 :class="bulkProgress.status==='done' ? 'bg-success' : 'bg-primary'"
								 :style="{width: bulkProgress.percent + '%'}"></div>
						</div>
						<div class="text-muted mt-1" style="font-size:11px">@{{ bulkProgress.currentFile }}</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button class="btn btn-primary" @click="doUpdatePermission" :disabled="aclFetching || !aclSupported || modalLoading || bulkProgress.active">
						<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span>
						<i v-else class="bi bi-shield-check me-1"></i>
						Simpan Permission
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- ══ METADATA MODAL ══ --}}
	<div class="modal fade fm-modal-arunika" id="modalMetadata" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered" style="max-width:600px">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="bi bi-tags me-2"></i>Metadata S3</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="fm-modal-form-area">
						<div class="mb-3">
							<label class="form-label">Target</label>
							<div class="d-flex gap-2 align-items-center p-2 bg-white rounded border" style="font-size:13px">
								<i :class="metadataTarget && metadataTarget.type==='folder' ? 'bi bi-folder-fill ic-folder' : 'bi bi-file-earmark'"></i>
								<span class="text-truncate">@{{ metadataTarget ? metadataTarget.path || metadataTarget.name : '-' }}</span>
								<span class="badge bg-secondary ms-auto">@{{ metadataTarget ? metadataTarget.type : '' }}</span>
							</div>
						</div>

						<div class="row g-2 mb-3">
							<div class="col-6">
								<label class="form-label">Content-Type</label>
								<input type="text" class="form-control form-control-sm" placeholder="image/jpeg" v-model="metadataForm.content_type">
							</div>
							<div class="col-6">
								<label class="form-label">Cache-Control</label>
								<input type="text" class="form-control form-control-sm" placeholder="public, max-age=31536000" v-model="metadataForm.cache_control">
							</div>
							<div class="col-6">
								<label class="form-label">Content-Disposition</label>
								<input type="text" class="form-control form-control-sm" placeholder="inline / attachment" v-model="metadataForm.content_disposition">
							</div>
							<div class="col-6">
								<label class="form-label">Content-Encoding</label>
								<input type="text" class="form-control form-control-sm" placeholder="gzip" v-model="metadataForm.content_encoding">
							</div>
							<div class="col-6">
								<label class="form-label">Content-Language</label>
								<input type="text" class="form-control form-control-sm" placeholder="id, en" v-model="metadataForm.content_language">
							</div>
						</div>

						<div v-if="metadataTarget && metadataTarget.type === 'folder'" class="mb-3">
							<div class="form-check mb-2">
								<input class="form-check-input" type="checkbox" id="metaRecursive" v-model="metadataForm.recursive">
								<label class="form-check-label" for="metaRecursive" style="font-size:13px">
									Terapkan ke semua file dalam subfolder (recursive)
								</label>
							</div>

							<label class="form-label">Filter ekstensi <span class="text-muted">(opsional)</span></label>
							<div class="input-group input-group-sm mb-2">
								<span class="input-group-text">.</span>
								<input type="text" class="form-control" placeholder="mp4, jpg, pdf..." v-model="metadataForm.extensionFilter">
							</div>

							<label class="form-label">Concurrency</label>
							<div class="d-flex align-items-center gap-3">
								<input type="range" class="form-range" min="1" max="100" v-model.number="metadataForm.concurrency" style="flex:1">
								<span class="badge bg-primary" style="min-width:40px">@{{ metadataForm.concurrency }}</span>
							</div>
						</div>
					</div>

					{{-- Progress bar bulk --}}
					<div v-if="bulkProgress.active" class="mt-3">
						<div class="d-flex justify-content-between mb-1" style="font-size:12px">
							<span>@{{ bulkProgress.message }}</span>
							<span>@{{ bulkProgress.current }} / @{{ bulkProgress.total }}</span>
						</div>
						<div class="progress" style="height:8px">
							<div class="progress-bar progress-bar-striped progress-bar-animated"
								 :class="bulkProgress.status==='done' ? 'bg-success' : 'bg-primary'"
								 :style="{width: bulkProgress.percent + '%'}"></div>
						</div>
						<div class="text-muted mt-1" style="font-size:11px">@{{ bulkProgress.currentFile }}</div>
					</div>
				</div>
				<div class="modal-footer">
					<button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button class="btn btn-primary" @click="doUpdateMetadata" :disabled="modalLoading || bulkProgress.active">
						<span v-if="modalLoading" class="spinner-border spinner-border-sm me-1"></span>
						<i v-else class="bi bi-tags me-1"></i>
						Simpan Metadata
					</button>
				</div>
			</div>
		</div>
	</div>

	{{-- ══ IMAGE EDITOR MODAL ══ --}}
	<div class="modal fade" id="modalImageEditor" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content" style="max-height:92vh; overflow:hidden; display:flex; flex-direction:column">
				<div class="modal-header py-2" style="background:#1e293b; color:#fff; flex-shrink:0">
					<h6 class="modal-title mb-0">
						<i class="bi bi-pencil-square me-1"></i>
						Image Editor
						<span v-if="imageEditor.file" class="text-muted ms-2" style="font-size:11px; font-weight:400">
							@{{ imageEditor.file.name }}
							<span v-if="imageEditor.resize.origW">(@{{ imageEditor.resize.origW }}×@{{ imageEditor.resize.origH }}px)</span>
						</span>
					</h6>
					<div class="d-flex align-items-center gap-2">
						<span v-if="imageEditor.operations.length" class="badge bg-warning text-dark" style="font-size:11px">
							@{{ imageEditor.operations.length }} operasi
						</span>
						<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
					</div>
				</div>
				<div class="modal-body p-0" style="flex:1; overflow:hidden; display:flex">

					{{-- Sidebar tools --}}
					<div style="width:200px; background:#f8fafc; border-right:1px solid #e2e8f0; flex-shrink:0; overflow-y:auto; padding:8px">
						<div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.4px; padding:4px 8px 8px">Tools</div>

						{{-- Tool buttons --}}
						<button v-for="tool in [
							{id:'crop',   icon:'bi-crop',              label:'Crop'},
							{id:'resize', icon:'bi-arrows-angle-expand',label:'Resize'},
							{id:'rotate', icon:'bi-arrow-clockwise',   label:'Rotate'},
							{id:'flip',   icon:'bi-symmetry-horizontal',label:'Flip'},
							{id:'adjust', icon:'bi-sliders',           label:'Brightness/Contrast'},
							{id:'filter', icon:'bi-magic',             label:'Filter'},
						]" :key="tool.id"
							class="btn btn-sm w-100 text-start mb-1"
							:class="imageEditor.activeTool===tool.id ? 'btn-primary' : 'btn-outline-secondary'"
							style="font-size:12px"
							@click="ieSetTool(tool.id)">
							<i :class="'bi '+tool.icon+' me-2'"></i>@{{ tool.label }}
						</button>

						<hr style="margin:8px 0">

						{{-- Operations history --}}
						<div style="font-size:11px; font-weight:600; color:#64748b; padding:0 8px 6px">Antrian Operasi</div>
						<div v-if="!imageEditor.operations.length" style="font-size:11px; color:#94a3b8; padding:0 8px">Belum ada operasi</div>
						<div v-for="(op, i) in imageEditor.operations" :key="i"
							 style="font-size:11px; padding:3px 8px; background:#fff; border:1px solid #e2e8f0; border-radius:4px; margin-bottom:3px; display:flex; justify-content:space-between; align-items:center">
							<span><i class="bi bi-check2 text-success me-1"></i>@{{ op.type }}</span>
							<span v-if="i===imageEditor.operations.length-1"
								  @click="ieUndoLast" style="cursor:pointer; color:#ef4444" title="Hapus operasi ini">✕</span>
						</div>

						<div v-if="imageEditor.operations.length" class="mt-2">
							<button class="btn btn-sm btn-outline-danger w-100" style="font-size:11px" @click="ieClearAll">
								<i class="bi bi-trash me-1"></i> Hapus Semua
							</button>
						</div>
					</div>

					{{-- Main area: preview + tool panel --}}
					<div style="flex:1; display:flex; flex-direction:column; overflow:hidden">

						{{-- Tool options panel --}}
						<div v-if="imageEditor.activeTool" style="background:#fff; border-bottom:1px solid #e2e8f0; padding:10px 16px; flex-shrink:0">

							{{-- CROP --}}
							<div v-if="imageEditor.activeTool==='crop'" class="d-flex align-items-center gap-3 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-crop me-1"></i>Crop</span>
								<div class="d-flex align-items-center gap-2">
									<label style="font-size:12px; color:#64748b">X:</label>
									<input type="number" class="form-control form-control-sm" style="width:70px" v-model.number="imageEditor.crop.x" min="0">
									<label style="font-size:12px; color:#64748b">Y:</label>
									<input type="number" class="form-control form-control-sm" style="width:70px" v-model.number="imageEditor.crop.y" min="0">
									<label style="font-size:12px; color:#64748b">W:</label>
									<input type="number" class="form-control form-control-sm" style="width:70px" v-model.number="imageEditor.crop.width" min="1">
									<label style="font-size:12px; color:#64748b">H:</label>
									<input type="number" class="form-control form-control-sm" style="width:70px" v-model.number="imageEditor.crop.height" min="1">
								</div>
								<div class="d-flex gap-2">
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.crop={x:0,y:0,width:imageEditor.resize.origW,height:Math.round(imageEditor.resize.origW*9/16),aspect:'16:9'}">16:9</button>
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.crop={x:0,y:0,width:imageEditor.resize.origW,height:imageEditor.resize.origW,aspect:'1:1'}">1:1</button>
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.crop={x:0,y:0,width:imageEditor.resize.origW,height:Math.round(imageEditor.resize.origW*3/4),aspect:'4:3'}">4:3</button>
								</div>
								<button class="btn btn-sm btn-primary" style="font-size:12px" @click="ieApplyCrop">
									<i class="bi bi-check me-1"></i> Tambah Crop
								</button>
							</div>

							{{-- RESIZE --}}
							<div v-if="imageEditor.activeTool==='resize'" class="d-flex align-items-center gap-3 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-arrows-angle-expand me-1"></i>Resize</span>
								<div class="d-flex align-items-center gap-2">
									<label style="font-size:12px; color:#64748b">W:</label>
									<input type="number" class="form-control form-control-sm" style="width:80px"
										   v-model.number="imageEditor.resize.width" min="1" @input="ieResizeWidthChanged">
									<label style="font-size:12px; color:#64748b">H:</label>
									<input type="number" class="form-control form-control-sm" style="width:80px"
										   v-model.number="imageEditor.resize.height" min="1" @input="ieResizeHeightChanged">
								</div>
								<div class="form-check mb-0">
									<input class="form-check-input" type="checkbox" id="ieKeepRatio" v-model="imageEditor.resize.keepRatio">
									<label class="form-check-label" for="ieKeepRatio" style="font-size:12px">Pertahankan rasio</label>
								</div>
								<div class="d-flex gap-2">
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.resize.width=1920;imageEditor.resize.height=1080;imageEditor.resize.keepRatio=false">1920×1080</button>
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.resize.width=1280;imageEditor.resize.height=720;imageEditor.resize.keepRatio=false">1280×720</button>
									<button class="btn btn-sm btn-outline-secondary" style="font-size:12px" @click="imageEditor.resize.width=800;imageEditor.resize.height=600;imageEditor.resize.keepRatio=false">800×600</button>
								</div>
								<button class="btn btn-sm btn-primary" style="font-size:12px" @click="ieApplyResize">
									<i class="bi bi-check me-1"></i> Tambah Resize
								</button>
							</div>

							{{-- ROTATE --}}
							<div v-if="imageEditor.activeTool==='rotate'" class="d-flex align-items-center gap-3 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-arrow-clockwise me-1"></i>Rotate</span>
								<button class="btn btn-sm btn-outline-secondary" @click="ieRotate(90)"><i class="bi bi-arrow-clockwise"></i> +90°</button>
								<button class="btn btn-sm btn-outline-secondary" @click="ieRotate(-90)"><i class="bi bi-arrow-counterclockwise"></i> -90°</button>
								<button class="btn btn-sm btn-outline-secondary" @click="ieRotate(180)">180°</button>
							</div>

							{{-- FLIP --}}
							<div v-if="imageEditor.activeTool==='flip'" class="d-flex align-items-center gap-3 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-symmetry-horizontal me-1"></i>Flip</span>
								<button class="btn btn-sm btn-outline-secondary" @click="ieFlip('h')"><i class="bi bi-symmetry-vertical me-1"></i> Horizontal</button>
								<button class="btn btn-sm btn-outline-secondary" @click="ieFlip('v')"><i class="bi bi-symmetry-horizontal me-1"></i> Vertikal</button>
							</div>

							{{-- ADJUST --}}
							<div v-if="imageEditor.activeTool==='adjust'" class="d-flex align-items-center gap-4 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-sliders me-1"></i>Brightness/Contrast</span>
								<div class="d-flex align-items-center gap-2">
									<label style="font-size:12px; color:#64748b; white-space:nowrap">Brightness: @{{ imageEditor.brightness }}</label>
									<input type="range" class="form-range" style="width:120px" min="-100" max="100" v-model.number="imageEditor.brightness">
								</div>
								<div class="d-flex align-items-center gap-2">
									<label style="font-size:12px; color:#64748b; white-space:nowrap">Contrast: @{{ imageEditor.contrast }}</label>
									<input type="range" class="form-range" style="width:120px" min="-100" max="100" v-model.number="imageEditor.contrast">
								</div>
								<button class="btn btn-sm btn-primary" style="font-size:12px" @click="ieApplyAdjust">
									<i class="bi bi-check me-1"></i> Tambah Adjustment
								</button>
							</div>

							{{-- FILTER --}}
							<div v-if="imageEditor.activeTool==='filter'" class="d-flex align-items-center gap-2 flex-wrap">
								<span style="font-weight:600; font-size:13px"><i class="bi bi-magic me-1"></i>Filter</span>
								<button v-for="f in ['grayscale','sepia','blur','sharpen','invert']" :key="f"
										class="btn btn-sm"
										:class="imageEditor.activeFilter===f ? 'btn-primary' : 'btn-outline-secondary'"
										style="font-size:12px; text-transform:capitalize"
										@click="ieApplyFilter(f)">
									@{{ f }}
								</button>
							</div>

						</div>

						{{-- Image preview --}}
						<div style="flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#0f172a; padding:16px">
							<div v-if="imageEditor.loading" class="text-white">
								<div class="spinner-border me-2"></div> Memuat gambar...
							</div>
							<img v-else-if="imageEditor.base64"
								 :src="imageEditor.base64"
								 style="max-width:100%; max-height:100%; border-radius:4px; box-shadow:0 4px 32px rgba(0,0,0,.5)"
								 :style="imageEditor.activeFilter==='grayscale' ? 'filter:grayscale(1)' :
										 imageEditor.activeFilter==='sepia' ? 'filter:sepia(1)' :
										 imageEditor.activeFilter==='blur' ? 'filter:blur(3px)' :
										 imageEditor.activeFilter==='invert' ? 'filter:invert(1)' :
										 `filter:brightness(${1 + imageEditor.brightness/100}) contrast(${1 + imageEditor.contrast/100})`">
							<div v-else class="text-muted">Tidak ada gambar</div>
						</div>
					</div>
				</div>

				{{-- Footer --}}
				<div class="modal-footer py-2" style="flex-shrink:0; background:#f8fafc; border-top:1px solid #e2e8f0">
					<div class="form-check form-check-inline mb-0 me-auto">
						<input class="form-check-input" type="checkbox" id="ieSaveAsNew" v-model="imageEditor.saveAsNew">
						<label class="form-check-label" for="ieSaveAsNew" style="font-size:12px">
							Simpan sebagai file baru (tidak overwrite asli)
						</label>
					</div>
					<button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button class="btn btn-sm btn-success" @click="saveImageEdit"
							:disabled="imageEditor.saving || imageEditor.operations.length===0">
						<span v-if="imageEditor.saving" class="spinner-border spinner-border-sm me-1"></span>
						<i v-else class="bi bi-check2 me-1"></i>
						Simpan Hasil Edit
						<span v-if="imageEditor.operations.length" class="badge bg-light text-dark ms-1">@{{ imageEditor.operations.length }}</span>
					</button>
				</div>
			</div>
		</div>
	</div>

</div>{{-- #fm-app --}}

{{-- Pusher.js CDN — untuk Reverb FM App 2 (File Manager) --}}
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>

<script>
// Reverb FM App 2 — key terpisah dari CMS Main (App 1)
// Port sama (9001), app_id berbeda — Reverb server route berdasarkan key
const fmReverb = new Pusher('{{ env("REVERB_FM_APP_KEY", "fm_reverb_key_arunika") }}', {
	wsHost:            '{{ env("REVERB_FM_HOST", env("REVERB_HOST", "laravel-12-phoenix.aruna")) }}',
	wsPort:            {{ env("REVERB_SERVER_PORT", 9001) }},
	wssPort:           443,
	forceTLS:          true,
	disableStats:      true,
	enabledTransports: ['ws', 'wss'],
	cluster:           '',
});

fmReverb.connection.bind('connected', function() {
	console.log('✅ Reverb FM connected!');
});

fmReverb.connection.bind('error', function(err) {
	console.warn('❌ Reverb FM error:', err);
});

// Expose ke window agar vueV3-filemanager-2026.js bisa akses
window.fmReverb = fmReverb;
</script>

{{-- JS: pakai asset lokal Arunika --}}
<script src="{{ url('assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ url('assets/plugins/vue/core/v3/vue.3.5.21.global.prod.js') }}"></script>
<script src="{{ url('assets/plugins/axios/v1/1.7.7.js') }}"></script>
<script src="{{ url('assets/plugins/lodash/lodash.4.17.21.min.js') }}"></script>

<script>
window.FM_CONFIG = {
	apiBase:   '{{ url("/api/v1/filemanager") }}',
	csrfToken: '{{ csrf_token() }}',
	defaultDisk: '{{ config("filemanager.default_disk", "public") }}',
	@auth
	apiToken: '{{ auth()->user()->tokens()->where("revoked", false)->where(function($q){ $q->whereNull("expires_at")->orWhere("expires_at", ">", now()); })->latest()->value("id") ?? "" }}',
	@else
	apiToken: '',
	@endauth
};
</script>
<script src="{{ url('assets/js/vue3/filemanager/vueV3-filemanager-2026.js?v=') }}{{ time() }}"></script>
</body>
</html>
