@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Roles') }}
@endsection

@section('content')
	<div id="ph-app-manage-role">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.role') }}
		</div>

		<div class="ph-content p-3 mb-3 rounded">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Roles') }} v1</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ar-fetch-listdata-role" id="ar-fetch-listdata-role" data-url="{{ url('awesome_admin/role/listdata') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> {{ t('List of Roles') }}</div>

					<div v-if="loadingData" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading') }}...</div>
					</div>

					<div v-else-if="responseStatus == 'failed'" class="ph-data-load-status text-danger text-center h6" style="display: none">
						@{{ responseMessage }}
					</div>

					<div v-else class="ph-data-load-content" v-cloak>
						<ul class="list-group list-group-flush">
							<li class="list-group-item" v-for="(item, index) in responseData">
								@{{ item.name }}

								<span class="float-end">
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-role-modalRead" :class="'ar-fetch-detail-data-role-modalRead-'+item.id" data-url="{{ url('awesome_admin/role/detaildata') }}" data-url-2="{{ url('awesome_admin/role/detaildatapermission/') }}" v-on:click="showModal('modalRead', item.id)">{{ t('View') }}</a>
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-role-modalUpdate" :class="'ar-fetch-detail-data-role-modalUpdate-'+item.id" data-url="{{ url('awesome_admin/role/detaildata') }}" data-url-2="{{ url('awesome_admin/role/detaildatapermission/') }}" v-on:click="showModal('modalUpdate', item.id)" class="mx-3">{{ t('Edit') }}</a>
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-role-modalDelete" :class="'ar-fetch-detail-data-role-modalDelete-'+item.id" data-url="{{ url('awesome_admin/role/detaildata') }}" v-on:click="showModal('modalDelete', item.id)" class="text-danger">{{ t('Delete') }}</a>
								</span>
							</li>
						</ul>
					</div>

				</div>
			</div>

			<div class="col-md-6">
				<div class="ph-content rounded p-4 ar-fetch-listdata-permission" data-url="{{ url('awesome_admin/role/listdatapermission') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-plus fa-fw me-1"></i> Add New Role</div>				
					
					<div id="ph-submit-data-role">
						<div class="ph-notice" v-cloak>
							<div aria-live="polite" aria-atomic="true" class="position-relative">
								<div class="toast-container position-fixed top-0 end-0 p-3">
									<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
										<div :class="'toast-header pe-3 pt-3 pb-1 '+responseStatusToast+' border-0'">
											<strong class="toast-header-title toast-header-icon me-auto">Notice</strong>
											<small>just now</small>
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

						<form action="{{ url('awesome_admin/role') }}" method="post" @submit="submitData" ref="formHTML" auto-refresh-data="true" auto-reset-form="true">
							<div class="form-group mb-3">
								<label class="form-label">{{ t('Role Name') }}</label>
								<input type="text" name="role_name" class="form-control">
							</div>

							<div class="form-group mb-3">
								<label class="form-label">{{ t('Permissions') }}</label>

								<v-select :options="responseDataPermissions" v-model="responseDetailDataPermission" class="font-size-inherit" multiple>
									<template #open-indicator="{ attributes }">
										<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
									</template>
								</v-select>
							
								<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseDetailDataPermission">
							</div>

							<div class="form-group text-end">
								<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Create') }}</button>
							</div>
						</form>
					</div>

					<!--- View Role Modal--->
					<Teleport to="body">
						<div class="modal fade" id="ph-submit-data-role-modalRead" tabindex="-1" aria-labelledby="modalReadLabel" aria-hidden="true">
							<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<div v-if="loadingDataModal" class="text-center p-3 py-4">
										<div class="spinner-border" role="status">
											<span class="visually-hidden">{{ t('Loading') }}...</span>
										</div>

										<div class="h6 mt-2 mb-0">{{ t('Loading') }}...</div>
									</div>

									<div v-else-if="responseStatusModal == 'failed'">
										<div class="modal-body p-4 text-center text-danger h6 mb-0">
											@{{ responseMessageModal }}
										</div>
									</div>

									<div v-else>
										<div class="modal-header align-items-start">
											<div class="modal-title">
												<h5 class="mb-1">{{ t('View Role') }}</h5>
												<div class="d-block text-secondary">{{ t('You can view detail role here, before edit') }}</div>
											</div>

											<a href="javascript:void(0)" class="text-secondary ms-auto" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle fs-4"></i></a>
										</div>

										<div class="modal-body">
											<div class="bg-body-secondary p-3 mb-3 rounded">
												<div class="form-group">
													<div class="fw-bold mb-2">{{ t('Role Name') }}:</div>

													@{{ responseDataModal.read.name }}
												</div>
											</div>

											<div class="bg-body-secondary p-3 rounded">
												<div class="fw-bold mb-2">{{ t('List Permission') }}:</div>

												<ul class="list-group list-group-flush">
													<li v-for="item in responseDataModal.read.permissions" class="list-group-item bg-transparent ps-0">
														<i class="fas fa-users-class fa-fw me-2"></i> @{{ item }}
													</li>	
												</ul>
											</div>
										</div>

										<div class="modal-footer">
											<button type="button" class="btn btn-secondary font-size-inherit" data-bs-dismiss="modal">{{ t('Close') }}</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</Teleport>

					<!--- Edit Role Modal --->
					<Teleport to="body">	
						<div class="modal fade" id="ph-submit-data-role-modalUpdate" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
							<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<form :action="'{{ url('awesome_admin/role/edit') }}/'+responseDataModal.update.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="false" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'modalUpdate')" ref="formHTML-modalUpdate">
										
										<div class="ph-notice">
											<div aria-live="polite" aria-atomic="true" class="position-relative">
												<div class="toast-container position-fixed top-0 end-0 p-3">
													<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
														<div :class="'toast-header px-3 pt-3 pb-1 '+responseStatusToast+' border-0'">
															<strong class="me-auto">Notice</strong>
															<small>just now</small>
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

										<div v-if="loadingDataModal" class="text-center p-3 py-4">
											<div class="spinner-border" role="status">
												<span class="visually-hidden">{{ t('Loading') }}...</span>
											</div>

											<div class="h6 mt-2 mb-0">{{ t('Loading') }}...</div>
										</div>

										<div v-else-if="responseStatusModal == 'failed'">
											<div class="modal-body p-4 text-center text-danger h6 mb-0">
												@{{ responseMessageModal }}
											</div>
										</div>

										<div v-else>
											<div class="modal-header align-items-start">
												<div class="modal-title">
													<h5 class="mb-1">{{ t('Edit Role') }}</h5>
													<div class="d-block text-secondary">{{ t('You can edit role name and permission here') }}</div>
												</div>

												<a href="javascript:void(0)" class="text-secondary ms-auto" v-on:click="closeModal('ph-submit-data-role-modalUpdate', 'ph-submit-data-role-modalUpdate')" aria-label="Close"><i class="fal fa-times-circle fs-4"></i></a>
											</div>

											<div class="modal-body">
												<div class="bg-body-secondary p-3 mb-3 rounded">
													<div class="form-group">
														<div class="fw-bold mb-2">{{ t('Role Name') }}:</div>
														<input type="text" name="role_name" class="form-control" v-model="responseDataModal.update.name">
													</div>
												</div>

												<div class="bg-body-secondary p-3 rounded">
													<div class="fw-bold mb-2">{{ t('List Permission') }}:</div>

													<v-select :options="responseDataPermissions" v-model="responseDataModal.update.permissions" class="font-size-inherit" multiple>
														<template #open-indicator="{ attributes }">
															<span v-bind="attributes"><i class="fal fa-angle-down fa-lg"></i></span>
														</template>
													</v-select>

													<input type="hidden" name="permissions" class="form-control font-size-inherit" :value="responseDataModal.update.permissions">

												</div>
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-role-modalUpdate', 'ph-submit-data-role-modalUpdate')">{{ t('Cancel') }}</button>
												<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Save') }}</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</Teleport>

					<!--- Delete Role Modal--->
					<Teleport to="body">
						<div class="modal ph-modal-delete fade" id="ph-submit-data-role-modalDelete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
							<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<form :action="'{{ url('awesome_admin/role/delete') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'modalDelete')" ref="formHTML-modalDelete">
										
										<div class="ph-notice">
											<div aria-live="polite" aria-atomic="true" class="position-relative">
												<div class="toast-container position-fixed top-0 end-0 p-3">
													<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
														<div :class="'toast-header px-3 pt-3 pb-1 '+responseStatusToast+' border-0'">
															<strong class="me-auto">Notice</strong>
															<small>just now</small>
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

										<div v-if="loadingDataModal" class="text-center p-3 py-4">
											<div class="spinner-border" role="status">
												<span class="visually-hidden">{{ t('Loading') }}...</span>
											</div>

											<div class="h6 mt-2 mb-0">{{ t('Loading') }}...</div>
										</div>

										<div v-else-if="responseStatusModal == 'failed'">
											<div class="modal-body p-4 text-center text-danger h6 mb-0">
												@{{ responseMessageModal }}
											</div>
										</div>

										<div v-else>
											<div class="modal-header d-none">
												<h5 class="modal-title">{{ t('Delete Role') }}</h5>
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
												<div class="row gx-2 justify-content-center">
													<div class="col-auto">	
														<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-role-modalDelete', 'ph-submit-data-role-modalDelete')">{{ t('No, keep it') }}</button>
													</div>

													<div class="col-auto">
														<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Yes, Delete') }}</button>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</Teleport>

				</div>
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_role/vueV3-manage-role-2026.js?v=').time() }}"></script>
@endpushonce