@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Backend Menu Categories') }}
@endsection

@section('content')
	<div id="ph-app-manage-categorymenu">
		<div class="mb-3">		
			{{ Breadcrumbs::render('awesome_admin.menu.be.category_menu') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Backend Menu Categories') }}</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ph-fetch-listdata-categorymenu" id="ph-fetch-listdata-categorymenu" data-url="{{ url('awesome_admin/menu/be/listdata/categorymenu') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> {{ t('Menu Category List') }}</div>

					<div v-if="loading" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading') }}...</div>
					</div>

					<div v-else-if="responseStatusCategoryMenu == 'failed'" class="ph-data-load-status h6" style="display: none">
						@{{ responseMessageMenuCategory }}
					</div>

					<div v-else class="ph-data-load-content" id="ph-form-manage-categorymenu" style="display: none">
						<form action="{{ route('cms.admin.awesome_admin.menu.be.update_categorymenu') }}" method="post" auto-refresh="false" auto-lock-button="false" custom-button-value="Save" ref="formHTML" @submit.prevent="submitData">
							<div class="ph-notice" style="display: none">
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

							<div v-if="responseDataCategoryMenu == ''" class="h6 text-center text-danger p-3 mb-3">
								{{ t('No Category Menu') }}
							</div>

							<div v-else>
								<draggable class="list-group list-group-flush mb-3" v-model="responseDataCategoryMenu" v-bind="dragOptions" tag="transition-group" @start="drag = true" @end="drag = false" :component-data="{ tag: 'div', type: 'transition-group', name: !drag ? 'flip-list fade' : null }">
									<template #item="{ element, index }">

										<div :class="'list-group-item list-group-item-menu p-0 button-collapsed collapseHeaderMenu'+index" :key="category_name">
											<div class="px-0" style="padding: var(--bs-list-group-item-padding-y) var(--bs-list-group-item-padding-x);">
												<div class="row g-1">
													<div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
														<i class="fas fa-arrows-alt-v fa-fw me-2"></i> | 
														
														<span class="ms-2">															
															@{{ element.category_name }}
														</span>
													</div>

													<div class="col-md-6 d-flex align-items-center justify-content-end">
														<a href="javascript:void(0)" class="btn btn-link text-danger font-size-inherit" v-on:click="collapseCategoryMenu($event, 'collapseHeaderMenu'+index, index)" aria-expanded="false" aria-controls="collapseHeaderMenu">View detail</a> |
														<a href="javascript:void(0)" class="btn btn-link text-danger ar-alert-bootbox font-size-inherit" v-on:click="openDeleteModalCategoryMenu(responseDataMenuCategory, index, element.category_icon_path)">Remove</a>
													</div>
												</div>
											</div>

											<div class="collapse ph-content" :id="'collapseHeaderMenu'+index">
												<div class="border-top mx-3 py-3">
													<div class="row">
														<div class="col-12">
															<label class="form-label">{{ t('Category Menu Name') }}</label>
															<input type="text" :class="'category_name'+index+' form-control font-size-inherit'" v-model="element.category_name">
														</div>

														<div class="col-12 d-none">
															<label class="form-label">{{ t('Roles') }}</label>

															<v-select :options="responseDataRoles" v-model="element.category_roles" class="font-size-inherit" multiple>
																<template #open-indicator="{ attributes }">
																	<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
																</template>
															</v-select>
														
															<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="element.category_roles">
														</div>

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
									<input type="submit" class="btn ph-btn-theme btn-submit-data btn-submit-update-data-list font-size-inherit" value="Save">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="ph-content rounded p-4 ph-fetch-listdata-roles" data-url="{{ url('awesome_admin/menu/be/listdata/roles') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add Menu Category') }}</div>

					<div id="ph-submit-data-rp">
						<div class="ph-notice" style="display: none">
							<div aria-live="polite" aria-atomic="true" class="position-relative">
								<div class="toast-container position-fixed top-0 end-0 p-3">
									<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
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

						<div class="row g-3 ph-fetch-listdata-routes" data-url="{{ url('awesome_admin/menu/be/listdata/routes') }}">

							<div class="col-12 form-group">
								<label class="form-label">{{ t('Category Name') }}</label>
								<input type="text" name="category_name" class="form-control font-size-inherit" v-model="responseNewDataCategoryMenu.category_name">
							</div>

							<div class="col-12 form-group d-none">
								<label class="form-label">{{ t('Roles') }}</label>

								<v-select :options="responseDataRoles" v-model="responseNewDataCategoryMenu.category_roles" class="font-size-inherit" multiple>
									<template #open-indicator="{ attributes }">
										<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
									</template>
								</v-select>
							
								<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseNewDataCategoryMenu.category_roles">
							</div>

							<div class="col-12 form-group text-end">
								<a href="javascript:void(0)" v-on:click="addCategoryMenu" class="btn ph-btn-theme btn-submit-data font-size-inherit">Create</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal Delete Category Menu -->
		<Teleport to="body">
			<div class="modal fade" id="modalDeleteCategoryMenu" tabindex="-1" aria-labelledby="modalDeleteCategoryMenuLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content" id="ph-form-delete-categorymenu">
						<form action="{{ url('awesome_admin/menu/be/delete_categorymenu') }}" method="post" auto-refresh="true" auto-lock-button="true" custom-button-value="<i class='fas fa-trash-alt fa-fw me-1'></i> Delete" ref="formHTMLdelete" @submit="submitDataDelete($event)">
							
							<div class="ph-notice">
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
								<h5 class="modal-title" id="modalDeleteCategoryMenuLabel">{{ t('Delete Category Menu') }}</h5>
							</div>

							<div class="modal-body pt-5 px-5 text-center">
								<div class="mb-4">
									<i class="far fa-trash-alt fs-1"></i>
								</div>

								<div class="h5">
									{!! t('Do you really want to delete these data? {1} This process cannot be undone.', '<br/>') !!}
								</div>
							</div>

							<div class="modal-footer pb-5 d-block border-0">
								<input type="hidden" name="index_data" class="index-data">
								<input type="hidden" name="path_file" class="path-file">

								<div class="row gx-2 justify-content-center">
									<div class="col-md-auto">	
										<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeDeleteModalCategoryMenu">{{ t('Cancel') }}</button>
									</div>

									<div class="col-md-auto">
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit"><i class="fas fa-trash-alt fa-fw me-1"></i> {{ t('Delete') }}</button>
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
	<script src="{{ url('assets/js/vue3/manage_menu/be/vueV3-manage-backend-category-menu-2026.js?v=').time() }}"></script>
@endpushonce