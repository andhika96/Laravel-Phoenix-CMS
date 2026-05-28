@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Permission') }}
@endsection

@section('content')
	<div id="ph-manage-permission">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.permission') }}
		</div>

		<div class="ph-content p-3 mb-3 rounded">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Permissions') }}</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ar-fetch-listdata-permission" id="ar-fetch-listdata-permission" data-url="{{ url('awesome_admin/permission/listdata') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> List of Permissions</div>

					<div v-if="loadingData" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading ...') }}</div>
					</div>

					<div v-else-if="responseStatus == 'failed'" class="ph-data-load-status text-danger text-center h6" style="display: none">
						@{{ responseMessage }}
					</div>

					<div v-else class="ph-data-load-content" style="display: none">
						<ul class="list-group list-group-flush">
							<li class="list-group-item" v-for="(item, index) in responseData">
								@{{ item.name }}

								<span class="float-end">
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-permission-editModal" :class="'ar-fetch-detail-data-permission-editModal-'+item.id" data-url="{{ url('awesome_admin/permission/detaildata') }}" v-on:click="showModal('editModal', item.id)">{{ t('Edit') }}</a>
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-permission-deleteModal" :class="'ar-fetch-detail-data-permission-deleteModal-'+item.id" data-url="{{ url('awesome_admin/permission/detaildata') }}" v-on:click="showModal('deleteModal', item.id)" class="text-danger ms-3">{{ t('Delete') }}</a>
								</span>
							</li>
						</ul>
					</div>

				</div>
			</div>

			<div class="col-md-6">
				<div class="ph-content rounded p-4">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add New Permission') }}</div>				

					<div id="ph-submit-data-permission">
						<div class="ph-notice" style="display: none">
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

						<form action="{{ url('awesome_admin/permission') }}" method="post" @submit="submitData" ref="formHTML">
							<div class="form-group mb-3">
								<label class="form-label">{{ t('Permission Name') }}</label>
								<input type="text" name="permission_name" class="form-control">
							</div>

							<div class="form-group text-end">
								<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Create') }}">
							</div>
						</form>
					</div>

					<!---  Edit Modal --->
					<Teleport to="body">
						<div class="modal fade" id="ph-submit-data-permission-editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
							<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<form :action="'{{ url('awesome_admin/permission/edit') }}/'+responseDataModal.edit.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="false" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'editModal')" ref="formHTML-editModal">
						
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

										<div v-if="loadingDataModal" class="text-center p-5">
											<div class="spinner-border text-primary mb-2" role="status">
												<span class="sr-only"></span>
											</div>

											<div class="h6">{{ t('Loading ...') }}</div>
										</div>

										<div v-else-if="responseStatusModal == 'failed'" class="ph-data-load-status" style="display: none">
											<div class="modal-body p-4 text-center text-danger h6 mb-0">
												@{{ responseMessageModal }}
											</div>
										</div>

										<div v-else>
											<div class="modal-header align-items-start">
												<div class="modal-title">
													<h5 class="mb-1">{{ t('Edit Permission') }}</h5>
													<div class="d-block text-secondary">{{ t('You can edit permission here') }}</div>
												</div>

												<a href="javascript:void(0)" class="text-secondary ms-auto" v-on:click="closeModal('ph-submit-data-permission-editModal', 'ph-submit-data-permission-editModal')" aria-label="Close"><i class="fal fa-times-circle fs-4"></i></a>
											</div>

											<div class="modal-body" id="ar-fetch-detail-multipledata-permission-editPermissionModal">
												<div class="bg-body-secondary p-3 rounded">
													<div class="form-group mb-3">
														<div class="fw-bold mb-2">{{ t('Permission Name') }}:</div>
														<input type="text" name="permission_name" class="form-control" v-model="responseDataModal.edit.name">
													</div>
												</div>						
											</div>

											<div class="modal-footer">
												<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-permission-editModal', 'ph-submit-data-permission-editModal')">{{ t('Cancel') }}</button>
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
						<div class="modal ph-modal-delete fade" id="ph-submit-data-permission-deleteModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
							<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
								<div class="modal-content">
									<form :action="'{{ url('awesome_admin/permission/delete') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'deleteModal')" ref="formHTML-deleteModal">
										
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
												<span class="visually-hidden">{{ t('Loading ...') }}</span>
											</div>

											<div class="h6 mt-2 mb-0">{{ t('Loading ...') }}</div>
										</div>

										<div v-else-if="responseStatusModal == 'failed'">
											<div class="modal-body p-4 text-center text-danger h6 mb-0">
												@{{ responseMessageModal }}
											</div>
										</div>

										<div v-else>
											<div class="modal-header d-none">
												<h5 class="modal-title">{{ t('Delete Permission') }}</h5>
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
														<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-permission-deleteModal', 'ph-submit-data-permission-deleteModal')">{{ t('No, keep it') }}</button>
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
	<script src="{{ url('assets/js/vue3/manage_permission/vueV3-manage-permission-2026.js?v=').time() }}"></script>
@endpushonce