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

	<link href="{{ asset('assets/css/pagebuilder.css') }}" rel="stylesheet">
</head>

<body>

<div id="app" class="d-flex flex-column vh-100" v-cloak>

	{{-- Toast Notification --}}
	<div class="ph-notice" v-cloak>
		<div aria-live="polite" aria-atomic="true">
			<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
				<div :class="'toast ph-notice-toast ph-callout-no-border ' + toastStatus" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3500" ref="toastEl">
					<div :class="'toast-header ' + toastStatus + ' px-3 pt-3 pb-1 border-0'">
						<i :class="toastStatus === 'bg-success' ? 'fas fa-check-circle text-success me-2' : 'fas fa-times-circle text-danger me-2'"></i>
						<strong class="me-auto">{{ t('Notice') }}</strong>
						<small>just now</small>
						<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
					</div>
					<div class="toast-body p-3 text-start">
						@{{ toastMessage }}
					</div>
				</div>
			</div>
		</div>
	</div>
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
				<button class="btn btn-sm btn-light border me-3" @click="toggleLeftSidebar" :class="{'active': showLeftSidebar}">
					<i class="fas fa-bars"></i>
				</button>

				<div class="bg-primary text-white rounded px-2 py-1 me-2 fw-bold" style="font-size: 14px;">PB</div>
				<span class="fw-bold" style="font-size: 16px;">ProBuilder <span class="fw-normal text-muted">| V13 Complete (Fix Preview)</span></span>
			</div>
			
			<div class="ms-auto d-flex align-items-center gap-2">
				<button class="btn btn-sm btn-light border" @click="toggleTheme"><i class="fas" :class="theme === 'light' ? 'fa-moon' : 'fa-sun'"></i></button>

				<div class="btn-group">
					<button class="btn btn-sm btn-light border" @click="undo" :disabled="!canUndo"><i class="fas fa-undo"></i></button>
					<button class="btn btn-sm btn-light border" @click="redo" :disabled="!canRedo"><i class="fas fa-redo"></i></button>
				</div>

				<div class="vr mx-2"></div>

				<!-- Viewport Switcher — selalu tampil -->
				<div class="btn-group me-2 preview-btn-group">
					<button class="btn btn-sm" :class="previewType === 'mobile' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'mobile'" title="Mobile (375px)"><i class="fas fa-mobile-alt"></i></button>
					<button class="btn btn-sm" :class="previewType === 'tablet' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'tablet'" title="Tablet (768px)"><i class="fas fa-tablet-alt"></i></button>
					<button class="btn btn-sm" :class="previewType === 'desktop' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'desktop'" title="Desktop (1366px)"><i class="fas fa-laptop"></i></button>
					<button class="btn btn-sm" :class="previewType === 'fhd' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = 'fhd'" title="Full HD (1920px)"><i class="fas fa-desktop"></i></button>
					<button class="btn btn-sm" :class="previewType === '4k' ? 'btn-primary' : 'btn-outline-secondary border'" @click="previewType = '4k'" title="4K (2560px)"><i class="fas fa-tv"></i></button>
				</div>

				<button class="btn btn-sm btn-outline-secondary px-3" @click="showCssEditor = true" title="Custom CSS Editor">
					<i class="fas fa-paint-brush me-1"></i> CSS
				</button>

				<button class="btn btn-sm btn-light border px-3" @click="toggleRightSidebar" :class="{'active': showRightSidebar}"><i class="fas fa-columns"></i></button>
				<button class="btn btn-sm btn-primary px-3 fw-medium" @click="saveJson"><i class="fas fa-save me-1"></i> Save</button>
			</div>
		</nav>

		<div class="builder-layout">
			
			<div class="sidebar-left" :class="{'collapsed': !showLeftSidebar}">
				<div class="mb-4">
					<div class="sidebar-category-title">{{ t('Page Settings') }}</div>

					<div class="bg-secondary-subtle rounded p-3">
						<div class="row g-3">
							<div class="col-12">
								<label class="form-label small">{{ t('Page Name') }}</label>
								<input type="text" name="page_name" v-model="pageName" class="form-control form-control-sm" placeholder="{{ t('Page Name') }}">
							</div>

							<div class="col-12">
								<label class="form-label small">{{ t('Page Status') }}</label>
								<select name="page_status" v-model="pageStatus" class="form-select form-select-sm" aria-label="Default select example">
									<option value="">{{ t('Select Status') }}</option>
									<option value="publish">{{ t('Publish') }}</option>
									<option value="not_active">{{ t('Not Active') }}</option>
									<option value="draft">{{ t('Draft') }}</option>
								</select>
							</div>
						</div>
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

			<div class="canvas-area" @click="deselectAll" id="canvasArea">
				<component :is="'style'" v-if="customCss">@{{ customCss }}</component>
				<div class="wysiwyg-frame" :style="canvasFrameStyle">
				<draggable v-model="layout" group="root" item-key="id" class="drop-zone-root" ghost-class="opacity-50">
					<template #item="{element: cont, index: i}">
						
						{{-- Section wrapper — full width, untuk Section Background --}}
						<div class="pb-section-wrapper" :style="getSectionWrapperStyle(cont)">

						<div class="ui-box container-ui" :class="[getContainerClass(cont), filterResponsiveClasses(cont.customClass), {'active': activeItem && activeItem.id === cont.id }]" :style="getContainerStyle(cont)" @click.stop="setActive(cont, 'container')">
							
							<div class="ui-header">
								<div class="ui-label">{{ t('Container') }}</div>
								
								<div class="ui-actions">
									<button class="action-btn action-btn-edit" title="Edit Properties" @click.stop="setActive(cont, 'container')"><i class="fas fa-pencil-alt"></i></button>
									<button class="action-btn" title="Add Row" @click.stop="quickAddRow(cont)" @mousedown.stop><i class="far fa-plus"></i></button>
									<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(layout, i)"><i class="far fa-copy"></i></button>
									<button class="action-btn danger" title="Remove" @click.stop="removeItem(layout, i)"><i class="far fa-trash-alt"></i></button>
								</div>                         
							</div>

							<div class="ui-body">
								<draggable v-model="cont.children" :group="containerGroup" :move="checkContainerDrop" item-key="id" style="min-height: 60px; height:100%;" @add="onDropToContainer($event, cont)">
									<template #item="{element: row, index: j}">
										
										<div class="ui-box row-ui" :class="[getRowClasses(row), filterResponsiveClasses(row.customClass), {'active': activeItem && activeItem.id === row.id }]" @click.stop="setActive(row, 'row')">
											<div class="ui-header">
												<div class="ui-label">{{ t('Row') }}</div>

												<div class="ui-actions">
													<button class="action-btn action-btn-edit" title="Edit Properties" @click.stop="setActive(row, 'row')"><i class="fas fa-pencil-alt"></i></button>
													<button class="action-btn" title="Add Column" @click.stop="addColumn(row)"><i class="fas fa-plus"></i></button>
													<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(cont.children, j)"><i class="far fa-copy"></i></button>
													<button class="action-btn danger" title="Delete" @click.stop="removeItem(cont.children, j)"><i class="fas fa-times"></i></button>
												</div>                                               
											</div>
											
											<div class="ui-body">
												<draggable v-model="row.children" :group="rowGroup" :move="checkRowDrop" item-key="id" class="row-drop-zone row m-0 h-100" @add="onDropToRow($event, row)">
													
													<template #item="{element: col, index: k}">
														
														<div :class="[getColClasses(col), filterResponsiveClasses(col.customClass)]" class="position-relative">
																
															<div class="col-ui" :class="[{ 'active': activeItem && activeItem.id === col.id }]" @click.stop="setActive(col, 'column')">
																<div class="col-handle">{{ t('Column') }}</div>
																
																<div class="col-actions">
																	<button class="action-btn action-btn-edit" title="Edit Properties" @click.stop="setActive(col, 'column')"><i class="fas fa-pencil-alt"></i></button>
																	<button class="action-btn" title="Add Nested Row" @click.stop="addNestedRow(col)"><i class="fas fa-level-down-alt"></i></button>
																	<button class="action-btn" title="Add Column" @click.stop="addColumn(row)"><i class="fas fa-plus"></i></button>
																	<button class="action-btn" title="Duplicate" @click.stop="duplicateItem(row.children, k)"><i class="far fa-copy"></i></button>
																	<button class="action-btn danger" title="Delete" v-if="row.children.length > 0" @click.stop="removeColumn(row, k)"><i class="fas fa-times"></i></button>
																</div>
																
																<draggable v-model="col.children" :group="{ name: 'col-children', put: ['widget', 'section', 'col-children'] }" item-key="id" class="widget-drop-zone px-4 pb-4" ghost-class="bg-info-subtle" @add="onDropToColumn($event, col)">
																	
																	<template #item="{element: item, index: l}">
																<div class="widget-ui" :class="[{ 'active': activeItem && activeItem.id === item.id }]" @click.stop="setActive(item, 'widget')">
																	<div class="widget-overlay">
																		<span>@{{ item.label }}</span>
																		<button class="widget-btn widget-btn-edit" title="{{ t('Edit Properties') }}" @click.stop="setActive(item, 'widget')"><i class="fas fa-pencil-alt"></i></button>
																		<button class="widget-btn" @click.stop="duplicateItem(col.children, l)"><i class="far fa-copy"></i></button>
																		<button class="widget-btn" @click.stop="removeItem(col.children, l)"><i class="fas fa-times"></i></button>
																	</div>
																	<div v-if="item.type === 'text'">
																		<div v-if="activeItem && activeItem.id === item.id" @mousedown.stop @click.stop>
																			<ckeditor-component v-model="item.content" :base-url="baseUrl"></ckeditor-component>
																		</div>
																		<div v-else v-html="item.content" style="min-height:24px;"></div>
																	</div>
																	<div v-if="item.type !== 'text'">
																		<component :is="getWidgetTemplate(item)" :item="item" :filter-class="filterResponsiveClasses" :view-type="previewType"></component>
																	</div>
																</div>
													</template>

													<template #footer>
														<div class="col-add-widget w-100 text-center rounded border border-dashed p-2 mt-2 text-muted user-select-none small" @click.stop="openWidgetPicker(col.children)"><i class="fas fa-plus me-1 opacity-50"></i>{{ t('Add Widget') }}</div>
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
							</div>{{-- /.ui-body --}}
						</div>{{-- /.container-ui --}}
						</div>{{-- /.pb-section-wrapper --}}
					</template>

					<template #footer>
						<div class="py-4 text-center">
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
				</div><!-- /.wysiwyg-frame -->

			</div>

			<div class="sidebar-right" v-if="showRightSidebar" :style="{ top: popupPos.top + 'px', left: popupPos.left + 'px' }">

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

								{{-- Toggle: Section BG vs Container BG --}}
								<div class="mb-3">
									<label class="small fw-bold text-dark mb-2 d-block">{{ t('Background Target') }}</label>
									<div class="btn-group w-100" role="group">
										<button type="button"
											:class="['btn btn-sm', (!activeItem.bgTarget || activeItem.bgTarget === 'section') ? 'btn-primary' : 'btn-outline-secondary']"
											@click="activeItem.bgTarget = 'section'"
											title="Warna mengisi seluruh lebar layar (full width)">
											<i class="fas fa-expand-alt me-1"></i> Section BG
										</button>
										<button type="button"
											:class="['btn btn-sm', activeItem.bgTarget === 'container' ? 'btn-primary' : 'btn-outline-secondary']"
											@click="activeItem.bgTarget = 'container'"
											title="Warna hanya di dalam area container">
											<i class="fas fa-compress-alt me-1"></i> Container BG
										</button>
										<button type="button"
											:class="['btn btn-sm', activeItem.bgTarget === 'both' ? 'btn-primary' : 'btn-outline-secondary']"
											@click="activeItem.bgTarget = 'both'"
											title="Warna di section luar dan container dalam (berbeda)">
											<i class="fas fa-layer-group me-1"></i> Both
										</button>
									</div>
									<div class="small text-muted mt-1">
										<span v-if="!activeItem.bgTarget || activeItem.bgTarget === 'section'"><i class="fas fa-info-circle me-1 text-primary"></i>Warna mengisi seluruh lebar layar</span>
										<span v-if="activeItem.bgTarget === 'container'"><i class="fas fa-info-circle me-1 text-primary"></i>Warna hanya di dalam area container</span>
										<span v-if="activeItem.bgTarget === 'both'"><i class="fas fa-info-circle me-1 text-primary"></i>Atur warna section & container secara terpisah</span>
									</div>
								</div>

								{{-- SECTION BG --}}
								<div v-if="!activeItem.bgTarget || activeItem.bgTarget === 'section' || activeItem.bgTarget === 'both'">
									<div v-if="activeItem.bgTarget === 'both'" class="small fw-bold text-primary mb-2 border-top pt-2">
										<i class="fas fa-expand-alt me-1"></i> Section Background (Full Width)
									</div>
									<div class="row g-3" :class="activeItem.bgTarget === 'both' ? 'ps-2 border-start border-primary border-2' : ''">
										<div class="col-12">
											<div class="row g-0">
												<div class="col-auto d-flex align-items-center">
													<input type="color" v-model="activeItem.sectionStyles.bgColor" class="form-control form-control-color p-0 me-2" style="width:36px;height:36px">
												</div>
												<div class="col-auto d-flex align-items-center">
													<span class="small text-muted">{{ t('Background Color') }}</span>
												</div>
											</div>
										</div>
										<div class="col-12">
											<div class="input-group">
												<input v-model="activeItem.sectionStyles.bgImage" class="form-control font-size-12px" placeholder="Image URL">
												<button class="btn btn-outline-secondary font-size-inherit" @click="openCkFinder(activeItem.sectionStyles, 'bgImage')"><i class="far fa-folder"></i></button>
											</div>
										</div>
										<div class="col-6">
											<label class="small text-muted mb-2 d-block">{{ t('Background Size') }}</label>
											<select v-model="activeItem.sectionStyles.bgSize" class="form-select font-size-12px">
												<option value="cover">{{ t('Cover') }}</option>
												<option value="contain">{{ t('Contain') }}</option>
												<option value="auto">{{ t('Auto') }}</option>
											</select>
										</div>
										<div class="col-6">
											<label class="small text-muted mb-2 d-block">{{ t('Background Repeat') }}</label>
											<select v-model="activeItem.sectionStyles.bgRepeat" class="form-select font-size-12px">
												<option value="no-repeat">{{ t('No Repeat') }}</option>
												<option value="repeat">{{ t('Repeat') }}</option>
												<option value="repeat-x">{{ t('Repeat X') }}</option>
												<option value="repeat-y">{{ t('Repeat Y') }}</option>
											</select>
										</div>
										<div class="col-6">
											<label class="small text-muted mb-2 d-block">{{ t('Background Position') }}</label>
											<select v-model="activeItem.sectionStyles.bgPos" class="form-select font-size-12px">
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
											<label class="small text-muted mb-2 d-block">{{ t('Min Height') }}</label>
											<select v-model="activeItem.sectionStyles.minHeight" class="form-select font-size-12px">
												<option value="auto">{{ t('Auto Height') }}</option>
												<option value="25vh">25vh</option>
												<option value="50vh">50vh</option>
												<option value="75vh">75vh</option>
												<option value="100vh">100vh</option>
											</select>
										</div>
									</div>
								</div>

								{{-- CONTAINER BG (tampil jika bgTarget = container atau both) --}}
								<div v-if="activeItem.bgTarget === 'container' || activeItem.bgTarget === 'both'">
									<div class="small fw-bold text-warning mb-2 mt-3 border-top pt-2">
										<i class="fas fa-compress-alt me-1"></i> Container Background (Inner Only)
									</div>
									<div class="row g-3 ps-2 border-start border-warning border-2">
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
											<label class="small text-muted mb-2 d-block">{{ t('Min Height') }}</label>
											<select v-model="activeItem.styles.minHeight" class="form-select font-size-12px">
												<option value="auto">{{ t('Auto Height') }}</option>
												<option value="25vh">25vh</option>
												<option value="50vh">50vh</option>
												<option value="75vh">75vh</option>
												<option value="100vh">100vh</option>
											</select>
										</div>
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
	const PAGE_URI  = "{{ isset($pageData) ? $pageData->uri : 'null' }}";
	const PAGE_DATA = {!! isset($pageData)
		? json_encode([
			'id'			=> $pageData->id			?? 0,
			'uri'			=> $pageData->uri			?? '',
			'page_name'		=> $pageData->page_name		?? '',
			'layout'		=> $pageData->vars			?? '[]',
			'custom_css'	=> $pageData->custom_css	?? '',
			'status'		=> $pageData->status		?? 'draft',
		])
		: 'null'
	!!};
</script>

<script>
	const { createApp, ref, onMounted, onBeforeUnmount, watch, computed, nextTick, resolveComponent, toValue } = Vue;
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
</script>

{{-- Page Builder JS Modules --}}
<script src="{{ asset('js/pagebuilder/pb-components.js') }}"></script>
<script src="{{ asset('js/pagebuilder/pb-preview.js') }}"></script>
<script src="{{ asset('js/pagebuilder/pb-app.js') }}"></script>
</body>
</html>