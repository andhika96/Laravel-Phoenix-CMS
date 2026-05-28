@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage User') }}
@endsection

@section('content')
	<style>
	/* ============================================================
		Responsive Table - Column Priority (DTR Style, Pure Vue)
		============================================================ */

	/* Child / detail row */
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

	/* Detail items list */
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
		/*display: flex;
		align-items: center;
		gap: 0;*/
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
		/*width: 110px;
		min-width: 110px;*/
		color: #555;
		flex-shrink: 0;
		/*padding-right: 12px;*/
	}

	.ph-dtr-detail-data 
	{
		flex: 1;
		min-width: 0;
		overflow-wrap: break-word;
		word-break: break-word;
	}

	/* Expanded row highlight */
	tr.ph-dtr-expanded-row > td 
	{
		background-color: rgba(0, 0, 0, 0.015) !important;
	}

	/* Title cell as expand trigger */
	.ph-dtr-title-cell 
	{
		cursor: default;
	}

	.ph-dtr-title-cell.ph-dtr-title-expandable 
	{
		cursor: pointer;
		user-select: none;
	}

	/* Expand indicator on Title column */
	.ph-dtr-title-expand-icon 
	{
		display: inline-block;
		width: 0;
		height: 0;
		border-top: 5px solid transparent;
		border-bottom: 5px solid transparent;
		border-left: 8px solid rgba(0, 0, 0, 0.45);
		transition: transform 0.15s ease;
		vertical-align: middle;
		margin-right: 6px;
		flex-shrink: 0;
	}

	.ph-dtr-title-expand-icon.ph-dtr-open 
	{
		transform: rotate(90deg);
		border-left-color: rgba(0, 0, 0, 0.7);
	}

	.ph-dtr-title-text 
	{
		flex: 1;
		word-wrap: break-word;
		overflow-wrap: break-word;
	}

	/* Title cell inner layout */
	.ph-dtr-title-inner 
	{
		display: flex;
		align-items: flex-start;
		gap: 0;
	}

	/* Dark mode support */
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
			{{ Breadcrumbs::render('awesome_admin.user') }}
		</div>

		<div id="ph-app-list-user">
			<div class="ph-fetch-listdata" id="ph-form-submit-data" data-url="{{ url('awesome_admin/user/listdata') }}">
				<form action="{{ route('cms.admin.awesome_admin.user.bulk_update') }}" method="post" ref="formHTML" auto-refresh="true" auto-lock-button="false" auto-block-button-mobile="true" custom-button-value="Submit" @submit.prevent="submitData">

					<div class="ph-content rounded p-3 mb-3">

						<div class="row g-3 mb-3">
							<div class="col-md-6 d-flex align-items-center">
								<h4 class="mb-0">{{ t('Manage User') }}</h4>
							</div>

							<div class="col-md-6 d-flex align-items-center justify-content-end">
								<a href="javascript:void(0)" class="btn ph-btn-theme font-size-inherit" v-on:click="modalAddUser"><i class="fas fa-plus fa-fw me-1"></i> {{ t('Add New User') }}</a>
							</div>
						</div>

						<div class="row">
							<div class="col-md-8 d-flex align-items-center">
								<div class="row w-100 gx-2 gy-lg-2">
									<div class="col-md-4 d-flex align-items-center">
										<select name="changeStatus" class="form-select font-size-inherit" v-on:change="selectStatus($event, 'listData')">
											<option value="">{{ t('Change Status') }}</option>

											@foreach ($account_status as $acc_status)
												<option value="{{ $acc_status->id }}" @if ($acc_status->is_active == 1) disabled @endif>{{ $acc_status->name }} @if ($acc_status->is_active == 1) (coming soon) @endif</option>
											@endforeach

										</select>
									</div>

									<div class="col-md-4 d-flex align-items-center">
										<select name="changeRole" class="form-select font-size-inherit">
											<option value="">{{ t('Change Role') }}</option>
											
											@foreach ($roles as $role)
												<option value="{{ $role->name }}">{{ $role->name }}</option>
											@endforeach

										</select>
									</div>

									<div class="col-12 col-md-auto">
										<input type="hidden" name="mode" value="bulk_update">
										<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit w-sm-auto w-100" value="{{ t('Submit') }}">
									</div>

									<div v-if="responseSelectUserStatus.listData == 4" class="col-md-4 d-flex align-items-center" v-cloak>
										<div v-cloak class="w-100">
											<select name="suspended_until" class="form-select font-size-inherit">
												<option value="">{{ t('Select Day') }}</option>
												<option value="1">{{ t('1 Day') }}</option>
												<option value="3">{{ t('3 Days') }}</option>
												<option value="7">{{ t('7 Days') }}</option>
												<option value="14">{{ t('14 Days') }}</option>
												<option value="30">{{ t('30 Days') }}</option>
												<option value="60">{{ t('2 Months') }}</option>
												<option value="90">{{ t('3 Months') }}</option>
												<option value="180">{{ t('6 Months') }}</option>
											</select>
										</div>
									</div>
								</div>														
							</div>

							<div class="col-md-4">
								<input type="text" name="search_user" class="form-control font-size-inherit bg-body-tertiary" placeholder="{{ t('Search user by Email & Fullname') }}" v-model="getQuerySearchUser" @keyup="searchData">
							</div>
						</div>
					</div>

					<div class="ph-content rounded">
						<div v-if="loading" class="text-center p-5">
							<div class="spinner-border text-primary mb-2" role="status">
								<span class="sr-only"></span>
							</div>

							<div class="h6 m-0">{{ t('Loading') }} ...</div>
						</div>

						<div v-else-if="responseStatus === 'failed'" class="ph-data-load-status text-center text-danger h5 p-5" style="display: none">
							@{{ responseMessage }}
						</div>

						<div v-else class="ph-data-load-content" v-cloak>
							<div v-if="loadingNextPage" class="text-center p-5">
								<div class="spinner-border text-primary mb-2" role="status">
									<span class="sr-only"></span>
								</div>

								<div class="h6 m-0">{{ t('Loading') }} ...</div>
							</div>

							<div v-else>								
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

								<div class="rounded-top overflow-scroll w-100" id="ph-user-table-wrapper">
									<table class="table table-hover rounded w-100 mb-0">
										<thead>
											<tr>
												<th scope="col" data-col-idx="0" data-col-priority="0" v-show="!isColHidden(0)" style="width: 5%">
													<div class="d-flex justify-content-center">
														<div class="form-check m-0" style="min-height: 0 !important">
															<input type="checkbox" class="form-check-input" id="clickSelectAll" v-on:click="clickSelectAll">
															<label class="form-check-label" for="clickSelectAll"></label>
														</div>
													</div>
												</th>

												<th scope="col" data-col-idx="7" data-col-priority="7" v-show="!isColHidden(7)" class="text-nowrap">{{ t('ID') }}</th>
												<th scope="col" data-col-idx="2" data-col-priority="all" class="text-nowrap">{{ t('Fullname') }}</th>
												<th scope="col" data-col-idx="3" data-col-priority="3" v-show="!isColHidden(3)" class="text-nowrap">{{ t('Email Address') }}</th>
												<th scope="col" data-col-idx="4" data-col-priority="4" v-show="!isColHidden(4)" class="text-nowrap">{{ t('Roles') }}</th>
												<th scope="col" data-col-idx="5" data-col-priority="5" v-show="!isColHidden(5)" class="text-nowrap">{{ t('Status') }}</th>
												<th scope="col" data-col-idx="6" data-col-priority="6" v-show="!isColHidden(6)" class="text-nowrap">{{ t('Options') }}</th>
											</tr>
										</thead>

										<tbody>
											<template v-for="(info, index) in responseData" v-bind:key="info.id">
												<tr class="text-nowrap" :class="{'ph-dtr-expanded-row': responsiveExpandedRows[info.id]}">
													<td class="align-middle" data-col-idx="0" v-show="!isColHidden(0)">
														<div class="d-flex justify-content-center">
															<div class="form-check m-0">
																<input type="checkbox" name="getSelected[]" class="form-check-input checkids" v-on:click="clickCheckbox(info.id, $event)" v-bind:id="'user_'+info.id" v-bind:value="info.id">
																<label class="form-check-label" v-bind:for="'user_'+info.id"></label>
															</div>
														</div>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="7" v-show="!isColHidden(7)">#@{{ info.id }}</td>

													<td class="align-middle ph-dtr-title-cell text-nowrap" data-col-idx="2" :class="{'ph-dtr-title-expandable': responsiveHiddenCols.length > 0}" style="word-wrap: break-word;" @click="responsiveHiddenCols.length > 0 ? toggleExpandRow(info.id) : null">										
														<div class="ph-dtr-title-inner d-flex align-items-center">
															<span v-if="responsiveHiddenCols.length > 0" class="ph-dtr-title-expand-icon" :class="{'ph-dtr-open': responsiveExpandedRows[info.id]}"></span>
															<span class="ph-dtr-title-text">@{{ info.fullname }}</span>
														</div>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="3" v-show="!isColHidden(3)">@{{ info.email }}</td>

													<td class="align-middle text-nowrap" data-col-idx="4" v-show="!isColHidden(4)">
														<span v-for="(value, key) in info.roles" class="d-none">
															@{{ value.name }}
														</span>

														@{{ info.role_name }}
													</td>

													<td class="align-middle text-nowrap" data-col-idx="5" v-show="!isColHidden(5)">
														<span v-if="info.get_status !== null">
															<span v-html="info.status_formatted"></span>
														</span>

														<span v-else class="text-danger">
															{{ t('Unknown status, please re-sett status this account') }}
														</span>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="6" v-show="!isColHidden(6)">
														<a :href="'{{ url('awesome_admin/user/edit/\'+info.id+\'') }}'" class="d-none">{{ t('Edit') }}</a>
														<a href="javascript:void(0)" :class="'ph-fetch-userdata-'+info.id" data-url="{{ url('awesome_admin/user/getdata') }}" v-on:click="modalEditUser(info.id)">{{ t('Edit') }}</a>
													</td>
												</tr>

												{{-- Child / Detail Row — otomatis dari responsiveColDefs di JS --}}
												<tr v-if="responsiveExpandedRows[info.id] && responsiveHiddenCols.length > 0" class="ph-dtr-child-row">
													<td :colspan="responsiveVisibleColCount" class="ph-dtr-child-td" style="width: 0; min-width: 100%;">
														<ul class="ph-dtr-details list-unstyled">

															{{-- Checkbox: special case karena butuh Vue event binding --}}
															<li v-if="isColHidden(0)" class="ph-dtr-detail-item">
																<span class="ph-dtr-detail-title">Select</span>
																<span class="ph-dtr-detail-data">
																	<div class="form-check m-0">
																		<input type="checkbox" name="getSelected[]" class="form-check-input checkids"
																			v-on:click="clickCheckbox(info.id, $event)"
																			v-bind:id="'user_child_'+info.id"
																			v-bind:value="info.id">
																		<label class="form-check-label" v-bind:for="'user_child_'+info.id"></label>
																	</div>
																</span>
															</li>
															
															<template v-for="col in responsiveColDefs" :key="col.idx">
																<li v-if="isColHidden(col.idx)" class="ph-dtr-detail-item">
																	<div class="row g-3">
																		<div class="col-12 col-sm-auto col-md-auto ph-dtr-detail-title">
																			@{{ col.label }}:
																		</div>

																		<div class="col-12 col-sm-auto col-md-auto ph-dtr-detail-data" v-html="col.render(info)">
																		</div>
																	</div>

																	<!-- <span class="ph-dtr-detail-title">@{{ col.label }}</span>
																	<span class="ph-dtr-detail-data" v-html="col.render(info)"></span> -->
																</li>
															</template>

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
				</form>

				<!-- Modal Add New User -->
				<Teleport to="body">
					<div class="modal fade" id="modalAddNewUser" tabindex="-1" aria-labelledby="modalAddNewUserLabel" aria-hidden="true">
						<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
							<div class="modal-content" id="ph-form-submit-data-adduser">
								<form action="{{ route('cms.admin.awesome_admin.user.store') }}" method="post" class="needs-validation" custom-button-value="{{ t('Create') }}" auto-lock-button="true" auto-reset-form="true" auto-refresh-data="true" ref="formHTMLAddUser" @submit.prevent="submitDataAddUser">
									<div class="modal-header">
										<h5 class="modal-title" id="modalAddNewUserLabel">{{ t('Add New User') }}</h5>
									</div>

									<div class="modal-body">
										<div class="bg-body-secondary p-3 rounded">
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

											<div class="mb-3">
												<label for="userEmail" class="form-label ph-fetch-checkdata-email" data-url="{{ url('awesome_admin/user/checkdata') }}">{{ t('Email Address') }}</label>
												
												<div class="input-group rounded" :class="[ responseStatusCheckData.email == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.email == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
													<input type="text" name="account[email]" class="form-control" id="userEmailAddress" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : ''), loadingCheckData.email == true ? 'border-end-0 with-no-bg-image' : '' ]" v-model="getQueryCheckData.email" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkData('email')" required>
												
													<span v-if="loadingCheckData.email == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : '') ]">
														<div class="spinner-border spinner-border-sm text-primary" role="status">
															<span class="visually-hidden">{{ t('Loading') }} ...</span>
														</div>								
													</span>
												</div>

												<div v-if="responseStatusCheckData.email !== '' && loadingCheckData.email !== true" :class="[ responseStatusCheckData.email == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
													<span v-if="responseStatusCheckData.email == 'success'" class="d-none">
														<i class="far fa-check-circle fa-fw"></i>  
													</span>

													<span v-else-if="responseStatusCheckData.email == 'failed'" class="d-none">
														<i class="far fa-times fa-fw"></i> 
													</span>

													@{{ responseMessageCheckData.email }}
												</div>
											</div>

											<div class="mb-3">
												<label for="userName" class="form-label ph-fetch-checkdata-username" data-url="{{ url('awesome_admin/user/checkdata') }}">{{ t('Username') }}</label>
													
												<div class="input-group rounded" :class="[ responseStatusCheckData.username == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.username == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
													<input type="text" name="account[username]" class="form-control form-control-username" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : ''), loadingCheckData.username == true ? 'border-end-0 with-no-bg-image' : '' ]" id="userName" v-model="getQueryCheckData.username" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkData('username')" required>
													
													<span v-if="loadingCheckData.username == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : '') ]">
														<div class="spinner-border spinner-border-sm text-primary" role="status">
															<span class="visually-hidden">{{ t('Loading') }} ...</span>
														</div>								
													</span>

													<span class="input-group-text" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : '') ]">
														<input class="form-check-input mt-0" type="checkbox" id="autoFillUsername" v-on:click="autoFillUsername($event)">

														<label class="form-check-label small ms-2" for="autoFillUsername">
															{{ t('Autofill') }}
														</label>

														<input type="hidden" name="account[autofill_username]" id="autofill_username" :value="autoFill.username">
													</span>
												</div>

												<div v-if="responseStatusCheckData.username !== '' && loadingCheckData.username !== true" :class="[ responseStatusCheckData.username == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
													<span v-if="responseStatusCheckData.username == 'success'" class="d-none">
														<i class="far fa-check-circle fa-fw"></i>  
													</span>

													<span v-else-if="responseStatusCheckData.username == 'failed'" class="d-none">
														<i class="far fa-times fa-fw"></i> 
													</span>

													@{{ responseMessageCheckData.username }}
												</div>
											</div>

											<div class="mb-3">
												<label for="userFullName" class="form-label">{{ t('Fullname') }}</label>					
												<input type="text" name="account[fullname]" class="form-control border-end-0" id="userFullName" required>
											</div>

											<div class="mb-3">
												<label for="userRole" class="form-label">{{ t('Role') }}</label>					
											
												<select name="account[roles]" class="form-select font-size-inherit" id="userRole" required>
													<option value="">{{ t('Select') }}</option>
													
													@foreach ($roles as $role)
														<option value="{{ $role->name }}">{{ $role->name }}</option>
													@endforeach

												</select>
											</div>

											<div class="mb-3">
												<label for="userStatus" class="form-label">{{ t('Status') }}</label>					
											
												<select name="account[status]" class="form-select font-size-inherit" id="userStatus" v-on:change="selectStatus($event, 'formAdd')" required>
													<option value="">{{ t('Select') }}</option>
													
													@foreach ($account_status as $acc_status)
														<option value="{{ $acc_status->id }}" @if ($acc_status->is_active == 1) disabled @endif>{{ $acc_status->name }} @if ($acc_status->is_active == 1) (coming soon) @endif</option>
													@endforeach

												</select>
											</div>

											<div v-if="responseSelectUserStatus.formAdd == 4" class="mb-3" v-cloak>
												<label for="userSuspendedUntil" class="form-label">{{ t('Select Day') }}</label>

												<select name="account[suspended_until]" class="form-select font-size-inherit">
													<option value="">{{ t('Select') }}</option>
													<option value="1">{{ t('1 Day') }}</option>
													<option value="3">{{ t('3 Days') }}</option>
													<option value="7">{{ t('7 Days') }}</option>
													<option value="14">{{ t('14 Days') }}</option>
													<option value="30">{{ t('30 Days') }}</option>
													<option value="60">{{ t('2 Months') }}</option>
													<option value="90">{{ t('3 Months') }}</option>
													<option value="180">{{ t('6 Months') }}</option>
												</select>
											</div>

											<div class="mb-3">
												<div class="form-check">
													<input class="form-check-input" type="checkbox" id="showFormPassword" v-on:click="showPasswordForm($event)">

													<label class="form-check-label" for="showFormPassword">
														{{ t('Set Password Manually') }}
													</label>
												</div>
											</div>

											<div class="mb-3">
												<div v-if="showForm.passwordForm == false" class="border rounded p-3 text-bg-light">
													<div>{{ t('Automatic Set Password') }}</div>
													<div>{!! t('Default Password is {1}', '<strong>LaraPhoenixDev</strong>') !!}</div>
												</div>

												<div v-else-if="showForm.passwordForm == true">
													<label for="userPassword" class="form-label">{{ t('Password') }}</label>
													<input type="text" name="account[password]" class="form-control" id="userPassword">
												</div>

												<input type="hidden" name="account[autoset_password]" id="autoset_password" :value="showForm.autoSetPassword">
											</div>
										</div>
									</div>

									<div class="modal-footer">
										<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalAddUser">{{ t('Cancel') }}</button>
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Create') }}</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</Teleport>

				<!-- Modal Edit User -->
				<Teleport to="body">
					<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
						<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
							<div class="modal-content" id="ph-form-submit-data-edituser">
								<form action="{{ route('cms.admin.awesome_admin.user.update') }}" method="post" class="needs-validation" custom-button-value="{{ t('Save') }}" auto-lock-button="true" auto-reset-form="true" auto-refresh-data="true" ref="formHTMLEditUser" @submit.prevent="submitDataEditUser">
									<div v-if="loadingDetailData" class="text-center p-3 py-4">
										<div class="spinner-border" role="status">
											<span class="visually-hidden">{{ t('Loading ...') }}</span>
										</div>

										<div class="h6 mt-2 mb-0">{{ t('Loading ...') }}</div>
									</div>

									<div v-else-if="responseStatusDetailData == 'failed'">
										<div class="modal-body p-4 text-center text-danger h6 mb-0">
											@{{ responseMessageDetailData }}
										</div>
									</div>

									<div v-else>
										<div class="modal-header">
											<h5 class="modal-title" id="modalEditUserLabel">{{ t('Edit User') }}</h5>
										</div>

										<div class="modal-body">
											<div class="bg-body-secondary p-3 rounded">
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

												<div class="mb-3">
													<label for="userEmail" class="form-label ph-fetch-checkdatafor-update-email" data-url="{{ url('awesome_admin/user/checkdataforupdate') }}">{{ t('Email Address') }}</label>
													
													<div class="input-group rounded" :class="[ responseStatusCheckData.email == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.email == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
														<input type="text" name="account[email]" class="form-control" id="userEmailAddress" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : ''), loadingCheckData.email == true ? 'border-end-0 with-no-bg-image' : '' ]" v-model="responseDetailData.email" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkDataForUpdate('email')" required>
													
														<span v-if="loadingCheckData.email == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.email == 'success' ? 'is-valid' : (responseStatusCheckData.email == 'failed' ? 'is-invalid' : '') ]">
															<div class="spinner-border spinner-border-sm text-primary" role="status">
																<span class="visually-hidden">{{ t('Loading') }} ...</span>
															</div>								
														</span>
													</div>

													<div v-if="responseStatusCheckData.email !== '' && loadingCheckData.email !== true" :class="[ responseStatusCheckData.email == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
														<span v-if="responseStatusCheckData.email == 'success'" class="d-none">
															<i class="far fa-check-circle fa-fw"></i>  
														</span>

														<span v-else-if="responseStatusCheckData.email == 'failed'" class="d-none">
															<i class="far fa-times fa-fw"></i> 
														</span>

														@{{ responseMessageCheckData.email }}
													</div>
												</div>

												<div class="mb-3">
													<label for="userName" class="form-label ph-fetch-checkdatafor-update-username" data-url="{{ url('awesome_admin/user/checkdataforupdate') }}">{{ t('Username') }}</label>
														
													<div class="input-group rounded" :class="[ responseStatusCheckData.username == 'success' ? 'input-group-focus-is-valid is-valid' : (responseStatusCheckData.username == 'failed' ? 'input-group-focus-is-invalid is-invalid' : '')]">
														<input type="text" name="account[username]" class="form-control form-control-username" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : ''), loadingCheckData.username == true ? 'border-end-0 with-no-bg-image' : '' ]" id="userName" v-model="responseDetailData.username" v-on:focus="focusForm($event)" v-on:blur="blurForm" v-on:keyup="checkDataForUpdate('username')" required>
														
														<span v-if="loadingCheckData.username == true" class="input-group-text bg-white border-start-0" :class="[ responseStatusCheckData.username == 'success' ? 'is-valid' : (responseStatusCheckData.username == 'failed' ? 'is-invalid' : '') ]">
															<div class="spinner-border spinner-border-sm text-primary" role="status">
																<span class="visually-hidden">{{ t('Loading') }} ...</span>
															</div>								
														</span>
													</div>

													<div v-if="responseStatusCheckData.username !== '' && loadingCheckData.username !== true" :class="[ responseStatusCheckData.username == 'success' ? 'valid-feedback d-block mt-1' : 'invalid-feedback d-block mt-1' ]" v-cloak>
														<span v-if="responseStatusCheckData.username == 'success'" class="d-none">
															<i class="far fa-check-circle fa-fw"></i>  
														</span>

														<span v-else-if="responseStatusCheckData.username == 'failed'" class="d-none">
															<i class="far fa-times fa-fw"></i> 
														</span>

														@{{ responseMessageCheckData.username }}
													</div>
												</div>

												<div class="mb-3">
													<label for="userFullName" class="form-label">{{ t('Fullname') }}</label>					
													<input type="text" name="account[fullname]" class="form-control border-end-0" id="userFullName" v-model="responseDetailData.fullname" required>
												</div>

												<div class="mb-3">
													<label for="userRole" class="form-label">{{ t('Role') }}</label>					
												
													<select name="account[roles]" class="form-select font-size-inherit" v-model="responseDetailData.roles[0].name" id="userRole" required>
														<option value="">{{ t('Select') }}</option>
														
														@foreach ($roles as $role)
															<option value="{{ $role->name }}">{{ $role->name }}</option>
														@endforeach
													</select>
												</div>

												<div class="mb-3">
													<label for="userStatus" class="form-label">{{ t('Status') }}</label>					
												
													<select name="account[status]" class="form-select font-size-inherit" v-model="responseDetailData.status" id="userStatus" v-on:change="selectStatus($event, 'formEdit')" required>
														<option value="">{{ t('Select') }}</option>
														
														@foreach ($account_status as $acc_status)
															<option value="{{ $acc_status->id }}" @if ($acc_status->is_active == 1) disabled @endif>{{ $acc_status->name }} @if ($acc_status->is_active == 1) (coming soon) @endif</option>
														@endforeach
													</select>
												</div>

												<div v-if="responseSelectUserStatus.formEdit == 4" class="mb-3" v-cloak>
													<label for="userSuspendedUntil" class="form-label">{{ t('Select Day') }}</label>

													<select name="account[suspended_until]" class="form-select font-size-inherit">
														<option value="">{{ t('Select') }}</option>
														<option value="1">{{ t('1 Day') }}</option>
														<option value="3">{{ t('3 Days') }}</option>
														<option value="7">{{ t('7 Days') }}</option>
														<option value="14">{{ t('14 Days') }}</option>
														<option value="30">{{ t('30 Days') }}</option>
														<option value="60">{{ t('2 Months') }}</option>
														<option value="90">{{ t('3 Months') }}</option>
														<option value="180">{{ t('6 Months') }}</option>
													</select>
												</div>

												<div class="mb-3">
													<label for="userPassword" class="form-label">{{ t('Password') }}</label>
													<input type="text" name="account[password]" class="form-control" id="userPassword">
												</div>
											</div>
										</div>

										<div class="modal-footer">
											<input type="hidden" name="user_id" class="user_id">

											<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalEditUser">{{ t('Cancel') }}</button>
											<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Save') }}</button>
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

@push('js')
	<script src="{{ url('assets/js/vue3/manage_user/vueV3-manage-user-2026.js?v=').time() }}"></script>
@endpush