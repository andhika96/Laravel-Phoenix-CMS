@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Backend Parent Menus') }}
@endsection

@section('content')
	<div id="ph-app-manage-parentmenu">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.menu.be.parent_menu') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Backend Parent Menus') }}</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ph-fetch-listdata-parentmenu" id="ph-fetch-listdata-parentmenu" data-url="{{ url('awesome_admin/menu/be/listdata/parentmenu') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> {{ t('Parent Menu List') }}</div>

					<div v-if="loading" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading') }} ...</div>
					</div>

					<div v-else-if="responseStatusParentMenu == 'failed'" class="ph-data-load-status" style="display: none">
						@{{ responseMessageMenuParent }}
					</div>

					<div v-else class="ph-data-load-content" id="ph-form-manage-parentmenu" style="display: none">
						<form action="{{ route('cms.admin.awesome_admin.menu.be.update_parentmenu') }}" method="post" auto-refresh="false" auto-lock-button="false" custom-button-value="Save" ref="formHTML" @submit.prevent="submitData">
							<div class="ph-notice" v-cloak>
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
											<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
												<strong class="toast-header-title toast-header-icon me-auto">{{ t('Notice') }}</strong>
												<small>{{ t('just now') }}</small>
												<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
											</div>
										
											<div class="toast-body p-3 text-start">
												<div v-if="isArrayMessageAfterSubmit == 1">
													<ul class="ps-3 m-0">
														<li v-for="(item, index) in responseMessageAfterSubmit">
															@{{ item[0] }}
														</li>
													</ul>
												</div>

												<div v-else>
													@{{ responseMessageAfterSubmit }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div v-if="responseDataParentMenu == ''" class="border rounded text-center text-danger p-3 mb-3">
								{{ t('No Menu') }}
							</div>

							<div v-else>
								<draggable class="list-group list-group-flush mb-3" v-model="responseDataParentMenu" v-bind="dragOptions" tag="transition-group" @start="drag = true" @end="drag = false" :component-data="{ tag: 'div', type: 'transition-group', name: !drag ? 'flip-list fade' : null }">
									<template #item="{ element, index }">

										<div :class="'list-group-item list-group-item-menu p-0 button-collapsed collapseHeaderMenu'+index" :key="parent_name">
											<div class="px-2" style="padding: var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);">
												<div class="row g-1">
													<div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
														<i class="fas fa-arrows-alt-v fa-fw me-2"></i> | 
														
														<span class="ms-2">
															<span v-if="element.parent_icon_type == 'custom_input'">
																<span v-if="element.parent_icon_custom !== ''" v-html="element.parent_icon_custom"></span>
															</span>

															<span v-if="element.parent_icon_type == 'upload_file'">
																<span v-if="element.parent_icon_path !== '' && element.parent_icon_url !== '' && element.parent_icon_url.length > 0">
																	<img :src="element.parent_icon_url" class="img-fluid me-1" style="width: 20px;">
																</span> 
															</span>
															
															@{{ element.parent_name }}
														</span>
													</div>

													<div class="col-md-6 d-flex align-items-center justify-content-end">
														<a href="javascript:void(0)" class="btn btn-link text-danger font-size-inherit" v-on:click="collapseParentMenu($event, 'collapseHeaderMenu'+index, index)" aria-expanded="false" aria-controls="collapseHeaderMenu">{{ t('View detail') }}</a> |
														<a href="javascript:void(0)" class="btn btn-link text-danger ar-alert-bootbox font-size-inherit" v-on:click="openDeleteModalParentMenu(element, index, element.parent_icon_path)">{{ t('Remove') }}</a>
													</div>
												</div>
											</div>

											<div class="collapse ph-content" :id="'collapseHeaderMenu'+index">
												<div class="border-top mx-3 py-3">
													<div class="row">
														<div class="col-12 form-group mb-3">
															<label class="form-label">{{ t('Category Menu') }}</label>

															<select name="category_menu" class="form-select font-size-inherit" aria-label="Select Category Menu" v-model="element.category_code">
																<option value="">{{ t('Select Category Menu') }}</option>
																
																@foreach ($categorymenu['data'] as $category)
																	<option value="{{ $category['category_code'] }}">{{ $category['category_name'] }}</option>
																@endforeach
															</select>
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Parent Menu Name') }}</label>
															<input type="text" :class="'parent_name'+index+' form-control font-size-inherit'" v-model="element.parent_name">
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Parent Menu Link') }}</label>
															
															<div v-if="element.parent_type == 'page'">
																<input type="text" :class="'parent_link'+index+' form-control font-size-inherit'" :value="element.parent_link" disabled>
															</div>

															<div v-else-if="element.parent_type == 'custom'">
																<input type="text" :class="'parent_link'+index+' form-control font-size-inherit'" v-model="element.parent_link">
															</div>
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Is For Parent Menu') }}</label>

															<select name="is_for_parent_menu" class="form-select font-size-inherit" aria-label="Select Parent Type" v-model="element.is_for_parent_menu">
																<option value="">{{ t('Select') }}</option>
																<option value="single">{{ t('Single Menu') }}</option>
																<option value="parent">{{ t('Parent Menu') }}</option>
															</select>
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Parent Page Type') }}</label>
															<input type="text" :class="'parent_link'+index+' form-control font-size-inherit'" :value="element.parent_type" disabled>
														</div>

														<div class="col-12 form-group mb-3">
															<label class="form-label">{{ t('Parent Menu Icon Type') }}</label>

															<select name="parent_icon_type" class="form-select font-size-inherit" aria-label="Select Parent Menu Icon Type" v-model="element.parent_icon_type">
																<option value="">{{ t('Select') }}</option>
																<option value="upload_file">{{ t('Upload File') }}</option>
																<option value="custom_input">{{ t('Custom Input') }}</option>
															</select>
														</div>

														<div v-if="element.parent_icon_type == 'upload_file'" class="col-12 mb-3">
															<label class="form-label">{{ t('Parent Menu Icon') }}</label>

															<div class="input-group">
																<input type="file" :name="'parent_icon_'+index" :class="'parent_icon_'+index+' form-control font-size-inherit'">
																<a :href="element.parent_icon_url" v-if="element.parent_icon_path !== '' && element.parent_icon_url !== '' && element.parent_icon_url.length > 0" class="btn btn-outline-secondary font-size-inherit" target="_blank">{{ t('View image') }}</a>
															</div>

															<input type="hidden" :name="'parent_type['+index+']'" :value="element.parent_type">
														</div>

														<div v-else-if="element.parent_icon_type == 'custom_input'" class="col-12 form-group mb-3">
															<label class="form-label">{{ t('Parent Menu Icon Custom') }}</label>
															<input type="text" name="parent_icon_custom_default" class="form-control font-size-inherit" v-model="element.parent_icon_custom">
														</div>

													@if (site_config()->management_menu == 'v1')

														<div class="col-12">
															<label class="form-label">{{ t('Roles') }}</label>

															<v-select :options="responseDataRoles" v-model="element.parent_roles" class="font-size-inherit" multiple>
																<template #open-indicator="{ attributes }">
																	<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
																</template>
															</v-select>
														
															<input type="hidden" name="roles" class="form-control font-size-inherit" :value="element.parent_roles">
														</div>

														<div class="col-12 d-none">
															<label class="form-label">{{ t('Permissions') }}</label>

															<v-select :options="responseDataPermissions" v-model="element.submenu_permissions" class="font-size-inherit" multiple>
																<template #open-indicator="{ attributes }">
																	<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
																</template>
															</v-select>
														
															<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="element.submenu_permissions">
														</div>

													@endif

													</div>
												</div>
											</div>
										</div>

									</template>
								</draggable>
							</div>

							<div class="row">
								<div class="col-md-4 offset-md-8 text-end">
									<input type="hidden" name="step" value="post">
									<input type="submit" class="btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit" value="{{ t('Save') }}">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="ph-content rounded p-4">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add Parent Menu') }}</div>

					<div id="ph-submit-data-rp" class="ph-fetch-listdata-roles" data-url="{{ url('awesome_admin/menu/be/listdata/roles') }}">
						<form action="{{ route('cms.admin.awesome_admin.menu.be.checkParentMenuLink') }}" method="post" auto-refresh="false" auto-lock-button="false" custom-button-value="{{ t('Create') }}" ref="formHTMLCreateNewMenu" @submit.prevent="addParentMenu">
							<div class="ph-notice" v-cloak>
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
											<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
												<strong class="toast-header-title toast-header-icon me-auto">{{ t('Notice') }}</strong>
												<small>{{ t('just now') }}</small>
												<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
											</div>
										
											<div class="toast-body p-3 text-start">
												<div v-if="isArrayMessageAfterSubmit == 1">
													<ul class="ps-3 m-0">
														<li v-for="(item, index) in responseMessageAfterSubmit">
															@{{ item[0] }}
														</li>
													</ul>
												</div>

												<div v-else>
													@{{ responseMessageAfterSubmit }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row g-3 ph-fetch-listdata-routes" data-url="{{ url('awesome_admin/menu/be/listdata/routes') }}">
								<div class="col-12 form-group">
									<label class="form-label">{{ t('Menu Category') }}</label>

									<select name="category_menu" class="form-select font-size-inherit" aria-label="Select Category Menu" v-model="responseNewDataParentMenu.category_code">
										<option value="">{{ t('Select Menu Category') }}</option>
										
										@foreach ($categorymenu['data'] as $category)
											<option value="{{ $category['category_code'] }}">{{ $category['category_name'] }}</option>
										@endforeach
									
									</select>
								</div>

								<div class="col-md-6 form-group">
									<label class="form-label">{{ t('Menu Type') }}</label>

									<select name="parent_type" class="form-select font-size-inherit" aria-label="Select Menu Type" v-on:change="selectParentType($event)" v-model="responseNewDataParentMenu.parent_type">
										<option value="">{{ t('Select Menu Type') }}</option>
										<option value="page">{{ t('Page') }}</option>
										<option value="custom">{{ t('Custom') }}</option>
									</select>
								</div>

								<div class="col-md-6 form-group">
									<label class="form-label">{{ t('Set as Parent?') }}</label>

									<select name="is_for_parent_menu" class="form-select font-size-inherit" aria-label="Select Menu Type" v-model="responseNewDataParentMenu.is_for_parent_menu">
										<option value="">{{ t('Select') }}</option>
										<option value="single">{{ t('Single Menu') }}</option>
										<option value="parent">{{ t('Parent Menu') }}</option>
									</select>
								</div>

								<div class="col-12 form-group">
									<label class="form-label">{{ t('Menu Name') }}</label>
									<input type="text" name="parent_name" class="form-control font-size-inherit" v-model="responseNewDataParentMenu.parent_name">
								</div>

								<div v-if="responseNewDataParentMenu.is_for_parent_menu == '' || responseNewDataParentMenu.is_for_parent_menu == 'single'" class="col-12 form-group">
									<div v-if="responseNewDataParentMenu.parent_type == '' || responseNewDataParentMenu.parent_type == 'custom'" class="col-12 form-group">
										<label class="form-label">{{ t('Menu Link') }}</label>
										<input type="text" name="parent_link" class="form-control font-size-inherit" v-model="responseNewDataParentMenu.parent_link">
									</div>

									<div v-else-if="responseNewDataParentMenu.parent_type == 'page'" class="col-12 form-group">
										<label class="form-label">{{ t('Menu Link') }}</label>

										<select name="parent_link" class="form-select font-size-inherit" aria-label="Select Menu Link" v-on:change="selectParentLink($event)">
											<option value="">{{ t('Select') }}</option>
											<option v-for="(item, index) in responseDataRoutes" :value="item.name+'-'+item.uri">@{{ item.uri }}</option>
										</select>

										<input type="hidden" v-model="responseNewDataParentMenu.parent_link">
									</div>
								</div>

								<div class="col-12 form-group">
									<label class="form-label">{{ t('Menu Icon Type') }}</label>

									<select name="parent_icon_type" class="form-select font-size-inherit" aria-label="Select Menu Menu Icon Type" v-on:change="selectParentMenuIconType($event)">
										<option value="">{{ t('Select') }}</option>
										<option value="upload_file">{{ t('Upload File') }}</option>
										<option value="custom_input">{{ t('Input Custom Icon') }}</option>
									</select>
								</div>

								<div v-if="responseNewDataParentMenu.parent_icon_type == 'upload_file'" class="col-12 form-group" v-cloak>
									<label class="form-label">{{ t('Upload Menu Icon') }}</label>
									<input type="file" name="parent_icon" class="form-control form-control-new-icon font-size-inherit" v-on:change="addIconParentMenu($event)">
								</div>

								<div v-else-if="responseNewDataParentMenu.parent_icon_type == 'custom_input'" class="col-12 form-group" v-cloak>
									<label class="form-label">{{ t('Menu Icon Custom') }}</label>
									<input type="text" name="parent_icon_custom_default" class="form-control font-size-inherit" v-model="responseNewDataParentMenu.parent_icon_custom">
								</div>

							@if (site_config()->management_menu == 'v1')

								<div class="col-12 form-group">
									<label class="form-label">{{ t('Roles') }}</label>

									<v-select :options="responseDataRoles" v-model="responseNewDataParentMenu.parent_roles" class="font-size-inherit" multiple>
										<template #open-indicator="{ attributes }">
											<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
										</template>
									</v-select>
								
									<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseNewDataParentMenu.parent_roles">
								</div>

								<div class="col-12 ph-fetch-listdata-permissions d-none" data-url="{{ url('awesome_admin/menu/listdata/permissions') }}">
									<label class="form-label">{{ t('Permissions') }}</label>

									<v-select :options="responseDataPermissions" v-model="responseNewDataParentMenu.parent_permissions" class="font-size-inherit" multiple>
										<template #open-indicator="{ attributes }">
											<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
										</template>
									</v-select>
								
									<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseNewDataParentMenu.parent_permissions">
								</div>

							@endif

								<div class="col-12 form-group text-end">
									<input type="hidden" name="step" value="post">
									<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Create') }}">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal Delete Parent Menu -->
		<Teleport to="body">
			<div class="modal ph-modal-delete fade" id="modalDeleteParentMenu" tabindex="-1" aria-labelledby="modalDeleteParentMenuLabel" aria-hidden="true">
				<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
					<div class="modal-content" id="ph-form-delete-parentmenu">
						<form action="{{ url('awesome_admin/menu/be/delete_parentmenu') }}" method="post" auto-refresh="true" auto-lock-button="true" custom-button-value="{{ t('Yes, Delete') }}" ref="formHTMLdelete" @submit="submitDataDelete($event)">
							
							<div class="ph-notice" style="display: none">
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
											<div :class="'toast-header '+responseStatusToast+' px-3 pt-3 pb-1 border-0'">
												<strong class="me-auto">{{ t('Notice') }}</strong>
												<small>{{ t('just now') }}</small>
												<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
											</div>
										
											<div class="toast-body p-3 text-start">
												<div v-if="isArrayMessageAfterSubmit == 1">
													<ul class="ps-3 m-0">
														<li v-for="(item, index) in responseMessageAfterSubmit">
															@{{ item[0] }}
														</li>
													</ul>
												</div>

												<div v-else>
													@{{ responseMessageAfterSubmit }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="modal-header d-none">
								<h5 class="modal-title" id="modalDeleteParentMenuLabel">{{ t('Delete Parent Menu') }}</h5>
							</div>

							<div class="modal-body pt-5 px-5 text-center">
								<div class="mb-4">
									<i class="fad fa-trash-alt fs-1 text-danger"></i>
								</div>

								<div class="h5">
									{{ t('Delete Data') }}
								</div>

								<div>
									{!! t('Do you really want to delete these data? {1} This process cannot be undone.', '<br/>') !!}
								</div>
							</div>

							<div class="modal-footer pb-5 d-block border-0">
								<input type="hidden" name="index_data" class="index-data">
								<input type="hidden" name="menu_code" class="menu-code">
								<input type="hidden" name="path_file" class="path-file">

								<div class="row gx-2 justify-content-center">
									<div class="col-md-auto">	
										<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeDeleteModalParentMenu">{{ t('No, keep it') }}</button>
									</div>

									<div class="col-md-auto">
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Yes, Delete') }}</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</Teleport>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_menu/be/vueV3-manage-backend-parent-menu-2026.js?v=').time() }}"></script>
@endpushonce