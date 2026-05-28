<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<title>Pro Builder: Final Complete V13 (Fix Preview)</title>
	
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

	<link href="https://cdn.plyr.io/3.7.8/plyr.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">

	<style>
	/* =========================================
	   1. VARIABLES & THEME CONFIGURATION
	   ========================================= */
	:root {
		/* Fonts & Dimensions */
		--font-main: 'Inter', sans-serif;
		--sidebar-width: 280px;
		--prop-sidebar-width: 425px;

		/* Colors - Light Theme (Default) */
		--builder-bg: #f8fafc;
		--sidebar-bg: #ffffff;
		--sidebar-border: #e2e8f0;
		--text-primary: #334155;
		--text-secondary: #64748b;
		--accent-color: #3b82f6;

		/* UI Builder Specific Colors */
		--ui-container-border: #10b981;
		--ui-container-bg: #ffffff;
		--ui-row-border: #3b82f6;
		--ui-row-bg: #f1f5f9;
		--ui-col-border: #cbd5e1;
		--ui-widget-hover: #f59e0b;
	}

	[data-bs-theme="dark"] {
		/* Colors - Dark Theme */
		--builder-bg: #0f172a;
		--sidebar-bg: #1e293b;
		--sidebar-border: #334155;
		--text-primary: #f1f5f9;
		--text-secondary: #94a3b8;

		/* UI Builder Specific Colors - Dark */
		--ui-container-bg: #1e293b;
		--ui-row-bg: #334155;
		--ui-col-border: #475569;
	}


	/* =========================================
	   2. GLOBAL RESETS & BOOTSTRAP OVERRIDES
	   ========================================= */
	body {
		background-color: var(--builder-bg);
		font-size: 14px;
		font-family: var(--font-main);
		color: var(--text-primary);
		overflow: hidden;
		margin: 0;
	}

	/* Scrollbar Styles */
	.sidebar-left,
	.sidebar-right,
	.widget-picker-modal,
	.overflow-y-scroll,
	.popup-body {
		scrollbar-width: thin;
		scrollbar-color: var(--text-secondary) transparent;
	}

	.popup-body::-webkit-scrollbar {
		width: 6px;
	}

	.popup-body::-webkit-scrollbar-thumb {
		background: #ccc;
		border-radius: 3px;
	}

	/* Bootstrap Dark Mode Overrides */
	[data-bs-theme="dark"] .form-control,
	[data-bs-theme="dark"] .form-select,
	[data-bs-theme="dark"] .form-control-modern {
		background-color: #1e293b;
		border-color: #475569;
		color: #f1f5f9;
	}

	[data-bs-theme="dark"] .form-control:focus,
	[data-bs-theme="dark"] .form-control-modern:focus {
		border-color: var(--accent-color);
		background-color: #0f172a;
		color: white;
	}

	/* Fix Background Helpers in Dark Mode */
	[data-bs-theme="dark"] .bg-light {
		background-color: #334155 !important;
		color: #f1f5f9 !important;
		border-color: #475569 !important;
	}

	[data-bs-theme="dark"] .bg-white {
		background-color: var(--sidebar-bg) !important;
		color: var(--text-primary) !important;
	}


	/* =========================================
	   3. CUSTOM 4K GRID SYSTEM (3XL)
	   ========================================= */
	.container-3xl {
		max-width: 2400px;
		width: 100%;
		margin-right: auto;
		margin-left: auto;
	}

	.col-3xl-1 {
		flex: 0 0 auto;
		width: 8.33333333%;
	}

	.col-3xl-2 {
		flex: 0 0 auto;
		width: 16.66666667%;
	}

	.col-3xl-3 {
		flex: 0 0 auto;
		width: 25%;
	}

	.col-3xl-4 {
		flex: 0 0 auto;
		width: 33.33333333%;
	}

	.col-3xl-5 {
		flex: 0 0 auto;
		width: 41.66666667%;
	}

	.col-3xl-6 {
		flex: 0 0 auto;
		width: 50%;
	}

	.col-3xl-7 {
		flex: 0 0 auto;
		width: 58.33333333%;
	}

	.col-3xl-8 {
		flex: 0 0 auto;
		width: 66.66666667%;
	}

	.col-3xl-9 {
		flex: 0 0 auto;
		width: 75%;
	}

	.col-3xl-10 {
		flex: 0 0 auto;
		width: 83.33333333%;
	}

	.col-3xl-11 {
		flex: 0 0 auto;
		width: 91.66666667%;
	}

	.col-3xl-12 {
		flex: 0 0 auto;
		width: 100%;
	}

	.col-3xl-auto {
		flex: 0 0 auto;
		width: auto;
	}

	/* Grid Utilities */
	.w-3xl-25 {
		width: 25% !important;
	}

	.w-3xl-50 {
		width: 50% !important;
	}

	.w-3xl-75 {
		width: 75% !important;
	}

	.w-3xl-100 {
		width: 100% !important;
	}

	.w-3xl-auto {
		width: auto !important;
	}

	.g-3xl-0 {
		--bs-gutter-x: 0;
	}

	.g-3xl-1 {
		--bs-gutter-x: 0.25rem;
	}

	.g-3xl-2 {
		--bs-gutter-x: 0.5rem;
	}

	.g-3xl-3 {
		--bs-gutter-x: 1rem;
	}

	.g-3xl-4 {
		--bs-gutter-x: 1.5rem;
	}

	.g-3xl-5 {
		--bs-gutter-x: 3rem;
	}

	/* Standard Responsive Utilities Overrides */
	@media (min-width: 768px) {
		.w-md-25 {
			width: 25% !important;
		}

		.w-md-50 {
			width: 50% !important;
		}

		.w-md-75 {
			width: 75% !important;
		}

		.w-md-100 {
			width: 100% !important;
		}

		.w-md-auto {
			width: auto !important;
		}
	}

	@media (min-width: 1200px) {
		.w-xl-25 {
			width: 25% !important;
		}

		.w-xl-50 {
			width: 50% !important;
		}

		.w-xl-75 {
			width: 75% !important;
		}

		.w-xl-100 {
			width: 100% !important;
		}

		.w-xl-auto {
			width: auto !important;
		}
	}

	@media (min-width: 1400px) {
		.w-xxl-25 {
			width: 25% !important;
		}

		.w-xxl-50 {
			width: 50% !important;
		}

		.w-xxl-75 {
			width: 75% !important;
		}

		.w-xxl-100 {
			width: 100% !important;
		}

		.w-xxl-auto {
			width: auto !important;
		}
	}


	/* =========================================
	   4. MAIN BUILDER LAYOUT
	   ========================================= */
	.builder-layout {
		display: flex;
		height: calc(100vh - 60px);
		width: 100%;
		position: relative;
	}

	/* Canvas Area (Editor) */
	.canvas-area {
		flex-grow: 1;
		background-color: var(--builder-bg);
		background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
		background-size: 24px 24px;
		overflow-y: auto;
		overflow-x: hidden;
		display: flex;
		flex-direction: column;
		align-items: stretch;
		transition: all 0.3s ease;
		position: relative;
		box-shadow: none !important;
		padding: 40px;
	}

	/* Preview / Viewer Styles */
	.preview-container {
		flex-grow: 1;
		background-color: var(--builder-bg);
		overflow-y: auto;
		overflow-x: hidden;
		display: flex;
		flex-direction: column;
		align-items: center;
		padding-top: 20px;
		height: calc(100vh - 60px);
		transition: background-color 0.3s ease;
	}

	.preview-frame {
		min-height: 100%;
		box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
	}


	/* =========================================
	   5. SIDEBARS
	   ========================================= */

	/* --- Sidebar Left (Components) --- */
	.sidebar-left {
		width: var(--sidebar-width);
		background: var(--sidebar-bg);
		border-right: 1px solid var(--sidebar-border);
		overflow-y: auto;
		flex-shrink: 0;
		padding: 15px;
		z-index: 20;
		transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
		overflow-x: hidden;
		white-space: nowrap;
	}

	.sidebar-left.collapsed {
		width: 0 !important;
		padding: 15px 0 !important;
		border-right: none;
	}

	.sidebar-category-title {
		font-size: 0.7rem;
		text-transform: uppercase;
		font-weight: 700;
		color: var(--text-secondary);
		margin: 20px 0 10px 0;
		letter-spacing: 0.5px;
	}

	.sidebar-grid {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 10px;
	}

	/* Sidebar Item (Draggable) */
	.draggable-item {
		cursor: grab;
		background: white;
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		height: 80px;
		transition: all 0.2s;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
		text-align: center;
		padding: 5px;
	}

	.draggable-item:hover {
		border-color: var(--accent-color);
		color: var(--accent-color);
		transform: translateY(-2px);
		box-shadow: 0 4px 8px rgba(59, 130, 246, 0.1);
	}

	.draggable-item i {
		font-size: 20px;
		margin-bottom: 8px;
		color: var(--text-secondary);
	}

	.draggable-item:hover i {
		color: var(--accent-color);
	}

	.draggable-item span {
		font-size: 11px;
		font-weight: 500;
	}

	/* Dark Mode: Sidebar Items */
	[data-bs-theme="dark"] .draggable-item {
		background-color: #1e293b;
		border-color: #475569;
		color: #cbd5e1;
		box-shadow: none;
	}

	[data-bs-theme="dark"] .draggable-item:hover {
		border-color: var(--accent-color);
		color: var(--accent-color);
		background-color: #0f172a;
	}

	/* --- Sidebar Right (Floating Properties Popup) --- */
	.sidebar-right {
		position: fixed;
		width: var(--prop-sidebar-width);
		max-height: 82.5vh;
		background: var(--sidebar-bg);
		border: 1px solid var(--sidebar-border) !important;
		box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
		border-radius: 12px;
		z-index: 1050;
		display: flex;
		flex-direction: column;
		overflow: hidden;
		transition: opacity 0.2s ease;
	}

	.popup-header {
		color: var(--text-primary);
		background: var(--ui-row-bg);
		padding: 10px 15px;
		border-bottom: 1px solid var(--sidebar-border);
		cursor: grab;
		display: flex;
		justify-content: space-between;
		align-items: center;
		user-select: none;
	}

	.popup-header:active {
		cursor: grabbing;
	}

	.popup-body {
		padding: 15px;
		overflow-y: auto;
		flex-grow: 1;
	}

	/* Sidebar Settings Animation */
	.slider-settings-enter-active,
	.slider-settings-leave-active {
		transition: all 0.3s ease-in-out;
		max-height: 500px;
		opacity: 1;
		overflow: hidden;
	}

	.slider-settings-enter-from,
	.slider-settings-leave-to {
		max-height: 0;
		opacity: 0;
		padding-top: 0 !important;
		padding-bottom: 0 !important;
		margin-top: 0 !important;
		margin-bottom: 0 !important;
	}


	/* =========================================
	   6. UI BUILDER COMPONENTS (THE BOX MODEL)
	   ========================================= */

	/* --- Base UI Box --- */
	.ui-box {
		position: relative;
		background: var(--ui-container-bg);
		border: 1px solid #e2e8f0;
		border-radius: 6px;
		margin-top: 24px;
		margin-bottom: 24px;
		box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
		transition: all 0.2s ease;
		display: flex;
		flex-direction: column;
	}

	.ui-box.active {
		border: 2px solid var(--accent-color);
		z-index: 10;
		box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
	}

	.ui-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0 10px;
		height: 32px;
		border-bottom: 1px solid #e2e8f0;
		border-radius: 5px 5px 0 0;
		background: #f8fafc;
		user-select: none;
	}

	[data-bs-theme="dark"] .ui-header {
		background: #334155;
		border-color: #475569;
	}

	.ui-label {
		font-size: 11px;
		font-weight: 700;
		text-transform: uppercase;
		color: white;
		padding: 3px 8px;
		border-radius: 3px;
		letter-spacing: 0.5px;
		line-height: 1;
	}

	.ui-body {
		padding: 20px;
		min-height: 80px;
		height: 100%;
		border-radius: 0 0 6px 6px;
	}

	/* Actions (Edit/Delete Buttons) */
	.ui-actions {
		display: flex;
		gap: 4px;
		opacity: 0.7;
	}

	.ui-box:hover .ui-actions {
		opacity: 1;
	}

	.action-btn {
		width: 22px;
		height: 22px;
		display: flex;
		align-items: center;
		justify-content: center;
		border: 1px solid #cbd5e1;
		background: white;
		color: #64748b;
		border-radius: 4px;
		font-size: 10px;
		cursor: pointer;
		transition: all 0.1s;
	}

	.action-btn:hover {
		background: var(--accent-color);
		color: white;
		border-color: var(--accent-color);
	}

	.action-btn.danger:hover {
		background: #ef4444;
		border-color: #ef4444;
		color: white;
	}

	/* --- Container UI --- */
	.container-ui {
		border-top: 4px solid var(--ui-container-border);
	}

	.container-ui > .ui-header .ui-label {
		background: var(--ui-container-border);
	}

	/* --- Row UI --- */
	.row-ui {
		border-top: 4px solid var(--ui-row-border);
		background-color: var(--ui-row-bg);
		margin-bottom: 15px;
	}

	.row-ui > .ui-header .ui-label {
		background: var(--ui-row-border);
	}

	.row-ui > .ui-body {
		padding: 28px 15px;
	}

	/* Nested Row Fixes */
	.nested-row {
		border-top-color: #8b5cf6 !important;
		background: white;
	}

	.nested-row .ui-label {
		background: #8b5cf6 !important;
	}

	[data-bs-theme="dark"] .nested-row {
		background: var(--ui-row-bg) !important;
		border-color: #475569 !important;
	}

	/* --- Column UI --- */
	.col-ui {
		border: 1px dashed #cbd5e1;
		background: rgba(255, 255, 255, 0.5);
		height: 100%;
		position: relative;
		min-height: 60px;
		border-radius: 4px;
		transition: background 0.2s;
	}

	.col-ui:hover {
		background: #fff;
		border-color: var(--text-secondary);
	}

	.col-ui.active {
		border: 2px solid var(--text-secondary);
		background: white;
	}

	/* Dark Mode: Column UI */
	[data-bs-theme="dark"] .col-ui {
		background: rgba(30, 41, 59, 0.3); /* Slate-800 transparent */
		border-color: #475569;
		color: #e2e8f0;
	}

	[data-bs-theme="dark"] .col-ui:hover {
		background: #1e293b;
		border-color: #94a3b8;
	}

	[data-bs-theme="dark"] .col-ui.active {
		background: #0f172a;
		border-color: var(--accent-color);
	}

	/* Column Handles & Actions */
	.col-handle {
		position: absolute;
		top: -16px;
		left: 0px;
		background: white;
		border: 1px solid #cbd5e1;
		color: var(--text-primary);
		font-size: 10px;
		font-weight: 700;
		padding: 1px 6px;
		border-radius: 3px;
		z-index: 5;
		cursor: grab;
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
	}

	[data-bs-theme="dark"] .col-handle {
		background: #334155;
		border-color: #475569;
		color: #cbd5e1;
	}

	.col-actions {
		position: absolute;
		top: -25px;
		left: auto;
		right: 0px;
		display: none;
		background: white;
		padding: 2px;
		border-radius: 3px;
		border: 1px solid #e2e8f0;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		z-index: 90;
	}

	.col-ui:hover .col-actions,
	.col-ui.active .col-actions {
		display: flex;
		gap: 3px;
	}

	/* --- Widget UI --- */
	.widget-ui {
		position: relative;
		margin-top: 8px;
		margin-bottom: 8px;
		border: 1px solid transparent;
		padding: 2px;
	}

	.widget-ui:hover {
		border: 1px dashed var(--ui-widget-hover);
	}

	.widget-ui.active {
		outline: 2px solid var(--ui-widget-hover);
		z-index: 2;
	}

	/* Widget Overlay (Hover Menu) */
	.widget-overlay {
		position: absolute;
		top: -22px;
		left: auto;
		right: 0px;
		background: var(--ui-widget-hover);
		color: white;
		border-radius: 4px 4px 0 0;
		padding: 2px 6px;
		display: none;
		align-items: center;
		gap: 6px;
		font-size: 10px;
		font-weight: 600;
		z-index: 100;
		text-transform: uppercase;
	}

	.widget-ui:hover .widget-overlay,
	.widget-ui.active .widget-overlay {
		display: flex;
	}

	.widget-btn {
		background: rgba(255, 255, 255, 0.2);
		border: none;
		color: white;
		width: 16px;
		height: 16px;
		border-radius: 2px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		pointer-events: auto;
	}

	.widget-btn:hover {
		background: rgba(255, 255, 255, 0.4);
	}

	/* Dark Mode: Widget UI Fixes */
	[data-bs-theme="dark"] .widget-ui .text-dark {
		color: #f1f5f9 !important;
	}

	[data-bs-theme="dark"] .widget-ui .text-muted {
		color: #94a3b8 !important;
	}

	[data-bs-theme="dark"] .widget-ui .bg-white,
	[data-bs-theme="dark"] .widget-ui .card {
		background-color: #1e293b !important;
		border-color: #475569 !important;
		color: #f1f5f9 !important;
	}

	[data-bs-theme="dark"] .widget-ui .bg-light {
		background-color: rgba(255, 255, 255, 0.1) !important;
		border-color: rgba(255, 255, 255, 0.1) !important;
	}

	/* Widget Placeholders (Empty State) */
	[data-bs-theme="dark"] .bg-light {
		background-color: rgba(255, 255, 255, 0.05) !important;
		border-color: #475569 !important;
	}


	/* =========================================
	   7. DROP ZONES & PLACEHOLDERS
	   ========================================= */
	/* Root Drop Zone */
	.drop-zone-root {
		min-height: 600px;
		padding-bottom: 100px;
	}

	.drop-zone-root .ui-box:first-child {
		margin-top: 0 !important;
	}

	.drop-zone-root .ui-box:last-child {
		margin-bottom: 0 !important;
	}

	.drop-zone-root:empty:before {
		content: "Drag and Drop Layouts Here";
		display: block;
		text-align: center;
		margin-top: 100px;
		color: var(--text-secondary);
		font-weight: 500;
	}

	/* Row Drop Zone */
	.row-drop-zone {
		min-height: 60px;
		border-radius: 4px;
		transition: background-color 0.2s;
	}

	.row-drop-zone:empty {
		background-color: rgba(59, 130, 246, 0.05);
		border: 1px dashed #cbd5e1;
	}

	/* Widget Drop Zone (Inside Column) */
	.widget-drop-zone {
		min-height: 50px;
		height: 100%;
		border-radius: 4px;
		padding-top: 1.8rem;
	}

	.widget-drop-zone:empty {
		background-color: rgba(241, 245, 249, 0.6);
		background-image: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" style="fill:none;stroke:%23cbd5e1;stroke-width:1;stroke-dasharray:4,4" /></svg>');
	}

	/* Dark Mode: Widget Drop Zone */
	[data-bs-theme="dark"] .widget-drop-zone:empty {
		background-color: rgba(30, 41, 59, 0.3);
		background-image: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" style="fill:none;stroke:%23475569;stroke-width:1;stroke-dasharray:4,4" /></svg>');
	}

	/* Add Widget Button Fixes (Dark Mode) */
	[data-bs-theme="dark"] .widget-drop-zone + div {
		background-color: rgba(255, 255, 255, 0.05) !important;
		border-color: #475569 !important;
		color: #94a3b8 !important;
	}

	[data-bs-theme="dark"] .widget-drop-zone + div:hover {
		background-color: rgba(255, 255, 255, 0.1) !important;
		border-color: var(--accent-color) !important;
		color: var(--accent-color) !important;
	}

	/* Clickable "Add Widget" Area Fix */
	[data-bs-theme="dark"] div[onclick*="openWidgetPicker"] {
		background-color: rgba(255, 255, 255, 0.08) !important;
		border-color: rgba(255, 255, 255, 0.3) !important;
		color: rgba(255, 255, 255, 0.9) !important;
	}

	[data-bs-theme="dark"] div[onclick*="openWidgetPicker"]:hover {
		background-color: rgba(255, 255, 255, 0.15) !important;
		border-color: var(--accent-color) !important;
		color: var(--accent-color) !important;
	}


	/* =========================================
	   8. PROPERTY PANEL COMPONENTS
	   ========================================= */
	.prop-header {
		padding: 15px;
		border-bottom: 1px solid var(--sidebar-border);
		background: #f8fafc;
		font-weight: 600;
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 50px;
	}

	.prop-body {
		padding: 20px;
		flex-grow: 1;
		overflow-y: auto;
	}

	.prop-group {
		margin-bottom: 24px;
	}

	.prop-label {
		font-size: 11px;
		font-weight: 600;
		color: var(--text-secondary);
		margin-bottom: 8px;
		text-transform: uppercase;
	}

	/* Form Controls */
	.form-control-modern {
		border: 1px solid #e2e8f0;
		border-radius: 4px;
		font-size: 13px;
		padding: 8px 10px;
		background: white;
		color: var(--text-primary);
		width: 100%;
		display: block;
	}

	.form-control-modern:focus {
		border-color: var(--accent-color);
		outline: none;
		box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
	}

	.ck-editor__editable {
		min-height: 120px;
		background-color: white !important;
		color: black !important;
	}

	.ck.ck-toolbar {
		flex-wrap: wrap !important;
	}

	/* Responsive Switcher (5-Tier) */
	.resp-switcher {
		display: flex;
		background: #e2e8f0;
		padding: 2px;
		border-radius: 6px;
		margin-bottom: 10px;
	}

	[data-bs-theme="dark"] .resp-switcher {
		background: #334155;
	}

	.resp-btn {
		flex: 1;
		border: none;
		background: transparent;
		padding: 6px 4px;
		font-size: 12px;
		border-radius: 4px;
		color: #64748b;
		cursor: pointer;
		transition: 0.2s;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	[data-bs-theme="dark"] .resp-btn {
		color: #cbd5e1;
	}

	.resp-btn.active {
		background: white;
		color: var(--accent-color);
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
		font-weight: 700;
	}

	[data-bs-theme="dark"] .resp-btn.active {
		background: #0f172a;
		color: var(--accent-color);
	}

	.resp-btn:hover {
		color: var(--accent-color);
	}

	/* Sidebar Right Specific Inputs */
	.sidebar-right .form-check-input {
		height: 1.25em;
	}

	.sidebar-right .form-switch .form-check-input {
		width: 2.25em;
		border-radius: 2.25em;
		margin-right: .5em;
	}

	/* Preview Button Group */
	.preview-btn-group button {
		padding: 4px 8px;
		font-size: 12px;
	}

	.preview-btn-group button i {
		margin-right: 4px;
	}

	/* =========================================
	   9. MODALS & OVERLAYS
	   ========================================= */

	/* Widget Picker Modal */
	.widget-picker-modal {
		background: var(--sidebar-bg) !important;
		color: var(--text-primary);
		border: 1px solid var(--sidebar-border);
	}

	.widget-picker-btn {
		background: var(--ui-container-bg);
		border: 1px solid var(--sidebar-border);
		color: var(--text-primary) !important;
		transition: all 0.2s;
	}

	.widget-picker-btn:hover {
		border-color: var(--accent-color);
		background: var(--ui-row-bg);
		color: var(--accent-color) !important;
		transform: translateY(-2px);
		box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
	}

	.widget-picker-btn i {
		color: var(--accent-color);
	}

	[data-bs-theme="dark"] .widget-picker-btn i {
		color: #60a5fa;
	}

	/* Mobile Warning Overlay */
	.mobile-warning-overlay {
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		background-color: var(--builder-bg);
		z-index: 99999;
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		text-align: center;
		padding: 40px;
	}

	.mw-icon {
		font-size: 64px;
		color: var(--text-secondary);
		margin-bottom: 20px;
	}

	.mw-title {
		font-size: 24px;
		font-weight: 700;
		color: var(--text-primary);
		margin-bottom: 10px;
	}

	.mw-text {
		font-size: 14px;
		color: var(--text-secondary);
		max-width: 400px;
		line-height: 1.6;
	}

	/* =========================================
	   10. WIDGET SPECIFIC STYLES
	   ========================================= */

	/* Media List Widget */
	.media-list-item {
		position: relative;
		overflow: hidden;
		height: 100%;
		transition: transform 0.2s;
	}

	.media-list-item:hover {
		z-index: 2;
	}

	.media-wrapper {
		position: relative;
		width: 100%;
		overflow: hidden;
	}

	.media-overlay-content {
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		padding: 15px;
		z-index: 10;
		transition: background-color 0.3s;
	}

	.media-list-video-container {
		width: 100%;
		aspect-ratio: 16/9;
	}

	.media-list-video-container iframe,
	.media-list-video-container video {
		width: 100%;
		height: 100%;
		object-fit: cover;
	}

	.hover-fade {
		opacity: 0;
		transition: opacity 0.3s ease-in-out;
	}

	.media-list-item:hover .hover-fade {
		opacity: 1;
	}

	/* =========================================
	   11. UTILITIES & HELPERS
	   ========================================= */
	[v-cloak] {
		display: none !important;
	}

	.font-size-inherit {
		font-size: inherit;
	}

	.font-size-10px {
		font-size: 10px;
	}

	.font-size-12px {
		font-size: 12px;
	}

	.font-size-14px {
		font-size: 14px;
	}

	/* Custom Collapse Up */
	.collapse-up {
		position: absolute;
		bottom: 100%;
		margin-bottom: 0.5rem;
		width: 100%;
		z-index: 10;
	}

	/* Animations for Collapse Content */
	.anim-fadeIn { animation: fadeIn 0.5s ease-in-out; }
	.anim-bounce { animation: bounce 0.5s ease-in-out; }
	.anim-pulse { animation: pulse 0.5s ease-in-out; }
	.anim-flip { animation: flip 0.6s ease-in-out; }
	.anim-rotate { animation: rotate 0.5s ease-in-out; }

	@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
	@keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-15px); } 60% { transform: translateY(-7px); } }
	@keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
	@keyframes flip { from { transform: perspective(400px) rotate3d(1, 0, 0, 90deg); animation-timing-function: ease-in; opacity: 0; } 40% { transform: perspective(400px) rotate3d(1, 0, 0, -20deg); animation-timing-function: ease-in; } 60% { transform: perspective(400px) rotate3d(1, 0, 0, 10deg); opacity: 1; } 80% { transform: perspective(400px) rotate3d(1, 0, 0, -5deg); } to { transform: perspective(400px); } }
	@keyframes rotate { from { transform: rotate(-10deg); opacity: 0; } to { transform: rotate(0); opacity: 1; } }
	
	/* Custom utility untuk menyembunyikan nomor pada list-group-numbered */
	.list-group-numbered > .list-group-item.hide-marker-ol::before {
		display: none !important;
	}

	/* Fix nav-tabs hover border on inactive items */
	.nav-tabs .nav-link:not(.active):hover 
	{
		border-bottom-color: transparent !important;
	}

	/* Nav Tabs: mobile horizontal scroll */
	.nav-scroll-mobile {
		-webkit-overflow-scrolling: touch;
		max-width: 100%;
		padding-bottom: 8px;
	}

	@media (max-width: 767.98px) {
		.nav-scroll-mobile {
			overflow-x: auto;
		}
		.nav-scroll-mobile .nav {
			flex-wrap: nowrap !important;
		}
		.nav-scroll-mobile .nav-link {
			white-space: nowrap;
		}
	}

	/* =========================================
	   CSS EDITOR MODAL
	   ========================================= */
	.css-editor-modal {
		position: fixed;
		top: 0; left: 0;
		width: 100%; height: 100%;
		background: rgba(0, 0, 0, 0.5);
		z-index: 9998;
		display: flex;
		align-items: flex-start;
		justify-content: center;
		padding-top: 60px;
	}

	.css-editor-panel {
		background: var(--sidebar-bg);
		border: 1px solid var(--sidebar-border);
		border-radius: 10px;
		box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
		display: flex;
		flex-direction: column;
		overflow: hidden;
		transition: width 0.25s ease, height 0.25s ease, top 0.25s ease, border-radius 0.25s ease;
		width: 520px;
		max-height: calc(100vh - 80px);
	}

	.css-editor-panel.fullscreen {
		position: fixed;
		top: 60px; left: 0;
		width: 100vw !important;
		height: calc(100vh - 60px) !important;
		max-height: none;
		border-radius: 0;
		z-index: 9999;
		margin: 0;
	}

	.css-editor-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 10px 15px;
		background: var(--ui-row-bg);
		border-bottom: 1px solid var(--sidebar-border);
		flex-shrink: 0;
		user-select: none;
	}

	.css-editor-header .editor-title {
		font-size: 13px;
		font-weight: 700;
		color: var(--text-primary);
		display: flex;
		align-items: center;
		gap: 8px;
	}

	.css-editor-header .editor-title i {
		color: #3b82f6;
	}

	.css-editor-body {
		flex: 1;
		display: flex;
		flex-direction: column;
		overflow: hidden;
		padding: 12px;
		gap: 10px;
	}

	.css-editor-hint {
		font-size: 11px;
		color: var(--text-secondary);
		background: var(--ui-row-bg);
		border: 1px solid var(--sidebar-border);
		border-radius: 6px;
		padding: 8px 12px;
		line-height: 1.5;
		flex-shrink: 0;
	}

	.css-editor-hint code {
		background: rgba(59, 130, 246, 0.1);
		color: #3b82f6;
		padding: 1px 5px;
		border-radius: 3px;
		font-size: 11px;
	}

	.css-editor-textarea {
		flex: 1;
		width: 100%;
		min-height: 200px;
		padding: 14px;
		font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
		font-size: 13px;
		line-height: 1.6;
		resize: none;
		border: 1px solid var(--sidebar-border);
		border-radius: 6px;
		background: #0f172a;
		color: #e2e8f0;
		outline: none;
		tab-size: 2;
		transition: border-color 0.2s;
	}

	.css-editor-textarea:focus {
		border-color: var(--accent-color);
		box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
	}

	[data-bs-theme="light"] .css-editor-textarea {
		background: #1e293b;
		color: #e2e8f0;
	}

	.css-editor-footer {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 10px 12px;
		border-top: 1px solid var(--sidebar-border);
		flex-shrink: 0;
		background: var(--ui-row-bg);
	}

	.css-char-count {
		font-size: 11px;
		color: var(--text-secondary);
	}

	.btn-css-apply {
		background: #3b82f6;
		color: white;
		border: none;
		padding: 6px 16px;
		border-radius: 6px;
		font-size: 12px;
		font-weight: 600;
		cursor: pointer;
		transition: background 0.2s;
	}

	.btn-css-apply:hover {
		background: #2563eb;
	}

	.btn-css-clear {
		background: transparent;
		color: #ef4444;
		border: 1px solid #ef4444;
		padding: 5px 12px;
		border-radius: 6px;
		font-size: 12px;
		font-weight: 500;
		cursor: pointer;
		transition: all 0.2s;
	}

	.btn-css-clear:hover {
		background: #ef4444;
		color: white;
	}
	</style>
</head>

<body>

<div id="app" class="d-flex flex-column vh-100" v-cloak>
	<div v-if="isMobileDevice" class="mobile-warning-overlay">
		<i class="fas fa-desktop mw-icon"></i>
		<div class="mw-title">Desktop Required</div>
		<p class="mw-text">
			Maaf, halaman Page Builder ini tidak mendukung perangkat Mobile atau layar kecil.<br>
			Mohon akses menggunakan <strong>Laptop atau PC Desktop</strong> dengan resolusi layar minimal 768px.
		</p>
	</div>

	<div v-else class="d-flex flex-column vh-100">
		<nav class="navbar navbar-expand-lg border-bottom px-4" style="height: 60px; background: var(--sidebar-bg); z-index: 1000;">
			<div class="d-flex align-items-center">
				<button v-if="!previewMode" class="btn btn-sm btn-light border me-3" @click="toggleLeftSidebar" :class="{'active': showLeftSidebar}">
					<i class="fas fa-bars"></i>
				</button>

				<div class="bg-primary text-white rounded px-2 py-1 me-2 fw-bold" style="font-size: 14px;">PB</div>
				<span class="fw-bold" style="font-size: 16px;">ProBuilder <span class="fw-normal text-muted">| V13 Complete (Fix Preview)</span></span>
			</div>
			
			<div class="ms-auto d-flex align-items-center gap-2">
				<button class="btn btn-sm btn-light border" @click="toggleTheme"><i class="fas" :class="theme === 'light' ? 'fa-moon' : 'fa-sun'"></i></button>
				
				<div class="btn-group" v-if="!previewMode">
					<button class="btn btn-sm btn-light border" @click="undo" :disabled="!canUndo"><i class="fas fa-undo"></i></button>
					<button class="btn btn-sm btn-light border" @click="redo" :disabled="!canRedo"><i class="fas fa-redo"></i></button>
				</div>

				<div class="vr mx-2"></div>

				<div class="btn-group me-2 preview-btn-group" v-if="previewMode">
					<button class="btn btn-sm" :class="previewType === 'mobile' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'mobile'"><i class="fas fa-mobile-alt"></i> Mobile</button>
					<button class="btn btn-sm" :class="previewType === 'tablet' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'tablet'"><i class="fas fa-tablet-alt"></i> Tablet</button>
					<button class="btn btn-sm" :class="previewType === 'desktop' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'desktop'"><i class="fas fa-laptop"></i> Std</button>
					<button class="btn btn-sm" :class="previewType === 'fhd' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'fhd'"><i class="fas fa-desktop"></i> FHD</button>
					<button class="btn btn-sm" :class="previewType === '4k' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = '4k'"><i class="fas fa-tv"></i> 4K</button>
				</div>

				<!-- CSS Editor Button -->
				<button class="btn btn-sm btn-outline-secondary px-3" @click="showCssEditor = true" title="Custom CSS Editor" v-if="!previewMode">
					<i class="fas fa-paint-brush me-1"></i> CSS
				</button>

				<button class="btn btn-sm btn-outline-primary px-3" @click="togglePreview"><i class="fas" :class="previewMode ? 'fa-edit' : 'fa-eye'"></i> @{{ previewMode ? 'Edit' : 'Preview' }}</button>
				<button v-if="!previewMode" class="btn btn-sm btn-light border px-3" @click="toggleRightSidebar" :class="{'active': showRightSidebar}"><i class="fas fa-columns"></i></button>
				<button class="btn btn-sm btn-primary px-3 fw-medium" @click="saveJson"><i class="fas fa-save me-1"></i> Save</button>
			</div>
		</nav>

		<div class="builder-layout">
			
			<div class="sidebar-left" v-if="!previewMode" :class="{'collapsed': !showLeftSidebar}">
				<div class="mb-4">
					<div class="sidebar-category-title">{{ t('Page Settings') }}</div>

					<div class="form-group">
						<label class="form-label d-none">{{ t('Page Name') }}</label>
						<input type="text" name="page_name" v-model="pageName" class="form-control form-control-sm" placeholder="{{ t('Page Name') }}">
					</div>
				</div>

				<div class="mb-4">
					<div class="sidebar-category-title">{{ t('Layouts') }}</div>

					<draggable :list="tools.containers" :group="{ name: 'root', pull: 'clone', put: false }" :clone="cloneItem" item-key="label" class="sidebar-grid">
						<template #item="{element}">
							<div class="draggable-item"><i class="far fa-square"></i><span>@{{ element.label }}</span></div>
						</template>
					</draggable>

					<div class="sidebar-category-title mt-4 pt-2">{{ t('Grid') }}</div>

					<draggable :list="tools.rows" :group="{ name: 'section', pull: 'clone', put: false }" :clone="cloneItem" item-key="label" class="sidebar-grid">
						<template #item="{element}">
							<div class="draggable-item"><i class="fas fa-columns"></i><span>@{{ element.label }}</span></div>
						</template>
					</draggable>

					<div class="sidebar-category-title mt-4 pt-2">{{ t('Widgets') }}</div>

					<draggable :list="tools.widgets" :group="{ name: 'widget', pull: 'clone', put: false }" :clone="cloneItem" item-key="label" class="sidebar-grid">
						<template #item="{element}">
							<div class="draggable-item"><i :class="element.icon"></i><span>@{{ element.label }}</span></div>
						</template>
					</draggable>

					<div class="sidebar-category-title mt-4 pt-2">{{ t('Advanced') }}</div>

					<draggable :list="tools.advanced" :group="{ name: 'widget', pull: 'clone', put: false }" :clone="cloneItem" item-key="label" class="sidebar-grid">
						<template #item="{element}">
							<div class="draggable-item"><i :class="element.icon"></i><span>@{{ element.label }}</span></div>
						</template>
					</draggable>
				</div>
			</div>

			<div class="canvas-area" v-if="!previewMode" @click="deselectAll" id="canvasArea">
		
				<draggable v-model="layout" group="root" item-key="id" class="drop-zone-root" ghost-class="opacity-50">
					<template #item="{element: cont, index: i}">
						
						<div class="ui-box container-ui" :class="[{'active': activeItem && activeItem.id === cont.id }]" @click.stop="setActive(cont, 'container')">
							
							<div class="ui-header">
								<div class="ui-label">{{ t('Container') }}</div>
								
								<div class="ui-actions">
									<button class="action-btn" title="Add Row" @click.stop="quickAddRow(cont)" @mousedown.stop><i class="far fa-plus"></i></button>
									<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(layout, i)"><i class="far fa-copy"></i></button>
									<button class="action-btn danger" title="Remove" @click.stop="removeItem(layout, i)"><i class="far fa-trash-alt"></i></button>
								</div>                         
							</div>

							<div class="ui-body">
								<draggable v-model="cont.children" :group="containerGroup" :move="checkContainerDrop" item-key="id" style="min-height: 60px; height:100%;" @add="onDropToContainer($event, cont)">
									<template #item="{element: row, index: j}">
										
										<div class="ui-box row-ui" :class="[{'active': activeItem && activeItem.id === row.id }]" @click.stop="setActive(row, 'row')">
											<div class="ui-header">
												<div class="ui-label">{{ t('Row') }}</div>

												<div class="ui-actions">
													<button class="action-btn" title="Add Column" @click.stop="addColumn(row)"><i class="fas fa-plus"></i></button>
													<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(cont.children, j)"><i class="far fa-copy"></i></button>
													<button class="action-btn danger" title="Delete" @click.stop="removeItem(cont.children, j)"><i class="fas fa-times"></i></button>
												</div>                                               
											</div>
											
											<div class="ui-body">
												<draggable v-model="row.children" :group="rowGroup" :move="checkRowDrop" item-key="id" class="row-drop-zone row m-0 h-100" @add="onDropToRow($event, row)">
													
													<template #item="{element: col, index: k}">
														
														<div :class="[col.widthMobile, col.widthTablet, col.widthDesktop, col.widthFHD]" class="p-1 position-relative">
																
															<div class="col-ui" :class="[{ 'active': activeItem && activeItem.id === col.id }]" @click.stop="setActive(col, 'column')">
																<div class="col-handle">{{ t('Column') }}</div>
																
																<div class="col-actions">
																	<button class="action-btn" title="Add Nested Row" @click.stop="addNestedRow(col)"><i class="fas fa-level-down-alt"></i></button>
																	<button class="action-btn" title="Add Column" @click.stop="addColumn(row)"><i class="fas fa-plus"></i></button>
																	<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(row.children, k)"><i class="far fa-copy"></i></button>
																	<button class="action-btn danger" title="Delete" v-if="row.children.length > 0" @click.stop="removeColumn(row, k)"><i class="fas fa-times"></i></button>
																</div>
																
																<draggable v-model="col.children" :group="{ name: 'col-children', put: ['widget', 'section', 'col-children'] }" item-key="id" class="widget-drop-zone px-4 pb-4" ghost-class="bg-info-subtle" @add="onDropToColumn($event, col)">
																	
																	<template #item="{element: item, index: l}">
																		
																		<div v-if="item.type === 'row'" class="ui-box row-ui nested-row" :class="[{'active': activeItem && activeItem.id === item.id }]" @click.stop="setActive(item, 'row')">
																			<div class="ui-header">
																				<div class="ui-label">{{ t('Nested Row') }}</div>
																				<div class="ui-actions">
																					<button class="action-btn" title="Add Column" @click.stop="addColumn(item)"><i class="fas fa-plus"></i></button>
																					<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(col.children, l)"><i class="far fa-copy"></i></button>
																					<button class="action-btn danger" title="Delete" @click.stop="removeItem(col.children, l)"><i class="fas fa-times"></i></button>
																				</div>																		
																			</div>

																			<div class="ui-body">
																				<draggable v-model="item.children" :group="{ name: 'nested-cols', put: ['section', 'row-cols'] }" item-key="id" class="row-drop-zone row m-0 h-100" @add="onDropToRow($event, item)">
																					<template #item="{element: nestedCol, index: m}">
																						<div :class="[nestedCol.widthMobile, nestedCol.widthTablet, nestedCol.widthDesktop, nestedCol.widthFHD]" class="p-1 position-relative">
																							<div class="col-ui" :class="[{ 'active': activeItem && activeItem.id === nestedCol.id }]" @click.stop="setActive(nestedCol, 'column')">
																								<div class="col-handle">{{ t('Column') }}</div>
																								
																								<div class="col-actions">
																									<button class="action-btn" title="Add Column" @click.stop="addColumn(item)"><i class="fas fa-plus"></i></button>
																									<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(item.children, m)"><i class="far fa-copy"></i></button>
																									<button class="action-btn danger" title="Delete" v-if="item.children.length > 1" @click.stop="removeColumn(item, m)"><i class="fas fa-times"></i></button>
																								</div>
																								
																								<draggable v-model="nestedCol.children" :group="{ name: 'col-children', put: ['widget', 'section', 'col-children'] }" item-key="id" class="widget-drop-zone px-4 pb-4">
																									
																									<template #item="{element: widget, index: n}">
																											<div class="widget-ui" :class="[{ 'active': activeItem && activeItem.id === widget.id }]" @click.stop="setActive(widget, 'widget')">
																												<div class="widget-overlay"><span>@{{ widget.label }}</span><button class="widget-btn" @click.stop="duplicateItem(nestedCol.children, n)"><i class="far fa-copy"></i></button><button class="widget-btn" @click.stop="removeItem(nestedCol.children, n)"><i class="fas fa-times"></i></button></div>																																																				
																												<div v-if="widget.type === 'text'"><div @mousedown.stop><ckeditor-component v-model="widget.content" :base-url="baseUrl"></ckeditor-component></div></div>
																												<div v-if="widget.type === 'image'" class="bg-light border rounded d-flex align-items-center justify-content-center user-select-none shadow-sm" style="height: 150px; pointer-events: none;"><div class="text-center"><i class="far fa-image fa-3x text-secondary opacity-50"></i><div class="small text-muted mt-2 fw-medium" style="font-size: 11px;">{{ t('Image') }}</div></div></div>
																												<div v-if="widget.type === 'button'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-mouse-pointer fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1" style="min-width: 0;"><h6 class="fw-bold text-dark mb-0 text-truncate">@{{ widget.text || 'Button Label' }}</h6><div class="small text-muted text-truncate">Link: @{{ widget.href || '#' }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'card'" class="card h-100 shadow-sm" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border-bottom" style="height: 100px;"><i class="far fa-image fa-2x text-secondary opacity-50"></i></div><div class="card-body p-3"><h6 class="fw-bold text-dark text-truncate mb-1">{{ t('Title') }}</h6><p class="small text-muted text-truncate mb-2">{{ t('Description') }}</p><div class="btn btn-sm btn-primary w-100 disabled" style="opacity: 0.6;">@{{ widget.btnText || 'Button' }}</div></div></div>
																												<div v-if="widget.type === 'list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-lg text-secondary opacity-50" :class="widget.listType === 'ol' ? 'fa-list-ol' : 'fa-list-ul'"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('List Group') }}</h6><div class="small text-muted">@{{ widget.items.length }} Items</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border text-uppercase">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'media'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-2" :class="widget.imagePos === 'end' ? 'flex-row-reverse text-end' : 'text-start'" style="pointer-events: none; overflow: hidden;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 18%; min-width: 40px; max-width: 60px; aspect-ratio: 1/1;"><i class="fas fa-photo-video text-secondary opacity-50" style="font-size: clamp(14px, 1.5vw, 20px);"></i></div><div class="flex-grow-1" style="min-width: 0;"><h6 class="fw-bold text-dark text-truncate mb-0" style="font-size: clamp(12px, 1.2vw, 14px);">{{ t('Media Heading') }}</h6><div class="text-muted text-truncate" style="font-size: 11px;">{{ t('Media Description') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'accordion'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-layer-group fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Accordion Group') }}</h6><div class="small text-muted">@{{ widget.items.length }} {{ t('Collapsibles') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'table'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-table fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Data Table') }}</h6><div class="small text-muted">@{{ widget.tableData.rows.length }} {{ t('Rows') }} × @{{ widget.tableData.headers.length }} {{ t('Cols') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'video'" :class="widget.customClass"><div v-if="!previewMode" class="bg-light border rounded d-flex align-items-center justify-content-center user-select-none shadow-sm position-relative" style="height: 150px; pointer-events: none;"><div class="text-center"><i class="fas fa-play-circle fa-3x text-secondary opacity-50 mb-2"></i><h6 class="fw-bold text-dark mb-1 small">{{ t('Video') }}</h6><div class="small text-muted" style="font-size: 10px;">{{ t('Source') }}: @{{ widget.videoType === 'file' ? 'Local File' : 'YouTube' }}</div></div></div><div v-else class="position-relative w-100" :style="{ width: (widget.width || 100) + '%' }"><div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background: #000;"><iframe v-if="!widget.videoType || widget.videoType === 'youtube'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" :src="'https://www.youtube.com/embed/' + (widget.youtubeUrl ? (widget.youtubeUrl.includes('v=') ? widget.youtubeUrl.split('v=')[1].split('&')[0] : widget.youtubeUrl.split('/').pop()) : 'dQw4w9WgXcQ')" frameborder="0" allowfullscreen></iframe><video v-if="widget.videoType === 'file'" :src="widget.videoSrc" controls style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"></video></div></div></div>
																												<div v-if="widget.type === 'spacer'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-arrows-alt-v fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Spacer') }}</h6><div class="small text-muted">{{ t('Height') }}: @{{ widget.height }}@{{ widget.unit || 'px' }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'divider'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-minus fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Divider') }}</h6><div class="small text-muted">{{ t('Style') }}: @{{ widget.style }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'heading'" class="d-flex p-2 border rounded bg-light shadow-sm align-widgets-center justify-content-center flex-column user-select-none" style="height: 100px; pointer-events: none; border-style: dashed !important;"><i class="fas fa-heading fa-2x text-secondary opacity-50 mb-2"></i> <div class="small text-muted fw-medium text-truncate" style="max-width: 90%;"> {{ t('Heading') }} </div> <div class="badge bg-secondary bg-opacity-10 text-secondary border mt-1" style="font-size: 9px;">{{ t('HEADING') }}</div></div>
																												<div v-if="widget.type === 'media_list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-photo-video fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Media List Group') }}</h6><div class="small text-muted">@{{ widget.items.length }} {{ t('Items') }} (@{{ widget.viewMode }})</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																												<div v-if="widget.type === 'dynamic_post_list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-database fa-lg text-primary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Dynamic Post List') }}</h6><div class="small text-muted">{{ t('Source') }}: @{{ widget.dataType }}</div></div><div class="badge bg-primary bg-opacity-10 text-primary border">{{ t('ADVANCED') }}</div></div>																												

																												<div v-if="widget.type === 'collapse'" class="widget-placeholder-box text-center p-4 w-100" style="border: 2px dashed #adb5bd; background-color: rgba(0,0,0,0.02); border-radius: 6px;">
																													<i :class="widget.icon" class="fa-2x text-secondary mb-2"></i>
																													<h6 class="text-secondary m-0 fw-bold">@{{ widget.label }}</h6>
																													<small class="text-muted d-block mt-1">@{{ widget.triggerText }}</small>
																												</div>

																												<div v-if="widget.type === 'nav_tabs'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;">
																													<div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;">
																														<i class="fas fa-folder-open fa-lg text-secondary opacity-50"></i>
																													</div>
																													<div class="flex-grow-1">
																														<h6 class="fw-bold text-dark mb-0">Nav Tabs</h6>
																														<div class="small text-muted">@{{ widget.items.length }} Tab &bull; Style: @{{ widget.navStyle }} &bull; <em class="text-info" style="font-size: 10px;">Preview untuk interaksi</em></div>
																													</div>
																													<div class="badge bg-secondary bg-opacity-10 text-secondary border">BASIC</div>
																												</div>

																											</div>
																									</template>

																									<template #footer>
																										<div v-if="!previewMode" class="w-100 text-center rounded border border-dashed p-1 mt-3 text-muted user-select-none small" style="cursor: pointer; background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.1) !important; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(0,0,0,0.05)'; this.style.borderColor='rgba(0,0,0,0.2) !important';" onmouseout="this.style.backgroundColor='rgba(0,0,0,0.02)'; this.style.borderColor='rgba(0,0,0,0.1) !important';" @click.stop="openWidgetPicker(nestedCol.children)"><i class="fas fa-plus me-1 opacity-50"></i> {{ t('Add Widget') }}</div>
																									</template>

																								</draggable>
																							</div>
																						</div>
																					</template>
																				</draggable>
																			</div>
																		</div>

																		<div v-else class="widget-ui" :class="[{ 'active': activeItem && activeItem.id === item.id }]" @click.stop="setActive(item, 'widget')">
																			<div class="widget-overlay"><span>@{{ item.label }}</span><button class="widget-btn" @click.stop="duplicateItem(col.children, l)"><i class="far fa-copy"></i></button><button class="widget-btn" @click.stop="removeItem(col.children, l)"><i class="fas fa-times"></i></button></div>
																			
																			<div v-if="item.type === 'text'">
																				<div @mousedown.stop>
																					<ckeditor-component v-model="item.content" :base-url="baseUrl"></ckeditor-component>
																				</div>
																			</div>

																			<div v-if="item.type === 'image'" class="bg-light border rounded d-flex align-items-center justify-content-center user-select-none shadow-sm" style="height: 150px; pointer-events: none;"><div class="text-center"><i class="far fa-image fa-3x text-secondary opacity-25"></i><div class="small text-muted mt-2 fw-medium" style="font-size: 11px;">{{ t('Image') }}</div></div></div>
																			<div v-if="item.type === 'button'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-mouse-pointer fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1" style="min-width: 0;"><h6 class="fw-bold text-dark mb-0 text-truncate">@{{ item.text || 'Button Label' }}</h6><div class="small text-muted text-truncate">{{ t('Link') }}: @{{ item.href || '#' }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'card'" class="card h-100 shadow-sm" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center rounded-top border-bottom" style="height: 100px;"><i class="far fa-image fa-2x text-secondary opacity-25"></i></div><div class="card-body p-3"><h6 class="fw-bold text-dark text-truncate mb-1">{{ t('Title') }}</h6><p class="small text-muted text-truncate mb-2">{{ t('Description') }}</p><div class="btn btn-sm btn-primary w-100 disabled" style="opacity: 0.6;">{{ t('Button') }}</div></div></div>
																			<div v-if="item.type === 'list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-lg text-secondary opacity-50" :class="item.listType === 'ol' ? 'fa-list-ol' : 'fa-list-ul'"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('List Group') }}</h6><div class="small text-muted">@{{ item.items.length }} {{ t('Items') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border text-uppercase">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'media'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3" :class="item.imagePos === 'end' ? 'flex-row-reverse text-end' : 'text-start'" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-photo-video fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1" style="min-width: 0;"><h6 class="fw-bold text-dark text-truncate mb-1">{{ t('Media Heading') }}</h6><div class="small text-muted text-truncate">{{ t('Media Description') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'accordion'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-layer-group fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Accordion Group') }}</h6><div class="small text-muted">@{{ item.items.length }} {{ t('Collapsibles') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'table'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-table fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Data Table') }}</h6><div class="small text-muted">@{{ item.tableData.rows.length }} {{ t('Rows') }} × @{{ item.tableData.headers.length }} {{ t('Cols') }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'video'" :class="item.customClass"><div v-if="!previewMode" class="bg-light border rounded d-flex align-items-center justify-content-center user-select-none shadow-sm position-relative" style="height: 150px; pointer-events: none;"><div class="text-center"><i class="fas fa-play-circle fa-3x text-secondary opacity-50 mb-2"></i><h6 class="fw-bold text-dark mb-1 small">{{ t('Video') }}</h6><div class="small text-muted" style="font-size: 10px;">{{ t('Source') }}: @{{ item.videoType === 'file' ? 'Local File' : 'YouTube' }}</div></div></div><div v-else class="position-relative w-100" :style="{ width: (item.width || 100) + '%' }"><div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background: #000;"><iframe v-if="!item.videoType || item.videoType === 'youtube'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" :src="'https://www.youtube.com/embed/' + (item.youtubeUrl ? (item.youtubeUrl.includes('v=') ? item.youtubeUrl.split('v=')[1].split('&')[0] : item.youtubeUrl.split('/').pop()) : 'dQw4w9WgXcQ')" frameborder="0" allowfullscreen></iframe><video v-if="item.videoType === 'file'" :src="item.videoSrc" controls style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;"></video></div></div></div>
																			<div v-if="item.type === 'spacer'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-arrows-alt-v fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Spacer') }}</h6><div class="small text-muted">{{ t('Height') }}: @{{ item.height }}@{{ item.unit || 'px' }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'divider'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-minus fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Divider') }}</h6><div class="small text-muted">{{ t('Style') }}: @{{ item.style }}</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'heading'" class="d-flex p-2 border rounded bg-light shadow-sm align-items-center justify-content-center flex-column user-select-none" style="height: 100px; pointer-events: none; border-style: dashed !important;"><i class="fas fa-heading fa-2x text-secondary opacity-50 mb-2"></i> <div class="small text-muted fw-medium text-truncate" style="max-width: 90%;"> {{ t('Heading') }} </div> <div class="badge bg-secondary bg-opacity-10 text-secondary border mt-1" style="font-size: 9px;">{{ t('HEADING') }}</div></div>
																			<div v-if="item.type === 'media_list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-photo-video fa-lg text-secondary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Media List Group') }}</h6><div class="small text-muted">@{{ item.items.length }} {{ t('Items') }} (@{{ item.viewMode }})</div></div><div class="badge bg-secondary bg-opacity-10 text-secondary border">{{ t('BASIC') }}</div></div>
																			<div v-if="item.type === 'dynamic_post_list'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;"><div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;"><i class="fas fa-database fa-lg text-primary opacity-50"></i></div><div class="flex-grow-1"><h6 class="fw-bold text-dark mb-0">{{ t('Dynamic Post List') }}</h6><div class="small text-muted">{{ t('Source') }}: @{{ item.dataType }}</div></div><div class="badge bg-primary bg-opacity-10 text-primary border">{{ t('ADVANCED') }}</div></div>

																			<div v-if="item.type === 'collapse'" class="widget-placeholder-box text-center p-4 w-100" style="border: 2px dashed #adb5bd; background-color: rgba(0,0,0,0.02); border-radius: 6px;">
																				<i :class="item.icon" class="fa-2x text-secondary mb-2"></i>
																				<h6 class="text-secondary m-0 fw-bold">@{{ item.label }}</h6>
																				<small class="text-muted d-block mt-1">@{{ item.triggerText }}</small>
																			</div>

																			<div v-if="item.type === 'nav_tabs'" class="d-flex p-2 border rounded bg-white shadow-sm align-items-center gap-3 user-select-none" style="pointer-events: none;">
																				<div class="bg-light d-flex align-items-center justify-content-center border rounded flex-shrink-0" style="width: 50px; height: 50px;">
																					<i class="fas fa-folder-open fa-lg text-secondary opacity-50"></i>
																				</div>
																				<div class="flex-grow-1">
																					<h6 class="fw-bold text-dark mb-0">Nav Tabs</h6>
																					<div class="small text-muted">@{{ item.items.length }} Tab &bull; Style: @{{ item.navStyle }} &bull; <em class="text-info" style="font-size: 10px;">Preview untuk interaksi</em></div>
																				</div>
																				<div class="badge bg-secondary bg-opacity-10 text-secondary border">BASIC</div>
																			</div>

																		</div>
																	</template>
																
																	<template #footer>
																		<div v-if="!previewMode" class="w-100 text-center rounded border border-dashed p-2 mt-3 text-muted user-select-none small"style="cursor: pointer; background-color: rgba(0,0,0,0.02); border-color: rgba(0,0,0,0.1) !important; transition: all 0.2s ease;"onmouseover="this.style.backgroundColor='rgba(0,0,0,0.05)'; this.style.borderColor='rgba(0,0,0,0.2) !important';" onmouseout="this.style.backgroundColor='rgba(0,0,0,0.02)'; this.style.borderColor='rgba(0,0,0,0.1) !important';"@click.stop="openWidgetPicker(col.children)"><i class="fas fa-plus me-1 opacity-50"></i>{{ t('Add Widget') }}</div>
																	</template>

																</draggable>

															</div>
														</div>
													</template>
												
												</draggable>
											</div>
										</div>
									</template>

									<template #footer>
										<div v-if="!cont.children || cont.children.length === 0" class="text-center p-4 text-muted border border-dashed m-2 rounded bg-light">
											<small>{!! t('Container Empty. Click {1} above or drop row here.', '<i class="fas fa-plus text-success"></i>') !!}</small>
										</div>
									</template>

								</draggable>
							</div>
						</div>
					</template>

					<template #footer>
						<div v-if="!previewMode" class="py-4 text-center">
							<div v-if="layout.length === 0" class="text-muted mb-3 user-select-none opacity-50">
								<i class="fas fa-arrows-alt fa-2x mb-2"></i>
								<p>{{ ('Drag and drop layouts here') }}</p>
							</div>

							<button class="btn btn-outline-primary border-dashed px-4 py-2 rounded-pill" @click="quickAddContainer">
								<i class="fas fa-plus me-2"></i> {{ t('Add New Container Section') }}
							</button>
						</div>
					</template>
				</draggable>

			</div>

			<preview-viewer v-if="previewMode" :data="layout" :view-type="previewType" :custom-css="customCss"></preview-viewer>

			<div class="sidebar-right" v-if="!previewMode && showRightSidebar" :style="{ top: popupPos.top + 'px', left: popupPos.left + 'px' }">

				<div class="popup-header" @mousedown="startDrag">
					<div class="fw-bold text-secondary">
						<i class="fas fa-arrows-alt me-1"></i> {{ t('Properties') }}
					</div>
				
					<button class="btn btn-light btn-sm border-0 text-muted" @click="toggleRightSidebar">
						<i class="fas fa-times fa-lg"></i>
					</button>
				</div>
				
				<div class="prop-body p-2">
					<div v-if="!activeItem" class="text-center text-secondary my-5"><i class="far fa-hand-pointer fa-2x mb-3 text-muted opacity-50"></i><p class="small fw-medium">{{ t('Select an element to edit properties') }}</p></div>

					<!-- Container --->
					<div v-if="activeType === 'container'">
						<div class="prop-group">
							<div class="prop-label border-bottom py-2 mx-2">{{ t('Container Width') }}</div>

							<div class="prop-group-body p-2">
								<div class="resp-switcher">
									<button class="resp-btn" :class="{active: activeViewMode === 'mobile'}" @click="activeViewMode = 'mobile'">{{ t('Mobile') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'tablet'}" @click="activeViewMode = 'tablet'">{{ t('Tablet') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'desktop'}" @click="activeViewMode = 'desktop'">{{ t('Desktop') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'fhd'}" @click="activeViewMode = 'fhd'">{{ t('Full HD') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === '4k'}" @click="activeViewMode = '4k'">{{ t('4K') }}</button>
								</div>

								<div v-if="activeViewMode === 'mobile'">
									<label class="small text-muted mb-2 d-block">{{ t('Layout') }} {{ t('Mobile') }} (< 768px)</label>
									
									<select v-model="activeItem.classMobile" class="form-select font-size-12px">
										<option value="container">{{ t('Fixed Width') }}</option>
										<option value="container-fluid">{{ t('Full Width') }}</option>
									</select>
								</div>
								
								<div v-if="activeViewMode === 'tablet'">
									<label class="small text-muted mb-2 d-block">{{ t('Layout') }} {{ t('Tablet') }} (≥ 768px)</label>
									
									<select v-model="activeItem.classTablet" class="form-select font-size-12px">
										<option value="container">{{ t('Fixed Width') }}</option>
										<option value="container-fluid">{{ t('Full Width') }}</option>
									</select>
								</div>
								
								<div v-if="activeViewMode === 'desktop'">
									<label class="small text-muted mb-2 d-block">{{ t('Layout') }} {{ t('Desktop') }} (≥ 1200px)</label>
									
									<select v-model="activeItem.classDesktop" class="form-select font-size-12px">
										<option value="container">{{ t('Fixed Width') }}</option>
										<option value="container-fluid">{{ t('Full Width') }}</option>
									</select>
								</div>
								
								<div v-if="activeViewMode === 'fhd'">
									<label class="small text-muted mb-2 d-block">{{ t('Layout') }} {{ t('Full HD') }} (≥ 1400px)</label>
									
									<select v-model="activeItem.classFHD" class="form-select font-size-12px">
										<option value="container">{{ t('Fixed Width') }}</option>
										<option value="container-fluid">{{ t('Full Width') }}</option>
									</select>
								</div>
								
								<div v-if="activeViewMode === '4k'">
									<label class="small text-muted mb-2 d-block">{{ t('Layout') }} {{ t('4K') }} (≥ 2560px)</label>
									
									<select v-model="activeItem.class4K" class="form-select font-size-12px">
										<option value="container">{{ t('Fixed Width') }}</option>
										<option value="container-fluid">{{ t('Full Width') }}</option>
									</select>
								</div>
								
								<div class="mt-3">
									<label class="small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
									<input v-model="activeItem.customClass" class="form-control-modern" placeholder="{{ t('e.g p-4 m-4') }}">
								</div>
							</div>
						</div>

						<div class="prop-group">
							<div class="prop-label border-bottom py-2 mx-2">{{ t('Appearance') }}</div>
							
							<div class="prop-group-body p-2">
								<div class="row g-3">
									<div class="col-12">
										<div class="row g-0">									
											<div class="col-auto d-flex align-items-center">
												<input type="color" v-model="activeItem.styles.bgColor" class="form-control form-control-color p-0 me-2" style="width:36px;height:36px">
											</div>

											<div class="col-auto d-flex align-items-center">
												<span class="small text-muted">{{ t('Background Color') }}</span>
											</div>
										</div>
									</div>
									
									<div class="col-12">
										<div class="input-group">
											<input v-model="activeItem.styles.bgImage" class="form-control font-size-12px" placeholder="Image URL">
											<button class="btn btn-outline-secondary font-size-inherit" @click="openCkFinder(activeItem.styles, 'bgImage')"><i class="far fa-folder"></i></button>
										</div>
									</div>
								
									<div class="col-6">
										<label class="small text-muted mb-2 d-block">{{ t('Background Size') }}</label>
										
										<select v-model="activeItem.styles.bgSize" class="form-select font-size-12px">
											<option value="cover">{{ t('Cover') }}</option>
											<option value="contain">{{ t('Contain') }}</option>
											<option value="auto">{{ t('Auto') }}</option>
										</select>
									</div>

									<div class="col-6">
										<label class="small text-muted mb-2 d-block">{{ t('Background Repeat') }}</label>
										
										<select v-model="activeItem.styles.bgRepeat" class="form-select font-size-12px">
											<option value="no-repeat">{{ t('No Repeat') }}</option>
											<option value="repeat">{{ t('Repeat') }}</option>
											<option value="repeat-x">{{ t('Repeat X') }}</option>
											<option value="repeat-y">{{ t('Repeat Y') }}</option>
										</select>
									</div>

									<div class="col-6">
										<label class="small text-muted mb-2 d-block">{{ t('Background Position') }}</label>
										
										<select v-model="activeItem.styles.bgPos" class="form-select font-size-12px">
											<option value="left top">{{ t('Left Top') }}</option>
											<option value="center top">{{ t('Center Top') }}</option>
											<option value="right top">{{ t('Right Top') }}</option>
											<option value="left center">{{ t('Left Center') }}</option>
											<option value="center center">{{ t('Center Center') }}</option>
											<option value="right center">{{ t('Right Center') }}</option>
											<option value="left bottom">{{ t('Left Bottom') }}</option>
											<option value="center bottom">{{ t('Center Bottom') }}</option>
											<option value="right bottom">{{ t('Right Bottom') }}</option>
										</select>
									</div>

									<div class="col-6">
										<label class="small text-muted mb-2 d-block">{{ t('Background Height') }}</label>
										
										<select v-model="activeItem.styles.minHeight" class="form-select font-size-12px">
											<option value="auto">{{ t('Auto Height') }}</option>
											<option value="25vh">{{ t('25vh') }}</option>
											<option value="50vh">{{ t('50vh') }}</option>
											<option value="75vh">{{ t('75vh') }}</option>
											<option value="100vh">{{ t('100vh') }}</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Row -->
					<div v-if="activeType === 'row'">
						<div class="prop-group">
							<div class="prop-label border-bottom py-2 mx-2">{{ t('Row Width') }}</div>

							<div class="prop-group-body p-2">
								<div class="resp-switcher">
									<button class="resp-btn" :class="{active: activeViewMode === 'mobile'}" @click="activeViewMode = 'mobile'">Mobile</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'tablet'}" @click="activeViewMode = 'tablet'">Tablet</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'desktop'}" @click="activeViewMode = 'desktop'">Desktop</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'fhd'}" @click="activeViewMode = 'fhd'">Full HD</button>
									<button class="resp-btn" :class="{active: activeViewMode === '4k'}" @click="activeViewMode = '4k'">4K</button>
								</div>
								
								<div class="row g-3">
									<div class="col-12">
										<div class="form-label small">{{ t('Width') }}</div>

										<div v-if="activeViewMode === 'mobile'">
											<select v-model="activeItem.widthMobile" class="form-select font-size-12px">
												<option value="w-100">100%</option>
												<option value="w-75">75%</option>
												<option value="w-50">50%</option>
												<option value="w-25">25%</option>
												<option value="w-auto">Auto</option>
											</select>
										</div>
										
										<div v-if="activeViewMode === 'tablet'">
											<select v-model="activeItem.widthTablet" class="form-select font-size-12px">
												<option value="w-md-100">100%</option>
												<option value="w-md-75">75%</option>
												<option value="w-md-50">50%</option>
												<option value="w-md-25">25%</option>
												<option value="w-md-auto">Auto</option>
											</select>
										</div>

										<div v-if="activeViewMode === 'desktop'">
											<select v-model="activeItem.widthDesktop" class="form-select font-size-12px">
												<option value="w-xl-100">100%</option>
												<option value="w-xl-75">75%</option>
												<option value="w-xl-50">50%</option>
												<option value="w-xl-25">25%</option>
												<option value="w-xl-auto">Auto</option>
											</select>
										</div>

										<div v-if="activeViewMode === 'fhd'">
											<select v-model="activeItem.widthFHD" class="form-select font-size-12px">
												<option value="w-xxl-100">100%</option>
												<option value="w-xxl-75">75%</option>
												<option value="w-xxl-50">50%</option>
												<option value="w-xxl-25">25%</option>
												<option value="w-xxl-auto">Auto</option>
											</select>
										</div>

										<div v-if="activeViewMode === '4k'">
											<select v-model="activeItem.width4K" class="form-select font-size-12px">
												<option value="w-3xl-100">100%</option>
												<option value="w-3xl-75">75%</option>
												<option value="w-3xl-50">50%</option>
												<option value="w-3xl-25">25%</option>
												<option value="w-3xl-auto">Auto</option>
											</select>
										</div>
									</div>

									<div class="col-12">
										<div class="form-label small">{{ t('Gutter') }}</div>

										<div v-if="activeViewMode === 'mobile'">
											<select v-model="activeItem.gutter" class="form-select font-size-12px text-center">
												<option value="">{{ t('Default') }}</option>
												<option v-for="n in 5" :value="'g-'+n">g-@{{n}}</option>
												<option value="g-0">g-0</option>
											</select>
										</div>

										<div v-if="activeViewMode === 'tablet'">
											<select v-model="activeItem.gutterTablet" class="form-select font-size-12px text-center">
												<option value="">{{ t('Default') }}</option>
												<option v-for="n in 5" :value="'g-md-'+n">g-@{{n}}</option>
												<option value="g-md-0">g-0</option>
											</select>
										</div>

										<div v-if="activeViewMode === 'desktop'">
											<select v-model="activeItem.gutterDesktop" class="form-select font-size-12px text-center">
												<option value="">{{ t('Default') }}</option>
												<option v-for="n in 5" :value="'g-xl-'+n">g-@{{n}}</option>
												<option value="g-xl-0">g-0</option>
											</select>
										</div>

										<div v-if="activeViewMode === 'fhd'">
											<select v-model="activeItem.gutterFHD" class="form-select font-size-12px text-center">
												<option value="">{{ t('Default') }}</option>
												<option v-for="n in 5" :value="'g-xxl-'+n">g-@{{n}}</option>
												<option value="g-xxl-0">g-0</option>
											</select>
										</div>

										<div v-if="activeViewMode === '4k'">
											<select v-model="activeItem.gutter4K" class="form-select font-size-12px text-center">
												<option value="">{{ t('Default') }}</option>
												<option v-for="n in 5" :value="'g-3xl-'+n">g-@{{n}}</option>
												<option value="g-3xl-0">g-0</option>
											</select>
										</div>
									</div>

									<div class="col-12">
										<div class="form-label small">{{ t('Custom Class') }}</div>
										<input v-model="activeItem.customClass" class="form-control font-size-12px" placeholder="{{ t('e.g p-4 m-4')}}">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Column -->
					<div v-if="activeType === 'column'">
						<div class="prop-group">
							<div class="prop-label border-bottom py-2 mx-2">{{ t('Column Width') }}</div>

							<div class="prop-group-body p-2">
								<div class="resp-switcher">
									<button class="resp-btn" :class="{active: activeViewMode === 'mobile'}" @click="activeViewMode = 'mobile'">{{ t('Mobile') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'tablet'}" @click="activeViewMode = 'tablet'">{{ t('Tablet') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'desktop'}" @click="activeViewMode = 'desktop'">{{ t('Desktop') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === 'fhd'}" @click="activeViewMode = 'fhd'">{{ t('Full HD') }}</button>
									<button class="resp-btn" :class="{active: activeViewMode === '4k'}" @click="activeViewMode = '4k'">{{ t('4K') }}</button>
								</div>

								<div class="row g-3">
									<div class="col-12">
										<div v-if="activeViewMode === 'mobile'">
											<label class="small text-muted mb-2 d-block">Mobile (< 768px)</label>

											<select v-model="activeItem.widthMobile" class="form-control-modern">
												<option value="col-12">12 (Full)</option>
												<option v-for="n in 11" :value="'col-'+n">@{{n}}</option>
												<option value="col">Auto</option>
											</select>
										</div>
										
										<div v-if="activeViewMode === 'tablet'">
											<label class="small text-muted mb-2 d-block">Tablet (≥ 768px)</label>

											<select v-model="activeItem.widthTablet" class="form-control-modern">
												<option value="col-md-12">12 (Full)</option>
												<option v-for="n in 11" :value="'col-md-'+n">@{{n}}</option>
												<option value="col-md">Auto</option>
											</select>
										</div>
										
										<div v-if="activeViewMode === 'desktop'">
											<label class="small text-muted mb-2 d-block">Desktop (≥ 1200px)</label>

											<select v-model="activeItem.widthDesktop" class="form-control-modern">
												<option value="col-xl-12">12 (Full)</option>
												<option v-for="n in 11" :value="'col-xl-'+n">@{{n}}</option>
												<option value="col-xl">Auto</option>
											</select>
										</div>
										
										<div v-if="activeViewMode === 'fhd'">
											<label class="small text-muted mb-2 d-block">FHD (≥ 1400px)</label>

											<select v-model="activeItem.widthFHD" class="form-control-modern">
												<option value="col-xxl-12">12 (Full)</option>
												<option v-for="n in 11" :value="'col-xxl-'+n">@{{n}}</option>
												<option value="col-xxl">Auto</option>
											</select>
										</div>
										
										<div v-if="activeViewMode === '4k'">
											<label class="small text-muted mb-2 d-block">4K (≥ 2560px)</label>

											<select v-model="activeItem.width4K" class="form-control-modern">
												<option value="col-3xl-12">12 (Full)</option>
												<option v-for="n in 11" :value="'col-3xl-'+n">@{{n}}</option>
												<option value="col-3xl">Auto</option>
											</select>
										</div>
									</div>
									
									<div class="col-12">
										<div class="form-label small">{{ t('Custom Class') }}</div>
										<input v-model="activeItem.customClass" class="form-control-modern" placeholder="{{ t('e.g p-4 m-4') }}">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Widget -->
					<div v-if="activeType === 'widget'">
						<div class="prop-group">
							<div class="prop-label border-bottom py-2 mx-2">{{ t('Common Settings') }}</div>
							
							<div class="prop-group">
								<div class="prop-group-body p-2 mb-3">
									<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
									<input v-model="activeItem.customClass" class="form-control font-size-12px" placeholder="{{ t('e.g p-4 m-4') }}">
								</div>
							</div>

							<!-- Widget Text -->
							<div v-if="activeItem.type === 'text'">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Text Settings') }}</div>

								<div class="prop-group-body p-2">
									<div class="alert alert-primary bg-opacity-10 border-0 small m-0">
										<i class="fas fa-info-circle me-1"></i> {{ t('Click text in canvas to edit.') }}
									</div>
								</div>
							</div>
							
							<!-- Widget Image -->
							<div v-if="activeItem.type === 'image'">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Image Settings') }}</div>
								
								<div class="prop-group-body p-2">		
									<div class="row g-3">	
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Source') }}</label>							
											
											<div class="input-group">
												<input v-model="activeItem.src" class="form-control font-size-12px" placeholder="https://...">
												<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'src')"><i class="far fa-folder"></i></button>
											</div>
										</div>
									
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Width') }}</label>

											<div class="row g-3">	
												<div class="col-8">
													<input v-model="activeItem.widthVal" class="form-control font-size-12px" :disabled="activeItem.widthUnit === 'auto'">
												</div>
												
												<div class="col-4">
													<select v-model="activeItem.widthUnit" class="form-select font-size-12px">
														<option value="px">px</option>
														<option value="em">em</option>
														<option value="rem">rem</option>
														<option value="%">%</option>
														<option value="pt">pt</option>
														<option value="auto">auto</option>
													</select>
												</div>
											</div>
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Height') }}</label>

											<div class="row g-3">	
												<div class="col-8">
													<input v-model="activeItem.heightVal" class="form-control font-size-12px" :disabled="activeItem.heightUnit === 'auto'">
												</div>
												
												<div class="col-4">
													<select v-model="activeItem.heightUnit" class="form-select font-size-12px">
														<option value="px">px</option>
														<option value="em">em</option>
														<option value="rem">rem</option>
														<option value="%">%</option>
														<option value="pt">pt</option>
														<option value="auto">auto</option>
													</select>
												</div>
											</div>
										</div>
																		
									</div>
								</div>
							</div>

							<!-- Widget Button -->
							<div v-if="activeItem.type === 'button'">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Button Settings') }}</div>

								<div class="prop-group">
									<div class="prop-group-body p-2">
										<div class="row g-3">	
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Title') }}</label>	
												<input v-model="activeItem.text" class="form-control font-size-12px mb-2" placeholder="Label">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Link') }}</label>
												<input v-model="activeItem.href" class="form-control font-size-12px mb-2" placeholder="Link">
											</div>

											<div class="col-12">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" v-model="activeItem.newTab" id="btnPropNewTab">
													<label class="form-check-label small" for="btnPropNewTab">{{ t('Open New Tab') }}</label>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-label border-bottom py-2 mx-2">{{ t('Button Icon Settings') }}</div>
									
								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Font Type') }}</label>	
											
											<select v-model="activeItem.iconType" class="form-select font-size-12px">
												<option value="none">{{ t('No Icon') }}</option>
												<option value="class">{{ t('Font Icon (Class)') }}</option>
												<option value="image">{{ t('Custom Image') }}</option>
											</select>
										</div>

										<div v-if="activeItem.iconType === 'class'" class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Font Awesome Class') }}</label>	
											<input v-model="activeItem.iconClass" class="form-control font-size-12px" placeholder="{{ t('e.g. fas fa-arrow-right') }}">
										</div>

										<div v-if="activeItem.iconType === 'image'" class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Icon with Image') }}</label>	

											<div class="input-group input-group-sm">
												<input v-model="activeItem.iconSrc" class="form-control font-size-12px" placeholder="{{ t('Icon URL') }}">
												
												<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'iconSrc')">
													<i class="far fa-folder"></i>
												</button>
											</div>
										</div>

										<div v-if="activeItem.iconType && activeItem.iconType !== 'none'" class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Position') }}</label>
											<select v-model="activeItem.iconPos" class="form-select font-size-12px">
												<option value="start">Left (Start)</option>
												<option value="end">Right (End)</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<!-- Widget List -->
							<div v-if="activeItem.type === 'list'">

								<div class="prop-label border-bottom py-2 mx-2">{{ t('List Type') }}</div>

								<div class="prop-group">	
									<div class="prop-group-body p-2">
										<div class="row g-3">	
											<div class="col-12">								
												<select v-model="activeItem.listType" class="form-select font-size-12px">
													<option value="ul">{{ t('Bulleted (UL)') }}</option>
													<option value="ol">{{ t('Numbered (OL)') }}</option>
												</select>
											</div>

											<div class="col-12">
												<div v-if="activeItem.listType === 'ul'">
													<select v-model="activeItem.styleType" class="form-select font-size-12px">
														<option value="standard">{{ t('Standard') }}</option>
														<option value="icon">{{ t('Font Icon (Class)') }}</option>
														<option value="image">{{ t('Custom Image') }}</option>
													</select>
												</div>
											</div>

											<div class="col-12">
												<div v-if="activeItem.listType === 'ul'">
													<div v-if="activeItem.styleType === 'icon'">
														<input v-model="activeItem.commonIcon" class="form-control font-size-12px" placeholder="{{ t('Icon Class') }}"></div>
														
														<div v-if="activeItem.styleType === 'image'" class="input-group">
															<input v-model="activeItem.commonImage" class="form-control font-size-12px" placeholder="{{ t('Image URL') }}">
															<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'commonImage')"><i class="fas fa-folder"></i></button>
														</div>
													</div>
												</div>
											</div>

											<div class="col-12">
												<input class="form-check-input" type="checkbox" v-model="activeItem.flush" id="flushToggle">
												<label class="form-check-label small fw-bold" for="flushToggle">Flush List (Hilangkan Border Luar)</label>
											</div>

											<div class="col-12">
												<input class="form-check-input" type="checkbox" v-model="activeItem.noBorder" id="noBorderToggle">
												<label class="form-check-label small fw-bold" for="noBorderToggle">Full No Border (Tanpa Garis Sama Sekali)</label>
											</div>

											<div class="col-12">
												<label class="form-label small fw-bold">Ukuran List (Size)</label>
												<select v-model="activeItem.listSize" class="form-select form-select-sm mb-2">
													<option value="default">Default</option>
													<option value="h6">H6 (Terkecil)</option>
													<option value="h5">H5</option>
													<option value="h4">H4</option>
													<option value="h3">H3</option>
													<option value="h2">H2</option>
													<option value="h1">H1 (Terbesar)</option>
													<option value="custom">-- Custom Size --</option>
												</select>
												
												<div v-if="activeItem.listSize === 'custom'" class="row g-2">
													<div class="col-7">
														<input type="number" v-model="activeItem.customSizeValue" class="form-control form-control-sm" placeholder="Nilai">
													</div>
													<div class="col-5">
														<select v-model="activeItem.customSizeUnit" class="form-select form-select-sm">
															<option value="px">px</option>
															<option value="rem">rem</option>
															<option value="em">em</option>
															<option value="pt">pt</option>
															<option value="%">%</option>
														</select>
													</div>
												</div>
												<div class="text-muted mt-1" style="font-size: 10px;">Mengubah ukuran akan ikut menyesuaikan ikon, checkbox, dan badge.</div>
											</div>
										</div>
									</div>

								<div class="prop-label border-bottom py-2 mx-2">{{ t('Items') }}</div>
										
								<div class="prop-group">	
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">		
												<div class="row g-3">								
													<div v-for="(sub, idx) in activeItem.items" :key="idx" class="mb-3 border p-3 rounded bg-light">
														<div class="mb-2">
															<label class="form-label small mb-1 fw-bold">Text Item</label>
															<input v-model="sub.text" class="form-control form-control-sm">
														</div>

														<div class="d-flex gap-3 mb-2 mt-2">
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" v-model="sub.active">
																<label class="form-check-label small">Active</label>
															</div>
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" v-model="sub.disabled">
																<label class="form-check-label small">Disabled</label>
															</div>
														</div>

														<div v-if="sub.active" class="mb-2 p-2 border border-primary rounded bg-white">
															<label class="form-label small mb-1 fw-bold text-primary">Custom CSS State Active</label>
															<input v-model="sub.activeCss" class="form-control form-control-sm border-primary" placeholder="Misal: bg-warning text-dark border-0">
														</div>

														<hr class="my-3 text-muted" style="opacity: 0.15;">

														<div class="mb-2">
															<label class="form-label small mb-1 fw-bold">Tipe Input</label>
															<select v-model="sub.inputType" class="form-select form-select-sm">
																<option value="none">Tidak Ada</option>
																<option value="checkbox">Checkbox</option>
																<option value="radio">Radio</option>
															</select>
														</div>
														
														<div v-if="sub.inputType !== 'none'" class="mb-2 ms-2 border-start border-2 border-secondary ps-2">
															<div class="form-check mb-2">
																<input class="form-check-input" type="checkbox" v-model="sub.inputChecked">
																<label class="form-check-label small">Centang Default</label>
															</div>
															<div class="form-check mb-2">
																<input class="form-check-input" type="checkbox" v-model="sub.showMarker">
																<label class="form-check-label small">Tetap Tampilkan Marker</label>
															</div>
															<div v-if="sub.showMarker" class="mb-1 mt-2">
																<label class="form-label small mb-1">Posisi Marker</label>
																<select v-model="sub.markerPosition" class="form-select form-select-sm">
																	<option value="after">Sesudah Input (Default)</option>
																	<option value="before">Sebelum Input</option>
																</select>
															</div>
														</div>

														<hr class="my-3 text-muted" style="opacity: 0.15;">

														<div class="form-check form-switch mb-2">
															<input class="form-check-input" type="checkbox" v-model="sub.badge">
															<label class="form-check-label small fw-bold">Gunakan Badge</label>
														</div>
														<div v-if="sub.badge" class="row g-2 mb-2">
															<div class="col-12 mb-1">
																<input v-model="sub.badgeText" class="form-control form-control-sm" placeholder="Teks Badge">
															</div>
															<div class="col-12 mb-1">
																<select v-model="sub.badgeType" class="form-select form-select-sm">
																	<option value="bg-primary">Primary</option>
																	<option value="bg-secondary">Secondary</option>
																	<option value="bg-success">Success</option>
																	<option value="bg-danger">Danger</option>
																	<option value="bg-warning text-dark">Warning</option>
																	<option value="bg-info text-dark">Info</option>
																	<option value="bg-dark">Dark</option>
																	<option value="custom">-- Custom Class --</option>
																</select>
															</div>
															<div v-if="sub.badgeType === 'custom'" class="col-12 mb-1">
																<input v-model="sub.customBadgeCss" class="form-control form-control-sm border-primary" placeholder="Misal: bg-transparent text-danger border">
															</div>
														</div>

														<hr class="my-3 text-muted" style="opacity: 0.15;">

														<div class="mb-3">
															<label class="form-label small mb-1 fw-bold">Custom CSS Item</label>
															<input v-model="sub.customCss" class="form-control form-control-sm" placeholder="Misal: fw-bold pb-2">
														</div>

														<button class="btn btn-sm btn-outline-danger w-100" @click="activeItem.items.splice(idx, 1)">
															<i class="fas fa-trash me-1"></i> Hapus Item
														</button>
													</div>

												</div>
											</div>

											<div class="col-12">
												<button class="btn btn-sm btn-outline-primary w-100" @click="addListItem(activeItem)">+ Add Item</button>
											</div>
										</div>
									</div>

								</div>
							</div>

							<!-- Widget Card -->
							<div v-if="activeItem.type === 'card'">								
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Card Image Settings') }}</div>

									<div class="prop-group-body p-2">		
										<div class="row g-3">	
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Source') }}</label>

												<div class="input-group input-group-sm mb-2">
													<input v-model="activeItem.src" class="form-control form-control-modern" placeholder="{{ t('Image URL') }}">												
													<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'src')"><i class="far fa-folder"></i></button>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Width') }}</label>

												<div class="row g-3">	
													<div class="col-8">
														<input v-model="activeItem.imgWidthVal" class="form-control font-size-12px" :disabled="activeItem.imgWidthUnit === 'auto'">
													</div>
													
													<div class="col-4">
														<select v-model="activeItem.imgWidthUnit" class="form-select font-size-12px">
															<option value="px">px</option>
															<option value="em">em</option>
															<option value="rem">rem</option>
															<option value="%">%</option>
															<option value="pt">pt</option>
															<option value="auto">auto</option>
														</select>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Height') }}</label>

												<div class="row g-3">	
													<div class="col-8">
														<input v-model="activeItem.imgHeightVal" class="form-control font-size-12px" :disabled="activeItem.imgHeightUnit === 'auto'">
													</div>
													
													<div class="col-4">
														<select v-model="activeItem.imgHeightUnit" class="form-select font-size-12px">
															<option value="px">px</option>
															<option value="em">em</option>
															<option value="rem">rem</option>
															<option value="%">%</option>
															<option value="pt">pt</option>
															<option value="auto">auto</option>
														</select>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.imgClass" class="form-control font-size-12px" placeholder="{{ t('e.g. p-3 rounded-circle') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Card Content') }}</div>

									<div class="prop-group-body p-2">	
										<div class="row g-3">	
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Title') }}</label>
												<input v-model="activeItem.cardTitle" class="form-control font-size-12px" placeholder="{{ t('Card Title') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.titleClass" class="form-control font-size-12px" placeholder="{{ t('Title Class (e.g. h4 text-danger)') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Title Options') }}</label>
											
												<div class="row g-3 align-items-center">
													<div :class="activeItem.truncTitleMode === 'chars' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncTitleMode" class="form-select font-size-12px">
															<option value="off">{{ t('No Truncate') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (1 Line)') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.truncTitleMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncTitleLimit" class="form-control font-size-12px" min="1">
															<span class="input-group-text small text-muted">chars</span>
														</div>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Description') }}</label>
												<textarea v-model="activeItem.cardText" class="form-control font-size-12px" rows="6" placeholder="{{ t('Body Text') }}"></textarea>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.textClass" class="form-control font-size-12px" placeholder="{{ t('Text Class (e.g. small text-justify)') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Description Options') }}</label>
											
												<div class="row g-3 align-items-center">
													<div :class="activeItem.truncTextMode === 'chars' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncTextMode" class="form-select font-size-12px">
															<option value="off">{{ t('No Truncate') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (1 Line)') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.truncTextMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncTitleLimit" class="form-control font-size-12px" min="1">
															<span class="input-group-text small text-muted">{{ t('chars') }}</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<!-- Action Button -->
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Action Button') }}</div>

									<div class="prop-group-body p-2">
										<div class="row g-3">	
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Text') }}</label>
												<input v-model="activeItem.btnText" class="form-control font-size-12px" placeholder="{{ t('Button Text') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Link') }}</label>
												<input v-model="activeItem.btnLink" class="form-control font-size-12px" placeholder="{{ t('Button Link') }}">
											</div>

											<div class="col-12">
												<div class="form-check form-switch">
													<input class="form-check-input" type="checkbox" v-model="activeItem.newTab" id="cardPropNewTab">
													<label class="form-check-label small" for="cardPropNewTab" style="cursor: pointer;">Open New Tab</label>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Custom Class') }}</label>
												<input v-model="activeItem.btnClass" class="form-control font-size-12px" placeholder="{{ t('Button Custom Class (e.g. btn-sm)') }}">
											</div>
										</div>
									</div>
								</div>

								<!-- Button Icon Settings -->
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Button Icon Settings') }}</div>

									<div class="prop-group-body p-2">
										<div class="row g-3">	
											<div class="col-12">												
												<select v-model="activeItem.btnIconType" class="form-control-modern mb-2">
													<option value="none">{{ t('No Icon') }}</option>
													<option value="class">{{ t('Font Icon (Class)') }}</option>
													<option value="image">{{ t('Custom Image') }}</option>
												</select>
											</div>

											<div class="col-12" v-if="activeItem.btnIconType === 'class'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Font Awesome Class') }}</label>	
												<input v-model="activeItem.btnIconClass" class="form-control font-size-12px" placeholder="{{ t('e.g. fas fa-chevron-right') }}">
											</div>

											<div class="col-12" v-if="activeItem.btnIconType === 'image'">
												<div class="input-group input-group-sm">
													<input v-model="activeItem.btnIconSrc" class="form-control font-size-12px" placeholder="{{ t('Icon URL') }}">
													<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'btnIconSrc')">
														<i class="far fa-folder"></i>
													</button>
												</div>
											</div>

											<div class="col-12" v-if="activeItem.btnIconType && activeItem.btnIconType !== 'none'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Position') }}</label>
												
												<select v-model="activeItem.btnIconPos" class="form-select font-size-12px">
													<option value="start">{{ t('Left (Start)') }}</option>
													<option value="end">{{ t('Right (End)') }}</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Widget Media -->
							<div v-if="activeItem.type === 'media'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Image Settings') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">	
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Source') }}</label>

												<div class="input-group input-group-sm mb-2">
													<input v-model="activeItem.src" class="form-control font-size-12px" placeholder="{{ t('Image URL') }}">
													
													<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'src')">
														<i class="far fa-folder"></i>
													</button>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Width') }}</label>

												<div class="row g-2 mb-2">													
													<div class="col-8">
														<input v-model="activeItem.imgWidthVal" class="form-control font-size-12px" :disabled="activeItem.imgWidthUnit === 'auto'">
													</div>

													<div class="col-4">
														<select v-model="activeItem.imgWidthUnit" class="form-select font-size-12px">
															<option value="px">px</option>
															<option value="em">em</option>
															<option value="rem">rem</option>
															<option value="%">%</option>
															<option value="pt">pt</option>
															<option value="auto">auto</option>
														</select>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Height') }}</label>

												<div class="row g-2 mb-2">													
													<div class="col-8">
														<input v-model="activeItem.imgHeightVal" class="form-control font-size-12px" :disabled="activeItem.imgHeightUnit === 'auto'">
													</div>

													<div class="col-4">
														<select v-model="activeItem.imgHeightUnit" class="form-select font-size-12px">
															<option value="px">px</option>
															<option value="em">em</option>
															<option value="rem">rem</option>
															<option value="%">%</option>
															<option value="pt">pt</option>
															<option value="auto">auto</option>
														</select>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.imgClass" class="form-control font-size-12px" placeholder="{{ t('e.g. rounded-circle shadow-sm border') }}">
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Alignment') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-6">
												<select v-model="activeItem.imagePos" class="form-select font-size-12px">
													<option value="start">{{ t('Image Left') }}</option>
													<option value="end">{{ t('Image Right') }}</option>
												</select>
											</div>

											<div class="col-6">
												<select v-model="activeItem.align" class="form-select font-size-12px">
													<option value="start">{{ t('Top') }}</option>
													<option value="center">{{ t('Middle') }}</option>
													<option value="end">{{ t('Bottom') }}</option>
												</select>
											</div>
										</div>
									</div>
								</div>
								
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Media Content') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Heading') }}</label>
												<input v-model="activeItem.heading" class="form-control font-size-12px" placeholder="{{ t('Heading Text') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.headingClass" class="form-control font-size-12px" placeholder="{{ t('Heading Class (e.g. h6 text-primary)') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Heading Options') }}</label>

												<div class="row g-3 align-items-center">
													<div :class="activeItem.truncHeadingMode === 'chars' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncHeadingMode" class="form-select font-size-12px">
															<option value="off">{{ t('No Truncate') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (1 Line)') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.truncHeadingMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncHeadingLimit" class="form-control font-size-12px" min="1">
															<span class="input-group-text small text-muted">{{ t('chars') }}</span>
														</div>
													</div>
												</div>
											</div>
										
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Content') }}</label>
												<textarea v-model="activeItem.content" class="form-control font-size-12px" rows="8" placeholder="Content Text"></textarea>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Custom Class') }}</label>
												<input v-model="activeItem.contentClass" class="form-control font-size-12px" placeholder="{{ t('Content Class (e.g. small text-muted)') }}">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Content Options') }}</label>

												<div class="row g-3">
													<div :class="activeItem.truncContentMode === 'chars' || activeItem.truncContentMode === 'auto' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncContentMode" class="form-select font-size-12px">
															<option value="off">{{ t('No Truncate') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (Lines)') }}</option>
														</select>
													</div>
													
													<div class="col-6" v-if="activeItem.truncContentMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncContentLimit" class="form-control font-size-12px" min="1">
															<span class="input-group-text small text-muted">{{ t('chars') }}</span>
														</div>
													</div>

													<div class="col-6" v-if="activeItem.truncContentMode === 'auto'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncContentLines" class="form-control font-size-12px" min="1" max="10">
															<span class="input-group-text small text-muted">{{ t('lines') }}</span>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Action Button') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Text') }}</label>
												<input v-model="activeItem.mediaBtnText" class="form-control font-size-12px" placeholder="Button Label (Leave empty to hide)">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Link') }}</label>
												<input v-model="activeItem.mediaBtnLink" class="form-control font-size-12px" placeholder="https://...">
											</div>

											<div class="col-6">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Position') }}</label>
												<select v-model="activeItem.mediaBtnPos" class="form-select font-size-12px">
													<option value="below">{{ t('Below Text') }}</option>
													<option value="side">{{ t('Side (Opposite Image)') }}</option>
												</select>
											</div>

											<div class="col-6">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Target') }}</label>
												<div class="form-check form-switch pt-1">
													<input class="form-check-input" type="checkbox" v-model="activeItem.mediaBtnNewTab" id="mediaBtnTab">
													<label class="form-check-label small" for="mediaBtnTab">{{ t('New Tab') }}</label>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Class') }}</label>
												<input v-model="activeItem.mediaBtnClass" class="form-control font-size-12px" placeholder="e.g. btn btn-sm btn-outline-primary">
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Widget Accordion -->
							<div v-if="activeItem.type === 'accordion'">
								<div class="prop-group">
									<div class="d-flex justify-content-between align-items-center border-bottom py-2 mx-2">
										<label class="form-label small text-muted mb-2 d-block">{{ t('Items') }}</label>
										<button class="btn btn-sm btn-outline-primary py-0" @click="addAccordionItem(activeItem)">+ Add</button>
									</div>
								
									<div class="px-2 pb-2">
										<div class="row g-3">
											<div v-for="(item, idx) in activeItem.items" :key="idx" class="col-12 border rounded p-3 bg-light">
												<div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
													<span class="small fw-bold text-secondary">{{ t('Item') }} #@{{idx+1}}</span>
													<button class="btn btn-sm btn-outline-danger py-0 px-2" style="height: 24px;" @click="removeAccordionItem(activeItem, idx)"><i class="fas fa-trash-alt" style="font-size: 12px;"></i></button>
												</div>
											
												<div class="row g-3">
													<div class="col-12">	
														<input v-model="item.header" class="form-control font-size-12px" placeholder="{{ t('Header') }}">
													</div>

													<div class="col-12">
														<textarea v-model="item.content" class="form-control font-size-12px" rows="6" placeholder="{{ t('content') }}"></textarea>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Widget Table -->
							<div v-if="activeItem.type === 'table'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Structure') }}</div>

									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-6">
												<button class="btn btn-sm btn-outline-secondary w-100" @click="addTableRow(activeItem)">+ {{ t('Row') }}</button>
											</div>

											<div class="col-6">
												<button class="btn btn-sm btn-outline-secondary w-100" @click="addTableCol(activeItem)">+ {{ t('Column') }}</button>
											</div>
										</div>
									</div>
								</div>
								
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Edit Data') }}</div>

									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Headers') }}</label>
											
												<div v-for="(h, hi) in activeItem.tableData.headers" class="input-group input-group-sm mb-1">
													<input v-model="activeItem.tableData.headers[hi]" class="form-control">
													<button class="btn btn-outline-danger" @click="removeTableCol(activeItem, hi)"><i class="fas fa-times"></i></button>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Rows Data</label>
											
												<div v-for="(r, ri) in activeItem.tableData.rows" class="border p-2 mb-2 rounded">
													<div class="d-flex justify-content-between align-items-center mb-2">
														<span class="small fw-bold text-secondary">Row @{{ri+1}}</span>
														<button class="btn btn-sm btn-outline-danger py-0 px-2" style="height: 24px;" @click="removeTableRow(activeItem, ri)"><i class="fas fa-trash-alt" style="font-size: 12px;"></i></button>
													</div>

													<input v-for="(c, ci) in r" v-model="activeItem.tableData.rows[ri][ci]" class="form-control-modern mb-1" :placeholder="'Col '+ (ci+1)">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Widget Video -->
							<div v-if="activeItem.type === 'video'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Video Source') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Source') }}</label>
												
												<select v-model="activeItem.videoType" class="form-select font-size-12px">
													<option value="youtube">{{ t('YouTube URL') }}</option>
													<option value="file">{{ t('File Upload (CKFinder)') }}</option>
												</select>
											</div>

											<div class="col-12" v-if="activeItem.videoType === 'youtube'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('YouTube Link') }}</label>
												
												<input v-model="activeItem.youtubeUrl" class="form-control font-size-12px mb-2" placeholder="https://youtube.com/watch?v=...">
												<small class="text-muted">{{ t('Paste full YouTube URL here') }}</small>
											</div>

											<div class="col-12" v-if="activeItem.videoType === 'file'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Video File') }}</label>
												
												<div class="input-group input-group-sm">
													<input v-model="activeItem.videoSrc" class="form-control font-size-12px" placeholder="Select Video...">
													
													<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'videoSrc')">
														<i class="far fa-folder"></i>
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-label border-bottom py-2 mx-2">{{ t('Player Settings') }}</div>

								<div class="prop-group-body p-2">									
									<div class="row g-3">
										<div class="col-6">
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" v-model="activeItem.controls" id="vidControls">
												<label class="form-check-label small" for="vidControls">{{ t('Controls') }}</label>
											</div>
										</div>

										<div class="col-6">
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" v-model="activeItem.autoplay" id="vidAuto">
												<label class="form-check-label small" for="vidAuto">{{ t('Autoplay') }}</label>
											</div>
										</div>

										<div class="col-6">
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" v-model="activeItem.loop" id="vidLoop">
												<label class="form-check-label small" for="vidLoop">{{ t('Loop') }}</label>
											</div>
										</div>

										<div class="col-6">
											<div class="form-check form-switch">
												<input class="form-check-input" type="checkbox" v-model="activeItem.muted" id="vidMute">
												<label class="form-check-label small" for="vidMute">{{ t('Muted') }}</label>
											</div>
										</div>
										
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Aspect Ratio') }}</label>

											<select v-model="activeItem.aspectRatio" class="form-select font-size-12px">
												<option value="16/9">16:9 (Widescreen)</option>
												<option value="4/3">4:3 (Standard)</option>
												<option value="1/1">1:1 (Square)</option>
												<option value="21/9">21:9 (Ultrawide)</option>
											</select>
										</div>
									
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Width') }} @{{ activeItem.width }}%</label>
											<input type="range" v-model="activeItem.width" class="form-range" min="25" max="100">
										</div>
									</div>
								</div>
							</div>

							<!-- Spacer -->
							<div v-if="activeItem.type === 'spacer'">
								<div class="prop-label border-bottom py-2 mx-2">Spacer Settings</div>
								<div class="prop-group-body p-2">
									<label class="form-label small text-muted mb-2 d-block">Height</label>
									<div class="row g-2">
										<div class="col-8">
											<input type="number" v-model="activeItem.height" class="form-control font-size-12px">
										</div>
										
										<div class="col-4">
											<select v-model="activeItem.unit" class="form-select font-size-12px">
												<option value="px">px</option>
												<option value="em">em</option>
												<option value="rem">rem</option>
												<option value="%">%</option>
												<option value="pt">pt</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<!-- Divider -->
							<div v-if="activeItem.type === 'divider'">
								<div class="prop-label border-bottom py-2 mx-2">Divider Settings</div>
								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div class="col-6"><label class="form-label small text-muted mb-2 d-block">Style</label><select v-model="activeItem.style" class="form-select font-size-12px"><option value="solid">Solid</option><option value="dashed">Dashed</option><option value="dotted">Dotted</option><option value="double">Double</option></select></div>
										<div class="col-6"><label class="form-label small text-muted mb-2 d-block">Weight</label><input type="number" v-model="activeItem.thickness" class="form-control font-size-12px"></div>
										<div class="col-12"><label class="form-label small text-muted mb-2 d-block">Color</label><div class="d-flex align-items-center"><input type="color" v-model="activeItem.color" class="form-control form-control-color p-0 me-2" style="width:30px;height:30px"></div></div>
										<div class="col-12"><label class="form-label small text-muted mb-2 d-block">Width</label><div class="input-group input-group-sm"><input type="number" v-model="activeItem.width" class="form-control font-size-12px"><select v-model="activeItem.widthUnit" class="form-select font-size-12px" style="max-width: 70px;"><option value="px">px</option><option value="em">em</option><option value="rem">rem</option><option value="%">%</option><option value="pt">pt</option></select></div></div>
										<div class="col-12"><label class="form-label small text-muted mb-2 d-block">Alignment</label><div class="btn-group w-100"><button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.align === 'start'}" @click="activeItem.align = 'start'"><i class="fas fa-align-left"></i></button><button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.align === 'center'}" @click="activeItem.align = 'center'"><i class="fas fa-align-center"></i></button><button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.align === 'end'}" @click="activeItem.align = 'end'"><i class="fas fa-align-right"></i></button></div></div>
										<div class="col-12"><label class="form-label small text-muted mb-2 d-block">Gap (Top/Bottom)</label><input type="range" v-model="activeItem.gap" class="form-range" min="0" max="100"></div>
									</div>
								</div>
							</div>

							<!-- Heading -->
							<div v-if="activeItem.type === 'heading'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Heading Content') }}</div>
									<div class="prop-group-body p-2">
										<div class="mb-3">
											<label class="form-label form-label small text-muted mb-2 d-block">{{ t('Title') }}</label>
											<textarea v-model="activeItem.text" class="form-control font-size-12px" rows="2"></textarea>
										</div>
										<div class="mb-3">
											<label class="form-label form-label small text-muted mb-2 d-block">{{ t('Link') }}</label>
											<input v-model="activeItem.link" class="form-control font-size-12px" placeholder="https://...">
										</div>
										<div class="row g-2">
											<div class="col-6">
												<label class="form-label form-label small text-muted mb-2 d-block">{{ t('HTML Tag') }}</label>
												<select v-model="activeItem.htmlTag" class="form-select font-size-12px">
													<option value="h1">H1</option><option value="h2">H2</option><option value="h3">H3</option>
													<option value="h4">H4</option><option value="h5">H5</option><option value="h6">H6</option>
													<option value="div">div</option><option value="span">span</option><option value="p">p</option>
												</select>
											</div>
											<div class="col-6">
												<label class="form-label form-label small text-muted mb-2 d-block">{{ t('Alignment') }}</label>
												<div class="btn-group w-100">
													<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.alignment === 'left'}" @click="activeItem.alignment = 'left'"><i class="fas fa-align-left"></i></button>
													<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.alignment === 'center'}" @click="activeItem.alignment = 'center'"><i class="fas fa-align-center"></i></button>
													<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.alignment === 'right'}" @click="activeItem.alignment = 'right'"><i class="fas fa-align-right"></i></button>
													<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.alignment === 'justify'}" @click="activeItem.alignment = 'justify'"><i class="fas fa-align-justify"></i></button>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Style') }}</div>
									<div class="prop-group-body p-2">
										<div class="mb-3">
											<label class="form-label form-label small text-muted mb-2 d-block">{{ t('Color') }}</label>
											<div class="d-flex align-items-center">
												<input type="color" v-model="activeItem.color" class="form-control form-control-color p-0 me-2" style="width:30px;height:30px">
												<input type="text" v-model="activeItem.color" class="form-control font-size-12px">
											</div>
										</div>
										<div class="row g-2 mb-3">
											<label class="form-label form-label small text-muted mb-2 d-block d-block">{{ t('Size') }}</label>
											<div class="col-8"><input type="number" v-model="activeItem.fontSize" class="form-control font-size-12px"></div>
											<div class="col-4">
												<select v-model="activeItem.fontSizeUnit" class="form-select font-size-12px">
													<option value="px">px</option><option value="em">em</option><option value="rem">rem</option><option value="vw">vw</option>
												</select>
											</div>
										</div>
										<div class="mb-3">
											<label class="form-label form-label small text-muted mb-2 d-block">{{ t('Weight') }}</label>
											<select v-model="activeItem.fontWeight" class="form-select font-size-12px">
												<option value="">Default</option><option value="300">Light</option><option value="400">Normal</option>
												<option value="600">Semi Bold</option><option value="700">Bold</option><option value="900">Black</option>
											</select>
										</div>
									</div>
								</div>
							</div>

							<!-- Media List -->
							<div v-if="activeItem.type === 'media_list'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('List Settings') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('View Mode') }}</label>
												
												<select v-model="activeItem.viewMode" class="form-select font-size-12px">
													<option value="grid">{{ t('Grid') }}</option>
													<option value="list">{{ t('List') }}</option>
												</select>
											</div>

											<div class="col-12" v-if="activeItem.viewMode === 'grid'">
												<label class="small text-muted mb-2 fw-bold" style="font-size:11px; letter-spacing:0.5px;">{{ t('GRID COLUMNS (RESPONSIVE)') }}</label>

												<div class="resp-switcher">
													<button class="resp-btn" :class="{active: activeViewMode === 'mobile'}" @click="activeViewMode = 'mobile'">{{ t('Mobile') }}</button>
													<button class="resp-btn" :class="{active: activeViewMode === 'tablet'}" @click="activeViewMode = 'tablet'">{{ t('Tablet') }}</button>
													<button class="resp-btn" :class="{active: activeViewMode === 'desktop'}" @click="activeViewMode = 'desktop'">{{ t('Desktop') }}</button>
													<button class="resp-btn" :class="{active: activeViewMode === 'fhd'}" @click="activeViewMode = 'fhd'">{{ t('FHD') }}</button>
													<button class="resp-btn" :class="{active: activeViewMode === '4k'}" @click="activeViewMode = '4k'">{{ t('4K') }}</button>
												</div>

												<div v-if="activeViewMode === 'mobile'">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Mobile') }} (< 768px)</label>
													
													<select v-model="activeItem.colsMobile" class="form-select font-size-12px">
														<option :value="1">{{ t('1 Column') }}</option>
														<option :value="2">{{ t('2 Columns') }}</option>
														<option :value="3">{{ t('3 Columns') }}</option>
														<option :value="4">{{ t('4 Columns') }}</option>
														<option :value="5">{{ t('5 Columns') }}</option>
														<option :value="6">{{ t('6 Columns') }}</option>
													</select>
												</div>

												<div v-if="activeViewMode === 'tablet'">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Tablet') }} (≥ 768px)</label>
													
													<select v-model="activeItem.colsTablet" class="form-select font-size-12px">
														<option :value="1">{{ t('1 Column') }}</option>
														<option :value="2">{{ t('2 Columns') }}</option>
														<option :value="3">{{ t('3 Columns') }}</option>
														<option :value="4">{{ t('4 Columns') }}</option>
														<option :value="5">{{ t('5 Columns') }}</option>
														<option :value="6">{{ t('6 Columns') }}</option>
													</select>
												</div>

												<div v-if="activeViewMode === 'desktop'">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Desktop') }} (≥ 1200px)</label>
													
													<select v-model="activeItem.colsDesktop" class="form-select font-size-12px">
														<option :value="1">{{ t('1 Column') }}</option>
														<option :value="2">{{ t('2 Columns') }}</option>
														<option :value="3">{{ t('3 Columns') }}</option>
														<option :value="4">{{ t('4 Columns') }}</option>
														<option :value="5">{{ t('5 Columns') }}</option>
														<option :value="6">{{ t('6 Columns') }}</option>
													</select>
												</div>

												<div v-if="activeViewMode === 'fhd'">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Full HD') }} (≥ 1400px)</label>
													
													<select v-model="activeItem.colsFHD" class="form-select font-size-12px">
														<option :value="1">{{ t('1 Column') }}</option>
														<option :value="2">{{ t('2 Columns') }}</option>
														<option :value="3">{{ t('3 Columns') }}</option>
														<option :value="4">{{ t('4 Columns') }}</option>
														<option :value="5">{{ t('5 Columns') }}</option>
														<option :value="6">{{ t('6 Columns') }}</option>
													</select>
												</div>

												<div v-if="activeViewMode === '4k'">
													<label class="form-label small text-muted mb-2 d-block">{{ t('4K') }} (≥ 2560px)</label>
													<select v-model="activeItem.cols4K" class="form-select font-size-12px">
														<option :value="1">{{ t('1 Column') }}</option>
														<option :value="2">{{ t('2 Columns') }}</option>
														<option :value="3">{{ t('3 Columns') }}</option>
														<option :value="4">{{ t('4 Columns') }}</option>
														<option :value="5">{{ t('5 Columns') }}</option>
														<option :value="6">{{ t('6 Columns') }}</option>
													</select>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Border Style') }}</label>
												<select v-model="activeItem.borderStyle" class="form-select font-size-12px mb-2">
													<option value="card">{{ t('Full Card (Default)') }}</option>
													<option value="text">{{ t('Text Area Only') }}</option>
													<option value="none">{{ t('No Border') }}</option>
												</select>
											</div>											

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Text Position') }}</label>
												
												<select v-model="activeItem.textPos" class="form-select font-size-12px">
													<option value="below">{{ t('Below Media') }}</option>
													<option value="overlay">{{ t('Inside Media (Overlay)') }}</option>
												</select>
											</div>

											<div class="col-12" v-if="activeItem.textPos === 'overlay'">
												<div class="row g-3">
													<div class="col-12">
														<div class="form-check form-switch bg-light p-3 rounded border">
															<input class="form-check-input ms-0 me-2" type="checkbox" v-model="activeItem.hoverAnim" id="hoverAnimCheck">
															<label class="form-check-label small fw-medium" for="hoverAnimCheck">{{ t('Fade Text on Hover') }}</label>
														</div>
													</div>

													<div class="col-6">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Overlay Background') }}</label>
														
														<div class="input-group input-group-sm">
															<input type="text" v-model="activeItem.overlayColor" class="form-control font-size-12px" placeholder="{{ t('rgba(0,0,0,0.3)') }}">
														</div>
													</div>

													<div class="col-6">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Text Color') }}</label>
														<input type="color" v-model="activeItem.overlayTextColor" class="form-control p-0">
													</div>
												</div>
											</div>

											<div class="col-12" v-if="activeItem.textPos === 'below'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Text Color') }}</label>
												<input type="color" v-model="activeItem.textColor" class="form-control form-control-color w-100">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Title') }}</label>
												<div class="row g-2">
													<div :class="activeItem.truncTitleMode === 'chars' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncTitleMode" class="form-select font-size-12px">
															<option value="off">{{ t('Off (Full)') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (1 Line)') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.truncTitleMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncTitleLimit" class="form-control font-size-12px">
															<span class="input-group-text small text-muted">{{ t('ch') }}</span>
														</div>
													</div>
												</div>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Desc') }}</label>
												
												<div class="row g-3">
													<div :class="activeItem.truncDescMode === 'chars' || activeItem.truncDescMode === 'auto' ? 'col-6' : 'col-12'">
														<select v-model="activeItem.truncDescMode" class="form-select font-size-12px">
															<option value="off">{{ t('Off (Full)') }}</option>
															<option value="chars">{{ t('Limit Chars') }}</option>
															<option value="auto">{{ t('Auto (Lines)') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.truncDescMode === 'chars'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncDescLimit" class="form-control font-size-12px">
															<span class="input-group-text small text-muted">{{ t('ch') }}</span>
														</div>
													</div>

													<div class="col-6" v-if="activeItem.truncDescMode === 'auto'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.truncDescLines" class="form-control font-size-12px" min="1" max="10">
															<span class="input-group-text small text-muted">{{ t('lines') }}</span>
														</div>
													</div>
												</div>
											</div>											

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Gap (Spacing)') }}</label>
												
												<select v-model="activeItem.gap" class="form-select font-size-12px">
													<option :value="0">{{ t('0 (No Gap)') }}</option>
													<option :value="1">{{ t('1 (Small)') }}</option>
													<option :value="2">{{ t('2 (Medium)') }}</option>
													<option :value="3">{{ t('3 (Normal)') }}</option>
													<option :value="4">{{ t('4 (Large)') }}</option>
													<option :value="5">{{ t('5 (Extra Large)') }}</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Min Height (Item)') }}</label>
												
												<div class="input-group input-group-sm">
													<input type="number" v-model="activeItem.minHeight" class="form-control font-size-12px" min="0" placeholder="0">
													<span class="input-group-text small text-muted">px</span>
												</div>

												<small class="text-muted" style="font-size: 10px;">{{ t('Set 0 for auto height.') }}</small>
											</div>

											<div class="col-12">
												<div class="form-check form-switch bg-light p-3 rounded border">
													<input class="form-check-input ms-0 me-2" type="checkbox" v-model="activeItem.roundedCorners" id="roundedCheck">
													<label class="form-check-label small fw-medium" for="roundedCheck">{{ t('Rounded Corners') }}</label>
												</div>
											</div>

										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Custom Classes') }}</div>
									
									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Body Class') }}</label>
												<input v-model="activeItem.textWrapperClass" class="form-control font-size-12px" placeholder="{{ t('e.g. p-4 text-center') }}">
											</div>

											<div class="col-6">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Title Class') }}</label>
												<input v-model="activeItem.titleClass" class="form-control font-size-12px" placeholder="{{ t('e.g. h4 text-primary') }}">
											</div>

											<div class="col-6">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Description Class') }}</label>
												<input v-model="activeItem.descClass" class="form-control font-size-12px" placeholder="{{ t('e.g. small fst-italic') }}">
											</div>											
										</div>
									</div>
								</div>

								<div class="prop-group">																													
									<div class="d-flex justify-content-between align-items-center px-3 py-2 mx-2 bg-secondary-subtle cursor-pointer rounded" @click="activeItem.enableSplide = !activeItem.enableSplide">
										<div class="d-flex align-items-center gap-2">
											<i class="fas fa-play-circle text-primary"></i>
											<span class="small fw-bold text-dark">{{ t('Slider / Carousel') }}</span>
										</div>

										<div class="form-check form-switch m-0">
											<input class="form-check-input" type="checkbox" v-model="activeItem.enableSplide" @click.stop>
										</div>
									</div>

									<transition name="slider-settings">
										<div v-if="activeItem.enableSplide" class="p-2 mt-2 bg-white">

											<div class="alert alert-info py-1 px-2 mb-3 rounded-1 small">
												<i class="fas fa-info-circle me-1"></i> {{ t('Per Page follows Grid Columns.') }}
											</div>

											<div class="row g-3">
												<div class="col-12">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Type') }}</label>
												
													<select v-model="activeItem.splideType" class="form-select font-size-12px">
														<option value="slide">{{ t('Slide') }}</option>
														<option value="loop">{{ t('Loop') }}</option>
														<option value="fade">{{ t('Fade') }}</option>
													</select>
												</div>

												<div class="col-12">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Direction') }}</label>
												
													<select v-model="activeItem.splideDirection" class="form-select font-size-12px" :disabled="activeItem.splideType === 'fade'">
														<option value="ltr">{{ t('Horizontal') }}</option>
														<option value="ttb">{{ t('Vertical') }}</option>
													</select>
												</div>

												<div class="col-12">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Slider per Move') }}</label>
												
													<input type="number" v-model="activeItem.splidePerMove" class="form-control font-size-12px" min="1">
												</div>
											
												<div class="col-12">
													<div class="bg-secondary-subtle p-3 rounded">
														<div class="row g-3">
															<div class="col-9">
																<label class="form-check-label small" for="switchAutoPlay">{{ t('Auto Play') }}</label>
															</div>

															<div class="col-3 d-flex justify-content-end d-flex align-items-center">
																<div class="form-check form-switch">
																	<input class="form-check-input" type="checkbox" role="switch" id="switchAutoPlay" v-model="activeItem.splideAutoplay">								
																</div>
															</div>

															<transition name="slider-settings">
																<div class="col-12" v-if="activeItem.splideAutoplay">
																	<div class="card-body">	
																		<input type="number" v-model="activeItem.splideInterval" class="form-control font-size-12px" step="500">
																	</div>
																</div>
															</transition>

															<div class="col-9">
																<label class="form-check-label small" for="switchAutoHeight">{{ t('Auto Height') }}</label>
															</div>

															<div class="col-3 d-flex justify-content-end d-flex align-items-center">
																<div class="form-check form-switch">
																	<input class="form-check-input" type="checkbox" role="switch" id="switchAutoHeight" v-model="activeItem.splideAutoHeight">								
																</div>
															</div>

															<div class="col-9">
																<label class="form-check-label small" for="switchShowPagination">{{ t('Show Pagination') }}</label>
															</div>

															<div class="col-3 d-flex justify-content-end d-flex align-items-center">
																<div class="form-check form-switch">
																	<input class="form-check-input" type="checkbox" role="switch" id="switchShowPagination" v-model="activeItem.splidePagination">								
																</div>
															</div>

															<div class="col-9">
																<label class="form-check-label small" for="switchShowArrow">{{ t('Show Arrow') }}</label>
															</div>

															<div class="col-3 d-flex justify-content-end d-flex align-items-center">
																<div class="form-check form-switch">
																	<input class="form-check-input" type="checkbox" role="switch" id="switchShowArrow" v-model="activeItem.splideArrows">								
																</div>
															</div>

															<transition name="slider-settings">
																<div class="col-12" v-if="activeItem.splideArrows">
																	<div class="card-body">	
																		<div class="row g-3">
																			<div class="col-12">
																				<label class="form-label small text-muted mb-2 d-block">{{ t('Arrow Icon') }}</label>
																			
																				<select v-model="activeItem.splideArrowType" class="form-select font-size-12px">
																					<option value="default">{{ t('Default') }}</option>
																					<option value="custom">{{ t('Custom Icon (using Fontawesome)') }}</option>
																				</select>
																			</div>

																			<div class="col-12" v-if="activeItem.splideArrowType === 'custom'">
																				<div class="row g-3">
																					<div class="col-6">
																						<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Left') }}</label>
																						<input v-model="activeItem.splideArrowLeftIcon" class="form-control font-size-12px" placeholder="{{ t('Left Position') }}">
																					</div>

																					<div class="col-6">
																						<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Right') }}</label>
																						<input v-model="activeItem.splideArrowRightIcon" class="form-control font-size-12px" placeholder="{{ t('Right Position') }}">
																					</div>
																				</div>
																			</div>

																			<div class="col-6">
																				<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Position Y {1}', '(Vertical)') }}</label>
																				
																				<div class="input-group input-group-sm">
																					<input type="number" v-model="activeItem.splideArrowPosY" class="form-control font-size-12px">
																					<span class="input-group-text small text-muted">%</span>
																				</div>
																			</div>

																			<div class="col-6">
																				<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Position X {1}', '(Horizontal)') }}</label>
																					
																				<div class="input-group input-group-sm">
																					<input type="number" v-model="activeItem.splideArrowPosX" class="form-control font-size-12px">
																					<span class="input-group-text small text-muted">%</span>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</transition>
														</div>
													</div>
												</div>
											</div>
											
										</div>
									</transition>									
								</div>

								<div class="prop-group">
									<div class="d-flex justify-content-between align-items-center border-bottom py-2 mx-2">
										<label class="prop-label mb-0">{{ t('Items (Max 12)') }}</label>
										<button class="btn btn-sm btn-primary py-0" @click="activeItem.items.length < 12 ? activeItem.items.push({ type: 'image', aspectRatio: '16/9', src: '', title: 'New Item', desc: '', videoType: 'youtube' }) : alert('Max 12 items')" :disabled="activeItem.items.length >= 12"><i class="fas fa-plus"></i></button>
									</div>
									
									<div class="prop-group-body p-2">
										<div v-for="(item, idx) in activeItem.items" :key="idx" class="border rounded p-2 mb-3 bg-light position-relative">
											<div class="d-flex justify-content-between mb-2">
												<span class="badge bg-secondary">#@{{ idx+1 }}</span>
												<button class="btn btn-sm btn-outline-danger py-0 px-2" @click="activeItem.items.splice(idx, 1)"><i class="fas fa-times"></i></button>
											</div>

											<div class="mb-2">
												<select v-model="item.type" class="form-select font-size-12px mb-1">
													<option value="image">{{ t('Image') }}</option>
													<option value="video">{{ t('Video') }}</option>
												</select>
											</div>

											<div v-if="item.type === 'video'" class="mb-2">
												<label class="form-label small text-muted mb-2 d-block d-block" style="font-size: 10px;">{{ t('ASPECT RATIO') }}</label>
												<select v-model="item.aspectRatio" class="form-select font-size-12px mb-2">
													<option value="16/9">{{ t('16:9 (Landscape)') }}</option>
													<option value="4/3">{{ t('4:3 (Standard)') }}</option>
													<option value="1/1">{{ t('1:1 (Square)') }}</option>
													<option value="9/16">{{ t('9/16 (Vertical)') }}</option>
													<option value="21/9">{{ t('21:9 (Ultrawide)') }}</option>
												</select>

												<select v-model="item.videoType" class="form-select font-size-12px mb-1">
													<option value="youtube">{{ t('YouTube') }}</option>
													<option value="file">{{ t('File Upload') }}</option>
												</select>
												
												<input v-if="item.videoType === 'youtube'" v-model="item.youtubeUrl" class="form-control font-size-12px" placeholder="{{ t('YouTube URL') }}">
												
												<div v-if="item.videoType === 'file'" class="input-group input-group-sm mb-2">
													<input v-model="item.videoSrc" class="form-control font-size-12px" placeholder="{{ t('Video URL') }}">
													<button class="btn btn-outline-secondary" @click="openCkFinder(item, 'videoSrc')"><i class="far fa-folder"></i></button>
												</div>

												<div class="mb-2">
													<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('CUSTOM CLASS') }}</label>
													<input v-model="item.videoClass" class="form-control font-size-12px" placeholder="{{ t('e.g. img-fluid rounded-circle') }}">
												</div>
											</div>

											<div v-if="item.type === 'image'" class="mb-2">
												<div class="row g-2 mb-2">
													<div class="col-12">
														<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('IMAGE MODE') }}</label>
														<select v-model="item.imgMode" class="form-select font-size-12px">
															<option value="default">{{ t('Standard Image') }}</option>
															<option value="bg">{{ t('Background Image') }}</option>
														</select>
													</div>
												</div>

												<div class="input-group input-group-sm mb-2">
													<input v-model="item.src" class="form-control font-size-12px" placeholder="{{ t('Image URL') }}">
													<button class="btn btn-outline-secondary" @click="openCkFinder(item, 'src')"><i class="far fa-folder"></i></button>
												</div>

												<div class="mb-2">
													<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('CUSTOM CLASS') }}</label>
													<input v-model="item.imgClass" class="form-control font-size-12px" placeholder="{{ t('e.g. img-fluid rounded-circle') }}">
												</div>

												<div v-if="item.imgMode === 'bg'" class="bg-white border rounded p-2 mb-2">
													<div class="row g-2">
														<div class="col-6">
															<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('HEIGHT (PX)') }}</label>
															<input type="number" v-model="item.bgHeight" class="form-control font-size-12px" placeholder="200">
														</div>
														<div class="col-6">
															<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('SIZE') }}</label>
															<select v-model="item.bgSize" class="form-select font-size-12px">
																<option value="cover">{{ t('Cover') }}</option>
																<option value="contain">{{ t('Contain') }}</option>
																<option value="auto">{{ t('Auto') }}</option>
															</select>
														</div>
														<div class="col-6">
															<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('REPEAT') }}</label>
															<select v-model="item.bgRepeat" class="form-select font-size-12px">
																<option value="no-repeat">{{ t('No Repeat') }}</option>
																<option value="repeat">{{ t('Repeat') }}</option>
																<option value="repeat-x">{{ t('Repeat X') }}</option>
																<option value="repeat-y">{{ t('Repeat Y') }}</option>
															</select>
														</div>
														<div class="col-6">
															<label class="form-label small text-muted mb-2 d-block" style="font-size:10px">{{ t('POSITION') }}</label>
															<select v-model="item.bgPos" class="form-select font-size-12px">
																<option value="center">{{ t('Center') }}</option>
																<option value="top">{{ t('Top') }}</option>
																<option value="bottom">{{ t('Bottom') }}</option>
																<option value="left">{{ t('Left') }}</option>
																<option value="right">{{ t('Right') }}</option>
															</select>
														</div>
													</div>
												</div>
											</div>

											<input v-model="item.title" class="form-control font-size-12px mb-1 fw-bold" placeholder="{{ t('Title') }}">
											<textarea v-model="item.desc" class="form-control font-size-12px" rows="2" placeholder="{{ t('Description') }}"></textarea>
										</div>
									</div>
								</div>
							</div>

							<!-- Collapse -->
							<div v-if="activeItem.type === 'collapse'">
								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">{{ t('Collapse Settings') }}</div>

									<div class="prop-group-body p-2">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Tipe Trigger</label>
												
												<select class="form-select font-size-12px" v-model="activeItem.triggerType">
													<option value="button">Button (Tombol)</option>
													<option value="link">Link (a href)</option>
												</select>
											</div>
											
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Nama Button / Link</label>
												<input type="text" class="form-control font-size-12px" v-model="activeItem.triggerText">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">ID Unik Collapse</label>
												<input type="text" class="form-control font-size-12px" v-model="activeItem.collapseId">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Warna/Class Trigger</label>
												<select class="form-select font-size-12px" v-model="activeItem.triggerBtnColor">
													<option value="btn-primary">Primary</option>
													<option value="btn-secondary">Secondary</option>
													<option value="btn-success">Success</option>
													<option value="btn-danger">Danger</option>
													<option value="btn-outline-primary">Outline Primary</option>
													<option value="text-primary">Text Primary (Untuk Link)</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Status Default</label>
												<select class="form-select font-size-12px" v-model="activeItem.isOpen">
													<option :value="false">Hide (Tertutup)</option>
													<option :value="true">Show (Terbuka)</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Direction (Arah)</label>
												<select class="form-select font-size-12px" v-model="activeItem.direction">
													<option value="down">Ke Bawah (Default)</option>
													<option value="horizontal">Ke Samping (Kanan/Kiri)</option>
													<option value="up">Ke Atas</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Efek Animasi Konten</label>
												<select class="form-select font-size-12px" v-model="activeItem.animation">
													<option value="none">Tanpa Animasi Tambahan</option>
													<option value="fadeIn">Fade In</option>
													<option value="bounce">Bounce</option>
													<option value="pulse">Pulse</option>
													<option value="flip">Flip</option>
													<option value="rotate">Rotate</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Custom CSS (Button)</label>
												<input type="text" class="form-control font-size-12px" v-model="activeItem.customBtnClass" placeholder="misal: shadow-sm rounded-pill">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Custom CSS (Card Body)</label>
												<input type="text" class="form-control font-size-12px" v-model="activeItem.customCardClass" placeholder="misal: bg-light p-4">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">Isi Konten (HTML/CKEditor)</label>
												<ckeditor-component v-model="activeItem.content" :base-url="baseUrl"></ckeditor-component>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Nav Tab -->
							<div v-if="activeItem.type === 'nav_tabs'">

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">Nav Style & Layout</div>
									<div class="prop-group-body p-2">
										<div class="row g-2">
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Style Navigasi</label>
												<select v-model="activeItem.navStyle" class="form-select form-select-sm font-size-12px">
													<option value="tabs">Tabs (Bawaan)</option>
													<option value="pills">Pills</option>
													<option value="underline">Underline</option>
												</select>
											</div>
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Alignment</label>
												<select v-model="activeItem.alignment" class="form-select form-select-sm font-size-12px">
													<option value="horizontal">Horizontal</option>
													<option value="vertical">Vertical</option>
												</select>
											</div>
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Fill & Justify</label>
												<select v-model="activeItem.fillJustify" class="form-select form-select-sm font-size-12px">
													<option value="none">Default (Auto)</option>
													<option value="fill">Fill (Penuhi Ruang)</option>
													<option value="justify">Justify (Sama Rata)</option>
												</select>
											</div>
											<div class="col-12">
												<div class="form-check form-switch mb-1">
													<input class="form-check-input" type="checkbox" v-model="activeItem.mobileScroll" :disabled="activeItem.alignment === 'vertical'">
													<label class="form-check-label small text-muted">Scroll Horizontal di Mobile <span class="text-muted fw-normal" style="font-size: 10px;">(hanya horizontal)</span></label>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">Default Tab Aktif</div>
									<div class="prop-group-body p-2">
										<div class="row g-2">
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Tab yang terbuka pertama kali</label>
												<select v-model="activeItem.activeTabId" class="form-select form-select-sm font-size-12px">
													<option value="">Paling kiri (otomatis)</option>
													<template v-for="(tab, idx) in activeItem.items" :key="idx">
														<option v-if="tab.itemType === 'button' && !tab.disabled" :value="tab.id">@{{ tab.label || ('Tab #' + (idx+1)) }}</option>
													</template>
												</select>
												<small class="text-muted" style="font-size: 10px;">Hanya tab bertipe "Pane" yang bisa dipilih.</small>
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="prop-label border-bottom py-2 mx-2">Custom ID & Class</div>
									<div class="prop-group-body p-2">
										<div class="row g-2">
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Custom ID <span class="fw-normal">(div wrapper)</span></label>
												<input v-model="activeItem.customId" class="form-control form-control-sm font-size-12px" placeholder="Otomatis jika kosong">
											</div>
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(div wrapper)</span></label>
												<input v-model="activeItem.customClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: shadow-sm bg-white">
											</div>
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(ul.nav)</span></label>
												<input v-model="activeItem.navClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: border-0 gap-2 px-3">
											</div>
											<div class="col-12">
												<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(li.nav-item — semua item)</span></label>
												<input v-model="activeItem.navItemClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: me-1 flex-shrink-0">
											</div>
										</div>
									</div>
								</div>

								<div class="prop-group">
									<div class="d-flex justify-content-between align-items-center border-bottom py-2 mx-2">
										<span class="prop-label">Nav Tab Items <span class="fw-normal text-muted" style="font-size: 11px;">(@{{ activeItem.items.length }})</span></span>
										<button class="btn btn-sm btn-outline-primary py-0 font-size-12px" @click="activeItem.items.push({ id: 'tab-' + Math.random().toString(36).substr(2, 9), customId: '', customClass: '', navItemClass: '', paneClass: '', label: 'New Tab', icon: '', itemType: 'button', url: '#', newTab: false, disabled: false, body: 'Konten baru...', dropdownId: '', dropdownMenuClass: '', dropdownItems: [], _open: true })">
											<i class="fas fa-plus"></i> Tambah
										</button>
									</div>

									<div class="px-2 pb-2 pt-1">
										<div v-for="(sub, idx) in activeItem.items" :key="idx" class="mb-2 border rounded overflow-hidden">

											<!-- Accordion Header -->
											<div class="d-flex justify-content-between align-items-center px-2 py-2 bg-light" style="cursor: pointer; user-select: none;" @click="sub._open = !sub._open">
												<div class="d-flex align-items-center gap-2 flex-grow-1 overflow-hidden">
													<i class="fas fa-chevron-right text-muted flex-shrink-0" :style="{ transform: sub._open ? 'rotate(90deg)' : 'rotate(0deg)', transition: 'transform 0.2s ease' }" style="font-size: 10px;"></i>
													<i v-if="sub.icon" :class="sub.icon" class="text-secondary flex-shrink-0" style="font-size: 11px;"></i>
													<span class="small fw-semibold text-truncate">@{{ sub.label || 'Tab Item' }}</span>
													<span class="badge bg-secondary bg-opacity-25 text-secondary flex-shrink-0" style="font-size: 9px; padding: 2px 5px;">@{{ sub.itemType }}</span>
													<span v-if="sub.disabled" class="badge bg-danger bg-opacity-25 text-danger flex-shrink-0" style="font-size: 9px; padding: 2px 5px;">off</span>
													<span v-if="sub.itemType === 'button' && sub.id === (activeItem.activeTabId || activeItem.items.find(t => t.itemType === 'button' && !t.disabled).id)" class="badge bg-success bg-opacity-25 text-success flex-shrink-0" style="font-size: 9px; padding: 2px 5px;">default</span>
												</div>
												
												<button class="btn btn-sm text-danger p-0 ms-2 flex-shrink-0" style="line-height: 1; width: 18px;" @click.stop="activeItem.items.splice(idx, 1)" title="Hapus">
													<i class="fas fa-times" style="font-size: 11px;"></i>
												</button>
											</div>

											<!-- Accordion Body -->
											<div v-show="sub._open" class="border-top">

												<div class="prop-group">
													<div class="prop-label border-bottom py-2 mx-2" style="font-size: 11px;">Label & Icon</div>
													<div class="prop-group-body p-2">
														<div class="row g-2">
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Label Tab</label>
																<input v-model="sub.label" class="form-control form-control-sm font-size-12px">
															</div>
															
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Icon (FontAwesome class)</label>
																<input v-model="sub.icon" class="form-control form-control-sm font-size-12px" placeholder="Misal: fas fa-check">
															</div>
														</div>
													</div>
												</div>

												<div class="prop-group">
													<div class="prop-label border-bottom py-2 mx-2" style="font-size: 11px;">Custom ID & Class</div>
													<div class="prop-group-body p-2">
														<div class="row g-2">
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Custom ID Item</label>
																<input v-model="sub.customId" class="form-control form-control-sm font-size-12px" placeholder="Otomatis jika kosong">
															</div>
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(li.nav-item)</span></label>
																<input v-model="sub.navItemClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: me-2 flex-shrink-0">
															</div>
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(nav-link)</span></label>
																<input v-model="sub.customClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: px-4 fw-bold">
															</div>
															<div v-if="sub.itemType === 'button'" class="col-12">
																<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(div.tab-pane konten)</span></label>
																<input v-model="sub.paneClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: p-4 bg-light border">
															</div>
															<div v-if="sub.itemType === 'dropdown'" class="col-12">
																<label class="form-label small text-muted mb-1">Custom Class <span class="fw-normal">(ul.dropdown-menu)</span></label>
																<input v-model="sub.dropdownMenuClass" class="form-control form-control-sm font-size-12px" placeholder="Misal: shadow border-0">
															</div>
														</div>
													</div>
												</div>

												<div class="prop-group">
													<div class="prop-label border-bottom py-2 mx-2" style="font-size: 11px;">Fungsi & Konten</div>
													<div class="prop-group-body p-2">
														<div class="row g-2">
															<div class="col-12">
																<label class="form-label small text-muted mb-1">Tipe Item</label>
																<select v-model="sub.itemType" class="form-select form-select-sm font-size-12px">
																	<option value="button">Buka Konten Tab (Pane)</option>
																	<option value="link">Sebagai Tautan URL (a href)</option>
																	<option value="dropdown">Menu Dropdown</option>
																</select>
															</div>

															<!-- Type: link -->
															<div v-if="sub.itemType === 'link'" class="col-12 ms-1 border-start border-2 border-primary ps-2">
																<label class="form-label small text-muted mb-1">URL Tautan</label>
																<input v-model="sub.url" class="form-control form-control-sm font-size-12px mb-2">
																<div class="form-check form-switch mb-1">
																	<input class="form-check-input" type="checkbox" v-model="sub.newTab">
																	<label class="form-check-label small text-muted">Buka di Tab Baru (_blank)</label>
																</div>
															</div>

															<!-- Type: button/pane -->
															<div v-if="sub.itemType === 'button'" class="col-12 ms-1 border-start border-2 border-success ps-2">
																<label class="form-label small text-muted mb-1">Isi Konten Tab (HTML/Teks)</label>
																<textarea v-model="sub.body" class="form-control form-control-sm font-size-12px" rows="3" placeholder="Tuliskan konten pane di sini..."></textarea>
															</div>

															<!-- Type: dropdown -->
															<div v-if="sub.itemType === 'dropdown'" class="col-12 ms-1 border-start border-2 border-warning ps-2">
																<label class="form-label small text-muted mb-1">Custom ID Dropdown</label>
																<input v-model="sub.dropdownId" class="form-control form-control-sm font-size-12px mb-2" placeholder="Otomatis jika kosong">

																<label class="form-label small text-muted mb-1 d-block">Sub Items</label>
																
																<div v-for="(dropItem, dIdx) in sub.dropdownItems" :key="dIdx" class="p-2 border rounded bg-white mb-2 shadow-sm">
																	<input v-model="dropItem.label" class="form-control form-control-sm font-size-12px mb-1" placeholder="Label">
																	<input v-model="dropItem.url" class="form-control form-control-sm font-size-12px mb-1" placeholder="URL">
																	<input v-model="dropItem.itemClass" class="form-control form-control-sm font-size-12px mb-1" placeholder="Custom Class (a.dropdown-item)">
																	<div class="d-flex justify-content-between align-items-center mt-1">
																		<div class="form-check form-switch mb-0">
																			<input class="form-check-input" type="checkbox" v-model="dropItem.newTab">
																			<label class="form-check-label text-muted font-size-12px">New Tab</label>
																		</div>
																		<div class="form-check form-switch mb-0">
																			<input class="form-check-input" type="checkbox" v-model="dropItem.disabled">
																			<label class="form-check-label text-muted font-size-12px">Disabled</label>
																		</div>
																		<button class="btn btn-sm text-danger p-0" @click="sub.dropdownItems.splice(dIdx, 1)"><i class="fas fa-times"></i></button>
																	</div>
																</div>

																<button class="btn btn-sm btn-outline-warning w-100 mt-1 font-size-12px" @click="sub.dropdownItems.push({ label: 'Item Baru', url: '#', newTab: false, disabled: false, itemClass: '' })">

															</div>

															<!-- Disabled toggle -->
															<div class="col-12">
																<div class="form-check form-switch mb-1 mt-1">
																	<input class="form-check-input" type="checkbox" v-model="sub.disabled">
																	<label class="form-check-label small text-muted">Disabled (Nonaktifkan Tab)</label>
																</div>
															</div>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>

							</div>

						</div>

						<!-- Advanced -->
						<div v-if="activeItem.type === 'dynamic_post_list'">
							<div class="prop-group">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Data Source') }}</div>
								
								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Data Type') }}</label>
											<select v-model="activeItem.dataType" class="form-select font-size-12px" @change="activeItem.selectedCats = []">
												<option v-for="dt in dataTypesConfig" :value="dt.data">@{{ dt.label }}</option>
											</select>
										</div>

										<div class="col-12" v-if="getCurrentDataType(activeItem.dataType)">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Status') }}</label>
											<select v-model="activeItem.selectedStatus" class="form-select font-size-12px">
												<option v-for="st in getCurrentDataType(activeItem.dataType).status" :value="st">@{{ st }}</option>
											</select>
										</div>

										<div class="col-12" v-if="getCurrentDataType(activeItem.dataType)">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Categories') }}</label>
											<div class="border rounded p-2 bg-white" style="max-height: 100px; overflow-y: auto;">
												<div v-for="cat in getCurrentDataType(activeItem.dataType).categories" :key="cat" class="form-check">
													<input class="form-check-input" type="checkbox" :value="cat" v-model="activeItem.selectedCats" :id="'cat_'+cat" style="height: 1em !important;">
													<label class="form-check-label small" :for="'cat_'+cat">@{{ cat }}</label>
												</div>
											</div>
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Limit Per Page') }}</label>
											<input type="number" v-model="activeItem.perPage" class="form-control font-size-12px" min="1" max="24">
										</div>

										<div class="col-12">
											<div class="bg-secondary-subtle p-3 rounded">
												<div class="row g-3">
													
													<div class="col-9">
														<label class="form-check-label small" for="switchUsePagination">
															<div>{{ t('Use Pagination') }}</div>
															
															<div class="bg-info-subtle text-info-emphasis mt-1" v-if="!activeItem.usePagination">
																<i class="fas fa-info-circle fa-fw"></i> {{ t('You can use slider, scroll to the bottom') }}
															</div>
														</label>
													</div>

													<div class="col-3 d-flex justify-content-end d-flex align-items-center">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox" role="switch" id="switchUsePagination" v-model="activeItem.usePagination">
														</div>
													</div>

													<div class="col-8">
														<label class="form-check-label small" for="switchShowDate">{{ t('Show Date') }}</label>
													</div>
													<div class="col-4 d-flex justify-content-end">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox" role="switch" id="switchShowDate" v-model="activeItem.showDate">
														</div>
													</div>

													<div class="col-8">
														<label class="form-check-label small" for="switchShowCategory">{{ t('Show Category') }}</label>
													</div>
													<div class="col-4 d-flex justify-content-end">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox" role="switch" id="switchShowCategory" v-model="activeItem.showCategory">
														</div>
													</div>

													<div class="col-8">
														<label class="form-check-label small" for="switchShowText">{{ t('Show Text') }}</label>
													</div>

													<div class="col-4 d-flex justify-content-end">
														<div class="form-check form-switch">
															<input class="form-check-input" type="checkbox" role="switch" id="switchShowText" v-model="activeItem.showExcerpt">
														</div>
													</div>

												</div>
											</div>
										</div>

										

									</div>
								</div>
							</div>

							<div class="prop-group" v-if="activeItem.usePagination">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Pagination Settings') }}</div>

								<div class="prop-group-body p-2">
									<div class="row g-3">

										<div class="col-12" v-if="activeItem.usePagination">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Pagination Type') }}</label>
											
											<select v-model="activeItem.paginationType" class="form-select font-size-12px">
												<option value="number">{{ t('Numbering (Default)') }}</option>
												<option value="simple">{{ t('Simple (Prev / Next)') }}</option>
												<option value="cursor">{{ t('Cursor / Load More') }}</option>
											</select>
										</div>

										<div class="col-12">
											<label class="small text-muted mb-1">{{ t('Pagination Alignment') }}</label>
											
											<select v-model="activeItem.navAlign" class="form-select font-size-12px">
												<option value="center">{{ t('Center') }}</option>
												<option value="start">{{ t('Left (Start)') }}</option>
												<option value="end">{{ t('Right (End)') }}</option>
												<option value="between" v-if="activeItem.paginationType !== 'number'">{{ t('Space Between') }}</option>
											</select>
										</div>

										<div class="col-12">											
											<div class="mb-3">
												<label class="small text-muted mb-1">{{ t('Container Class') }}</label>
												<input v-model="activeItem.paginationClass" class="form-control font-size-12px" placeholder="e.g. mt-5 mb-5">
											</div>
										</div>

										<div class="col-12" v-if="!activeItem.paginationType || activeItem.paginationType === 'number'">
											<div class="row g-3">
												<div class="col-12">
													<label class="small text-muted mb-1">{{ t('Page Item Class') }}</label>
													<input v-model="activeItem.pageItemClass" class="form-control font-size-12px" placeholder="e.g. shadow-sm">
												</div>

												<div class="col-12">
													<div class="row g-3">
														<div class="col-12"><label class="small text-muted fw-bold">{{ t('Arrow Icons') }}</label></div>
														
														<div class="col-12">
															<select v-model="activeItem.pageIconType" class="form-select font-size-12px mb-2">
																<option value="class">{{ t('Font Icon (Class)') }}</option>
																<option value="image">{{ t('Custom Image') }}</option>
															</select>
														</div>

														<div class="col-6" v-if="activeItem.pageIconType === 'class'">
															<input v-model="activeItem.pagePrevIcon" class="form-control font-size-12px" placeholder="Prev Icon">
														</div>
														
														<div class="col-6" v-if="activeItem.pageIconType === 'class'">
															<input v-model="activeItem.pageNextIcon" class="form-control font-size-12px" placeholder="Next Icon">
														</div>

														<div class="col-12" v-if="activeItem.pageIconType === 'image'">
															<div class="input-group input-group-sm mb-1">
																<input v-model="activeItem.pagePrevImg" class="form-control" placeholder="Prev Image URL">
																<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'pagePrevImg')"><i class="far fa-folder"></i></button>
															</div>
															
															<div class="input-group input-group-sm">
																<input v-model="activeItem.pageNextImg" class="form-control" placeholder="Next Image URL">
																<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'pageNextImg')"><i class="far fa-folder"></i></button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-12" v-if="activeItem.paginationType === 'simple' || activeItem.paginationType === 'cursor'">
											<div class="row g-3">
												<div class="col-12">
													<label class="small text-muted mb-1">{{ t('Button Class') }}</label>
													<input v-model="activeItem.navBtnClass" class="form-control font-size-12px" placeholder="e.g. btn-primary rounded-pill">
												</div>

												<div class="col-12">
													<label class="small text-muted mb-1">{{ t('Show Icon') }}</label>
													<div class="form-check form-switch pt-1">
														<input class="form-check-input" type="checkbox" v-model="activeItem.navShowIcon">
													</div>
												</div>

												<div v-if="activeItem.navShowIcon" class="row g-3">
													<div class="col-12">
														<select v-model="activeItem.navIconType" class="form-select font-size-12px mb-2">
															<option value="class">{{ t('Font Icon (Class)') }}</option>
															<option value="image">{{ t('Custom Image') }}</option>
														</select>
													</div>

													<div class="col-6" v-if="activeItem.navIconType === 'class'">
														<input v-model="activeItem.navPrevIcon" class="form-control font-size-12px" placeholder="Prev Icon">
													</div>
													
													<div class="col-6" v-if="activeItem.navIconType === 'class'">
														<input v-model="activeItem.navNextIcon" class="form-control font-size-12px" placeholder="Next Icon">
													</div>

													<div class="col-12" v-if="activeItem.navIconType === 'image'">
														<div class="input-group input-group-sm mb-1">
															<input v-model="activeItem.navPrevImg" class="form-control" placeholder="Prev Image URL">
															<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'navPrevImg')"><i class="far fa-folder"></i></button>
														</div>

														<div class="input-group input-group-sm">
															<input v-model="activeItem.navNextImg" class="form-control" placeholder="Next Image URL">
															<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'navNextImg')"><i class="far fa-folder"></i></button>
														</div>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>

							<!-- Image Setting -->
							<div class="prop-group">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Image Settings') }}</div>
								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Image Mode') }}</label>

											<select v-model="activeItem.imgMode" class="form-select font-size-12px">
												<option value="standard">{{ t('Standard Image (img)') }}</option>
												<option value="background">{{ t('Background Image (div)') }}</option>
											</select>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'list' || activeItem.imgMode === 'background'">
											<div class="bg-light p-3 rounded border">
												<label class="form-label fw-bold small text-muted border-bottom pb-3 mb-3 d-block">{{ t('Dimensions') }}</label>
												
												<div class="row g-3">
													<div class="col-12">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Width') }}</label>
														
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.imgWidthVal" class="form-control font-size-12px">
															
															<select v-model="activeItem.imgWidthUnit" class="form-select font-size-12px" style="max-width: 100px">
																<option value="px">px</option>
																<option value="%">%</option>
																<option value="em">em</option>
																<option value="rem">rem</option>
															</select>
														</div>
													</div>

													<div class="col-12">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Height') }}</label>

														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.imgHeightVal" class="form-control font-size-12px">
															
															<select v-model="activeItem.imgHeightUnit" class="form-select font-size-12px" style="max-width: 100px">
																<option value="px">px</option>
																<option value="%">%</option>
																<option value="em">em</option>
																<option value="rem">rem</option>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="col-12" v-if="activeItem.imgMode === 'background'">
											<div class="p-3 bg-light rounded border">
												<label class="form-label fw-bold small text-muted border-bottom pb-3 mb-3 d-block">{{ t('Background Image Properties') }}</label>

												<div class="row g-3">
													<div class="col-6">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Background Size') }}</label>
														
														<select v-model="activeItem.bgSize" class="form-select font-size-12px">
															<option value="cover">{{ t('Cover') }}</option>
															<option value="contain">{{ t('Contain') }}</option>
															<option value="custom">{{ t('Custom') }}</option>
														</select>
													</div>
													
													<div class="col-12" v-if="activeItem.bgSize === 'custom'">
														<div class="input-group input-group-sm">
															<input type="number" v-model="activeItem.bgSizeVal" class="form-control font-size-12px">
															
															<select v-model="activeItem.bgSizeUnit" class="form-select font-size-12px">
																<option value="px">px</option>
																<option value="%">%</option>
																<option value="em">em</option>
																<option value="rem">rem</option>
															</select>
														</div>
													</div>
													
													<div class="col-6">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Background Position') }}</label>
														
														<select v-model="activeItem.bgPos" class="form-select font-size-12px">
															<option value="center">{{ t('Center') }}</option>
															<option value="top">{{ t('Top') }}</option>
															<option value="bottom">{{ t('Bottom') }}</option>
															<option value="left">{{ t('Left') }}</option>
															<option value="right">{{ t('Right') }}</option>
														</select>
													</div>
													
													<div class="col-6">
														<label class="form-label small text-muted mb-2 d-block">{{ t('Background Repeat') }}</label>
														
														<select v-model="activeItem.bgRepeat" class="form-select font-size-12px">
															<option value="no-repeat">{{ t('No Repeat') }}</option>
															<option value="repeat">{{ t('Repeat') }}</option>
														</select>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="prop-group">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('List Settings') }}</div>
								
								<div class="prop-group-body p-2">
									<div class="row g-3">
										
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('View Mode') }}</label>
											<select v-model="activeItem.viewMode" class="form-select font-size-12px">
												<option value="grid">{{ t('Grid View') }}</option>
												<option value="list">{{ t('List View') }}</option>
											</select>
										</div>
										
										<div class="col-12" v-if="activeItem.viewMode === 'list'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Image Alignment') }}</label>
											<select v-model="activeItem.imagePos" class="form-select font-size-12px">
												<option value="start">{{ t('Left') }}</option>
												<option value="end">{{ t('Right') }}</option>
											</select>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'list'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Text Alignment') }}</label>
											
											<div class="btn-group w-100">
												<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.textAlign === 'start'}" @click="activeItem.textAlign = 'start'"><i class="fas fa-align-left"></i></button>
												<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.textAlign === 'center'}" @click="activeItem.textAlign = 'center'"><i class="fas fa-align-center"></i></button>
												<button class="btn btn-sm btn-outline-secondary" :class="{active: activeItem.textAlign === 'end'}" @click="activeItem.textAlign = 'end'"><i class="fas fa-align-right"></i></button>
											</div>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'list'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Content Alignment (Vertical)') }}</label>
											
											<select v-model="activeItem.verticalAlign" class="form-select font-size-12px">
												<option value="start">{{ t('Top') }}</option>
												<option value="center">{{ t('Middle') }}</option>
												<option value="end">{{ t('Bottom') }}</option>
											</select>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'grid'">
											<label class="small text-muted mb-2 fw-bold" style="font-size:11px; letter-spacing:0.5px;">GRID COLUMNS (RESPONSIVE)</label>

											<div class="resp-switcher">
												<button class="resp-btn" :class="{active: activeViewMode === 'mobile'}" @click="activeViewMode = 'mobile'">{{ t('Mobile') }}</button>
												<button class="resp-btn" :class="{active: activeViewMode === 'tablet'}" @click="activeViewMode = 'tablet'">{{ t('Tablet') }}</button>
												<button class="resp-btn" :class="{active: activeViewMode === 'desktop'}" @click="activeViewMode = 'desktop'">{{ t('Desktop') }}</button>
												<button class="resp-btn" :class="{active: activeViewMode === 'fhd'}" @click="activeViewMode = 'fhd'">{{ t('FHD') }}</button>
												<button class="resp-btn" :class="{active: activeViewMode === '4k'}" @click="activeViewMode = '4k'">{{ t('4K') }}</button>
											</div>

											<div v-if="activeViewMode === 'mobile'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Mobile') }} (< 768px)</label>

												<select v-model="activeItem.colsMobile" class="form-select font-size-12px">
													<option :value="1">1 {{ t('Column') }}</option>
													<option :value="2">2 {{ t('Column') }}</option>
													<option :value="3">3 {{ t('Column') }}</option>
													<option :value="4">4 {{ t('Column') }}</option>
													<option :value="5">5 {{ t('Column') }}</option>
													<option :value="6">6 {{ t('Column') }}</option>
												</select>
											</div>

											<div v-if="activeViewMode === 'tablet'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Tablet') }} (≥ 768px)</label>

												<select v-model="activeItem.colsTablet" class="form-select font-size-12px">
													<option :value="1">1 {{ t('Column') }}</option>
													<option :value="2">2 {{ t('Column') }}</option>
													<option :value="3">3 {{ t('Column') }}</option>
													<option :value="4">4 {{ t('Column') }}</option>
													<option :value="5">5 {{ t('Column') }}</option>
													<option :value="6">6 {{ t('Column') }}</option>
												</select>
											</div>

											<div v-if="activeViewMode === 'desktop'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Desktop') }} (≥ 1200px)</label>

												<select v-model="activeItem.colsDesktop" class="form-select font-size-12px">
													<option :value="1">1 {{ t('Column') }}</option>
													<option :value="2">2 {{ t('Column') }}</option>
													<option :value="3">3 {{ t('Column') }}</option>
													<option :value="4">4 {{ t('Column') }}</option>
													<option :value="5">5 {{ t('Column') }}</option>
													<option :value="6">6 {{ t('Column') }}</option>
												</select>
											</div>

											<div v-if="activeViewMode === 'fhd'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Desktop Full HD') }} (≥ 1400px)</label>

												<select v-model="activeItem.colsFHD" class="form-select font-size-12px">
													<option :value="1">1 {{ t('Column') }}</option>
													<option :value="2">2 {{ t('Column') }}</option>
													<option :value="3">3 {{ t('Column') }}</option>
													<option :value="4">4 {{ t('Column') }}</option>
													<option :value="5">5 {{ t('Column') }}</option>
													<option :value="6">6 {{ t('Column') }}</option>
												</select>
											</div>

											<div v-if="activeViewMode === '4k'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Desktop 4K') }} (≥ 2560px)</label>

												<select v-model="activeItem.cols4K" class="form-select font-size-12px">
													<option :value="1">1 {{ t('Column') }}</option>
													<option :value="2">2 {{ t('Column') }}</option>
													<option :value="3">3 {{ t('Column') }}</option>
													<option :value="4">4 {{ t('Column') }}</option>
													<option :value="5">5 {{ t('Column') }}</option>
													<option :value="6">6 {{ t('Column') }}</option>
												</select>
											</div>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'grid'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Text Position') }}</label>

											<select v-model="activeItem.textPos" class="form-select font-size-12px">
												<option value="below">{{ t('Below Media') }}</option>
												<option value="overlay">{!! t('Inside Media {1}', '(Overlay)') !!}</option>
											</select>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'grid' && activeItem.textPos === 'overlay'">
											<div class="row g-3">
												<div class="col-12">
													<div class="form-check form-switch bg-light p-3 rounded border">
														<input class="form-check-input ms-0 me-2" type="checkbox" v-model="activeItem.hoverAnim" id="dynHoverAnim">
														<label class="form-check-label small" for="dynHoverAnim">{{ t('Fade Text on Hover') }}</label>
													</div>
												</div>

												<div class="col-6">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Overlay Background') }}</label>
													<input type="text" v-model="activeItem.overlayColor" class="form-control font-size-12px" placeholder="rgba(0, 0, 0, 0.3)">
												</div>
												
												<div class="col-6">
													<label class="form-label small text-muted mb-2 d-block">{{ t('Text Color') }}</label>
													<input type="color" v-model="activeItem.overlayTextColor" class="form-control form-control-sm p-0">
												</div>
											</div>
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'list' || (activeItem.viewMode === 'grid' && activeItem.textPos === 'below')">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Text Color') }}</label>
											<input type="color" v-model="activeItem.textColor" class="form-control form-control-sm p-0">
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{!! t('Gap {1}', '(Spacing)') !!}</label>
											
											<select v-model="activeItem.gap" class="form-select font-size-12px">
												<option :value="0">0 (No Gap)</option>
												<option :value="1">1 (Small)</option>
												<option :value="2">2 (Medium)</option>
												<option :value="3">3 (Normal)</option>
												<option :value="4">4 (Large)</option>
												<option :value="5">5 (Extra Large)</option>
											</select>
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Border Style') }}</label>
											
											<select v-model="activeItem.borderStyle" class="form-select font-size-12px">
												<option value="card">{!! t('Full Card {1}', '(Default)') !!}</option>
												<option value="text">{{ t('Text Only') }}</option>
												<option value="border-bottom" v-if="activeItem.viewMode === 'list'">{!! t('Border Bottom Only') !!}</option>
												<option value="none">{{ t('None') }}</option>
											</select>
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Min Height') }}</label>
											<input type="number" v-model="activeItem.minHeight" class="form-control font-size-12px" placeholder="0">
										</div>

										<div class="col-12">
											<div class="form-check form-switch bg-light p-3 rounded border">
												<input class="form-check-input ms-0 me-2" type="checkbox" v-model="activeItem.roundedCorners">
												<label class="form-check-label small fw-medium">{{ t('Rounded Corners') }}</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Custom Classes -->
							<div class="prop-group">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Custom Classes') }}</div>

								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Main Body Class') }}</label>
											<input v-model="activeItem.textWrapperClass" class="form-control font-size-12px" placeholder="e.g. p-4">
										</div>

										<div class="col-12" v-if="activeItem.viewMode === 'list'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('List Content Wrapper Class') }}</label>
											<input v-model="activeItem.listContentWrapperClass" class="form-control font-size-12px" placeholder="e.g. ps-3">
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Title Class') }}</label>
											<input v-model="activeItem.titleClass" class="form-control font-size-12px" placeholder="h4 text-primary">
										</div>

										<div class="col-12">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Description Class') }}</label>
											<input v-model="activeItem.descClass" class="form-control font-size-12px" placeholder="small text-muted">
										</div>
									</div>
								</div>
							</div>

							<!-- Content Settings -->
							<div class="prop-group">
								<div class="prop-label border-bottom py-2 mx-2">{{ t('Content Settings') }}</div>

								<div class="prop-group-body p-2">
									<div class="row g-3">
										<div :class="activeItem.truncTitleMode === 'chars' ? 'col-6' : 'col-12'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Title') }}</label>

											<select v-model="activeItem.truncTitleMode" class="form-select font-size-12px">
												<option value="off">{{ t('Off') }}</option>
												<option value="auto">{{ t('1 Line') }}</option>
												<option value="chars">{{ t('Chars') }}</option>
											</select>
										</div>

										<div class="col-6" v-if="activeItem.truncTitleMode === 'chars'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Limit') }}</label>

											<div class="input-group input-group-sm">
												<input type="number" v-model="activeItem.truncTitleLimit" class="form-control font-size-12px">
												<span class="input-group-text small text-muted">{{ t('chars') }}</span>
											</div>
										</div>

										<div :class="(activeItem.truncDescMode === 'chars' || activeItem.truncDescMode === 'auto') ? 'col-6' : 'col-12'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Truncate Description') }}</label>

											<select v-model="activeItem.truncDescMode" class="form-select font-size-12px">
												<option value="off">{{ t('Off') }}</option>
												<option value="auto">{{ t('Lines') }}</option>
												<option value="chars">{{ t('Chars') }}</option>
											</select>
										</div>

										<div class="col-6" v-if="activeItem.truncDescMode === 'chars'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Limit') }}</label>

											<div class="input-group input-group-sm">
												<input type="number" v-model="activeItem.truncDescLimit" class="form-control font-size-12px">
												<span class="input-group-text small text-muted">{{ t('chars') }}</span>
											</div>
										</div>

										<div class="col-6" v-if="activeItem.truncDescMode === 'auto'">
											<label class="form-label small text-muted mb-2 d-block">{{ t('Lines') }}</label>

											<div class="input-group input-group-sm">
												<input type="number" v-model="activeItem.truncDescLines" class="form-control font-size-12px">
												<span class="input-group-text small text-muted">{{ t('lines') }}</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Button Settings -->
							<div class="prop-group">
								<div class="d-flex justify-content-between align-items-center px-3 py-2 mx-2 bg-secondary-subtle cursor-pointer rounded" @click="activeItem.showBtn = !activeItem.showBtn">
									<label class="small fw-bold text-dark mb-0 cursor-pointer"><i class="fas fa-mouse-pointer me-1"></i> {{ t('Read More Button') }}</label>
									<div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" v-model="activeItem.showBtn" @click.stop></div>
								</div>

								<transition name="slider-settings">
									<div v-if="activeItem.showBtn" class="p-2 mt-2 bg-white">
										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Wrapper Class') }}</label>
												<input v-model="activeItem.btnWrapperClass" class="form-control font-size-12px" placeholder="e.g. p-3 text-center">
											</div>
		
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Text') }}</label>
												<input v-model="activeItem.btnText" class="form-control font-size-12px">
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Position') }}</label>
												
												<select v-model="activeItem.btnPos" class="form-select font-size-12px">
													<option value="below">{{ t('Below Description') }}</option>
													<option value="side">{{ t('Side (Opposite Image)') }}</option>
												</select>

												<div v-if="activeItem.viewMode === 'grid' && activeItem.btnPos === 'side'" class="small text-danger mt-1">{{ t('* Side position only works in List View') }}</div>
											</div>

											<div class="col-12" v-if="activeItem.btnPos === 'below'">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Vertical Position') }}</label>
												
												<select v-model="activeItem.btnVerticalPos" class="form-select font-size-12px">
													<option value="auto">{{ t('Auto (Flow)') }}</option>
													<option value="bottom">{{ t('Stick to Bottom') }}</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Button Class') }}</label>
												<input v-model="activeItem.btnClass" class="form-control font-size-12px">
											</div>
											
											<div class="col-12">
												<div class="bg-light p-3 rounded border">
													<label class="form-label small fw-bold mb-2 d-block">{{ t('Icon Settings') }}</label>
													
													<div class="row g-3">
														<div class="col-12">
															<select v-model="activeItem.btnIconType" class="form-select font-size-12px">
																<option value="none">{{ t('No Icon') }}</option>
																<option value="class">{{ t('Icon Class') }}</option>
																<option value="image">{{ t('Custom Image') }}</option>
															</select>
														</div>

														<div class="col-12" v-if="activeItem.btnIconType === 'class'">
															<label class="form-label small fw-bold mb-2 d-block">{{ t('Icon Source') }}</label>
															<input v-model="activeItem.btnIconClass" class="form-control font-size-12px" placeholder="fas fa-arrow-right">
														</div>

														<div class="col-12" v-if="activeItem.btnIconType === 'image'">
															<label class="form-label small fw-bold mb-2 d-block">{{ t('Icon Source') }}</label>

															<div class="input-group input-group-sm">
																<input v-model="activeItem.btnIconSrc" class="form-control font-size-12px" placeholder="{{ t('Image URL') }}">
																<button class="btn btn-outline-secondary" @click="openCkFinder(activeItem, 'btnIconSrc')"><i class="far fa-folder"></i></button>
															</div>
														</div>

														<div class="col-12" v-if="activeItem.btnIconType !== 'none'">
															<label class="form-label small fw-bold mb-2 d-block">{{ t('Icon Position') }}</label>
															
															<select v-model="activeItem.btnIconPos" class="form-select font-size-12px">
																<option value="start">Left (Start)</option>
																<option value="end">Right (End)</option>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</transition>
							</div>

							<div v-if="!activeItem.usePagination && activeItem.viewMode === 'grid'" class="prop-group">
								<div class="d-flex justify-content-between align-items-center px-3 py-2 mx-2 bg-secondary-subtle cursor-pointer rounded" @click="activeItem.enableSplide = !activeItem.enableSplide">
									<label class="small fw-bold text-dark mb-0 cursor-pointer"><i class="fas fa-play-circle me-1"></i> {{ t('Enable Slider') }}</label>
									<div class="form-check form-switch m-0">
										<input class="form-check-input" type="checkbox" v-model="activeItem.enableSplide" @click.stop>
									</div>
								</div>

								<transition name="slider-settings">
									<div v-if="activeItem.enableSplide" class="p-2 mt-2 bg-white">
										<div class="alert alert-info py-1 px-2 mb-3 rounded-1 small">
											<i class="fas fa-info-circle me-1"></i> {{ t('Per Page follows Grid Columns.') }}
										</div>

										<div class="row g-3">
											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Type') }}</label>
												<select v-model="activeItem.splideType" class="form-select font-size-12px">
													<option value="slide">{{ t('Slide') }}</option>
													<option value="loop">{{ t('Loop') }}</option>
													<option value="fade">{{ t('Fade') }}</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Slider Direction') }}</label>

												<select v-model="activeItem.splideDirection" class="form-select font-size-12px" :disabled="activeItem.splideType === 'fade'">
													<option value="ltr">{{ t('Horizontal') }}</option>
													<option value="ttb">{{ t('Vertical') }}</option>
												</select>
											</div>

											<div class="col-12">
												<label class="form-label small text-muted mb-2 d-block">{{ t('Slider per Move') }}</label>
												<input type="number" v-model="activeItem.splidePerMove" class="form-control font-size-12px" min="1">
											</div>

											<div class="col-12">
												<div class="bg-secondary-subtle p-3 rounded">
													<div class="row g-3">
														<div class="col-9">
															<label class="form-check-label small" for="switchAutoPlay">{{ t('Auto Play') }}</label>
														</div>

														<div class="col-3 d-flex justify-content-end d-flex align-items-center">
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" id="switchAutoPlay" v-model="activeItem.splideAutoplay">
															</div>
														</div>

														<transition name="slider-settings">
															<div class="col-12" v-if="activeItem.splideAutoplay">
																<div class="card-body">
																	<input type="number" v-model="activeItem.splideInterval" class="form-control font-size-12px" step="500">
																</div>
															</div>
														</transition>

														<div class="col-9">
															<label class="form-check-label small" for="switchAutoHeight">{{ t('Auto Height') }}</label>
														</div>

														<div class="col-3 d-flex justify-content-end d-flex align-items-center">
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" id="switchAutoHeight" v-model="activeItem.splideAutoHeight">
															</div>
														</div>

														<div class="col-9">
															<label class="form-check-label small" for="switchShowPagination">{{ t('Show Pagination') }}</label>
														</div>

														<div class="col-3 d-flex justify-content-end d-flex align-items-center">
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" id="switchShowPagination" v-model="activeItem.splidePagination">
															</div>
														</div>

														<div class="col-9">
															<label class="form-check-label small" for="switchShowArrow">{{ t('Show Arrow') }}</label>
														</div>

														<div class="col-3 d-flex justify-content-end d-flex align-items-center">
															<div class="form-check form-switch">
																<input class="form-check-input" type="checkbox" role="switch" id="switchShowArrow" v-model="activeItem.splideArrows">
															</div>
														</div>

														<transition name="slider-settings">
															<div class="col-12" v-if="activeItem.splideArrows">
																<div class="card-body">
																	<div class="row g-3">
																		<div class="col-12">
																			<label class="form-label small text-muted mb-2 d-block">{{ t('Arrow Icon') }}</label>
																			<select v-model="activeItem.splideArrowType" class="form-select font-size-12px">
																				<option value="default">{{ t('Default') }}</option>
																				<option value="custom">{{ t('Custom Icon (using Fontawesome)') }}</option>
																			</select>
																		</div>

																		<div class="col-12" v-if="activeItem.splideArrowType === 'custom'">
																			<div class="row g-3">
																				<div class="col-6">
																					<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Left') }}</label>
																					<input v-model="activeItem.splideArrowLeftIcon" class="form-control font-size-12px">
																				</div>

																				<div class="col-6">
																					<label class="form-label small text-muted mb-2 d-block">{{ t('Icon Right') }}</label>
																					<input v-model="activeItem.splideArrowRightIcon" class="form-control font-size-12px">
																				</div>
																			</div>
																		</div>

																		<div class="col-6">
																			<label class="form-label small text-muted mb-2 d-block">Pos Y</label>
																			<div class="input-group input-group-sm">
																				<input type="number" v-model="activeItem.splideArrowPosY" class="form-control font-size-12px">
																				<span class="input-group-text small text-muted">%</span>
																			</div>
																		</div>

																		<div class="col-6">
																			<label class="form-label small text-muted mb-2 d-block">Pos X</label>
																			<div class="input-group input-group-sm">
																				<input type="number" v-model="activeItem.splideArrowPosX" class="form-control font-size-12px">
																				<span class="input-group-text small text-muted">%</span>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</transition>
													</div>
												</div>
											</div>
										</div>
									</div>
								</transition>

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>

<!-- =============================================
	 CSS EDITOR MODAL
	 ============================================= -->
<div v-if="showCssEditor" class="css-editor-modal" @click.self="showCssEditor = false">
	<div class="css-editor-panel" :class="{ fullscreen: cssEditorFullscreen }">
		
		<!-- Header -->
		<div class="css-editor-header">
			<div class="editor-title">
				<i class="fas fa-paint-brush"></i>
				Custom CSS Editor
			</div>
			<div class="d-flex align-items-center gap-2">
				<!-- Toggle Fullscreen -->
				<button 
					class="action-btn" 
					:title="cssEditorFullscreen ? 'Exit Fullscreen' : 'Fullscreen'"
					@click="cssEditorFullscreen = !cssEditorFullscreen"
					style="width: 26px; height: 26px; font-size: 12px;">
					<i class="fas" :class="cssEditorFullscreen ? 'fa-compress-alt' : 'fa-expand-alt'"></i>
				</button>
				<!-- Close -->
				<button 
					class="action-btn danger" 
					title="Tutup" 
					@click="showCssEditor = false"
					style="width: 26px; height: 26px; font-size: 12px;">
					<i class="fas fa-times"></i>
				</button>
			</div>
		</div>

		<!-- Body -->
		<div class="css-editor-body">
			<!-- Hint Box -->
			<div class="css-editor-hint">
				<i class="fas fa-info-circle me-1 text-primary"></i>
				Tulis CSS kustom di sini. Gunakan <code>.class-name</code> atau <code>#id-name</code> yang 
				kamu isi pada field <strong>Custom Class</strong> di setiap widget/row/container.
				CSS ini otomatis aktif saat <strong>Preview</strong> dan tersimpan ke JSON.
			</div>
			
			<!-- Textarea -->
			<textarea 
				class="css-editor-textarea"
				v-model="customCss"
				placeholder=".my-custom-class {
  color: #333;
  font-size: 18px;
}

#my-section {
  background: linear-gradient(135deg, #667eea, #764ba2);
  padding: 60px 0;
}"
				@keydown.tab.prevent="handleCssTab($event)"
				spellcheck="false"
				autocomplete="off"
			></textarea>
		</div>

		<!-- Footer -->
		<div class="css-editor-footer">
			<span class="css-char-count">
				<i class="fas fa-code me-1 opacity-50"></i>
				@{{ customCss.length }} karakter &bull; @{{ customCss.split('\n').length }} baris
			</span>
			<div class="d-flex gap-2">
				<button class="btn-css-clear" @click="customCss = ''" title="Hapus semua CSS">
					<i class="fas fa-trash me-1"></i> Clear
				</button>
				<button class="btn-css-apply" @click="showCssEditor = false">
					<i class="fas fa-check me-1"></i> Apply & Close
				</button>
			</div>
		</div>
	</div>
</div>

	<div v-if="showWidgetModal" class="position-fixed top-0 start-0 w-100 h-100 overflow-y-auto overflow-x-hidden" style="background: rgba(0,0,0,0.5); z-index: 9999;">
		<div class="widget-picker-modal mx-auto rounded shadow-lg d-flex align-items-center justify-content-center" style="width: 800px; height: calc(100% - 1.75rem * 2);min-height: calc(100% - 1.75rem * 2);margin: 1.75rem;">
			<div class="d-flex flex-column w-100 mh-100 position-relative overflow-hidden">
				<div class="d-flex justify-content-between align-items-center border-bottom p-3" style="border-color: var(--sidebar-border) !important;">
					<h6 class="fw-bold mb-0">{{ t('Select Widget') }}</h6>
					<button class="btn btn-sm btn-light border" @click="showWidgetModal = false"><i class="fas fa-times"></i></button>
				</div>

				<div class="overflow-y-scroll p-4">
					<div class="row gx-3 gy-5">
						<div class="col-12 basic-widget"> 
							<h6 class="fw-bold mb-3 text-secondary">{{ t('Basic Widget') }}</h6>
							
							<div class="row g-3">
								<div class="col-3" v-for="tool in tools.widgets" :key="tool.type">
									<button class="widget-picker-btn w-100 h-100 py-3 rounded d-flex flex-column align-items-center gap-2" @click="addWidgetFromPicker(tool.type)">
										<i :class="tool.icon" class="fa-2x mb-1"></i>
										<span class="small fw-medium">@{{ tool.label }}</span>
									</button>
								</div>
							</div>
						</div>

						<div class="col-12 advanced-widget">
							<h6 class="fw-bold mb-3 text-secondary">{{ t('Advanced Widget') }}</h6>

							<div class="row g-3">
								<div class="col-3" v-for="tool in tools.advanced" :key="tool.type">
									<button class="widget-picker-btn w-100 h-100 py-3 rounded d-flex flex-column align-items-center gap-2" @click="addWidgetFromPicker(tool.type)">
										<i :class="tool.icon" class="fa-2x mb-1"></i>
										<span class="small fw-medium">@{{ tool.label }}</span>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="https://unpkg.com/vuejs-paginate-next@latest/dist/vuejs-paginate-next.umd.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuedraggable@4.1.0/dist/vuedraggable.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

<script src="{{ asset('assets/plugins/ckfinder/ckfinder.js') }}"></script>
<script src="{{ asset('assets/plugins/ckeditor5/build/ckeditor.js?v=0.0.1') }}"></script>

{{-- Inject data dari controller ke JavaScript --}}
<script>
	// Mode 'create' = null, Mode 'update' = object berisi layout & custom_css
	const PAGE_MODE = "{{ isset($pageData) ? 'update' : 'create' }}";
	const PAGE_ID   = {{ isset($pageData) ? $pageData->id : 'null' }};
	const PAGE_DATA = {!! isset($pageData)
		? json_encode([
			'page_name'		=> $pageData->page_name		?? '',
			'layout'		=> $pageData->vars			?? '[]',
			'custom_css'	=> $pageData->custom_css	?? '',
		])
		: 'null'
	!!};
</script>

<script>
	const { createApp, ref, onMounted, onBeforeUnmount, watch, computed, nextTick, resolveComponent } = Vue;
	const draggable = window.vuedraggable;
	const APP_BASE_URL = '/'; 

	// --- KONFIGURASI DATA TYPE ---
	const DATA_TYPES = [
		{
			data: 'article',
			label: 'Article',
			// Mapping field API ke UI
			field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', author: 'author', date: 'created_at', category: 'category' },
			categories: ['uncategorized', 'daily', 'weekly', 'monthly'],
			status: ['publish', 'private'],
			detailUrl: 'articles/detail/'
		},
		{
			data: 'event',
			label: 'Event',
			field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', location: 'location', author: 'author', date: 'start_date', end_date: 'end_date' },
			categories: ['uncategorized', 'today', 'ongoing', 'upcoming'],
			status: ['publish', 'private'],
			detailUrl: 'events/detail/'
		},
		{
			data: 'testimoni',
			label: 'Testimoni',
			field: { id: 'id', title: 'title', content: 'content', thumb_s: 'thumb_s', thumb_l: 'thumb_l', position: 'position', date: 'created_at' },
			categories: ['uncategorized', 'google', 'facebook', 'instagram'],
			status: ['publish'],
			detailUrl: ''
		}
	];

	// --- CKEDITOR COMPONENT (TIDAK BERUBAH) ---
	const CkEditorComponent = {
		props: ['modelValue', 'baseUrl'],
		template: `<div><div ref="editorRef"></div></div>`,
		setup(props, { emit }) {
			const editorRef = ref(null);
			let editorInstance = null;
			watch(() => props.modelValue, (newVal) => { if (editorInstance && newVal !== editorInstance.getData()) { editorInstance.setData(newVal || ''); } });
			onMounted(() => {
				ClassicEditor.create(editorRef.value, { 
					toolbar: { 
						items: ['heading', '|', 'fontFamily', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'code', 'removeFormat', '|', 'link', 'bulletedList', 'numberedList', 'todoList', '|', 'outdent', 'indent', 'alignment', '|', 'CKFinder', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed', 'horizontalLine', 'specialCharacters', '|', 'findAndReplace', 'sourceEditing', 'htmlEmbed', 'codeBlock', 'highlight', '|', 'undo', 'redo'], 
						shouldNotGroupWhenFull: true 
					},
					language: 'en',
					image: { styles: [ 'alignCenter', 'alignLeft', 'alignRight' ], resizeOptions: [ { name: 'resizeImage:original', label: 'Default', value: null }, { name: 'resizeImage:100', label: '100%', value: '100' } ], toolbar: [ 'imageTextAlternative', 'toggleImageCaption', '|', 'imageStyle:inline', 'imageStyle:wrapText', 'imageStyle:breakText', 'imageStyle:side', '|', 'resizeImage', 'linkImage' ] },
					table: { contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells', 'tableCellProperties', 'tableProperties' ] },
					ckfinder: { openerMethod: "modal", uploadUrl: props.baseUrl + "assets/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json" }
				}).then(editor => { 
					editorInstance = editor; editor.setData(props.modelValue || ''); 
					editor.model.document.on("change:data", () => { emit('update:modelValue', editor.getData()); }); 
					editor.ui.view.element.addEventListener('mousedown', (e) => { e.stopPropagation(); });
				});
			});
			onBeforeUnmount(() => { if (editorInstance) editorInstance.destroy(); });
			return { editorRef };
		}
	};

	// --- PREVIEW VIEWER COMPONENT (TIDAK BERUBAH) ---
	const PreviewViewer = {
		props: ['data', 'viewType', 'customCss'],
		template: `
		<div class="preview-container" style="position: relative; width: 100%; height: calc(100vh - 60px); overflow: auto; background-color: var(--builder-bg);">
			
			<!-- Inject Custom CSS -->
			<component :is="'style'" v-if="customCss">@{{ customCss }}</component>

			<div class="preview-frame" :style="scaleStyle">
				 
				 <div v-for="cont in data" :key="cont.id" 
					:class="[
						getContainerClass(cont),
						filterResponsiveClasses(cont.customClass)
					]"
					:style="{ backgroundColor: cont.styles.bgColor, backgroundImage: cont.styles.bgImage ? 'url(' + cont.styles.bgImage + ')' : null, backgroundPosition: cont.styles.bgPos, backgroundRepeat: cont.styles.bgRepeat, backgroundSize: cont.styles.bgSize, minHeight: cont.styles.minHeight }">
					
					<div v-for="row in cont.children" :key="row.id" class="row"
						:class="[
							getRowClasses(row),
							filterResponsiveClasses(row.customClass)
						]">
						
						<div v-for="col in row.children" :key="col.id" 
							 :class="[
								getColClasses(col),
								filterResponsiveClasses(col.customClass)
							 ]">
							 
							 <div v-for="item in col.children" :key="item.id">
								
								<div v-if="item.type === 'row'" class="row"
									:class="[
										getRowClasses(item),
										filterResponsiveClasses(item.customClass)
									]">
									<div v-for="nCol in item.children" :key="nCol.id"
										 :class="[
											getColClasses(nCol),
											filterResponsiveClasses(nCol.customClass)
										 ]">
										 <div v-for="nItem in nCol.children" :key="nItem.id">
											<component :is="getWidgetTemplate(nItem)" :item="nItem" :filter-class="filterResponsiveClasses" :view-type="viewType"></component>
										 </div>
									</div>
								</div>
								
								<div v-else>
									<component :is="getWidgetTemplate(item)" :item="item" :filter-class="filterResponsiveClasses" :view-type="viewType"></component>
								</div>
							 </div>
						</div>
					</div>
				 </div>
			</div>
		</div>`,
		setup(props) {
			const getContainerClass = (cont) => 
			{
				const map = 
				{
					'mobile': cont.classMobile || 'container-fluid',
					'tablet': cont.classTablet || 'container-fluid',
					'desktop': cont.classDesktop || 'container',
					'fhd': cont.classFHD || 'container',
					'4k': cont.class4K || 'container'
				};
				
				return map[props.viewType];
			};

			const getRowClasses = (row) => 
			{
				const map = 
				{
					'mobile': (row.gutter || '') + ' ' + (row.widthMobile || 'w-100'),
					'tablet': (row.gutterTablet || '') + ' ' + (row.widthTablet || 'w-md-100'),
					'desktop': (row.gutterDesktop || '') + ' ' + (row.widthDesktop || 'w-xl-100'),
					'fhd': (row.gutterFHD || '') + ' ' + (row.widthFHD || 'w-xxl-100'),
					'4k': (row.gutter4K || '') + ' ' + (row.width4K || 'w-3xl-100')
				};
				
				return map[props.viewType];
			};

			const getColClasses = (col) => 
			{
				const map = 
				{
					'mobile': col.widthMobile || 'col-12',
					'tablet': col.widthTablet || 'col-md',
					'desktop': col.widthDesktop || 'col-xl',
					'fhd': col.widthFHD || 'col-xxl',
					'4k': col.width4K || 'col-3xl'
				};
				
				return map[props.viewType];
			};

			const filterResponsiveClasses = (classString) => 
			{
				if ( ! classString) return '';
				const smPattern = /-(sm)-/i; const mdPattern = /-(md)-/i; const lgPattern = /-(lg)-/i; const xlPattern = /-(xl)-/i; const xxlPattern = /-(xxl)-/i;
				
				return classString.split(' ').filter(cls => 
				{
					if (props.viewType === 'mobile') { if (smPattern.test(cls) || mdPattern.test(cls) || lgPattern.test(cls) || xlPattern.test(cls) || xxlPattern.test(cls)) return false; }
					else if (props.viewType === 'tablet') { if (lgPattern.test(cls) || xlPattern.test(cls) || xxlPattern.test(cls)) return false; }
					else if (props.viewType === 'desktop') { if (xxlPattern.test(cls)) return false; }
					return true;

				}).join(' ');
			};

			const scaleStyle = computed(() => 
			{
				const widthMap = 
				{
					mobile: 375,
					tablet: 768,
					desktop: 1366, 
					fhd: 1920,
					'4k': 2560
				};

				const targetWidth = widthMap[props.viewType] || 1366;
				const windowWidth = window.innerWidth - 60; 

				let scale = 1;
				
				if (targetWidth > windowWidth) 
				{
					scale = windowWidth / targetWidth;
				}

				return {
					width: targetWidth + 'px',
					minHeight: '100%',
					backgroundColor: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#0f172a' : '#ffffff',
					
					position: 'absolute',
					top: '30px',
					left: '50%', 
					transform: `translateX(-50%) scale(${scale})`,
					transformOrigin: 'top center',
					
					boxShadow: '0 0 50px rgba(0,0,0,0.2)',
					marginBottom: '100px',
					display: 'block' 
				};
			});

			const WidgetImage = { props: ['item', 'filterClass'], template: `<img :src="item.src" class="img-fluid rounded" :class="filterClass(item.customClass)" :style="{ width: item.widthUnit === 'auto' ? 'auto' : item.widthVal + item.widthUnit, height: item.heightUnit === 'auto' ? 'auto' : item.heightVal + item.heightUnit, objectFit: 'cover' }">` };			
			const WidgetText = { props: ['item', 'filterClass'], template: `<div v-html="item.content" :class="filterClass(item.customClass)"></div>` };
			const WidgetVideo = { props: ['item', 'filterClass'], setup(props) { const playerRef = ref(null); let playerInstance = null; const getYoutubeId = (url) => { if (!url) return 'dQw4w9WgXcQ'; return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop(); }; onMounted(() => { if (playerRef.value) { const options = { controls: props.item.controls ? ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'] : [], autoplay: props.item.autoplay, loop: { active: props.item.loop }, muted: props.item.muted, youtube: { noCookie: true, rel: 0, showinfo: 0, iv_load_policy: 3, modestbranding: 1 }, ratio: (props.item.aspectRatio === '16/9' ? '16:9' : (props.item.aspectRatio === '4/3' ? '4:3' : (props.item.aspectRatio === '1/1' ? '1:1' : '21:9'))) }; playerInstance = new Plyr(playerRef.value, options); } }); onBeforeUnmount(() => { if (playerInstance) { playerInstance.destroy(); } }); return { playerRef, getYoutubeId }; }, template: `<div :class="filterClass(item.customClass)" :style="{ width: (item.width || 100) + '%' }"> <div class="ratio"> <div v-if="!item.videoType || item.videoType === 'youtube'" class="plyr__video-embed" ref="playerRef"> <iframe :src="'https://www.youtube.com/embed/'+getYoutubeId(item.youtubeUrl)+'?origin='+baseUrl+'&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe> </div> <video v-if="item.videoType === 'file'" ref="playerRef" class="plyr" playsinline :controls="item.controls" :data-poster="item.poster" style="width: 100%; height: 100%; object-fit: cover;"> <source :src="item.videoSrc" type="video/mp4"> </video> </div> </div>` };
			const WidgetButton = { props: ['item', 'filterClass'], template: `<a :href="item.href || '#'" :target="item.newTab ? '_blank' : ''" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" :class="filterClass(item.customClass)"><i v-if="item.iconType === 'class' && item.iconPos === 'start'" :class="item.iconClass"></i><img v-if="item.iconType === 'image' && item.iconPos === 'start' && item.iconSrc" :src="item.iconSrc" style="width:20px;height:20px;object-fit:contain;"><span v-text="item.text"></span><i v-if="item.iconType === 'class' && item.iconPos === 'end'" :class="item.iconClass"></i><img v-if="item.iconType === 'image' && item.iconPos === 'end' && item.iconSrc" :src="item.iconSrc" style="width: 20px;height: 20px;object-fit: contain;"></a>` };
			const WidgetList = { props: ['item', 'filterClass'], template: `<component :is="item.listType" class="list-group" :class="[item.listType === 'ol' ? 'list-group-numbered' : '', item.flush ? 'list-group-flush' : '', item.noBorder ? 'border-0' : '', item.listSize !== 'default' && item.listSize !== 'custom' ? item.listSize : '', filterClass(item.customClass)]" :style="item.listSize === 'custom' && item.customSizeValue ? 'font-size: ' + item.customSizeValue + item.customSizeUnit + ';' : ''"><li v-for="(sub, idx) in item.items" :key="idx" class="list-group-item d-flex justify-content-between align-items-center" :class="[sub.active ? 'active ' + (sub.activeCss ? filterClass(sub.activeCss) : '') : '', sub.disabled ? 'disabled' : '', sub.customCss ? filterClass(sub.customCss) : '', item.noBorder ? 'border-0' : '', (sub.inputType !== 'none' && item.listType === 'ol') ? 'hide-marker-ol' : '']"><label class="d-flex align-items-center mb-0 me-auto" :class="item.listType === 'ol' && sub.inputType === 'none' ? 'ms-2' : ''" :style="sub.inputType !== 'none' && !sub.disabled ? 'cursor:pointer; flex-grow:1;' : 'flex-grow:1;'"><input v-if="sub.inputType === 'checkbox'" type="checkbox" class="form-check-input mt-0" :class="sub.markerPosition === 'before' ? 'order-2 ms-3 me-2' : 'order-1 me-3'" v-model="sub.inputChecked" :disabled="sub.disabled"><input v-else-if="sub.inputType === 'radio'" type="radio" :name="'list_radio_' + (item.id || _uid)" class="form-check-input mt-0" :class="sub.markerPosition === 'before' ? 'order-2 ms-3 me-2' : 'order-1 me-3'" :checked="sub.inputChecked" @change="sub.inputChecked = $event.target.checked" :disabled="sub.disabled"><div v-if="item.listType === 'ul' && (sub.inputType === 'none' || sub.showMarker)" class="d-flex align-items-center" :class="sub.markerPosition === 'before' && sub.inputType !== 'none' ? 'order-1' : (sub.inputType !== 'none' ? 'order-2 me-2' : 'me-2')"><i v-if="item.styleType === 'icon'" :class="item.commonIcon"></i><img v-else-if="item.styleType === 'image'" :src="item.commonImage" style="width:1.25em;"><i v-else class="fas fa-circle" style="font-size:0.4em; vertical-align:middle"></i></div><span v-if="item.listType === 'ol' && sub.inputType !== 'none' && sub.showMarker" class="fw-bold" :class="sub.markerPosition === 'before' ? 'order-1' : 'order-2 me-2'" v-text="(idx + 1) + '.'"></span><span v-html="sub.text" class="order-3" :class="{'text-muted': sub.disabled, 'text-decoration-line-through': sub.disabled}"></span></label><span v-if="sub.badge" :class="['badge rounded-pill', sub.badgeType === 'custom' ? sub.customBadgeCss : sub.badgeType]" v-text="sub.badgeText"></span></li></component>` };
			const WidgetCard = { props: ['item', 'filterClass'], methods: { truncate(text, limit) { if (!text) return ''; if (text.length <= limit) return text; return text.substring(0, limit) + '...'; } }, template: `<div class="card" :class="[item.cardStyleClass, filterClass(item.customClass)]"> <img v-if="item.src" :src="item.src" class="card-img-top" :class="item.imgClass" :style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit: 'cover' }"> <div class="card-body"> <h5 class="card-title" :class="[{'text-truncate': item.truncTitleMode === 'auto'}, item.titleClass]" v-text="item.truncTitleMode === 'chars' ? truncate(item.cardTitle, item.truncTitleLimit) : item.cardTitle"> </h5> <p class="card-text" :class="item.textClass" :style="item.truncTextMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncTextLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncTextMode === 'chars' ? truncate(item.cardText, item.truncTextLimit) : item.cardText"> </p> <a v-if="item.btnLink" :href="item.btnLink" :target="item.newTab ? '_blank' : ''" class="btn btn-primary d-inline-flex align-items-center justify-content-center gap-2" :class="item.btnClass"> <i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass"></i> <img v-if="item.btnIconType === 'image' && item.btnIconPos === 'start' && item.btnIconSrc" :src="item.btnIconSrc" style="width:20px;height:20px;object-fit:contain;"> <span v-text="item.btnText"></span> <i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass"></i> <img v-if="item.btnIconType === 'image' && item.btnIconPos === 'end' && item.btnIconSrc" :src="item.btnIconSrc" style="width:20px;height:20px;object-fit:contain;"> </a> </div> </div>` };
			const WidgetMedia = { props: ['item', 'filterClass'], methods: { truncate(text, limit) { if (!text) return ''; if (text.length <= limit) return text; return text.substring(0, limit) + '...'; } }, template: `<div class="d-flex" :class="[item.imagePos === 'end' ? 'flex-row-reverse text-end' : 'text-start', 'align-items-' + item.align, filterClass(item.customClass)]"> <img :src="item.src" class="flex-shrink-0" :class="[item.imagePos === 'end' ? 'ms-3' : 'me-3', item.imgClass]" :style="{width: item.imgWidthUnit === 'auto' ? 'auto' : item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightUnit === 'auto' ? 'auto' : item.imgHeightVal + item.imgHeightUnit, objectFit: 'cover' }"> <div class="flex-grow-1" style="min-width: 0;"> <h5 :class="[{'text-truncate': item.truncHeadingMode === 'auto'}, item.headingClass]" v-text="item.truncHeadingMode === 'chars' ? truncate(item.heading, item.truncHeadingLimit) : item.heading"></h5> <div :class="item.contentClass" :style="item.truncContentMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncContentLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncContentMode === 'chars' ? truncate(item.content, item.truncContentLimit) : item.content"></div> <div v-if="item.mediaBtnText && item.mediaBtnLink && (!item.mediaBtnPos || item.mediaBtnPos === 'below')" class="mt-2"> <a :href="item.mediaBtnLink" :target="item.mediaBtnNewTab ? '_blank' : ''" :class="item.mediaBtnClass || 'btn btn-primary btn-sm'">@{{ item.mediaBtnText }}</a> </div> </div> <div v-if="item.mediaBtnText && item.mediaBtnLink && item.mediaBtnPos === 'side'" class="flex-shrink-0 align-self-center" :class="item.imagePos === 'end' ? 'me-3' : 'ms-3'"> <a :href="item.mediaBtnLink" :target="item.mediaBtnNewTab ? '_blank' : ''" :class="item.mediaBtnClass || 'btn btn-primary btn-sm'">@{{ item.mediaBtnText }}</a> </div> </div>` };
			const WidgetAccordion = { props: ['item', 'filterClass'], template: `<div class="accordion" :id="'acc_' + item.id" :class="filterClass(item.customClass)"><div v-for="(sub, idx) in item.items" :key="idx" class="accordion-item"><h2 class="accordion-header" :id="'heading_' + item.id + '_' + idx"><button class="accordion-button" :class="{collapsed: idx!==0}" type="button" data-bs-toggle="collapse" :data-bs-target="'#collapse_' + item.id + '_' + idx" v-text="sub.header"></button></h2><div :id="'collapse_' + item.id + '_' + idx" class="accordion-collapse collapse" :class="{show: idx===0}" :data-bs-parent="'#acc_' + item.id"><div class="accordion-body" v-text="sub.content"></div></div></div></div>` };
			const WidgetTable = { props: ['item', 'filterClass'], template: `<div class="table-responsive" :class="filterClass(item.customClass)"><table class="table table-bordered"><thead><tr><th v-for="h in item.tableData.headers" v-text="h"></th></tr></thead><tbody><tr v-for="r in item.tableData.rows"><td v-for="c in r" v-text="c"></td></tr></tbody></table></div>` };			
			const WidgetSpacer = { props: ['item', 'filterClass'], template: `<div :style="{height: item.height + (item.unit || 'px')}" :class="filterClass(item.customClass)"></div>` };
			const WidgetDivider = { props: ['item', 'filterClass'], template: `<div :class="['d-flex', 'w-100', 'align-items-center', item.align === 'center' ? 'justify-content-center' : (item.align === 'end' ? 'justify-content-end' : 'justify-content-start'), filterClass(item.customClass)]" :style="{paddingTop: item.gap+'px', paddingBottom: item.gap+'px'}"><div :style="{width: item.width + (item.widthUnit || '%'), borderTopWidth: item.thickness+'px', borderTopStyle: item.style, borderTopColor: item.color}"></div></div>` };
			const WidgetHeading = { props: ['item', 'filterClass'], template: `<component :is="item.htmlTag || 'h2'" :class="filterClass(item.customClass)" :style="{ textAlign: item.alignment, color: item.color, fontSize: item.fontSize ? item.fontSize + item.fontSizeUnit : null, fontWeight: item.fontWeight, margin: 0 }"><a v-if="item.link" :href="item.link" style="color: inherit; text-decoration: none;">@{{ item.text }}</a><span v-else>@{{ item.text }}</span></component> `};

			const WidgetMediaList = {
					props: ['item', 'filterClass', 'viewType'],
					setup(props) {
						// --- STATE ---
						const currentSlide = Vue.ref(0);
						const sliderViewport = Vue.ref(null);
						const sliderHeight = Vue.ref('auto');
						let autoplayTimer = null;
						let plyrInstances = []; 

						// Drag States
						const isDragging = Vue.ref(false);
						const startPos = Vue.ref(0);
						const currentTranslate = Vue.ref(0);

						// --- HELPERS ---
						const getYoutubeId = (url) => {
							if (!url) return '';
							return url.includes('v=') ? url.split('v=')[1].split('&')[0] : url.split('/').pop();
						};

						const truncate = (text, limit) => {
							if (!text) return '';
							if (text.length <= limit) return text;
							return text.substring(0, limit) + '...';
						};

						const getColClass = () => {
							const i = props.item;
							const v = props.viewType;
							if (v) {
								if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1)) + ' mb-3';
								if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2)) + ' mb-3';
								if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3)) + ' mb-3';
								if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4)) + ' mb-3';
								if (v === '4k') return 'col-' + (12 / (i.cols4K || 6)) + ' mb-3';
							}
							return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4)) + ' col-3xl-' + (12 / (i.cols4K || 6)) + ' mb-3';
						};

						// --- COMPUTED PROPERTIES ---

						// 1. Active Columns
						const activeColumns = Vue.computed(() => {
							if (props.viewType) {
								if (props.viewType === 'mobile') return props.item.colsMobile || 1;
								if (props.viewType === 'tablet') return props.item.colsTablet || 2;
								if (props.viewType === 'desktop') return props.item.colsDesktop || 3;
								if (props.viewType === 'fhd') return props.item.colsFHD || 4;
								if (props.viewType === '4k') return props.item.cols4K || 6;
							}

							const w = window.innerWidth;
							if (w < 768) return props.item.colsMobile || 1;
							if (w < 1200) return props.item.colsTablet || 2;
							if (w < 1400) return props.item.colsDesktop || 3;
							if (w < 2560) return props.item.colsFHD || 4;
							return props.item.cols4K || 6;
						});

						// 2. Track Style
						const trackStyle = Vue.computed(() => {
							const cols = activeColumns.value;
							const totalItems = props.item.items.length;
							const idx = currentSlide.value;
							
							if (props.item.splideType === 'fade') {
								return { position: 'relative', width: '100%', height: sliderHeight.value };
							}

							const movePct = (100 / totalItems) * idx; 
							const isVertical = props.item.splideDirection === 'ttb';
							
							const dragOffset = isDragging.value ? currentTranslate.value : 0;
							const transitionStyle = isDragging.value ? 'none' : 'transform 0.5s ease-in-out';

							if (!isVertical) {
								return {
									transform: `translateX(calc(-${movePct}% + ${dragOffset}px))`,
									display: 'flex', flexWrap: 'nowrap',
									width: `${(totalItems / cols) * 100}%`,
									transition: transitionStyle,
									cursor: isDragging.value ? 'grabbing' : 'grab'
								};
							} else {
								return {
									transform: `translateY(calc(-${movePct}% + ${dragOffset}px))`,
									display: 'flex', flexDirection: 'column',
									height: `${(totalItems / cols) * 100}%`,
									transition: transitionStyle,
									cursor: isDragging.value ? 'grabbing' : 'grab'
								};
							}
						});

						// 3. Pagination Dots (UPDATED FOR FADE GRID)
						const paginationDots = Vue.computed(() => {
							const total = props.item.items.length;
							const cols = activeColumns.value;
							const type = props.item.splideType;

							// Logic khusus Fade: Dots mewakili "Halaman", bukan Item individual
							if (type === 'fade') {
								const totalPages = Math.ceil(total / cols);
								// Mengembalikan array index awal setiap halaman: [0, 3, 6...] jika cols=3
								return Array.from({length: totalPages}, (_, i) => i * cols);
							}

							// Logic Slide Standard
							const move = props.item.splidePerMove || 1;
							const maxIndex = Math.max(0, total - cols);
							const dots = [];
							
							for (let i = 0; i <= maxIndex; i += move) {
								dots.push(i);
							}
							
							if (dots.length > 0 && dots[dots.length - 1] < maxIndex) {
								dots.push(maxIndex);
							} else if (dots.length === 0 && total > 0) {
								dots.push(0);
							}

							return dots;
						});

						// --- METHODS ---

						// 1. Slide Style (UPDATED FOR FADE GRID)
						const getSlideStyle = (index) => {
							const cols = activeColumns.value;
							const totalItems = props.item.items.length;
							const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16;
							const pad = gapVal / 2;

							if (props.item.splideType === 'fade') {
								// Hitung Halaman
								const itemPage = Math.floor(index / cols);       // Item ini ada di halaman ke berapa?
								const activePage = Math.floor(currentSlide.value / cols); // Halaman berapa yang sedang aktif?
								
								const isVisible = itemPage === activePage;
								
								// Hitung Posisi Grid dalam Halaman
								const colIndex = index % cols; // Urutan ke berapa dalam kolom (0, 1, 2...)
								const widthPct = 100 / cols;   // Lebar per item (misal 33.33%)

								return {
									position: 'absolute', 
									top: 0, 
									left: `${colIndex * widthPct}%`, // Geser ke samping sesuai urutan kolom
									width: `${widthPct}%`,
									height: 'auto', 
									opacity: isVisible ? 1 : 0, 
									zIndex: isVisible ? 2 : 1,
									visibility: isVisible ? 'visible' : 'hidden', 
									transition: 'opacity 0.8s ease-in-out',
									padding: `0 ${pad}px`, 
									boxSizing: 'border-box',
									pointerEvents: isVisible ? 'auto' : 'none' // Agar item tersembunyi tidak bisa diklik
								};
							}

							// Standard Slide
							if (!props.item.splideDirection || props.item.splideDirection === 'ltr') {
								return { 
									width: `${100 / totalItems}%`, 
									flexShrink: 0, 
									padding: `0 ${pad}px`, 
									boxSizing: 'border-box' 
								};
							} else {
								return { 
									width: '100%', 
									height: `${100 / totalItems}%`, 
									flexShrink: 0, 
									padding: `${pad}px 0`, 
									boxSizing: 'border-box' 
								};
							}
						};

						// 2. Update Dimensions
						const updateDimensions = () => {
							if(props.item.splideDirection === 'ltr' && props.item.splideType !== 'fade') {
								sliderHeight.value = 'auto';
								return;
							}

							setTimeout(() => {
								if (!sliderViewport.value) return;
								const items = sliderViewport.value.querySelectorAll('.media-inner-card');
								let maxH = 0;
								items.forEach(el => maxH = Math.max(maxH, el.offsetHeight));
								
								if (maxH > 0) {
									const cols = activeColumns.value; 
									const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16;
									
									if (props.item.splideType === 'fade') {
										sliderHeight.value = (maxH + gapVal) + 'px'; 
									} else {
										const totalH = (maxH * cols) + (gapVal * cols); 
										sliderHeight.value = totalH + 'px';
									}
								} else {
									sliderHeight.value = (props.item.minHeight || 400) + 'px';
								}
							}, 400); 
						};

						// 3. VIDEO & AUTOPLAY MANAGER
						const manageActiveSlide = () => {
							stopAutoplay();
							plyrInstances.forEach(p => p.pause());

							const currentItemData = props.item.items[currentSlide.value];
							
							if (currentItemData && currentItemData.type === 'video') {
								const activeSlideEl = sliderViewport.value ? sliderViewport.value.querySelectorAll('.media-list-item')[currentSlide.value] : null;
								if(activeSlideEl) {
									const videoContainer = activeSlideEl.querySelector('.plyr');
									if(videoContainer) {
										const player = plyrInstances.find(p => p.elements.container === videoContainer);
										if(player) {
											if (props.item.splideAutoplay) {
												player.play();
												player.off('ended');
												player.once('ended', () => { nextSlide(); });
											} 
											return; 
										}
									}
								}
							}
							startAutoplay();
						};

						const startAutoplay = () => {
							stopAutoplay();
							if (props.item.splideAutoplay) {
								autoplayTimer = setInterval(nextSlide, props.item.splideInterval || 3000);
							}
						};

						const stopAutoplay = () => {
							if (autoplayTimer) clearInterval(autoplayTimer);
						};

						// 4. Navigation (UPDATED FOR FADE GRID)
						const getMaxIndex = () => {
							if (props.item.splideType === 'fade') return props.item.items.length - 1;
							const cols = activeColumns.value;
							return Math.max(0, props.item.items.length - cols);
						};

						const nextSlide = () => {
							const maxIndex = getMaxIndex();
							const total = props.item.items.length;
							
							// Logic Fade Grid: Pindah per HALAMAN (per cols)
							if (props.item.splideType === 'fade') {
								const cols = activeColumns.value;
								let nextIdx = currentSlide.value + cols;
								
								// Jika sudah di halaman terakhir (atau lebih), loop ke awal
								if (nextIdx >= total) nextIdx = 0;
								
								currentSlide.value = nextIdx;
							} 
							else { 
								// Logic Slide Biasa
								const move = props.item.splidePerMove || 1;
								if (props.item.splideType === 'loop') {
									if (currentSlide.value >= maxIndex) currentSlide.value = 0; 
									else currentSlide.value = Math.min(maxIndex, currentSlide.value + move);
								} else {
									currentSlide.value = Math.min(maxIndex, currentSlide.value + move);
								}
							}
						};

						const prevSlide = () => {
							const total = props.item.items.length;
							const maxIndex = getMaxIndex();

							if (props.item.splideType === 'fade') {
								const cols = activeColumns.value;
								let prevIdx = currentSlide.value - cols;

								// Jika mundur dari 0, ke halaman terakhir yg valid
								if (prevIdx < 0) {
									// Cari index awal dari halaman terakhir
									const remainder = total % cols;
									// Jika remainder 0, berarti full page. Jika ada sisa, halaman terakhir isinya sisa itu.
									prevIdx = remainder === 0 ? total - cols : total - remainder;
								}

								currentSlide.value = prevIdx;
							} 
							else {
								const move = props.item.splidePerMove || 1;
								if (props.item.splideType === 'loop') {
									if (currentSlide.value <= 0) currentSlide.value = maxIndex;
									else currentSlide.value = Math.max(0, currentSlide.value - move);
								} else {
									currentSlide.value = Math.max(0, currentSlide.value - move);
								}
							}
						};

						const goToSlide = (idx) => {
							// Validasi batas index
							if (props.item.splideType !== 'fade') {
								const maxIndex = getMaxIndex();
								if(idx > maxIndex) idx = maxIndex;
							}
							currentSlide.value = idx;
						};

						// 5. Drag Handlers
						const getPosition = (e) => {
							const isVertical = props.item.splideDirection === 'ttb';
							return isVertical 
								? (e.touches ? e.touches[0].clientY : e.clientY)
								: (e.touches ? e.touches[0].clientX : e.clientX);
						};

						const onDragStart = (e) => {
							if (props.item.splideType === 'fade') return;
							isDragging.value = true;
							startPos.value = getPosition(e);
							currentTranslate.value = 0;
							stopAutoplay();
						};

						const onDragMove = (e) => {
							if (!isDragging.value) return;
							const currentPos = getPosition(e);
							currentTranslate.value = currentPos - startPos.value;
						};

						const onDragEnd = () => {
							if (!isDragging.value) return;
							isDragging.value = false;
							const threshold = 50;
							if (currentTranslate.value < -threshold) nextSlide();
							else if (currentTranslate.value > threshold) prevSlide();
							currentTranslate.value = 0;
						};

						// LIFECYCLE
						Vue.onMounted(() => {
							plyrInstances = [];
							const players = document.querySelectorAll('.plyr-list-' + props.item.id);
							players.forEach((el, index) => {
								const wrapper = el.closest('.media-list-video-container');
								const dataIndex = wrapper ? parseInt(wrapper.getAttribute('data-index')) : index;
								
								let ratioSetting = el.getAttribute('data-ratio') || '16/9';
								let configRatio = ratioSetting.replace('/', ':');
								
								const p = new Plyr(el, {
									ratio: configRatio,
									controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'fullscreen'],
									youtube: { noCookie: true, rel: 0, modestbranding: 1 },
									autopause: false,
									mediaIndex: dataIndex 
								});
								
								p.on('play', () => stopAutoplay());
								plyrInstances.push(p);
							});

							updateDimensions();
							setTimeout(manageActiveSlide, 500); 
							window.addEventListener('resize', updateDimensions);
						});

						Vue.watch(currentSlide, () => {
							manageActiveSlide();
						});

						Vue.watch(() => [
							props.item.items.length, 
							props.item.colsDesktop, props.item.colsTablet, props.item.colsMobile, props.item.colsFHD, props.item.cols4K, 
							props.item.splideDirection, props.item.gap, props.viewType, props.item.splideType
						], () => {
							updateDimensions();
							currentSlide.value = 0; 
						}, { deep: true });

						Vue.watch(() => [props.item.splideAutoplay, props.item.splideInterval], () => {
							manageActiveSlide();
						});

						Vue.onBeforeUnmount(() => {
							stopAutoplay();
							window.removeEventListener('resize', updateDimensions);
							plyrInstances.forEach(p => p.destroy());
						});

						return { getYoutubeId, truncate, getColClass, sliderViewport, trackStyle, getSlideStyle, sliderHeight, nextSlide, prevSlide, goToSlide, currentSlide, paginationDots, onDragStart, onDragMove, onDragEnd };
					},
					template: `
					<div :class="filterClass(item.customClass)">
						
						<div v-if="!item.enableSplide" class="row" :class="'g-' + item.gap">
							<div v-for="(sub, idx) in item.items" :key="idx" :class="getColClass()">
								<div class="media-list-item media-inner-card h-100 position-relative overflow-hidden" 
									:class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners}" 
									:style="{ minHeight: item.minHeight > 0 ? item.minHeight + 'px' : 'auto' }">
									
									<div class="media-wrapper position-relative" style="width:100%;" 
										:style="{ aspectRatio: (sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default' || sub.bgHeight)) ? 'auto' : (sub.aspectRatio || '16/9'), height: (sub.type === 'image' && sub.imgMode === 'bg' && sub.bgHeight) ? sub.bgHeight + 'px' : 'auto' }">
										<img v-if="sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default')" :src="sub.src || 'https://placehold.co/600x400'" :class="sub.imgClass" style="width:100%; height:100%; object-fit:cover; display:block;">
										<div v-if="sub.type === 'image' && sub.imgMode === 'bg'" :class="sub.imgClass" :style="{ backgroundImage: 'url(' + (sub.src || 'https://placehold.co/600x400') + ')', backgroundSize: sub.bgSize || 'cover', backgroundRepeat: sub.bgRepeat || 'no-repeat', backgroundPosition: sub.bgPos || 'center', width: '100%', height: '100%' }"></div>
										<div v-if="sub.type === 'video'" class="media-list-video-container" :class="sub.videoClass" :data-index="idx" style="height:100%;"><div v-if="sub.videoType === 'youtube'" :class="'plyr__video-embed plyr-list-' + item.id" :data-ratio="sub.aspectRatio"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(sub.youtubeUrl) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div><video v-if="sub.videoType === 'file'" :class="'plyr plyr-list-' + item.id" :data-ratio="sub.aspectRatio" playsinline controls style="width: 100%; height: 100%; object-fit: cover;"><source :src="sub.videoSrc" type="video/mp4"></video></div>
										<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="[{'hover-fade': item.hoverAnim}, item.textWrapperClass]" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
									</div>
									<div v-if="item.textPos === 'below'" class="p-3" :class="[{'bg-white border': item.borderStyle === 'text', 'rounded': item.borderStyle === 'text' && item.roundedCorners}, item.textWrapperClass]" :style="{ color: item.textColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
								</div>
							</div>
						</div>

						<div v-else class="custom-slider position-relative" ref="sliderViewport" :style="{ overflow: 'hidden', width: '100%', height: sliderHeight, userSelect: 'none' }" @mousedown="onDragStart" @touchstart="onDragStart" @mousemove="onDragMove" @touchmove="onDragMove" @mouseup="onDragEnd" @touchend="onDragEnd" @mouseleave="onDragEnd">
							
							<div class="custom-slider-track" :style="trackStyle">
								<div v-for="(sub, idx) in item.items" :key="idx" :style="getSlideStyle(idx)">
									<div class="media-list-item media-inner-card h-100 position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners}" :style="{ minHeight: item.minHeight > 0 ? item.minHeight + 'px' : 'auto' }">
										<div class="media-wrapper position-relative" style="width:100%;" 
											:style="{ aspectRatio: (sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default' || sub.bgHeight)) ? 'auto' : (sub.aspectRatio || '16/9'), height: (sub.type === 'image' && sub.imgMode === 'bg' && sub.bgHeight) ? sub.bgHeight + 'px' : 'auto' }">
											<img v-if="sub.type === 'image' && (!sub.imgMode || sub.imgMode === 'default')" :src="sub.src || 'https://placehold.co/600x400'" :class="sub.imgClass" style="width:100%; height:100%; object-fit:cover; display:block; pointer-events: none;">
											<div v-if="sub.type === 'image' && sub.imgMode === 'bg'" :class="sub.imgClass" :style="{ backgroundImage: 'url(' + (sub.src || 'https://placehold.co/600x400') + ')', backgroundSize: sub.bgSize || 'cover', backgroundRepeat: sub.bgRepeat || 'no-repeat', backgroundPosition: sub.bgPos || 'center', width: '100%', height: '100%' }"></div>
											<div v-if="sub.type === 'video'" class="media-list-video-container" :class="sub.videoClass" :data-index="idx" style="height:100%; pointer-events: auto;">
												<div v-if="sub.videoType === 'youtube'" :class="'plyr__video-embed plyr-list-' + item.id" :data-ratio="sub.aspectRatio"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(sub.youtubeUrl) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div>
												<video v-if="sub.videoType === 'file'" :class="'plyr plyr-list-' + item.id" :data-ratio="sub.aspectRatio" playsinline controls style="width: 100%; height: 100%; object-fit: cover;"><source :src="sub.videoSrc" type="video/mp4"></video>
											</div>
											<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="[{'hover-fade': item.hoverAnim}, item.textWrapperClass]" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
										</div>

										<div v-if="item.textPos === 'below'" class="p-3" :class="[{'bg-white border': item.borderStyle === 'text', 'rounded': item.borderStyle === 'text' && item.roundedCorners}, item.textWrapperClass]" :style="{ color: item.textColor }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(sub.title, item.truncTitleLimit) : sub.title"></h5><p class="small mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(sub.desc, item.truncDescLimit) : sub.desc"></p></div>
									</div>
								</div>
							</div>

							<div v-if="item.splideArrows" class="slider-arrows">
								<button type="button" @click.stop="prevSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', left: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }">
									<i :class="(item.splideArrowType === 'custom' ? item.splideArrowLeftIcon : 'fas fa-chevron-left') || 'fas fa-chevron-left'"></i>
								</button>

								<button type="button" @click.stop="nextSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', right: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }">
									<i :class="(item.splideArrowType === 'custom' ? item.splideArrowRightIcon : 'fas fa-chevron-right') || 'fas fa-chevron-right'"></i>
								</button>
							</div>

							<div v-if="item.splidePagination" class="slider-pagination d-flex justify-content-center gap-2 mt-3 position-absolute w-100" style="bottom: 10px; z-index: 9;">
								<button v-for="idx in paginationDots" :key="'dot-'+idx" 
										@click.stop="goToSlide(idx)"
										class="border-0 rounded-circle transition-all"
										:style="{ width: '10px', height: '10px', backgroundColor: currentSlide === idx ? (item.textColor || '#000') : '#ccc', transform: currentSlide === idx ? 'scale(1.2)' : 'scale(1)', opacity: currentSlide === idx ? 1 : 0.5, cursor: 'pointer' }">
								</button>
							</div>

						</div>
					</div>`
			};

			const WidgetDynamicPostList = {
				props: ['item', 'filterClass', 'viewType'],
				setup(props) {
					// STATE DATA
					const fetchedData = Vue.ref([]);
					const totalPages = Vue.ref(0);
					const currentPage = Vue.ref(1);
					const isLoading = Vue.ref(false);
					
					// STATE CURSOR
					const cursorNext = Vue.ref(null);
					const cursorPrev = Vue.ref(null);
					
					// SLIDER STATES
					const currentSlide = Vue.ref(0);
					const sliderViewport = Vue.ref(null);
					const sliderHeight = Vue.ref('auto');
					let autoplayTimer = null;
					let plyrInstances = []; 
					const isDragging = Vue.ref(false);
					const startPos = Vue.ref(0);
					const currentTranslate = Vue.ref(0);

					const config = Vue.computed(() => {
						const found = DATA_TYPES.find(d => d.data === props.item.dataType);
						return found || { field: {}, label: 'Unknown' };
					});

					// HELPERS
					const formatDate = (dateString) => {
						if(!dateString) return '';
						const date = new Date(dateString);
						return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
					};

					const stripHtml = (html) => {
						if (!html) return '';
						let tmp = document.createElement("DIV");
						tmp.innerHTML = html;
						let text = tmp.textContent || tmp.innerText || "";
						return text.trim();
					};

					const truncate = (text, limit) => {
						if (!text) return '';
						const cleanText = stripHtml(text);
						if (cleanText.length <= limit) return cleanText;
						return cleanText.substring(0, limit) + '...';
					};

					const getYoutubeId = (url) => (url && url.includes('v=')) ? url.split('v=')[1].split('&')[0] : (url ? url.split('/').pop() : '');

					// STYLE LOGIC
					const getBgStyle = (imgUrl, isGrid = false) => {
						if (props.item.imgMode !== 'background') return {};
						let size = props.item.bgSize === 'custom' ? `${props.item.bgSizeVal}${props.item.bgSizeUnit}` : props.item.bgSize;
						let width = '100%'; let height = '100%'; 
						if (isGrid) {
							width = props.item.imgWidthVal ? props.item.imgWidthVal + props.item.imgWidthUnit : '100%';
							height = props.item.imgHeightVal ? props.item.imgHeightVal + props.item.imgHeightUnit : (props.item.bgHeight ? props.item.bgHeight + 'px' : '200px');
						}
						return { backgroundImage: `url('${imgUrl || 'https://placehold.co/400x300'}')`, backgroundSize: size, backgroundPosition: props.item.bgPos, backgroundRepeat: props.item.bgRepeat, width: width, height: height };
					};

					const getCardStyle = () => {
						let style = {};
						const minH = parseInt(props.item.minHeight);
						if (minH > 0) style.minHeight = minH + 'px';
						style.display = 'flex'; style.flexDirection = 'column';
						return style;
					};

					// ALIGNMENT HELPER (NEW)
					const getPaginationAlign = () => {
						const align = props.item.navAlign || 'center'; // Default center
						const map = {
							'start': 'justify-content-start',
							'center': 'justify-content-center',
							'end': 'justify-content-end',
							'between': 'justify-content-between'
						};
						return map[align] || 'justify-content-center';
					};

					const getSmartPadding = (userClass) => (/\bp(?:[xytrblse])?-/.test(userClass || '')) ? '' : 'p-3'; 
					const getSmartButtonPadding = (userClass) => (/\bp(?:[xytrblse])?-/.test(userClass || '')) ? '' : 'p-0'; 
					const getBorderTextClasses = () => props.item.borderStyle === 'text' ? { 'border': true, 'rounded': props.item.roundedCorners } : {};
					const getListAlignClass = () => ({ 'start': 'align-items-start', 'center': 'align-items-center', 'end': 'align-items-end' }[props.item.verticalAlign] || 'align-items-center');

					const activeColumns = Vue.computed(() => {
						let w = window.innerWidth;
						if (props.viewType === 'mobile') w = 375; else if (props.viewType === 'tablet') w = 768; else if (props.viewType === 'desktop') w = 1200; else if (props.viewType === 'fhd') w = 1400; else if (props.viewType === '4k') w = 2560;
						if (w < 768) return props.item.colsMobile || 1; if (w < 1200) return props.item.colsTablet || 2; if (w < 1400) return props.item.colsDesktop || 3; if (w < 2560) return props.item.colsFHD || 4; return props.item.cols4K || 6;
					});

					// FETCH DATA
					const fetchData = async (input = 1) => {
						if(!props.item.dataType) return;
						isLoading.value = true; 
						const isCursorMode = props.item.paginationType === 'cursor';
						if (!isCursorMode) { currentPage.value = typeof input === 'number' ? input : 1; }

						const params = {
							type: props.item.dataType,
							category: props.item.selectedCats && props.item.selectedCats.length > 0 ? props.item.selectedCats.join(',') : '',
							status: props.item.selectedStatus || 'publish',
							field: Object.keys(config.value.field).length > 0 ? Object.values(config.value.field).join(',') : '*',
							perPage: props.item.perPage || 12,
							paginationType: props.item.paginationType || 'number'
						};

						if (isCursorMode) { if (typeof input === 'string') { params.cursor = input; } } else { params.page = currentPage.value; }
						
						try {
							const response = await axios.get('/home/listdata', { params });
							if(response.data.success || response.data.status === 'success') {
								fetchedData.value = response.data.data;
								if (isCursorMode) {
									const meta = response.data.meta || response.data; 
									cursorNext.value = meta.next_cursor || response.data.next_cursor || null;
									cursorPrev.value = meta.prev_cursor || response.data.prev_cursor || null;
								} else {
									totalPages.value = response.data.last_page || response.data.total_page || 1;
								}
							} else { fetchedData.value = []; }
						} catch (error) { fetchedData.value = []; } finally {
							isLoading.value = false;
							if(!props.item.usePagination && props.item.enableSplide) { setTimeout(() => { updateDimensions(); currentSlide.value = 0; }, 200); }
						}
					};

					// SLIDER LOGIC
					const trackStyle = Vue.computed(() => {
						if (props.item.splideType === 'fade') return { position: 'relative', width: '100%', height: sliderHeight.value };
						const cols = activeColumns.value; const idx = currentSlide.value; const dragOffset = isDragging.value ? currentTranslate.value : 0; const transition = isDragging.value ? 'none' : 'transform 0.5s ease-in-out'; const itemPct = 100 / cols; const movePct = itemPct * idx;
						if (props.item.splideDirection !== 'ttb') { return { transform: `translateX(calc(-${movePct}% + ${dragOffset}px))`, display: 'flex', flexWrap: 'nowrap', width: '100%', transition, cursor: isDragging.value ? 'grabbing' : 'grab', alignItems: 'flex-start' }; } 
						else { return { transform: `translateY(calc(-${movePct}% + ${dragOffset}px))`, display: 'flex', flexDirection: 'column', height: '100%', transition, cursor: isDragging.value ? 'grabbing' : 'grab' }; }
					});

					const getSlideStyle = (index) => {
						const cols = activeColumns.value; const pad = ([0, 4, 8, 16, 24, 48][props.item.gap] || 16) / 2;
						if (props.item.splideType === 'fade') {
							const itemPage = Math.floor(index / cols); const activePage = Math.floor(currentSlide.value / cols); const isVisible = itemPage === activePage; const colIndex = index % cols; const widthPct = 100 / cols;
							return { position: 'absolute', top: 0, left: `${colIndex * widthPct}%`, width: `${widthPct}%`, height: 'auto', opacity: isVisible ? 1 : 0, zIndex: isVisible ? 2 : 1, transition: 'opacity 0.6s ease-in-out', padding: `0 ${pad}px`, boxSizing: 'border-box', pointerEvents: isVisible ? 'auto' : 'none' };
						}
						const size = 100 / cols + '%';
						if (!props.item.splideDirection || props.item.splideDirection === 'ltr') return { width: size, flexShrink: 0, padding: `0 ${pad}px`, boxSizing: 'border-box' }; else return { width: '100%', height: size, flexShrink: 0, padding: `${pad}px 0`, boxSizing: 'border-box' };
					};

					const paginationDots = Vue.computed(() => {
						const total = fetchedData.value.length; const cols = activeColumns.value; 
						if (props.item.splideType === 'fade') { const tp = Math.ceil(total / cols); return Array.from({length: tp}, (_, i) => i * cols); }
						const move = props.item.splidePerMove || 1; const max = Math.max(0, total - cols); const dots = [];
						for (let i = 0; i <= max; i += move) dots.push(i);
						if (dots.length > 0 && dots[dots.length - 1] < max) dots.push(max); if (dots.length === 0 && total > 0) return [0]; return dots;
					});

					const updateDimensions = () => {
						setTimeout(() => {
							if (!sliderViewport.value) return;
							const userMinH = parseInt(props.item.minHeight) || 0; const gapVal = [0, 4, 8, 16, 24, 48][props.item.gap] || 16; const items = sliderViewport.value.querySelectorAll('.db-card'); const itemsArr = Array.from(items); const cols = activeColumns.value; let maxH = 0;
							if (props.item.splideDirection === 'ttb') { items.forEach(el => maxH = Math.max(maxH, el.offsetHeight)); sliderHeight.value = ((Math.max(maxH, userMinH) * cols) + (gapVal * cols)) + 'px'; return; }
							if (props.item.splideAutoHeight) { const startIndex = currentSlide.value; const totalItems = itemsArr.length; for (let i = 0; i < cols; i++) { const targetIndex = startIndex + i; if (targetIndex < totalItems && itemsArr[targetIndex]) maxH = Math.max(maxH, itemsArr[targetIndex].offsetHeight); } } else { items.forEach(el => maxH = Math.max(maxH, el.offsetHeight)); }
							const finalHeight = Math.max(maxH, userMinH); sliderHeight.value = finalHeight > 0 ? (finalHeight + gapVal) + 'px' : 'auto';
						}, 300);
					};

					const getMaxIndex = () => { if (props.item.splideType === 'fade') return fetchedData.value.length - 1; const cols = activeColumns.value; return Math.max(0, fetchedData.value.length - cols); };
					const nextSlide = () => { const move = props.item.splidePerMove || 1; const max = getMaxIndex(); if (props.item.splideType === 'fade') { const cols = activeColumns.value; let next = currentSlide.value + cols; if (next >= fetchedData.value.length) next = 0; currentSlide.value = next; } else if (props.item.splideType === 'loop') { currentSlide.value = (currentSlide.value >= max) ? 0 : Math.min(max, currentSlide.value + move); } else { currentSlide.value = Math.min(max, currentSlide.value + move); } };
					const prevSlide = () => { const move = props.item.splidePerMove || 1; const max = getMaxIndex(); if (props.item.splideType === 'fade') { const cols = activeColumns.value; let prev = currentSlide.value - cols; if (prev < 0) { const rem = fetchedData.value.length % cols; prev = rem === 0 ? fetchedData.value.length - cols : fetchedData.value.length - rem; } currentSlide.value = prev; } else if (props.item.splideType === 'loop') { currentSlide.value = (currentSlide.value <= 0) ? max : Math.max(0, currentSlide.value - move); } else { currentSlide.value = Math.max(0, currentSlide.value - move); } };
					const goToSlide = (idx) => { currentSlide.value = idx; };
					
					const startAutoplay = () => { stopAutoplay(); if (props.item.enableSplide && props.item.splideAutoplay && props.item.viewMode === 'grid') autoplayTimer = setInterval(nextSlide, props.item.splideInterval || 3000); };
					const stopAutoplay = () => { if (autoplayTimer) clearInterval(autoplayTimer); };
					const onDragStart = (e) => { if (props.item.splideType === 'fade') return; isDragging.value = true; startPos.value = props.item.splideDirection === 'ttb' ? (e.touches ? e.touches[0].clientY : e.clientY) : (e.touches ? e.touches[0].clientX : e.clientX); currentTranslate.value = 0; stopAutoplay(); };
					const onDragMove = (e) => { if (!isDragging.value) return; const currentPos = props.item.splideDirection === 'ttb' ? (e.touches ? e.touches[0].clientY : e.clientY) : (e.touches ? e.touches[0].clientX : e.clientX); currentTranslate.value = currentPos - startPos.value; };
					const onDragEnd = () => { if (!isDragging.value) return; isDragging.value = false; if (currentTranslate.value < -50) nextSlide(); else if (currentTranslate.value > 50) prevSlide(); currentTranslate.value = 0; startAutoplay(); };
					const getColClass = () => { const i = props.item; const v = props.viewType; if (v) { if (v === 'mobile') return 'col-' + (12 / (i.colsMobile || 1)) + ' mb-3'; if (v === 'tablet') return 'col-' + (12 / (i.colsTablet || 2)) + ' mb-3'; if (v === 'desktop') return 'col-' + (12 / (i.colsDesktop || 3)) + ' mb-3'; if (v === 'fhd') return 'col-' + (12 / (i.colsFHD || 4)) + ' mb-3'; if (v === '4k') return 'col-' + (12 / (i.cols4K || 6)) + ' mb-3'; } return 'col-' + (12 / (i.colsMobile || 1)) + ' col-md-' + (12 / (i.colsTablet || 2)) + ' col-xl-' + (12 / (i.colsDesktop || 3)) + ' col-xxl-' + (12 / (i.colsFHD || 4)) + ' col-3xl-' + (12 / (i.cols4K || 6)) + ' mb-3'; };

					// Watchers
					Vue.watch(() => [props.item.dataType, props.item.selectedCats, props.item.selectedStatus, props.item.perPage, props.item.paginationType], () => { fetchData(1); }, { deep: true });
					Vue.watch(() => props.item.viewMode, (newMode) => { if (newMode === 'list') stopAutoplay(); else { if (props.item.enableSplide) startAutoplay(); setTimeout(updateDimensions, 300); } });
					Vue.watch(() => [fetchedData.value.length, props.item.colsDesktop, props.item.splideDirection, props.item.gap, props.item.splideAutoHeight, props.item.minHeight], () => { if(!props.item.usePagination && props.item.enableSplide && props.item.viewMode === 'grid') { updateDimensions(); currentSlide.value = 0; } }, { deep: true });
					Vue.watch(() => [props.item.splideAutoplay, props.item.splideInterval], () => { if(props.item.enableSplide && props.item.splideAutoplay) startAutoplay(); else stopAutoplay(); });
					Vue.watch(currentSlide, () => { 
						stopAutoplay(); plyrInstances.forEach(p => p.pause());
						const cols = activeColumns.value; const start = currentSlide.value;
						if(sliderViewport.value) {
							const slides = sliderViewport.value.querySelectorAll('.media-list-item');
							if(slides[start]) { const videoEl = slides[start].querySelector('.plyr'); if(videoEl) { const player = plyrInstances.find(p => p.elements.container === videoEl); if(player && props.item.splideAutoplay) { player.play(); player.off('ended'); player.once('ended', () => nextSlide()); return; } } }
						}
						startAutoplay();
						if (props.item.enableSplide) updateDimensions();
					});

					Vue.onMounted(() => { fetchData(); window.addEventListener('resize', updateDimensions); if(props.item.enableSplide && props.item.viewMode === 'grid') startAutoplay(); });
					Vue.onBeforeUnmount(() => { stopAutoplay(); window.removeEventListener('resize', updateDimensions); plyrInstances.forEach(p => p.destroy()); });

					return { 
						fetchedData, totalPages, currentPage, isLoading, config, formatDate, truncate, stripHtml, fetchData,
						getColClass, sliderViewport, trackStyle, getSlideStyle, sliderHeight, nextSlide, prevSlide, goToSlide, currentSlide, paginationDots, onDragStart, onDragMove, onDragEnd, getYoutubeId, getListAlignClass, getBorderTextClasses, getBgStyle, getSmartPadding, getSmartButtonPadding, getCardStyle, cursorNext, cursorPrev,
						getPaginationAlign // Return helper function
					};
				},
				template: `
				<div :class="filterClass(item.customClass)">
					<div v-if="isLoading" class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
					<div v-else-if="!fetchedData || fetchedData.length === 0" class="text-center p-4 text-muted border border-dashed"><small>No Data Found</small></div>
					
					<div v-else>
						<div v-if="item.viewMode === 'list'" class="d-flex flex-column" :class="'gap-' + (item.gap !== undefined ? item.gap : 3)">
							<div v-for="(data, idx) in fetchedData" :key="idx" 
								class="d-flex w-100 overflow-hidden position-relative" 
								:class="[
									item.imagePos === 'end' ? 'flex-row-reverse' : 'flex-row', 
									getListAlignClass(), 
									{'border bg-white': item.borderStyle === 'card'}, 
									{'rounded': item.roundedCorners && item.borderStyle === 'card'}, 
									item.borderStyle === 'border-bottom' ? 'pb-' + (item.gap !== undefined ? item.gap : 3) : '',
									{'border-bottom': item.borderStyle === 'border-bottom' && idx < fetchedData.length - 1},
									item.textWrapperClass
								]">
								<div class="flex-shrink-0 position-relative bg-light overflow-hidden" :class="{'rounded': item.roundedCorners}" :style="{ width: item.imgWidthVal + item.imgWidthUnit, height: item.imgHeightVal + item.imgHeightUnit }">
									<img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover;">
									<div v-else :style="getBgStyle(data[config.field.thumb_l], false)"></div>
								</div>
								<div class="flex-grow-1 min-width-0" :class="[item.imagePos === 'end' ? 'me-3 pe-3' : 'ms-3', 'text-' + (item.textAlign || 'start'), getBorderTextClasses(true), getSmartPadding(item.listContentWrapperClass), item.listContentWrapperClass]" :style="{ color: item.textColor }">
									<div class="mb-2" v-if="item.showCategory || item.showDate"><span v-if="item.showCategory && data[config.field.category]" class="badge bg-primary me-2">@{{ data[config.field.category] }}</span><small v-if="item.showDate && data[config.field.date]" class="text-muted"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small></div>
									<h5 class="fw-bold mb-1" :class="[item.titleClass, {'d-block text-truncate': item.truncTitleMode === 'auto'}]"><a :href="config.detailUrl + data[config.field.id]" class="text-decoration-none text-reset">@{{ item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title]) }}</a></h5>
									<p v-if="item.showExcerpt && data[config.field.content]" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p>
									<div v-if="item.showBtn && item.btnPos === 'below'" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div>
									<a v-if="!item.showBtn" :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a>
								</div>
								<div v-if="item.showBtn && item.btnPos === 'side'" class="flex-shrink-0 d-flex align-items-center" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass]" style="position: relative; z-index: 2;"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass"></i><span v-if="item.btnText" :class="{'ms-2': item.btnIconPos === 'start', 'me-2': item.btnIconPos === 'end'}">@{{ item.btnText }}</span><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass"></i></a></div>
							</div>
						</div>

						<div v-else>
							<div v-if="!item.usePagination && item.enableSplide" class="custom-slider position-relative" ref="sliderViewport" :style="{ overflow: 'hidden', width: '100%', height: sliderHeight, transition: 'height 0.3s ease', userSelect: 'none' }" @mousedown="onDragStart" @touchstart="onDragStart" @mousemove="onDragMove" @touchmove="onDragMove" @mouseup="onDragEnd" @touchend="onDragEnd" @mouseleave="onDragEnd">
								<div class="custom-slider-track" :style="trackStyle">
									<div v-for="(data, idx) in fetchedData" :key="idx" :style="getSlideStyle(idx)">
										<div class="media-list-item db-card position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners, 'border-0': item.borderStyle === 'none'}" :style="getCardStyle()">
											<div class="media-wrapper position-relative" style="width:100%;" :style="{ aspectRatio: item.textPos === 'overlay' ? '16/9' : '' }">
												<template v-if="config.data !== 'video'"><img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover; pointer-events:none; display:block;"><div v-else :style="getBgStyle(data[config.field.thumb_l], true)"></div></template>
												<div v-if="config.data === 'video' || data.type === 'video'" class="media-list-video-container" style="height:100%; pointer-events: auto;" :data-index="idx"><div class="plyr-dynamic plyr__video-embed" style="width:100%; height:100%;"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(data[config.field.content]) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div></div>
												<div v-if="item.showCategory && data[config.field.category]" class="position-absolute top-0 start-0 p-2"><span class="badge bg-primary">@{{ data[config.field.category] }}</span></div>
												<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="{'hover-fade': item.hoverAnim}" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor, position: 'absolute', bottom: 0, width: '100%', padding: '15px' }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartPadding(item.btnWrapperClass), item.btnWrapperClass]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
											</div>
											<div v-if="item.textPos === 'below'" :class="[item.textWrapperClass, getBorderTextClasses(false), getSmartPadding(item.textWrapperClass), {'bg-white': item.borderStyle === 'text'}, {'rounded': item.borderStyle === 'text' && item.roundedCorners}, 'd-flex flex-column flex-grow-1']" :style="{ color: item.textColor }"><small v-if="item.showDate" class="text-muted d-block mb-2"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
										</div>
									</div>
								</div>
								<div v-if="item.splideArrows" class="slider-arrows"><button type="button" @click.stop="prevSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', left: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }"><i :class="(item.splideArrowType === 'custom' ? item.splideArrowLeftIcon : 'fas fa-chevron-left') || 'fas fa-chevron-left'"></i></button><button type="button" @click.stop="nextSlide" class="btn btn-light shadow-sm position-absolute d-flex align-items-center justify-content-center p-0" :style="{ width:'30px', height:'30px', borderRadius:'50%', top: item.splideArrowPosY + '%', right: item.splideArrowPosX + 'px', transform: 'translateY(-50%)', zIndex: 10 }"><i :class="(item.splideArrowType === 'custom' ? item.splideArrowRightIcon : 'fas fa-chevron-right') || 'fas fa-chevron-right'"></i></button></div>
								<div v-if="item.splidePagination" class="slider-pagination d-flex justify-content-center gap-2 mt-3 position-absolute w-100" style="bottom: 10px; z-index: 9;"><button v-for="idx in paginationDots" :key="'dot-'+idx" @click.stop="goToSlide(idx)" class="border-0 rounded-circle transition-all" :style="{ width: '10px', height: '10px', backgroundColor: currentSlide === idx ? (item.textColor || '#000') : '#ccc', transform: currentSlide === idx ? 'scale(1.2)' : 'scale(1)', opacity: currentSlide === idx ? 1 : 0.5, cursor: 'pointer' }"></button></div>
							</div>

							<div v-else class="row" :class="'g-' + item.gap">
								<div v-for="(data, idx) in fetchedData" :key="idx" :class="getColClass()">
									<div class="media-list-item position-relative overflow-hidden" :class="{'bg-white border': item.borderStyle === 'card', 'rounded': item.borderStyle === 'card' && item.roundedCorners, 'border-0': item.borderStyle === 'none'}" :style="getCardStyle()">
										<div class="media-wrapper position-relative" style="width:100%;" :style="{ aspectRatio: item.textPos === 'overlay' ? '16/9' : '' }">
											<template v-if="config.data !== 'video'"><img v-if="item.imgMode !== 'background'" :src="data[config.field.thumb_l] || 'https://placehold.co/400x300'" style="width:100%; height:100%; object-fit:cover; pointer-events:none; display:block;"><div v-else :style="getBgStyle(data[config.field.thumb_l], true)"></div></template>
											<div v-if="config.data === 'video' || data.type === 'video'" class="media-list-video-container" style="height:100%; pointer-events: auto;" :data-index="idx"><div class="plyr-dynamic plyr__video-embed" style="width:100%; height:100%;"><iframe :src="'https://www.youtube.com/embed/' + getYoutubeId(data[config.field.content]) + '?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1'" allowfullscreen allowtransparency allow="autoplay"></iframe></div></div>
											<div v-if="item.showCategory && data[config.field.category]" class="position-absolute top-0 start-0 p-2"><span class="badge bg-primary">@{{ data[config.field.category] }}</span></div>
											<div v-if="item.textPos === 'overlay'" class="media-overlay-content" :class="{'hover-fade': item.hoverAnim}" :style="{ backgroundColor: item.overlayColor, color: item.overlayTextColor, position: 'absolute', bottom: 0, width: '100%', padding: '15px' }"><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-0" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartPadding(item.btnWrapperClass), item.btnWrapperClass]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
										</div>
										<div v-if="item.textPos === 'below'" :class="[item.textWrapperClass, getBorderTextClasses(false), getSmartPadding(item.textWrapperClass), {'bg-white': item.borderStyle === 'text'}, {'rounded': item.borderStyle === 'text' && item.roundedCorners}, 'd-flex flex-column flex-grow-1']" :style="{ color: item.textColor }"><small v-if="item.showDate" class="text-muted d-block mb-2"><i class="far fa-calendar me-1"></i> @{{ formatDate(data[config.field.date]) }}</small><h5 class="fw-bold mb-1" :class="[item.titleClass, {'text-truncate': item.truncTitleMode === 'auto'}]" v-text="item.truncTitleMode === 'chars' ? truncate(data[config.field.title], item.truncTitleLimit) : stripHtml(data[config.field.title])"></h5><p v-if="item.showExcerpt" class="mb-2" :class="item.descClass" :style="item.truncDescMode === 'auto' ? { display: '-webkit-box', '-webkit-line-clamp': item.truncDescLines || 3, '-webkit-box-orient': 'vertical', overflow: 'hidden' } : {}" v-text="item.truncDescMode === 'chars' ? truncate(data[config.field.content], item.truncDescLimit) : stripHtml(data[config.field.content])"></p><div v-if="item.showBtn" class="mt-2" :class="[getSmartButtonPadding(item.btnWrapperClass), item.btnWrapperClass, {'mt-auto': item.btnVerticalPos === 'bottom'}]"><a :href="config.detailUrl + data[config.field.id]" :class="item.btnClass" style="position: relative; z-index: 2;"><i v-if="item.btnIconType === 'class' && item.btnIconPos === 'start'" :class="item.btnIconClass + ' me-2'"></i>@{{ item.btnText }}<i v-if="item.btnIconType === 'class' && item.btnIconPos === 'end'" :class="item.btnIconClass + ' ms-2'"></i></a></div><a v-else :href="config.detailUrl + data[config.field.id]" class="stretched-link"></a></div>
									</div>
								</div>
							</div>
						</div>

						<div v-if="item.usePagination" class="mt-4 d-flex w-100" :class="[item.paginationClass, getPaginationAlign()]">
							
							<nav v-if="!item.paginationType || item.paginationType === 'number'" aria-label="Page navigation" v-show="totalPages > 1">
								<ul class="pagination mb-0">
									<li class="page-item" :class="{ disabled: currentPage === 1 }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(currentPage - 1)" aria-label="Previous">
											<span v-if="item.pageIconType === 'image' && item.pagePrevImg"><img :src="item.pagePrevImg" style="height:1em;"></span>
											<i v-else :class="item.pagePrevIcon || 'fas fa-angle-left'"></i>
										</button>
									</li>
									<li v-for="p in totalPages" :key="p" class="page-item" :class="{ active: currentPage === p }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(p)">@{{ p }}</button>
									</li>
									<li class="page-item" :class="{ disabled: currentPage === totalPages }">
										<button class="page-link" :class="item.pageItemClass" @click="fetchData(currentPage + 1)" aria-label="Next">
											<span v-if="item.pageIconType === 'image' && item.pageNextImg"><img :src="item.pageNextImg" style="height:1em;"></span>
											<i v-else :class="item.pageNextIcon || 'fas fa-angle-right'"></i>
										</button>
									</li>
								</ul>
							</nav>

							<div v-else-if="item.paginationType === 'simple' || item.paginationType === 'cursor'" class="d-flex w-100" :class="getPaginationAlign()">
								
								<button class="btn" :class="item.navBtnClass || 'btn-outline-primary'" 
									:disabled="item.paginationType === 'cursor' ? !cursorPrev : currentPage === 1" 
									@click="fetchData(item.paginationType === 'cursor' ? cursorPrev : currentPage - 1)">
									
									<span v-if="item.navShowIcon" class="me-2">
										<img v-if="item.navIconType === 'image' && item.navPrevImg" :src="item.navPrevImg" style="height:1em; vertical-align:middle;">
										<i v-else :class="item.navPrevIcon || 'fas fa-arrow-left'"></i>
									</span>
									Previous
								</button>

								<button class="btn" :class="item.navBtnClass || 'btn-outline-primary'" 
									:disabled="item.paginationType === 'cursor' ? !cursorNext : currentPage === totalPages" 
									@click="fetchData(item.paginationType === 'cursor' ? cursorNext : currentPage + 1)">
									
									Next
									<span v-if="item.navShowIcon" class="ms-2">
										<img v-if="item.navIconType === 'image' && item.navNextImg" :src="item.navNextImg" style="height:1em; vertical-align:middle;">
										<i v-else :class="item.navNextIcon || 'fas fa-arrow-right'"></i>
									</span>
								</button>
							</div>

						</div>
					</div>
				</div>
				`
			};

			const WidgetCollapse = {
				props: ['item', 'filterClass'],
				template: `
					<div :class="['w-100', filterClass(item.customClass)]">
						<button v-if="item.triggerType === 'button'" 
								:class="['btn', item.triggerBtnColor, filterClass(item.customBtnClass)]" 
								type="button" 
								data-bs-toggle="collapse" 
								:data-bs-target="'#' + item.collapseId + '-preview'" 
								:aria-expanded="item.isOpen ? 'true' : 'false'" 
								:aria-controls="item.collapseId + '-preview'">
							@{{ item.triggerText }}
						</button>
						
						<a v-else-if="item.triggerType === 'link'" 
						   :class="['text-decoration-none', item.triggerBtnColor, filterClass(item.customBtnClass)]" 
						   data-bs-toggle="collapse" 
						   :href="'#' + item.collapseId + '-preview'" 
						   role="button" 
						   :aria-expanded="item.isOpen ? 'true' : 'false'" 
						   :aria-controls="item.collapseId + '-preview'"
						   style="cursor: pointer; font-weight: 500;">
							@{{ item.triggerText }}
						</a>

						<div :class="[
								'collapse', 
								item.isOpen ? 'show' : '', 
								item.direction === 'horizontal' ? 'collapse-horizontal' : '',
								item.direction === 'up' ? 'collapse-up' : '',
								'mt-2'
							 ]" 
							 :id="item.collapseId + '-preview'">
							
							<div :class="['card card-body', 'anim-' + item.animation, filterClass(item.customCardClass)]" 
								 :style="item.direction === 'horizontal' ? 'width: 300px;' : ''">
								<div v-html="item.content"></div>
							</div>
						</div>
					</div>
				`
			};

			const WidgetNavTabs = {
			  props: ['item', 'filterClass'],
			  data() { return { openDropdownId: null, ddTop: 0, ddLeft: 0 }; },
			  mounted() {
			    this._clickOutside = e => {
			      if (this.openDropdownId && !this.$el.contains(e.target)) this.openDropdownId = null;
			    };
			    document.addEventListener('click', this._clickOutside, true);
			  },
			  beforeUnmount() {
			    document.removeEventListener('click', this._clickOutside, true);
			  },
			  methods: {
			    getActiveId() {
			      const f = this.item.items.find(t => t.itemType === 'button' && !t.disabled);
			      return this.item.activeTabId || (f ? f.id : '');
			    },
			    toggleDd(tab, e) {
			      if (this.openDropdownId === tab.id) { this.openDropdownId = null; return; }
			      const r = e.currentTarget.getBoundingClientRect();
			      this.ddTop = r.bottom + window.scrollY;
			      this.ddLeft = r.left + window.scrollX;
			      this.openDropdownId = tab.id;
			    }
			  },
			  template: `<div :id="item.customId || ('nav-widget-' + item.id)" :class="filterClass(item.customClass)"><div :class="item.alignment === 'vertical' ? 'd-flex align-items-start gap-3' : ''"><div :style="item.mobileScroll && item.alignment !== 'vertical' ? 'overflow-x: auto; -webkit-overflow-scrolling: touch; max-width: 100%; padding-bottom: 8px;' : ''"><ul class="nav" :class="[item.navStyle === 'tabs' ? 'nav-tabs' : (item.navStyle === 'pills' ? 'nav-pills' : 'nav-underline'), item.alignment === 'vertical' ? 'flex-column' : '', item.fillJustify === 'fill' ? 'nav-fill' : (item.fillJustify === 'justify' ? 'nav-justified' : ''), item.mobileScroll && item.alignment !== 'vertical' ? 'flex-nowrap' : '', filterClass(item.navClass)]" role="tablist" :aria-orientation="item.alignment === 'vertical' ? 'vertical' : 'horizontal'"><li v-for="(tab, idx) in item.items" :key="idx" class="nav-item" :class="[{ 'dropdown': tab.itemType === 'dropdown' }, filterClass(item.navItemClass), filterClass(tab.navItemClass)]" role="presentation"><button v-if="tab.itemType === 'button'" class="nav-link" :class="[{ 'active': tab.id === getActiveId(), 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :id="(tab.customId || tab.id) + '-tab'" data-bs-toggle="tab" :data-bs-target="'#' + (tab.customId || tab.id) + '-pane'" type="button" role="tab" :aria-controls="(tab.customId || tab.id) + '-pane'" :aria-selected="tab.id === getActiveId()" :disabled="tab.disabled"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></button><a v-else-if="tab.itemType === 'link'" class="nav-link" :class="[{ 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :href="tab.url" :target="tab.newTab ? '_blank' : '_self'" :disabled="tab.disabled"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></a><template v-else-if="tab.itemType === 'dropdown'"><a class="nav-link dropdown-toggle" href="#" role="button" :class="[{ 'show': openDropdownId === tab.id, 'disabled': tab.disabled }, filterClass(tab.customClass)]" :style="item.mobileScroll && item.alignment !== 'vertical' ? 'white-space: nowrap;' : ''" :id="tab.dropdownId || ('dropdown-' + tab.id)" @click.prevent="toggleDd(tab, $event)"><i v-if="tab.icon" :class="tab.icon + ' me-2'"></i><span v-text="tab.label"></span></a><Teleport to="body"><ul v-if="openDropdownId === tab.id" class="dropdown-menu show" :class="filterClass(tab.dropdownMenuClass)" :style="{ position: 'absolute', top: ddTop + 'px', left: ddLeft + 'px', zIndex: 9999, minWidth: '10rem' }"><li v-for="(dropItem, dIdx) in tab.dropdownItems" :key="dIdx"><a class="dropdown-item" :class="[{ 'disabled': dropItem.disabled }, filterClass(dropItem.itemClass)]" :href="dropItem.url" :target="dropItem.newTab ? '_blank' : '_self'" v-text="dropItem.label"></a></li></ul></Teleport></template></li></ul></div><div class="tab-content" :class="item.alignment === 'vertical' ? 'flex-grow-1 w-100' : 'mt-3'" v-if="item.items.some(t => t.itemType === 'button')"><template v-for="(tab, idx) in item.items" :key="'pane-'+idx"><div v-if="tab.itemType === 'button'" class="tab-pane fade" :class="[{ 'show active': tab.id === getActiveId() }, filterClass(tab.paneClass)]" :id="(tab.customId || tab.id) + '-pane'" role="tabpanel" :aria-labelledby="(tab.customId || tab.id) + '-tab'" tabindex="0" v-html="tab.body"></div></template></div></div></div>`
			};

			const getWidgetTemplate = (item) => 
			{
				switch (item.type) 
				{
					case 'heading': return WidgetHeading;
					case 'text': return WidgetText;
					case 'image': return WidgetImage;
					case 'video': return WidgetVideo;
					case 'button': return WidgetButton;
					case 'list': return WidgetList;
					case 'card': return WidgetCard;
					case 'media': return WidgetMedia;
					case 'accordion': return WidgetAccordion;
					case 'table': return WidgetTable;
					case 'spacer': return WidgetSpacer;
					case 'divider': return WidgetDivider;
					case 'media_list': return WidgetMediaList;
					case 'dynamic_post_list': return WidgetDynamicPostList;
					case 'collapse': return WidgetCollapse;
					case 'nav_tabs': return WidgetNavTabs;
					default: return WidgetText;
				}
			};

			return { getContainerClass, getRowClasses, getColClasses, filterResponsiveClasses, scaleStyle, getWidgetTemplate };
		}
	};

	// --- UTILITY/APP SETUP ---
	createApp({
		components: { draggable, 'ckeditor-component': CkEditorComponent, 'preview-viewer': PreviewViewer },
		setup() {
			const previewMode = ref(false);
			const previewType = ref('fhd'); 
			const showRightSidebar = ref(false);
			const pageName = ref('');
			const layout = ref([]);
			const customCss = ref('');
			const showCssEditor = ref(false);
			const cssEditorFullscreen = ref(false);
			const baseUrl = APP_BASE_URL;
			const theme = ref('light');
			const activeItem = ref(null);
			const activeType = ref(''); 
			const activeViewMode = ref('fhd');
			const history = ref([]);
			const historyIndex = ref(-1);
			const isTimeTraveling = ref(false); 

			const toggleTheme = () => { theme.value = theme.value === 'light' ? 'dark' : 'light'; document.documentElement.setAttribute('data-bs-theme', theme.value); };
			const toggleRightSidebar = () => { showRightSidebar.value = !showRightSidebar.value; };

			const tools = 
			{
				containers: 
				[
					{ type: 'container', classDesktop: 'container', classTablet: 'container', classMobile: 'container-fluid', classFHD: 'container', class4K: 'container', label: 'Fixed Width', styles: { bgColor: '#ffffff', minHeight: 'auto', bgImage: '', bgPos: 'center', bgRepeat: 'no-repeat', bgSize: 'cover' } }, 
					{ type: 'container', classDesktop: 'container-fluid', classTablet: 'container-fluid', classMobile: 'container-fluid', classFHD: 'container-fluid', class4K: 'container-fluid', label: 'Full Width', styles: { bgColor: '#ffffff', minHeight: 'auto' } }
				],
				rows: 
				[
					{ type: 'row', label: '1 Column', cols: ['col-xl-12'] }, 
					{ type: 'row', label: '2 Columns', cols: ['col-xl-6', 'col-xl-6'] }, 
					{ type: 'row', label: '3 Columns', cols: ['col-xl-4', 'col-xl-4', 'col-xl-4'] },
					{ type: 'row', label: '4 Columns', cols: ['col-xl-3', 'col-xl-3', 'col-xl-3', 'col-xl-3'] },
					{ type: 'row', label: '5 Columns', cols: ['col-xl', 'col-xl', 'col-xl', 'col-xl', 'col-xl'] },
					{ type: 'row', label: '6 Columns', cols: ['col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2', 'col-xl-2'] }
				],
				widgets: 
				[			
					{ type: 'text', label: 'Text', icon: 'fas fa-paragraph', content: '<p>Lorem ipsum dolor sit amet...</p>' },
					{ type: 'button', label: 'Button', icon: 'fas fa-square', text: 'Click Me', href: '#', newTab: false, customClass: '', iconType: 'none', iconClass: 'fas fa-arrow-right', iconSrc: '', iconPos: 'start' },
					{ type: 'image', label: 'Image', icon: 'far fa-image', src: 'https://placehold.co/200x200', widthVal: '100', widthUnit: '%', heightVal: '', heightUnit: 'auto' },
					{ type: 'video', label: 'Video', icon: 'fas fa-film', videoType: 'youtube', videoSrc: '', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', autoplay: false, controls: true, loop: false, muted: false, width: '100', aspectRatio: '16/9', customClass: '' },
					{ type: 'card', label: 'Card', icon: 'far fa-id-card', cardTitle: 'Card Title', titleClass: '', truncTitleMode: 'off', truncTitleLimit: 30, cardText: 'Some quick example text to build on the card title and make up the bulk of the card\'s content.', textClass: '', truncTextMode: 'off', truncTextLimit: 100, truncTextLines: 3, src: 'https://placehold.co/200x200', imgClass: '', btnText: 'Go somewhere', btnLink: '#', newTab: false, cardStyleClass: '', imgWidthVal: '100', imgWidthUnit: '%', imgHeightVal: '', imgHeightUnit: 'auto', btnClass: '', btnIconType: 'none', btnIconClass: 'fas fa-arrow-right', btnIconSrc: '', btnIconPos: 'start' },
					{ type: 'media', label: 'Media Object', icon: 'fas fa-photo-video', src: 'https://placehold.co/120x120', imgClass: '', heading: 'Media Heading', headingClass: '', truncHeadingMode: 'off', truncHeadingLimit: 30, content: 'Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.', contentClass: '', truncContentMode: 'off', truncContentLimit: 100, truncContentLines: 3, imagePos: 'start', align: 'start', imgWidthVal: '64', imgWidthUnit: 'px', imgHeightVal: '', imgHeightUnit: 'auto', mediaBtnText: '', mediaBtnLink: '', mediaBtnClass: 'btn btn-primary btn-sm', mediaBtnPos: 'below', mediaBtnNewTab: false },		
					{
						type: 'list',
						label: 'List Group',
						icon: 'fas fa-list',
						listType: 'ul',
						styleType: 'icon',
						commonIcon: 'fas fa-check text-success',
						commonImage: '',
						flush: false,
						noBorder: false, // <-- Opsi Baru: Full No Border
						numbered: false,
						customClass: '',
						listSize: 'default', // <-- Opsi Baru: default, h6, h5, h4, h3, h2, h1, custom
					    customSizeValue: '16', // <-- Opsi Baru: Nilai ukuran custom
					    customSizeUnit: 'px', // <-- Opsi Baru: Satuan ukuran custom
						items: [
							{ 
								text: 'List item 1', 
								active: true, 
								disabled: false,
								badge: false, 
								badgeText: 'New', 
								badgeType: 'bg-primary',
								customBadgeCss: '', // <-- Opsi Baru: Input Custom Badge
								inputType: 'none',
								showMarker: true, // <-- Opsi Baru: Tampilkan list marker saat input
								markerPosition: 'after', // <-- Opsi Baru: 'before' atau 'after'
								inputChecked: false,
								customCss: ''
							},
							{ 
								text: 'List item 2', 
								active: false, 
								disabled: false,
								badge: true, 
								badgeText: '14', 
								badgeType: 'bg-danger',
								customBadgeCss: '', // <-- Opsi Baru: Input Custom Badge
								inputType: 'none',
								showMarker: true, // <-- Opsi Baru: Tampilkan list marker saat input
								markerPosition: 'after', // <-- Opsi Baru: 'before' atau 'after'
								inputChecked: false,
								customCss: '' 
							}
						]
					},
					{ type: 'accordion', label: 'Accordion', icon: 'fas fa-list', items: [{ header: 'Accordion Item #1', content: 'Body content 1' }, { header: 'Accordion Item #2', content: 'Body content 2' }] },
					{ type: 'table', label: 'Table', icon: 'fas fa-table', tableData: { headers: ['Head 1', 'Head 2'], rows: [['Data 1', 'Data 2']] } },
					{ type: 'spacer', label: 'Spacer', icon: 'fas fa-arrows-alt-v', height: 50, unit: 'px', customClass: '' },
					{ type: 'divider', label: 'Divider', icon: 'fas fa-minus', style: 'solid', width: 100, widthUnit: '%', align: 'center', color: '#000000', thickness: 1, gap: 15, customClass: '' },
					{ type: 'heading', label: 'Heading', icon: 'fas fa-heading', text: 'Add Your Heading Text Here', link: '', htmlTag: 'h2', alignment: 'left', color: '', fontSize: '', fontSizeUnit: 'px', fontWeight: '', customClass: '' },
					{ type: 'media_list', label: 'Media List', icon: 'fas fa-photo-video', viewMode: 'grid', colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6, borderStyle: 'card', roundedCorners: true, textPos: 'below', textWrapperClass: '', titleClass: '', descClass: '', hoverAnim: false, minHeight: 0, truncTitleMode: 'off', truncTitleLimit: 30, truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3, overlayColor: 'rgba(0, 0, 0, 0.6)', textColor: '#000000', overlayTextColor: '#ffffff', gap: 3, enableSplide: false, splideAutoplay: true, splideInterval: 3000, splideAutoHeight: true, splidePerMove: 1, splideType: 'loop', splideDirection: 'ltr', splideArrows: true, splidePagination: true, splideArrowType: 'default', splideArrowLeftIcon: 'fas fa-chevron-left', splideArrowRightIcon: 'fas fa-chevron-right', splideArrowPosY: 50, splideArrowPosX: 0, items: [ { type: 'image', imgMode: 'default', imgClass: '', bgHeight: 200, bgSize: 'cover', bgRepeat: 'no-repeat', bgPos: 'center', src: 'https://placehold.co/600x400', aspectRatio: '16/9', title: 'Image Title 1', desc: 'Description here...', videoType: 'youtube', youtubeUrl: '', videoSrc: '', videoClass: '' }, { type: 'video', aspectRatio: '16/9', videoType: 'youtube', youtubeUrl: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', src: '', title: 'Video Title 2', desc: 'Video description...', videoSrc: '', videoClass: '' } ], customClass: '' },
					{
						label: 'Collapse', 
						icon: 'fas fa-layer-group',
						category: 'basic',
						type: 'collapse', 
						collapseId: 'collapse-' + Math.random().toString(36).substr(2, 9),
						triggerType: 'button',
						triggerText: 'Klik untuk Buka',
						triggerBtnColor: 'btn-primary',
						customBtnClass: '', 
						content: '<p>Tulis konten collapse Anda di sini...</p>',
						isOpen: false,
						direction: 'down',
						animation: 'fadeIn',
						customClass: '',
						customCardClass: '' 
					},
					{
						type: 'nav_tabs',
						label: 'Nav Tabs',
						icon: 'fas fa-folder-open',
						navStyle: 'tabs',
						alignment: 'horizontal',
						fillJustify: 'none',
						mobileScroll: false,
						activeTabId: '',
						customId: '',
						customClass: '',
						navClass: '',
						navItemClass: '',
						items: [
							{
								id: 'tab-' + Math.random().toString(36).substr(2, 9),
								customId: '',
								customClass: '',
								navItemClass: '',
								paneClass: '',
								label: 'Home',
								icon: 'fas fa-home',
								itemType: 'button', // Opsi: button (buka konten), link (buka url), dropdown
								url: '#',
								newTab: false,
								disabled: false,
								body: '<p>Ini adalah konten untuk tab Home. Anda bisa mengeditnya di panel setting.</p>',
								dropdownId: '',
								dropdownMenuClass: '',
								dropdownItems: []
							},
							{
								id: 'tab-' + Math.random().toString(36).substr(2, 9),
								customId: '',
								customClass: '',
								navItemClass: '',
								paneClass: '',
								label: 'Profile',
								icon: 'fas fa-user',
								itemType: 'button',
								url: '#',
								newTab: false,
								disabled: false,
								body: '<p>Ini adalah konten untuk tab Profile.</p>',
								dropdownId: '',
								dropdownMenuClass: '',
								dropdownItems: []
							},
							{
								id: 'tab-' + Math.random().toString(36).substr(2, 9),
								customId: '',
								customClass: '',
								navItemClass: '',
								paneClass: '',
								label: 'Options',
								icon: 'fas fa-cog',
								itemType: 'dropdown',
								url: '#',
								newTab: false,
								disabled: false,
								body: '',
								dropdownId: '',
								dropdownMenuClass: '',
								dropdownItems: [
									{ label: 'Action 1', url: '#', newTab: false, disabled: false, itemClass: '' },
									{ label: 'Action 2', url: '#', newTab: false, disabled: false, itemClass: '' }
								]
							}
						]
					},
				],
				advanced: 
				[
					{ 
						type: 'dynamic_post_list', 
						label: 'Dynamic Post List', 
						icon: 'fas fa-newspaper',
						
						// Data Config
						dataType: 'article',
						selectedCats: [],
						selectedStatus: 'publish',
						
						// View Config
						viewMode: 'grid',
						gridCols: 3, 
						colsMobile: 1, colsTablet: 2, colsDesktop: 3, colsFHD: 4, cols4K: 6,
						
						// 1. IMAGE SETTINGS
						imgMode: 'standard', // 'standard' or 'background'
						bgHeight: 200, // Default height for bg mode
						bgSize: 'cover', // cover, contain, custom
						bgSizeVal: 100,
						bgSizeUnit: '%',
						bgPos: 'center',
						bgRepeat: 'no-repeat',

						imgWidthVal: 250, // Default request 250px
						imgWidthUnit: 'px',
						imgHeightVal: 200,
						imgHeightUnit: 'px',

						// 2. BUTTON SETTINGS
						showBtn: false, // Default false (pakai stretched link)
						btnText: 'Read More',
						btnWrapperClass: '',
						btnClass: 'btn btn-primary',
						btnPos: 'below', // 'below' or 'side'
						btnVerticalPos: 'auto',
						btnIconType: 'none',
						btnIconClass: 'fas fa-arrow-right',
						btnIconSrc: '',
						btnIconPos: 'end', // 'start' or 'end'

						// --- TAMBAHAN WARNA (DEFAULT HITAM) ---
						textColor: '#000000', // Default Text Color
						overlayColor: 'rgba(0, 0, 0, 0.4)',
						overlayTextColor: '#ffffff',
						// -------------------------------------

						// Toggles
						showDate: true,
						showCategory: true,
						showExcerpt: true,
						
						// Pagination
						paginationType: 'number',
						usePagination: true,
						perPage: 6,

						// NEW: Pagination Style Settings
						paginationClass: 'mt-4', // Class container

						// Settings for Number Pagination
						pageItemClass: '', // Class for li/a
						pageIconType: 'class', // class/image
						pagePrevIcon: 'fas fa-angle-left',
						pagePrevImg: '',
						pageNextIcon: 'fas fa-angle-right',
						pageNextImg: '',

						// Settings for Simple/Cursor Pagination
						navBtnClass: 'btn-outline-primary',
						navAlign: 'center', // center, between
						navShowIcon: true,
						navIconType: 'class',
						navPrevIcon: 'fas fa-arrow-left',
						navPrevImg: '',
						navNextIcon: 'fas fa-arrow-right',
						navNextImg: '',
						
						// Slider Config
						enableSplide: false,
						splideAutoplay: true,
						splideInterval: 3000,
						splidePerMove: 1,
						splideType: 'loop',
						splideDirection: 'ltr',
						splideArrows: true,
						splidePagination: true,
						splideArrowType: 'default',
						splideArrowLeftIcon: 'fas fa-chevron-left',
						splideArrowRightIcon: 'fas fa-chevron-right',
						splideArrowPosY: 50,
						splideArrowPosX: 0,
						
						// Styles
						textPos: 'below', // Default Text Position
						imagePos: 'start',
						textAlign: 'start',
						verticalAlign: 'center',
						gap: 3,
						roundedCorners: true,
						borderStyle: 'card',
						customClass: '',
						listContentWrapperClass: '',
						
						// Truncate Defaults
						truncTitleMode: 'off', truncTitleLimit: 30,
						truncDescMode: 'off', truncDescLimit: 100, truncDescLines: 3,
						
						// Style Options
						minHeight: 0
					}
				]
			};

			const setActive = (item, type) => { if (previewMode.value) return; activeItem.value = item; activeType.value = type; showRightSidebar.value = true; }; 
			const deselectAll = () => { /* Optional */ };

			const saveState = () => { if (isTimeTraveling.value) return; if (historyIndex.value < history.value.length - 1) history.value = history.value.slice(0, historyIndex.value + 1); history.value.push(JSON.stringify(layout.value)); historyIndex.value++; };
			let debounceTimer;
			watch(layout, () => { if (!isTimeTraveling.value) { clearTimeout(debounceTimer); debounceTimer = setTimeout(saveState, 500); } }, { deep: true });
			
			const undo = () => { if (historyIndex.value > 0) { isTimeTraveling.value = true; historyIndex.value--; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };
			const redo = () => { if (historyIndex.value < history.value.length - 1) { isTimeTraveling.value = true; historyIndex.value++; layout.value = JSON.parse(history.value[historyIndex.value]); nextTick(() => isTimeTraveling.value = false); } };

			const regenerateIds = (obj) => { obj.id = Math.random().toString(36).substr(2, 9); if(obj.children) obj.children.forEach(regenerateIds); };

			// --- GANTI FUNCTION CLONEITEM LAMA DENGAN INI ---
			const cloneItem = (origin) => 
			{
				const item = JSON.parse(JSON.stringify(origin));
				regenerateIds(item);
				
				// Logic Container (Tidak berubah)
				if (item.type === 'container') 
				{ 
					item.children = []; 
					if ( ! item.classMobile) item.classMobile = 'container-fluid';
					if ( ! item.classTablet) item.classTablet = 'container-fluid';
					if ( ! item.classDesktop) item.classDesktop = 'container';
					if ( ! item.classFHD) item.classFHD = 'container';
					if ( ! item.class4K) item.class4K = 'container';
				}

				// Logic Row (INI BAGIAN YANG DI-UPDATE)
				if (item.type === 'row') 
				{ 
					// AUTO RESPONSIVE LOGIC: Membaca angka kolom desktop (misal col-xl-6)
					// dan menerapkannya ke breakpoint lain.
					item.children = item.cols.map(w => 
					{
						let colNum = '';
						
						// Ekstrak angka dari class (misal ambil '6' dari 'col-xl-6')
						if (w.includes('-xl-')) 
						{
							colNum = w.split('-xl-')[1];
						} 
						else if (w.includes('-lg-')) 
						{
							colNum = w.split('-lg-')[1];
						} 
						else if (w.includes('-md-')) 
						{
							colNum = w.split('-md-')[1];
						} 
						else if (w.includes('col-') && w !== 'col') 
						{
							colNum = w.split('col-')[1];
						}

						return { 
							id: Math.random().toString(36).substr(2, 9), 
							type: 'col', 
							// Mobile default 12 (full) agar tidak gepeng
							widthMobile: 'col-12', 
							// Tablet mengikuti desktop tapi pakai prefix md
							widthTablet: colNum ? 'col-md-' + colNum : 'col-md', 
							// Desktop sesuai sidebar
							widthDesktop: w, 
							// FHD mengikuti desktop tapi pakai prefix xxl
							widthFHD: colNum ? 'col-xxl-' + colNum : 'col-xxl', 
							// 4K mengikuti desktop tapi pakai prefix 3xl
							width4K: colNum ? 'col-3xl-' + colNum : 'col-3xl', 
							children: [], 
							customClass: '' 
						};
					}); 
					
					// Reset gutter dan width row container
					item.gutter = ''; item.gutterTablet = ''; item.gutterDesktop = ''; item.gutterFHD = ''; item.gutter4K = ''; 
					item.widthMobile = 'w-100'; item.widthTablet = 'w-md-100'; item.widthDesktop = 'w-xl-100'; item.widthFHD = 'w-xxl-100'; item.width4K = 'w-3xl-100';
				}

				if (item.type === 'image' && !item.widthUnit) { item.widthVal = '100'; item.widthUnit = '%'; item.heightVal = ''; item.heightUnit = 'auto'; }
				return item;
			};

			const duplicateItem = (arr, index) => { const clone = JSON.parse(JSON.stringify(arr[index])); regenerateIds(clone); arr.splice(index + 1, 0, clone); };
			const removeItem = (arr, i) => { if (activeItem.value && arr[i] && arr[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } arr.splice(i, 1); };
			const removeColumn = (row, i) => { if (activeItem.value && row.children[i].id === activeItem.value.id) { activeItem.value = null; activeType.value = ''; } row.children.splice(i, 1); };
			const addColumn = (row) => row.children.push({ id: Math.random().toString(36).substr(2, 9), type:'col', widthMobile:'col-12', widthTablet:'col-md', widthDesktop:'col-xl', widthFHD:'col-xxl', width4K:'col-3xl', children:[], customClass:'' });

			// --- GANTI FUNCTION onDropToContainer DENGAN VERSI FINAL INI ---            
			const onDropToContainer = (evt, targetCont) => {
				const addedIndex = evt.newIndex;
				const rawItem = targetCont.children[addedIndex];

				if ( ! rawItem) return; 

				// SKENARIO 1: WIDGET (Text, Image, Button, dll) -> Bungkus Row & Col
				if (rawItem.type !== 'row') {
					
					// 1. DEEP COPY (PENTING! Agar data widget tidak hilang/corrupt)
					const savedWidget = JSON.parse(JSON.stringify(rawItem));
					savedWidget.id = Math.random().toString(36).substr(2, 9);

					// 2. Buat Struktur Wrapper Lengkap
					const newRow = {
						id: Math.random().toString(36).substr(2, 9),
						type: 'row',
						label: 'Wrapper Row',
						widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
						gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
						customClass: '',
						children: [
							{
								id: Math.random().toString(36).substr(2, 9),
								type: 'col',
								widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12',
								customClass: '',
								children: [ savedWidget ] // Masukkan widget clone ke sini
							}
						] 
					};

					// 3. Replace item mentah dengan Wrapper
					targetCont.children.splice(addedIndex, 1, newRow);
				} 
				
				// SKENARIO 2: NESTED ROW -> Normalisasi Row
				else {
					rawItem.widthMobile = 'w-100';
					rawItem.widthTablet = 'w-md-100';
					rawItem.widthDesktop = 'w-xl-100';
					rawItem.widthFHD = 'w-xxl-100';
					rawItem.width4K = 'w-3xl-100';
					rawItem.gutter = ''; rawItem.gutterTablet = ''; rawItem.gutterDesktop = ''; 
					rawItem.gutterFHD = ''; rawItem.gutter4K = '';
					regenerateIds(rawItem);
				}
			};

			// 2. UPDATE FUNCTION: Handle Drop ke ROW (Bisa dari Sidebar / dari Nested Row)
			const onDropToRow = (evt, targetRow) => { 
				const addedIndex = evt.newIndex; 
				const addedItem = targetRow.children[addedIndex]; 

				// KASUS A: Yang didrop adalah Nested Row (Un-nesting)
				// Logika: Row dibuang, Column-nya diambil jadi anak Row ini
				if (addedItem.type === 'row') { 
					targetRow.children.splice(addedIndex, 1); // Hapus Row pembungkus
					const newCols = addedItem.children; // Ambil isinya (cols)
					newCols.forEach(col => regenerateIds(col)); // ID Baru
					targetRow.children.splice(addedIndex, 0, ...newCols); // Masukkan cols
				} 
				
				// KASUS B: Yang didrop adalah WIDGET (Text, Image, Button, dll)
				// Logika: Widget tidak boleh telanjang di Row. Bungkus dengan Column.
				else if (addedItem.type !== 'col') {
					
					// Buat Column Pembungkus Baru
					const wrapperCol = {
						id: Math.random().toString(36).substr(2, 9),
						type: 'col',
						// Set width default agar widget terlihat (misal col-12 atau col-md)
						widthMobile: 'col-12', 
						widthTablet: 'col-md', 
						widthDesktop: 'col-xl', 
						widthFHD: 'col-xxl', 
						width4K: 'col-3xl',
						children: [ addedItem ], // Masukkan Widget ke dalam Column ini
						customClass: ''
					};

					// Ganti item widget mentah dengan Column Wrapper
					targetRow.children.splice(addedIndex, 1, wrapperCol);
				}
				
				// KASUS C: Yang didrop adalah Column (Move dari row lain) -> Biarkan saja
			};

			// --- UPDATE FUNCTION INI DI BAGIAN JAVASCRIPT SETUP() ---
			const onDropToColumn = (evt, targetCol) => 
			{
				const addedIndex = evt.newIndex;
				const addedItem = targetCol.children[addedIndex];

				// Jika item yang masuk adalah ROW (dari sidebar atau pindahan row lain)
				// Kita harus ubah dia menjadi NESTED ROW (Full Width)
				if (addedItem && addedItem.type === 'row')
				{
					// Force width menjadi 100% agar pas di dalam column
					addedItem.widthMobile = 'w-100';
					addedItem.widthTablet = 'w-md-100';
					addedItem.widthDesktop = 'w-xl-100';
					addedItem.widthFHD = 'w-xxl-100';
					addedItem.width4K = 'w-3xl-100';
					
					// Reset Gutter
					addedItem.gutter = ''; 
					addedItem.gutterTablet = ''; 
					addedItem.gutterDesktop = '';
					addedItem.gutterFHD = ''; 
					addedItem.gutter4K = '';

					// Jika ini row baru (belum punya ID), generate ID
					if ( ! addedItem.id) regenerateIds(addedItem);
				}
				
				// Jika yang masuk adalah WIDGET biasa, biarkan saja. 
				// VueDraggable akan menanganinya secara otomatis.
			};
			
			const togglePreview = () => { previewMode.value = !previewMode.value; if(previewMode.value) { activeItem.value = null; activeType.value = ''; previewType.value = 'fhd'; } };
			const openCkFinder = (targetObj, propName) => { if (typeof CKFinder !== 'undefined') CKFinder.popup({ chooseFiles: true, onInit: (finder) => finder.on('files:choose', (evt) => targetObj[propName] = evt.data.files.first().getUrl()) }); else alert("CKFinder not configured"); };

			const saveJson = () => 
			{
				const csrfToken = document.querySelector('meta[name="csrf-token"]');
				if (csrfToken) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.content;
				axios.post('/pagebuilder/store', 
				{ 
					pageName: pageName.value,
					layout: JSON.stringify(layout.value),
					customCss: customCss.value
				}).then(r => { alert("Saved!"); console.log(pageName.value); }).catch(e => { alert("Error saving"); console.log(pageName.value); });
			};

			// Helper: Tab key support di CSS textarea
			const handleCssTab = (e) => 
			{
				const textarea = e.target;
				const start = textarea.selectionStart;
				const end = textarea.selectionEnd;
				const spaces = '  '; // 2 spasi sebagai tab
				customCss.value = customCss.value.substring(0, start) + spaces + customCss.value.substring(end);
				nextTick(() => 
				{
					textarea.selectionStart = textarea.selectionEnd = start + spaces.length;
				});
			};
			
			const addListItem = (w) => w.items.push({ text: 'New Item' }); 
			const removeListItem = (w, i) => w.items.splice(i, 1);
			const addAccordionItem = (w) => w.items.push({ header: 'New', content: 'Content' }); 
			const removeAccordionItem = (w, i) => w.items.splice(i, 1);
			const addTableRow = (w) => w.tableData.rows.push(new Array(w.tableData.headers.length).fill('d')); 
			const addTableCol = (w) => { w.tableData.headers.push('H'); w.tableData.rows.forEach(r => r.push('d')); }; 
			const removeTableCol = (w, idx) => { if(w.tableData.headers.length <=1) return; w.tableData.headers.splice(idx, 1); w.tableData.rows.forEach(r => r.splice(idx, 1)); }; 
			const removeTableRow = (w, idx) => { w.tableData.rows.splice(idx, 1); };

			// 1. Definisikan Fungsi addNestedRow (Letakkan di dekat fungsi addColumn)
			const addNestedRow = (targetCol) => 
			{
				// Membuat object Row baru
				const newRow = {
					id: Math.random().toString(36).substr(2, 9),
					type: 'row',
					cols: [], // tidak dipakai utk render tapi utk struktur awal
					// Default properties untuk Row (sama seperti cloneItem)
					gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
					widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
					customClass: '',
					children: [
						// Langsung isi dengan 1 kolom agar terlihat
						{
							id: Math.random().toString(36).substr(2, 9),
							type: 'col',
							widthMobile: 'col-12', widthTablet: 'col-md-12', widthDesktop: 'col-xl-12', widthFHD: 'col-xxl-12', width4K: 'col-3xl-12',
							children: [],
							customClass: ''
						}
					]
				};
				// Masukkan Row baru ke dalam children dari Column yang diklik
				targetCol.children.push(newRow);
			};

			const containerGroup = {
				name: 'section',
				put: (to, from, dragEl) => {
					// 1. BLOKIR WIDGET (Sidebar & Canvas)
					if (from.options.group.name === 'widget') return false;
					if (dragEl.classList.contains('widget-ui')) return false;
					
					// 2. BLOKIR CONTAINER (BARU)
					// Container berasal dari group 'root'. Kita tolak agar tidak masuk ke dalam Container lain (Nesting Container).
					if (from.options.group.name === 'root') return false;
					if (dragEl.classList.contains('container-ui')) return false;

					// Selain itu (Row Sidebar atau Nested Row) BOLEH MASUK
					return true;
				}
			};

			const rowGroup = {
				name: 'row-cols',
				put: (to, from, dragEl) => {
					// 1. BLOKIR WIDGET (Sidebar & Canvas)
					if (from.options.group.name === 'widget') return false;
					if (dragEl.classList.contains('widget-ui')) return false;

					// 2. BLOKIR CONTAINER (BARU - SOLUSI MASALAH ANDA)
					// Mencegah Container masuk ke Row dan dianggap sebagai Column
					if (from.options.group.name === 'root') return false;
					if (dragEl.classList.contains('container-ui')) return false;

					// Selain itu (Row, Nested Row, atau Column Reorder) BOLEH MASUK
					return true;
				}
			};

			// --- TAMBAHKAN LOGIKA DETEKSI DEVICE INI DI DALAM SETUP() ---
			
			// Default false, nanti diupdate saat mounted
			const isMobileDevice = ref(false);

			const checkDeviceWidth = () => {
				// Batasnya 768px (Ukuran Tablet Portrait / HP Besar).
				// Jika lebar window kurang dari 768px, maka dianggap Mobile (True)
				isMobileDevice.value = window.innerWidth < 768;
			};

			const storedSidebar = localStorage.getItem('pb_show_left_sidebar');
			const showLeftSidebar = ref(storedSidebar === 'false' ? false : true); 
			const toggleLeftSidebar = () => { 
				showLeftSidebar.value = !showLeftSidebar.value; 
				localStorage.setItem('pb_show_left_sidebar', showLeftSidebar.value);
			};


			// const showLeftSidebar = ref(true); // State untuk Sidebar Kiri
			// const toggleLeftSidebar = () => { showLeftSidebar.value = !showLeftSidebar.value; };
			// --- LOGIC DRAGGABLE POPUP ---

			// State Posisi Popup (Default di kanan atas)
			const popupPos = ref({ top: 85, left: window.innerWidth - 447 }); 

			// Variabel temp untuk dragging
			let isDragging = false;
			let dragOffset = { x: 0, y: 0 };

			// 1. Mulai Drag (MouseDown pada Header)
			const startDrag = (e) => 
			{
				isDragging = true;
				// Hitung jarak cursor dari pojok kiri-atas popup agar tidak 'lompat'
				dragOffset.x = e.clientX - popupPos.value.left;
				dragOffset.y = e.clientY - popupPos.value.top;
				
				// Attach event listener ke window agar drag mulus meski cursor keluar elemen
				window.addEventListener('mousemove', onDrag);
				window.addEventListener('mouseup', stopDrag);
			};

			// 2. Proses Drag (MouseMove)
			const onDrag = (e) => {
				if ( ! isDragging) return;
				
				// Update posisi berdasarkan pergerakan mouse
				popupPos.value.left = e.clientX - dragOffset.x;
				popupPos.value.top = e.clientY - dragOffset.y;
			};

			// 3. Stop Drag (MouseUp)
			const stopDrag = () => {
				isDragging = false;
				window.removeEventListener('mousemove', onDrag);
				window.removeEventListener('mouseup', stopDrag);
			};

			// --- LOGIC QUICK ADD & WIDGET PICKER ---

			const showWidgetModal = ref(false);
			const targetWidgetList = ref(null); // Menyimpan referensi array tujuan (children)

			// 1. Fungsi Buka Modal Pemilih Widget
			const openWidgetPicker = (targetArray) => {
				targetWidgetList.value = targetArray; // Simpan referensi array column tempat widget akan ditambah
				showWidgetModal.value = true;
			};

			// 2. Fungsi Eksekusi Tambah Widget dari Modal
			// FIX: ADD WIDGET (DEEP CLONE)
			const addWidgetFromPicker = (type) => {
				if ( ! targetWidgetList.value) return;
				
				// PERBAIKAN: Cari di semua list (Widgets + Advanced)
				const allWidgets = [...tools.widgets, ...tools.advanced];

				// Cari template default widget dari tools
				// const defaultWidget = tools.widgets.find(w => w.type === type);
				const defaultWidget = allWidgets.find(w => w.type === type);

				if (defaultWidget) {
					// PENTING: Gunakan JSON parse/stringify untuk Deep Clone
					// Ini memastikan semua properti dalam (styles, settings) ikut ter-copy
					const newWidget = JSON.parse(JSON.stringify(defaultWidget));
					
					newWidget.id = Date.now(); // Generate ID unik baru
					targetWidgetList.value.push(newWidget);
				}
				
				showWidgetModal.value = false;
				targetWidgetList.value = null;
			};

			const quickAddContainer = () => {
				layout.value.push({
					id: Date.now(),
					type: 'container',
					fluid: false,
					classMobile: 'container-fluid', 
					classTablet: 'container', 
					classDesktop: 'container', 
					classFHD: 'container', 
					class4K: 'container',
					styles: 
					{
						bgColor: '',
						bgImage: '',
						bgSize: 'cover',
						bgPos: 'center',
						paddingTop: '3rem',    
						paddingBottom: '3rem',
						marginTop: '0',
						marginBottom: '0',
						minHeight: 'auto'
					},
					customClass: '',
					children: []
				});
				
				// Auto scroll ke bawah
				setTimeout(() => 
				{
					const canvas = document.getElementById('canvasArea');
					if (canvas) window.scrollTo(0, document.body.scrollHeight);

				}, 100);
			};

			const quickAddRow = (container) => 
			{
				if ( ! container.children || ! Array.isArray(container.children)) 
				{
					container.children = [];
				}

				const newRow = 
				{
					id: 'row-' + Date.now(),
					type: 'row',
					gutter: '', gutterTablet: '', gutterDesktop: '', gutterFHD: '', gutter4K: '',
					widthMobile: 'w-100', widthTablet: 'w-md-100', widthDesktop: 'w-xl-100', widthFHD: 'w-xxl-100', width4K: 'w-3xl-100',
					customClass: '',
					children: [{ 
						id: 'col-' + Date.now() + 1, 
						type: 'column',
						span: 12, 
						widthMobile: 'col-12', 
						widthTablet: 'col-md-12', 
						widthDesktop: 'col-xl-12', 
						widthFHD: 'col-xxl-12', 
						width4K: 'col-3xl-12',
						customClass: '',
						children: [] 
					}]
				};

				container.children = [...container.children, newRow];
			};

			// Masukkan ke return object setup()
			const getCurrentDataType = (type) => DATA_TYPES.find(d => d.data === type);

			// Pasang pendengar event saat aplikasi dimuat
			onMounted(() => {
				checkDeviceWidth(); // Cek saat pertama load
				window.addEventListener('resize', checkDeviceWidth); // Cek saat layar di-resize
				
				// Load data dari database jika mode UPDATE
				if (PAGE_MODE === 'update' && PAGE_DATA !== null) 
				{
					try 
					{
						// Load custom CSS
						pageName.value = PAGE_DATA.page_name || '';
					} 
					catch(e) 
					{
						pageName.value = '';
					}
					
					try 
					{
						// Load layout — bisa berupa string JSON atau sudah array
						const rawLayout = PAGE_DATA.layout;
						layout.value = typeof rawLayout === 'string'
							? (rawLayout.trim() !== '' ? JSON.parse(rawLayout) : [])
							: (Array.isArray(rawLayout) ? rawLayout : []);
					} 
					catch(e) 
					{
						console.warn('Gagal parse layout JSON:', e);
						layout.value = [];
					}

					try 
					{
						// Load custom CSS
						customCss.value = PAGE_DATA.custom_css || '';
					} 
					catch(e) 
					{
						customCss.value = '';
					}
				}

				saveState(); // (Ini kode lama anda)
			});

			// Bersihkan event saat aplikasi ditutup (Good Practice)
			onBeforeUnmount(() => {
				window.removeEventListener('resize', checkDeviceWidth);
				if (editorInstance) editorInstance.destroy(); // (Kode lama ckeditor)
			});

			return { 
				layout, tools, previewMode, previewType, togglePreview, cloneItem, removeItem, 
				removeColumn, addColumn, addNestedRow, 
				saveJson, duplicateItem, openCkFinder, 
				undo, redo, canUndo: computed(() => historyIndex.value > 0), canRedo: computed(() => history.value.length > historyIndex.value + 1), 
				theme, toggleTheme, baseUrl, activeItem, activeType, setActive, deselectAll, 
				onDropToRow, showRightSidebar, toggleRightSidebar, addListItem, removeListItem, 
				addAccordionItem, removeAccordionItem, addTableRow, addTableCol, removeTableCol, removeTableRow, activeViewMode, 
				onDropToContainer, onDropToRow, onDropToColumn, containerGroup, rowGroup, isMobileDevice, 
				showLeftSidebar, toggleLeftSidebar, popupPos, startDrag, showWidgetModal, 
				openWidgetPicker, addWidgetFromPicker, quickAddContainer, quickAddRow, dataTypesConfig: DATA_TYPES, getCurrentDataType, 
				customCss, showCssEditor, cssEditorFullscreen, handleCssTab, pageName					
			};
		}
	}).use(window.VueSplide).mount('#app');
</script>
</body>
</html>