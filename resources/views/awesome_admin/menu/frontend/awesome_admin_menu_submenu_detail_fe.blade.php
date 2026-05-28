@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Frontend Submenu Details') }}
@endsection

@section('content')
	<div id="ph-app-manage-menu">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.menu.fe.submenu.detail') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Frontend Submenu Details') }}</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ph-fetch-listdata-submenu" id="ph-fetch-listdata-submenu" data-url="{{ url('awesome_admin/menu/fe/listdata/submenu/detail/'.$idOrSlug) }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> {{ t('Submenu List') }}</div>

					<div v-if="loading" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading') }} ...</div>
					</div>

					<div v-else-if="responseStatusSubmenu == 'failed'" class="ph-data-load-status" style="display: none">
						@{{ responseMessageSubmenu }}
					</div>

					<div v-else class="ph-data-load-content" id="ph-form-manage-menu" style="display: none">
						<form action="{{ route('cms.admin.awesome_admin.menu.fe.update_submenu_detail', ['idOrSlug' => $idOrSlug]) }}" method="post" ref="formHTML" custom-button-value="{{ t('Save') }}" @submit.prevent="submitData">
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

							<div v-if="responseDataSubmenu == ''" class="border rounded text-center text-danger p-3 mb-3">
								{{ t('No Menu') }}
							</div>

							<div v-else>
								<draggable class="list-group list-group-flush mb-3" v-model="responseDataSubmenu" v-bind="dragOptions" tag="transition-group" @start="drag = true" @end="drag = false" :component-data="{ tag: 'div', type: 'transition-group', name: !drag ? 'flip-list fade' : null }">
									<template #item="{ element, index }">

										<div :class="'list-group-item list-group-item-menu p-0 button-collapsed collapseHeaderMenu'+index" :key="submenu_name">
											<div class="px-2" style="padding: var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);">
												<div class="row g-1">
													<div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
														<i class="fas fa-arrows-alt-v fa-fw me-1"></i> |

														<span class="ms-2">
															<span v-if="element.submenu_icon_type == 'custom_input'">
																<span v-if="element.submenu_icon_custom !== ''" v-html="element.submenu_icon_custom"></span>
															</span>

															<span v-if="element.submenu_icon_type == 'upload_file'">
																<span v-if="element.submenu_icon_path !== '' && element.submenu_icon_url !== '' && element.submenu_icon_url.length > 0">
																	<img :src="element.submenu_icon_url" class="img-fluid me-1" style="width: 20px;">
																</span> 
															</span>

															<span v-if="element.submenu_icon_url.length > 0">
																Ada
															</span>
															
															@{{ element.submenu_name }}
														</span>
													</div>

													<div class="col-md-6 d-flex align-items-center justify-content-end">
														<a href="javascript:void(0)" class="btn btn-link text-danger font-size-inherit" v-on:click="collapseSubmenu($event, 'collapseHeaderMenu'+index, index)" aria-expanded="false" aria-controls="collapseHeaderMenu">View detail</a> |
														<a href="javascript:void(0)" class="btn btn-link text-danger ar-alert-bootbox font-size-inherit" v-on:click="openDeleteModalSubmenuDetail(element, index, element.submenu_icon_path)">Remove</a>
													</div>
												</div>
											</div>

											<div class="collapse ph-content" :id="'collapseHeaderMenu'+index">
												<div class="border-top mx-3 py-3">
													<div class="row">
														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Submenu Name') }}</label>
															<input type="text" :class="'submenu_name'+index+' form-control font-size-inherit'" v-model="element.submenu_name">
														</div>

														<div class="col-md-6 mb-3">
															<label class="form-label">{{ t('Submenu Link') }}</label>
															
															<div v-if="element.submenu_type == 'page'">
																<input type="text" :class="'submenu_link'+index+' form-control font-size-inherit'" :value="element.submenu_link" disabled>
															</div>

															<div v-else-if="element.submenu_type == 'custom'">
																<input type="text" :class="'submenu_link'+index+' form-control font-size-inherit'" v-model="element.submenu_link">
															</div>
														</div>

														<div class="col-12 mb-3">
															<label class="form-label">{{ t('Submenu Type') }}</label>
															<input type="text" :class="'submenu_link'+index+' form-control font-size-inherit'" :value="element.submenu_type" disabled>
														</div>

														<div class="col-12 form-group mb-3">
															<label class="form-label">{{ t('Submenu Icon Type') }}</label>

															<select name="submenu_icon_type" class="form-select font-size-inherit" aria-label="Select Submenu Icon Type" v-model="element.submenu_icon_type">
																<option value="">{{ t('Select') }}</option>
																<option value="upload_file">{{ t('Upload File') }}</option>
																<option value="custom_input">{{ t('Custom Input') }}</option>
															</select>
														</div>

														<div v-if="element.submenu_icon_type == 'upload_file'" class="col-12 mb-3">
															<label class="form-label">{{ t('Submenu Icon') }}</label>

															<div class="input-group">
																<input type="file" :name="'submenu_icon_'+index" :class="'submenu_icon_'+index+' form-control font-size-inherit'">
																<a :href="element.submenu_icon_url" v-if="element.submenu_icon_path !== '' && element.submenu_icon_url !== '' && element.submenu_icon_url.length > 0" class="btn btn-outline-secondary font-size-inherit" target="_blank">{{ t('View image') }}</a>
															</div>

															<input type="hidden" :name="'submenu_type['+index+']'" :value="element.submenu_type">
														</div>

														<div v-else-if="element.submenu_icon_type == 'custom_input'" class="col-12 form-group mb-3">
															<label class="form-label">{{ t('Submenu Icon Custom') }}</label>
															<input type="text" name="submenu_icon_custom_default" class="form-control font-size-inherit" v-model="element.submenu_icon_custom">
														</div>

													@if (site_config()->management_menu == 'v1')

														<div class="col-12">
															<label class="form-label">{{ t('Roles') }}</label>

															<v-select :options="responseDataRoles" v-model="element.submenu_roles" class="font-size-inherit" multiple>
																<template #open-indicator="{ attributes }">
																	<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
																</template>
															</v-select>
														
															<input type="hidden" name="roles" class="form-control font-size-inherit" :value="element.submenu_roles">
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
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add Submenu') }}</div>

					<div id="ph-submit-data-rp" class="ph-fetch-listdata-roles" data-url="{{ url('awesome_admin/menu/fe/listdata/roles') }}">
						<form action="{{ route('cms.admin.awesome_admin.menu.fe.checkSubmenuLink') }}" method="post" auto-refresh="false" auto-lock-button="false" custom-button-value="{{ t('Create') }}" ref="formHTMLCreateNewSubmenu" @submit.prevent="addSubmenu">
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

							<div class="row g-3">
								<div class="col-md-6 form-group">
									<label class="form-label">{{ t('Submenu Name') }}</label>
									<input type="text" name="submenu_name" class="form-control font-size-inherit" v-model="responseNewDataSubmenu.submenu_name">
								</div>

								<div class="col-md-6 form-group">
									<label class="form-label">{{ t('Submenu Type') }}</label>

									<select name="submenu_type" class="form-select font-size-inherit" aria-label="Select Submenu Type" v-on:change="selectSubmenuType($event)" v-model="responseNewDataSubmenu.submenu_type">
										<option value="">{{ t('Select Submenu Type') }}</option>
										<option value="page">{{ t('Page') }}</option>
										<option value="custom">{{ t('Custom') }}</option>
									</select>
								</div>

								<div v-if="responseNewDataSubmenu.submenu_type == '' || responseNewDataSubmenu.submenu_type == 'custom'" class="col-12 form-group">
									<label class="form-label">{{ t('Submenu Link') }}</label>
									<input type="text" name="submenu_link" class="form-control font-size-inherit" v-model="responseNewDataSubmenu.submenu_link">
								</div>

								<div v-else-if="responseNewDataSubmenu.submenu_type == 'page'" class="col-12 form-group">
									<label class="form-label">{{ t('Submenu Link') }}</label>

									<select name="submenu_link" class="form-select font-size-inherit" aria-label="Select Submenu Link" v-on:change="selectSubmenuLink($event)">
										<option value="">{{ t('Select') }}</option>
										<option v-for="(item, index) in responseDataRoutes" :value="item.name+'-'+item.uri">@{{ item.uri }}</option>
									</select>

									<input type="hidden" v-model="responseNewDataSubmenu.submenu_link">
								</div>

								<div class="col-12 form-group">
									<label class="form-label">{{ t('Submenu Icon Type') }}</label>

									<select name="submenu_icon_type" class="form-select font-size-inherit" aria-label="Select Submenu Icon Type" v-on:change="selectSubmenuIconType($event)">
										<option value="">{{ t('Select') }}</option>
										<option value="upload_file">{{ t('Upload File') }}</option>
										<option value="custom_input">{{ t('Input Custom Icon') }}</option>
									</select>
								</div>

								<div v-if="responseNewDataSubmenu.submenu_icon_type == 'upload_file'" class="col-12 form-group" v-cloak>
									<label class="form-label">{{ t('Upload Submenu Icon') }}</label>
									<input type="file" name="submenu_icon" class="form-control form-control-new-icon font-size-inherit" v-on:change="addIconSubmenu($event)">
								</div>

								<div v-else-if="responseNewDataSubmenu.submenu_icon_type == 'custom_input'" class="col-12 form-group" v-cloak>
									<label class="form-label">{{ t('Submenu Icon Custom') }}</label>
									<input type="text" name="submenu_icon_custom_default" class="form-control font-size-inherit" v-model="responseNewDataSubmenu.submenu_icon_custom">
								</div>

							@if (site_config()->management_menu == 'v1')
							
								<div class="col-12">
									<label class="form-label">{{ t('Roles') }}</label>

									<v-select :options="responseDataRoles" v-model="responseNewDataSubmenu.submenu_roles" class="font-size-inherit" multiple>
										<template #open-indicator="{ attributes }">
											<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
										</template>
									</v-select>
								
									<input type="hidden" name="roles" class="form-control font-size-inherit" :value="responseNewDataSubmenu.submenu_roles">
								</div>

								<div class="col-12 ph-fetch-listdata-permissions d-none" data-url="{{ url('awesome_admin/menu/fe/listdata/permissions') }}">
									<label class="form-label">{{ t('Permissions') }}</label>

									<v-select :options="responseDataPermissions" v-model="responseNewDataSubmenu.submenu_permissions" class="font-size-inherit" multiple>
										<template #open-indicator="{ attributes }">
											<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
										</template>
									</v-select>
								
									<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseNewDataSubmenu.submenu_permissions">
								</div>

							@endif

								<div class="col-12 form-group text-end">
									<input type="hidden" name="parent_code" value="{{ $idOrSlug }}">
									<input type="hidden" name="step" value="post">
									<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Create') }}">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal Delete Submenu -->
		<Teleport to="body">
			<div class="modal ph-modal-delete fade" id="modalDeleteSubmenuDetail" tabindex="-1" aria-labelledby="modalDeleteSubmenuDetailLabel" aria-hidden="true">
				<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
					<div class="modal-content" id="ph-form-delete-submenu-detail">
						<form action="{{ url('awesome_admin/menu/fe/delete_submenu_detail') }}" method="post" auto-refresh="true" auto-lock-button="true" custom-button-value="<i class='fas fa-trash-alt fa-fw me-1'></i> Delete" ref="formHTMLdelete" @submit="submitDataDeleteSubmenu($event)">
							
							<div class="ph-notice" v-cloak>
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
								<h5 class="modal-title" id="modalDeleteSubmenuDetailLabel">{{ t('Delete Submenu') }}</h5>
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
										<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeDeleteModalSubmenuDetail">{{ t('No, keep it') }}</button>
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
	<script src="{{ url('assets/js/vue3/manage_menu/fe/vueV3-manage-frontend-submenu-2026.js?v=').time() }}"></script>
@endpushonce