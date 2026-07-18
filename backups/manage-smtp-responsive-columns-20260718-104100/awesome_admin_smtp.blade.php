@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('SMTP Settings') }}
@endsection

@section('content')
	<div>
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.smtp') }}
		</div>

		<div id="ph-app-data-smtp">
			
			<div class="ph-content rounded p-3 mb-3">
				<div class="row g-3 mb-3">
					<div class="col-md-6 d-flex align-items-center">
						<h4 class="mb-0">{{ t('SMTP Settings') }}</h4>
					</div>

					<div class="col-md-6 d-flex align-items-center justify-content-end">
						<a href="javascript:void(0)" class="btn ph-btn-theme font-size-inherit" v-on:click="openModdalAddSMTP"><i class="fas fa-plus fa-fw me-1"></i> {{ t('New SMTP') }}</a>
					</div>
				</div>

				<div class="row g-3">
					<div class="col-md-6 d-flex align-items-center">
						<div class="ph-fetch-detaildata-setservice" data-url="{{ url('awesome_admin/smtp/detailsetdata') }}"> 
							<div id="ph-form-app-data-setsmtpservice">
								<form action="{{ route('cms.admin.awesome_admin.smtp.update.service') }}" method="post" auto-refresh="false" auto-refresh-setservice="true" auto-reset="false" auto-block-button-mobile="true" custom-button-value="Save" ref="formHTMLsetsmtpservice" @submit.prevent="submitDataSMTP($event, 'setsmtpservice')">
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

									<div class="row gx-1">
										<div class="col-auto">
											<button type="button" class="btn btn-light font-size-inherit w-100">
												<i class="fas fa-envelope fa-fw"></i>

												<span v-if="loadingNameSetService" style="display: inline-block;">
													<div class="spinner-border text-primary ms-2" role="status" style="width: 1rem;height: 1rem;">
														<span class="sr-only"></span>
													</div>								
												</span>

												<span v-else class="ph-data-load-content-setservicesmtp ms-2" style="display: none">
													@{{ responseDetailDataSetService }}
												</span>
											</button>
										</div>

										<div class="col-auto">
											<select name="service_id" class="form-select font-size-inherit" aria-label="SelectServiceName">
												<option value="">Select SMTP Service</option>

												@foreach ($service_list as $key => $value)
													<option value="{{ $value->id }}">{{ $value->smtp_service }}</option>
												@endforeach
											</select>
										</div>

										<div class="col-auto">
											<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit w-sm-auto w-100" value="Save">
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="ph-fetch-listdata" data-url="{{ url('awesome_admin/smtp/listdata') }}">
				<div class="ph-content rounded ph-fetch-detaildata" data-url="{{ url('awesome_admin/smtp/detaildata/') }}">

					<div v-if="loading" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6 m-0">Loading ...</div>
					</div>

					<div v-else-if="responseStatus === 'failed'" class="ph-data-load-status text-center text-danger h5 p-5" style="display: none">
						@{{ responseMessage }}
					</div>

					<div v-else class="ph-data-load-content" style="display: none">
						<div v-if="loadingNextPage" class="text-center p-5">
							<div class="spinner-border text-primary mb-2" role="status">
								<span class="sr-only"></span>
							</div>

							<div class="h6 m-0">Loading ...</div>
						</div>

						<div v-else>							
							<div class="table-responsive rounded" id="table-responsive">
								<table class="table table-hover table-cs-vue3 rounded m-0" style="width: 100%;table-layout: auto">
									<thead>
										<tr class="table-tr-th-cs-vue3">
											<th scope="col" class="text-nowrap th-0">ID</th>
											<th scope="col" class="text-nowrap th-1">SMTP Service</th>
											<th scope="col" class="text-nowrap th-2">SMTP Host</th>
											<th scope="col" class="text-nowrap th-3">SMTP Username</th>
											<th scope="col" class="text-nowrap th-4">SMTP Password</th>
											<th scope="col" class="text-nowrap th-4">SMTP Port</th>
											<th scope="col" class="text-nowrap th-4">SMTP Encryption</th>
											<th scope="col" class="text-nowrap th-4">SMTP Sender Name</th>
											<th scope="col" class="text-nowrap th-4">SMTP Sender Address</th>
											<th scope="col" class="text-nowrap th-5">Options</th>
										</tr>
									</thead>

									<tbody is="transition-group" name="custom-classes-transition" enter-active-class="animate__animated animate__fadeIn animate__faster">
										<template v-for="(info, index) in responseData" v-bind:key="info.id">
											<tr class="text-nowrap">
												<td class="align-middle">#@{{ info.id }}</td>
												<td class="align-middle">@{{ info.smtp_service }}</td>
												<td class="align-middle">@{{ info.smtp_host }}</td>
												<td class="align-middle">@{{ info.smtp_username }}</td>
												<td class="align-middle">@{{ info.smtp_password }}</td>
												<td class="align-middle">@{{ info.smtp_port }}</td>
												<td class="align-middle">@{{ info.smtp_encryption }}</td>
												<td class="align-middle">@{{ info.smtp_sender_name }}</td>
												<td class="align-middle">@{{ info.smtp_sender_address }}</td>
												<td class="align-middle">
													<span class="me-3"><a href="javascript:void(0)" v-on:click="openModalEditSMTP($event, info.id)">Edit</a></span>
													<span><a href="javascript:void(0)" v-on:click="openModalDeleteSMTP($event, info.id)">Delete</a></span>
												</td>
											</tr>
										</template>
									</tbody>
								</table>
							</div>
						</div>

						<div class="p-3 d-flex">
							<paginate :page-count="pageCount" :page-range="pageRange" :click-handler="clickPaginate" :prev-text="'<i class=\'far fa-chevron-left\'></i>'" :next-text="'<i class=\'far fa-chevron-right\'></i>'" :container-class="'pagination ph-pagination ms-auto m-0 font-size-inherit'" v-model="getCurrentPage"></paginate>
						</div>
					</div>
				</div>

				<!-- Modal Add New SMTP -->
				<Teleport to="body">
					<div class="modal fade" id="modalAddNewSMTP" tabindex="-1" aria-labelledby="modalAddSMTPLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content" id="ph-form-app-data-add">
								<form action="{{ route('cms.admin.awesome_admin.smtp.store') }}" method="post" auto-refresh="true" auto-reset="true" auto-lock-button="true" custom-button-value="Create" ref="formHTMLadd" @submit.prevent="submitDataSMTP($event, 'add')">
									<div class="modal-header">
										<h5 class="modal-title" id="modalAddSMTPLabel">{{ t('Add New SMTP') }}</h5>
									</div>

									<div class="modal-body">
										<div class="ph-notice">
											<div aria-live="polite" aria-atomic="true" class="position-relative">
												<div class="toast-container position-fixed top-0 end-0 p-3">
													<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
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

										<div class="mb-3">
											<label for="smtpService" class="form-label">{{ t('SMTP Service') }}</label>
											<input type="text" name="smtp_service" class="form-control" id="smtpService">
										</div>

										<div class="mb-3">
											<label for="smtpHost" class="form-label">{{ t('SMTP Host') }}</label>
											<input type="text" name="smtp_host" class="form-control" id="smtpHost">
										</div>

										<div class="mb-3">
											<label for="smtpUser" class="form-label">{{ t('SMTP Username') }}</label>
											<input type="text" name="smtp_username" class="form-control" id="smtpUsername">
										</div>

										<div class="mb-3">
											<label for="smtpPassword" class="form-label">{{ t('SMTP Password') }}</label>
											<input type="text" name="smtp_password" class="form-control" id="smtpPassword">
										</div>

										<div class="mb-3">
											<label for="smtpPort" class="form-label">{{ t('SMTP Port') }}</label>
											<input type="text" name="smtp_port" class="form-control" id="smtpPort" >
										</div>

										<div class="mb-3">
											<label for="smtpEncryption" class="form-label">{{ t('SMTP Encryption') }}</label>							
											<select name="smtp_encryption" class="form-select" aria-label="Select SMTP Encryption" id="smtpEncryption">
												<option value="">{{ t('Select') }}</option>
												<option value="ssl">{{ t('SSL') }}</option>
												<option value="tls">{{ t('TLS') }}</option>
											</select>
										</div>

										<div class="mb-3">
											<label for="smtpSenderName" class="form-label">{{ t('SMTP Sender Name') }}</label>
											<input type="text" name="smtp_sender_name" class="form-control" id="smtpSenderName">
										</div>

										<div class="mb-3">
											<label for="smtpSenderAddress" class="form-label">{{ t('SMTP Sender Address') }}</label>
											<input type="text" name="smtp_sender_address" class="form-control" id="smtpSenderAddress">
										</div>
									</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalAddSMTP">{{ t('Cancel') }}</button>
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Create') }}</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</Teleport>

				<!-- Modal Edit SMTP -->
				<Teleport to="body">
					<div class="modal fade" id="modalEditSMTP" tabindex="-1" aria-labelledby="modalEditSMTPLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content" id="ph-form-app-data-edit">
								<form action="{{ route('cms.admin.awesome_admin.smtp.update') }}" method="post" auto-refresh="true" auto-reset="true" auto-lock-button="true" custom-button-value="Save" ref="formHTMLedit" @submit.prevent="submitDataSMTP($event, 'edit')">
									<div class="modal-header">
										<h5 class="modal-title" id="modalEditSMTPLabel">{{ t('Edit SMTP') }}</h5>
									</div>

									<div class="modal-body">
										<div class="ph-notice" v-cloak>
											<div aria-live="polite" aria-atomic="true" class="position-relative">
												<div class="toast-container position-fixed top-0 end-0 p-3">
													<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
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

										<div v-if="loadingDetail" class="text-center p-5">
											<div class="spinner-border text-primary mb-2" role="status">
												<span class="sr-only"></span>
											</div>

											<div class="h6 m-0">{{ t('Loading') }} ...</div>
										</div>

										<div v-else>
											<div class="mb-3">
												<label for="smtpService" class="form-label">{{ t('SMTP Service') }}</label>
												<input type="text" name="smtp_service" class="form-control" id="smtpService" :value="responseDetailData.smtp_service">
											</div>

											<div class="mb-3">
												<label for="smtpHost" class="form-label">{{ t('SMTP Host') }}</label>
												<input type="text" name="smtp_host" class="form-control" id="smtpHost" :value="responseDetailData.smtp_host">
											</div>

											<div class="mb-3">
												<label for="smtpUser" class="form-label">{{ t('SMTP Username') }}</label>
												<input type="text" name="smtp_username" class="form-control" id="smtpUser" :value="responseDetailData.smtp_username">
											</div>

											<div class="mb-3">
												<label for="smtpPassword" class="form-label">{{ t('SMTP Password') }}</label>
												<input type="text" name="smtp_password" class="form-control" id="smtpPassword" :value="responseDetailData.smtp_password">
											</div>

											<div class="mb-3">
												<label for="smtpPort" class="form-label">{{ t('SMTP Port') }}</label>
												<input type="text" name="smtp_port" class="form-control" id="smtpPort" :value="responseDetailData.smtp_port">
											</div>

											<div class="mb-3">
												<label for="smtpEncryption" class="form-label">{{ t('SMTP Encryption') }}</label>										
												<select name="smtp_encryption" class="form-select" v-model="responseDetailData.smtp_encryption" aria-label="Select SMTP Encryption" id="smtpEncryption">
													<option value="">{{ t('Select') }}</option>
													<option value="ssl">{{ t('SSL') }}</option>
													<option value="tls">{{ t('TLS') }}</option>
												</select>
											</div>

											<div class="mb-3">
												<label for="smtpSenderName" class="form-label">{{ t('SMTP Sender Name') }}</label>
												<input type="text" name="smtp_sender_name" class="form-control" id="smtpSenderName" :value="responseDetailData.smtp_sender_name">
											</div>

											<div class="mb-3">
												<label for="smtpSenderAddress" class="form-label">{{ t('SMTP Sender Address') }}</label>
												<input type="text" name="smtp_sender_address" class="form-control" id="smtpSenderAddress" :value="responseDetailData.smtp_sender_address">
											</div>
										</div>
									</div>

									<div class="modal-footer">
										<input type="hidden" name="data_id" id="data_id" value="">

										<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalEditSMTP">{{ t('Cancel') }}</button>
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Save') }}</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</Teleport>

				<!-- Modal Delete SMTP -->
				<Teleport to="body">
					<div class="modal fade" id="modalDeleteSMTP" tabindex="-1" aria-labelledby="modalDeleteSMTPLabel" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content" id="ph-form-app-data-delete">
								<form :action="'{{ url('awesome_admin/smtp/delete') }}/'+responseDetailData.id" method="post" auto-refresh="true" auto-lock-button="true" custom-button-value="<i class='fas fa-trash-alt fa-fw me-1'></i> Delete" ref="formHTMLdelete" @submit="submitDataSMTP($event, 'delete')">
									
									<div class="ph-notice" style="display: none">
										<div aria-live="polite" aria-atomic="true" class="position-relative">
											<div class="toast-container position-fixed top-0 end-0 p-3">
												<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
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

									<div class="modal-header d-none">
										<h5 class="modal-title" id="modalDeleteSMTPLabel">Delete SMTP Setting</h5>
									</div>

									<div class="modal-body pt-5 px-5 text-center">
										<div class="mb-4">
											<i class="far fa-trash-alt fs-1"></i>
										</div>

										<div class="h5">
											Do you really want to delete these data? <br/> This process cannot be undone.
										</div>
									</div>

									<div class="modal-footer pb-5 d-block border-0">
										<div class="row gx-2 justify-content-center">
											<div class="col-auto">	
												<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModalDeleteSMTP">Cancel</button>
											</div>

											<div class="col-auto">
												<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit"><i class="far fa-trash-alt fa-fw me-1"></i> Delete</button>
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
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_smtp/vueV3-manage-smtp-2026.js?v=').time() }}"></script>
@endpushonce