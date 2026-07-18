@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('SMTP Settings') }}
@endsection

@section('content')
	<style>
	/* ============================================================
		Responsive Table - Column Priority (DTR Style, Pure Vue)
		============================================================ */

	.ph-dtr-child-row > td
	{
		background-color: rgba(0, 0, 0, 0.025) !important;
		border-top: none !important;
	}

	.ph-dtr-child-td
	{
		padding: 0 !important;
		overflow: hidden;
		max-width: 0;
	}

	.ph-dtr-details
	{
		margin: 0;
		padding: 0;
		width: 100%;
		box-sizing: border-box;
		overflow: hidden;
	}

	.ph-dtr-detail-item
	{
		padding: 8px 12px;
		font-size: 0.875rem;
		border-bottom: 1px solid rgba(0, 0, 0, 0.06);
	}

	.ph-dtr-detail-item:last-child
	{
		border-bottom: none;
	}

	.ph-dtr-detail-title
	{
		font-weight: 600;
		color: #555;
	}

	.ph-dtr-detail-data
	{
		min-width: 0;
		overflow-wrap: break-word;
		word-break: break-word;
	}

	tr.ph-dtr-expanded-row > td
	{
		background-color: rgba(0, 0, 0, 0.015) !important;
	}

	.ph-dtr-title-cell
	{
		cursor: default;
	}

	.ph-dtr-title-cell.ph-dtr-title-expandable
	{
		cursor: pointer;
		user-select: none;
	}

	.ph-dtr-title-expand-icon
	{
		display: inline-block;
		width: 0;
		height: 0;
		border-top: 5px solid transparent;
		border-bottom: 5px solid transparent;
		border-left: 8px solid rgba(0, 0, 0, 0.45);
		transition: transform 0.15s ease;
		margin-right: 6px;
		flex-shrink: 0;
	}

	.ph-dtr-title-expand-icon.ph-dtr-open
	{
		transform: rotate(90deg);
		border-left-color: rgba(0, 0, 0, 0.7);
	}

	.ph-dtr-title-inner
	{
		display: flex;
		align-items: center;
	}

	.ph-dtr-title-text
	{
		flex: 1;
		overflow-wrap: break-word;
	}

	[data-bs-theme="dark"] .ph-dtr-title-expand-icon
	{
		border-left-color: rgba(255, 255, 255, 0.55);
	}

	[data-bs-theme="dark"] .ph-dtr-title-expand-icon.ph-dtr-open
	{
		border-left-color: rgba(255, 255, 255, 0.85);
	}

	[data-bs-theme="dark"] .ph-dtr-child-row > td
	{
		background-color: rgba(255, 255, 255, 0.03) !important;
	}

	[data-bs-theme="dark"] .ph-dtr-detail-title
	{
		color: #aaa;
	}

	[data-bs-theme="dark"] .ph-dtr-detail-item
	{
		border-bottom-color: rgba(255, 255, 255, 0.07);
	}

	[data-bs-theme="dark"] tr.ph-dtr-expanded-row > td
	{
		background-color: rgba(255, 255, 255, 0.02) !important;
	}
	</style>

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
							<div class="rounded-top overflow-scroll w-100" id="ph-smtp-table-wrapper">
								<table class="table table-hover table-cs-vue3 rounded w-100 mb-0">
									<thead>
										<tr class="table-tr-th-cs-vue3">
											<th scope="col" data-col-idx="0" data-col-priority="1" v-show="!isColHidden(0)" class="text-nowrap">ID</th>
											<th scope="col" data-col-idx="1" data-col-priority="all" class="text-nowrap">SMTP Service</th>
											<th scope="col" data-col-idx="2" data-col-priority="2" v-show="!isColHidden(2)" class="text-nowrap">SMTP Host</th>
											<th scope="col" data-col-idx="3" data-col-priority="3" v-show="!isColHidden(3)" class="text-nowrap">SMTP Username</th>
											<th scope="col" data-col-idx="4" data-col-priority="4" v-show="!isColHidden(4)" class="text-nowrap">SMTP Password</th>
											<th scope="col" data-col-idx="5" data-col-priority="5" v-show="!isColHidden(5)" class="text-nowrap">SMTP Port</th>
											<th scope="col" data-col-idx="6" data-col-priority="6" v-show="!isColHidden(6)" class="text-nowrap">SMTP Encryption</th>
											<th scope="col" data-col-idx="7" data-col-priority="7" v-show="!isColHidden(7)" class="text-nowrap">SMTP Sender Name</th>
											<th scope="col" data-col-idx="8" data-col-priority="8" v-show="!isColHidden(8)" class="text-nowrap">SMTP Sender Address</th>
											<th scope="col" data-col-idx="9" data-col-priority="9" v-show="!isColHidden(9)" class="text-nowrap">Options</th>
										</tr>
									</thead>

									<tbody is="transition-group" name="custom-classes-transition" enter-active-class="animate__animated animate__fadeIn animate__faster">
										<template v-for="(info, index) in responseData" v-bind:key="info.id">
											<tr class="text-nowrap" :class="{'ph-dtr-expanded-row': responsiveExpandedRows[info.id]}">
												<td class="align-middle" data-col-idx="0" v-show="!isColHidden(0)">#@{{ info.id }}</td>
												<td class="align-middle ph-dtr-title-cell" data-col-idx="1" :class="{'ph-dtr-title-expandable': responsiveHiddenCols.length > 0}" @click="responsiveHiddenCols.length > 0 ? toggleExpandRow(info.id) : null">
													<div class="ph-dtr-title-inner">
														<span v-if="responsiveHiddenCols.length > 0" class="ph-dtr-title-expand-icon" :class="{'ph-dtr-open': responsiveExpandedRows[info.id]}"></span>
														<span class="ph-dtr-title-text">@{{ info.smtp_service }}</span>
													</div>
												</td>
												<td class="align-middle" data-col-idx="2" v-show="!isColHidden(2)">@{{ info.smtp_host }}</td>
												<td class="align-middle" data-col-idx="3" v-show="!isColHidden(3)">@{{ info.smtp_username }}</td>
												<td class="align-middle" data-col-idx="4" v-show="!isColHidden(4)">@{{ info.smtp_password }}</td>
												<td class="align-middle" data-col-idx="5" v-show="!isColHidden(5)">@{{ info.smtp_port }}</td>
												<td class="align-middle" data-col-idx="6" v-show="!isColHidden(6)">@{{ info.smtp_encryption }}</td>
												<td class="align-middle" data-col-idx="7" v-show="!isColHidden(7)">@{{ info.smtp_sender_name }}</td>
												<td class="align-middle" data-col-idx="8" v-show="!isColHidden(8)">@{{ info.smtp_sender_address }}</td>
												<td class="align-middle" data-col-idx="9" v-show="!isColHidden(9)">
													<span class="me-3"><a href="javascript:void(0)" v-on:click="openModalEditSMTP($event, info.id)">Edit</a></span>
													<span><a href="javascript:void(0)" v-on:click="openModalDeleteSMTP($event, info.id)">Delete</a></span>
												</td>
											</tr>

											<tr v-if="responsiveExpandedRows[info.id] && responsiveHiddenCols.length > 0" class="ph-dtr-child-row">
												<td :colspan="responsiveVisibleColCount" class="ph-dtr-child-td" style="width: 0; min-width: 100%;">
													<ul class="ph-dtr-details list-unstyled">
														<li v-if="isColHidden(0)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">ID:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">#@{{ info.id }}</div>
															</div>
														</li>
														<li v-if="isColHidden(2)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Host:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_host }}</div>
															</div>
														</li>
														<li v-if="isColHidden(3)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Username:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_username }}</div>
															</div>
														</li>
														<li v-if="isColHidden(4)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Password:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_password }}</div>
															</div>
														</li>
														<li v-if="isColHidden(5)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Port:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_port }}</div>
															</div>
														</li>
														<li v-if="isColHidden(6)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Encryption:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_encryption }}</div>
															</div>
														</li>
														<li v-if="isColHidden(7)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Sender Name:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_sender_name }}</div>
															</div>
														</li>
														<li v-if="isColHidden(8)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">SMTP Sender Address:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">@{{ info.smtp_sender_address }}</div>
															</div>
														</li>
														<li v-if="isColHidden(9)" class="ph-dtr-detail-item">
															<div class="row g-2">
																<div class="col-12 col-sm-4 ph-dtr-detail-title">Options:</div>
																<div class="col-12 col-sm-8 ph-dtr-detail-data">
																	<span class="me-3"><a href="javascript:void(0)" v-on:click="openModalEditSMTP($event, info.id)">Edit</a></span>
																	<span><a href="javascript:void(0)" v-on:click="openModalDeleteSMTP($event, info.id)">Delete</a></span>
																</div>
															</div>
														</li>
													</ul>
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
