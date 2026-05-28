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
					<h4 class="mb-0">{{ t('Manage Roles') }} v2</h4>
				</div>

				<div class="col-md-6 d-flex align-items-center justify-content-end text-end">
					<a href="{{ url('awesome_admin/role/create/v2') }}" class="btn ph-btn-theme"><i class="fa fa-plus fa-fw me-1"></i> {{ t('New Role') }}</a>
				</div>
			</div>
		</div>

		<div class="ar-fetch-listdata-role" id="ar-fetch-listdata-role" data-url="{{ url('awesome_admin/role/listdata') }}">
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
				<div class="row g-4 mb-3">
					<div class="col-md-4" v-for="(item, index) in responseData">
						<div class="ph-content rounded p-4">

							<div class="d-flex align-items-center border-bottom pb-3 mb-3">
								<div class="flex-shrink-0">
									<i class="fad fa-user-secret fa-fw fa-2x" style="color: var(--ph-primary-link);"></i>
								</div>
							
								<div class="flex-grow-1 ms-3" style="min-width: 0">
									<h5 class="text-truncate mb-0">@{{ item.name }}</h5>
								</div>

								<div class="flex-grow-1 ms-3 text-center">
									<h5 class="mb-0">@{{ item.total_account }}</h5>
									<div v-if="item.total_account > 1">{{ t("User{1}s", "'") }}</div>
									<div v-else>{{ t("User") }}</div>
								</div>
							</div>

							<div class="row g-3">
								<div class="col-6 d-flex justify-content-start align-items-center">
									<a :href="'{{ url('awesome_admin/role/edit/v2') }}/'+item.id" class="btn ph-btn-theme">{{ t('Edit Role') }}</a>
								</div>

								<div class="col-6 d-flex justify-content-end align-items-center">
									<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-role-modalDelete" :class="'ar-fetch-detail-data-role-modalDelete-'+item.id" data-url="{{ url('awesome_admin/role/detaildata') }}" v-on:click="showModal('modalDelete', item.id)" class="btn ph-btn-theme-outline">{{ t('Delete Role') }}</a>
								</div>
							</div>

						</div>
					</div>					
				</div>
			</div>

			<!--- Delete Role Modal--->
			<Teleport to="body">
				<div class="modal ph-modal-delete fade" id="ph-submit-data-role-modalDelete" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<form :action="'{{ url('awesome_admin/role/delete/v2') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'modalDelete')" ref="formHTML-modalDelete">
								
								<div class="ph-notice">
									<div aria-live="polite" aria-atomic="true" class="position-relative">
										<div class="toast-container position-fixed top-0 end-0 p-3">
											<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
												<div :class="'toast-header pe-3 pt-3 pb-1 '+responseStatusToast+' border-0'">
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
										<h5 class="modal-title">{{ t('Delete Data') }}</h5>
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
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_role/vueV3-manage-role-2026.js?v=').time() }}"></script>
@endpushonce