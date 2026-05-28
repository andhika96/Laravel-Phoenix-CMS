@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Article') }}
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
		display: flex;
		align-items: center;
		gap: 0;
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
		width: 110px;
		min-width: 110px;
		color: #555;
		flex-shrink: 0;
		padding-right: 12px;
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
		<div id="ph-app-manage-article">
			<div id="ph-form-submit-data" class="ph-fetch-listdata-article" data-url="{{ url('manage_article/listdata') }}">
				<form action="{{ route('cms.core.manage_article.bulk_update') }}" method="post" ref="formHTML" auto-refresh="true" auto-lock-button="false" auto-block-button-mobile="true" custom-button-value="Submit" @submit.prevent="submitData">

					<div class="ph-content rounded p-3 mb-3">
						<div class="row g-3">
							<div class="col-md-7 d-flex align-items-center">
								<h4 class="mb-0">{{ t('Manage Article') }}</h4>
							</div>

							<div class="col-md-5 d-flex align-items-center justify-content-end">
								<div class="row gx-0 gy-3 g-lg-3 w-100">
									<div class="col-md d-flex align-items-center">
										<div class="input-group rounded">
											<input type="text" name="search_article" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" class="form-control form-control-username font-size-inherit bg-body-tertiary" placeholder="{{ t('Search article by Title') }}" v-model="getQuerySearchUser" v-on:focus="focusForm($event)" v-on:blur="blurForm" @keyup="searchData">
											<button v-if="showButtonRemoveQuerySearch == true" v-on:click="removeQuerySearch" class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04" v-cloak><i class="fas fa-times fa-fw"></i></button>
										</div>
									</div>

									<div class="col-md-auto d-flex align-items-center">
										<a href="{{ url('manage_article/add') }}" class="btn ph-btn-theme font-size-inherit"><i class="fas fa-plus fa-fw me-1"></i> {{ t('Add Post') }}</a>
										<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-category-modalRead" class="btn ph-btn-theme-outline ms-2 ar-fetch-detail-data-category-modalRead-categoryList" data-url="{{ url('manage_article/listdata/category') }}" v-on:click="showModalCategory('modalRead', 'categoryList', false)">{{ t('Category List') }}</a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="ph-content rounded p-3 mb-3">
						<div class="row g-3">
							<div class="col-md-3 d-flex align-items-center">
								<div class="row w-100 gy-3 gx-0 gx-lg-2 gy-lg-2">
									<div class="col-md d-flex align-items-center">
										<div class="w-100">
											<label class="form-label">{{ t('Change Status') }}</label>
											<select name="changeStatus" class="form-select bg-body-tertiary font-size-inherit" v-on:change="selectStatus($event, 'listData')">
												<option value="">{{ t('All') }}</option>

												@foreach ($article_status as $status)
													<option value="{{ $status->id }}">{{ $status->name }}</option>
												@endforeach

											</select>
										</div>
									</div>

									<div class="col-12 col-md-auto d-flex align-items-end">
										<input type="hidden" name="mode" value="bulk_update">
										<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit w-sm-auto w-100" value="{{ t('Submit') }}">
									</div>
								</div>														
							</div>

							<div class="col-md-3">
								<label class="form-label">{{ t('Filter By Status') }}</label>
								<select name="filter_by_status" class="form-select bg-body-tertiary font-size-inherit" v-on:change="filterByStatus($event)">
									<option value="">{{ t('All') }}</option>

									@foreach ($article_status as $status)
										<option value="{{ $status->code_name }}">{{ $status->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-md-3">
								<label class="form-label">{{ t('Filter By Category') }}</label>
								<select name="filter_by_category" class="form-select bg-body-tertiary font-size-inherit" v-on:change="filterByCategory($event)">
									<option value="">{{ t('All') }}</option>

									@foreach ($categories as $category)
										<option value="{{ $category->id }}">{{ $category->name }}</option>
									@endforeach
								</select>
							</div>

							<div class="col-md-3">
								<label class="form-label">{{ t('Filter By Scheduled') }}</label>
								<select name="filter_by_scheduled" class="form-select bg-body-tertiary font-size-inherit" v-on:change="filterByScheduled($event)">
									<option value="">{{ t('All') }}</option>

									<option value="true">{{ t('Scheduled') }}</option>
									<option value="false">{{ t('No Scheduled') }}</option>
								</select>
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

								<div class="rounded-top overflow-scroll w-100" id="ph-article-table-wrapper">
									<table class="table table-hover rounded w-100 mb-0">
										<thead class="rounded-top">
											<tr>
												<th scope="col" data-col-idx="0" data-col-priority="0" v-show="!isColHidden(0)">
													<div class="d-flex justify-content-center">
														<div class="form-check m-0" style="min-height: 0 !important">
															<input type="checkbox" class="form-check-input" id="clickSelectAll" v-on:click="clickSelectAll">
															<label class="form-check-label" for="clickSelectAll"></label>
														</div>
													</div>
												</th>

												<th scope="col" data-col-idx="1" data-col-priority="all" class="text-nowrap">{{ t('Title') }}</th>
												<th scope="col" data-col-idx="2" data-col-priority="2" v-show="!isColHidden(2)" class="text-nowrap">{{ t('Author') }}</th>
												<th scope="col" data-col-idx="3" data-col-priority="3" v-show="!isColHidden(3)" class="text-nowrap">{{ t('Scheduled') }}</th>
												<th scope="col" data-col-idx="4" data-col-priority="4" v-show="!isColHidden(4)" class="text-nowrap">{{ t('Status') }}</th>
												<th scope="col" data-col-idx="5" data-col-priority="5" v-show="!isColHidden(5)" class="text-nowrap">{{ t('Date') }}</th>
												<th scope="col" data-col-idx="6" data-col-priority="6" v-show="!isColHidden(6)" class="text-nowrap">{{ t('Options') }}</th>
											</tr>
										</thead>

										<tbody>
											<template v-for="(info, index) in responseData" v-bind:key="info.id">
												<tr :class="{'ph-dtr-expanded-row': responsiveExpandedRows[info.id]}">
													<td class="align-middle" data-col-idx="0" v-show="!isColHidden(0)">
														<div class="d-flex justify-content-center">
															<div class="form-check m-0">
																<input type="checkbox" name="getSelected[]" class="form-check-input checkids" v-on:click="clickCheckbox(info.id, $event)" v-bind:id="'user_'+info.id" v-bind:value="info.id">
																<label class="form-check-label" v-bind:for="'user_'+info.id"></label>
															</div>
														</div>
													</td>

													<td class="align-middle ph-dtr-title-cell text-nowrap" data-col-idx="1" :class="{'ph-dtr-title-expandable': responsiveHiddenCols.length > 0}" style="word-wrap: break-word;" @click="responsiveHiddenCols.length > 0 ? toggleExpandRow(info.id) : null">										
														<div class="ph-dtr-title-inner d-flex align-items-center">
															<span v-if="responsiveHiddenCols.length > 0" class="ph-dtr-title-expand-icon" :class="{'ph-dtr-open': responsiveExpandedRows[info.id]}"></span>
															<span class="ph-dtr-title-text">@{{ info.title }}</span>
														</div>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="2" v-show="!isColHidden(2)">@{{ info.author }}</td>
													
													<td class="align-middle text-nowrap" data-col-idx="3" v-show="!isColHidden(3)">
														<span v-if="info.scheduled == 'true'" class="badge text-bg-warning">{{ t('Scheduled') }}</span>
														<span v-else class="badge text-bg-secondary">{{ t('No Scheduled') }}</span>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="4" v-show="!isColHidden(4)">
														<span v-if="info.status !== null">
															<span v-html="info.status_formatted"></span>
														</span>

														<span v-else class="badge text-bg-danger">
															{{ t('Unknown') }}
														</span>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="5" v-show="!isColHidden(5)">
														<div class="mb-1">@{{ info.created_at_readable }}</div>
														<div class="form-text">(@{{ info.created_at_readforhuman }})</div>
													</td>

													<td class="align-middle text-nowrap" data-col-idx="6" v-show="!isColHidden(6)">
														<a :href="'{{ url('manage_article/edit/\'+info.id+\'') }}'" class="btn btn-sm ph-btn-theme-outline py-2 px-3 me-2" style="font-size: .64rem !important;"><i class="fas fa-pencil-alt fa-fw"></i></a>
														<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-article-modalDelete" :class="'ar-fetch-detail-data-article-modalDelete-'+info.id" data-url="{{ url('manage_article/detaildata') }}" v-on:click="showModal('modalDelete', info.id, true)" class="btn btn-sm btn-outline-danger py-2 px-3" style="font-size: .64rem !important;"><i class="fas fa-trash fa-fw"></i></a>
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
																	<span class="ph-dtr-detail-title">@{{ col.label }}</span>
																	<span class="ph-dtr-detail-data" v-html="col.render(info)"></span>
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
								<div class="row gx-lg-0 w-100">
									<div class="col-md-6 d-flex align-items-center justify-content-start">
										{{ t('Total Data') }}: @{{ getTotalData.toLocaleString('en-US', { maximumFractionDigits: 0 }) }}
									</div>

									<div class="col-md-6 d-flex align-items-center justify-content-end">
										<paginate :page-count="pageCount" :page-range="pageRange" :click-handler="clickPaginate" :prev-text="'<i class=\'far fa-chevron-left\'></i>'" :next-text="'<i class=\'far fa-chevron-right\'></i>'" :container-class="'pagination ph-pagination ms-auto m-0 font-size-inherit'" v-model="getCurrentPage"></paginate>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>

			<!-- Delete Article Modal -->
			<Teleport to="body">
				<div class="modal ph-modal-delete fade" id="ph-submit-data-article-modalDelete" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<form :action="'{{ url('manage_article/delete') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'modalDelete')" ref="formHTML-modalDelete">
								
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
												<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-article-modalDelete', 'ph-submit-data-article-modalDelete')">{{ t('No, keep it') }}</button>
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

			<!-- List Categories Modal -->
			<Teleport to="body">
				<div class="modal fade" id="ph-submit-data-category-modalRead" tabindex="-1" aria-labelledby="modalReadLabel" aria-hidden="true">
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
										<h5 class="mb-1">{{ t('Category List') }}</h5>
										<div class="d-block text-secondary">{{ t('You can manage your categories here') }}</div>
									</div>

									<a href="javascript:void(0)" class="text-secondary ms-auto" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle fs-4"></i></a>
								</div>

								<div class="modal-body">
									<div class="bg-body-secondary p-3 mb-3 rounded">
										<a href="javascript:void(0)" class="btn ph-btn-theme-outline" data-bs-target="#ph-submit-data-category-modalCreate" data-bs-toggle="modal">{{ t('Add New Category') }}</a>
									</div>

									<div class="bg-body-secondary p-3 rounded">
										<div class="fw-bold mb-2">{{ t('Category List') }}:</div>

										<ul class="list-group list-group-flush">
											<li v-for="(item, index) in responseDataModal.read" class="list-group-item bg-transparent d-inline-flex d-md-flex ps-0">
												<i class="fas fa-users-class fa-fw me-2"></i> @{{ item.name }}

												<span class="ms-auto">
													<a href="javascript:void(0)" v-if="item.code !== 'uncategorized'" data-bs-toggle="modal" data-bs-target="#ph-submit-data-category-modalUpdate" :class="'ar-fetch-detail-data-category-modalUpdate-'+item.id" data-url="{{ url('manage_article/detaildata/category') }}" v-on:click="showModalCategory('modalUpdate', item.id, true)">{{ t('Edit') }}</a>
													<a href="javascript:void(0)" v-if="item.code !== 'uncategorized'" data-bs-toggle="modal" data-bs-target="#ph-submit-data-category-modalDelete" :class="'ms-2 ar-fetch-detail-data-category-modalDelete-'+item.id" data-url="{{ url('manage_article/checkdata/category') }}" v-on:click="showModalCategory('modalDelete', item.id, true)">{{ t('Delete') }}</a>
												</span>
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

			<!-- Add New Category Modal -->
			<Teleport to="body">
				<div class="modal fade" id="ph-submit-data-category-modalCreate" tabindex="-1" aria-labelledby="modalAddNewCategoryLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<form action="{{ route('cms.core.manage_article.store.category') }}" method="post" class="needs-validation" custom-button-value="{{ t('Create') }}" auto-lock-button="false" auto-reset-form="true" auto-refresh-data="true" ref="formHTML-modalCreate" @submit.prevent="submitDataModalCategory($event, 'modalCreate')">
								<div class="modal-header">
									<h5 class="modal-title" id="modalAddNewCategoryLabel">{{ t('Add New Category') }}</h5>
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
											<label for="newCategoryName" class="form-label">{{ t('Category Name') }}</label>					
											<input type="text" name="category_name" class="form-control border-end-0" id="newCategoryName" required>
										</div>

										<div class="mb-3">
											<label for="newCategoryStatus" class="form-label">{{ t('Category Status') }}</label>

											<select name="category_status" class="form-select font-size-inherit" id="newCategoryStatus">
												<option value="">{{ t('Select') }}</option>
												<option value="active">{{ t('Active') }}</option>
												<option value="inactive">{{ t('Inactive') }}</option>
											</select>
										</div>
									</div>
								</div>

								<div class="modal-footer">									
									<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalCategory('ph-submit-data-category-modalCreate', 'ph-submit-data-category-modalCreate', true, 'ph-submit-data-category-modalRead', 'categoryList')">{{ t('Cancel') }}</button>
									<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Create') }}</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</Teleport>

			<!-- Edit Category Modal -->
			<Teleport to="body">
				<div class="modal fade" id="ph-submit-data-category-modalUpdate" tabindex="-1" aria-labelledby="modalUpdateCategoryLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<form action="{{ route('cms.core.manage_article.update.category') }}" method="post" class="needs-validation" custom-button-value="{{ t('Save') }}" auto-lock-button="false" auto-reset-form="true" auto-refresh-data="true" ref="formHTML-modalUpdate" @submit.prevent="submitDataModalCategory($event, 'modalUpdate')">
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
									<div class="modal-header">
										<h5 class="modal-title" id="modalEditCategoryLabel">{{ t('Edit Category') }}</h5>
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
												<label for="editCategoryName" class="form-label">{{ t('Category Name') }}</label>					
												<input type="text" name="category_name" class="form-control border-end-0" id="editCategoryName" v-model="responseDataModal.update.name" required>
											</div>

											<div class="mb-3">
												<label for="editCategoryStatus" class="form-label">{{ t('Category Status') }}</label>

												<select name="category_status" class="form-select font-size-inherit" id="editCategoryStatus" v-model="responseDataModal.update.status">
													<option value="">{{ t('Select') }}</option>
													<option value="active">{{ t('Active') }}</option>
													<option value="inactive">{{ t('Inactive') }}</option>
												</select>
											</div>
										</div>
									</div>

									<div class="modal-footer">				
										<input type="hidden" name="idOrSlug" :value="responseDataModal.update.id">					
										<button type="button" class="btn btn-secondary btn-cancel-submit font-size-inherit me-2" v-on:click="closeModalCategory('ph-submit-data-category-modalUpdate', 'ph-submit-data-category-modalUpdate', true, 'ph-submit-data-category-modalRead', 'categoryList')">{{ t('Cancel') }}</button>
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Save') }}</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</Teleport>

			<!-- Delete Category Modal -->
			<Teleport to="body">
				<div class="modal fade" id="ph-submit-data-category-modalDelete" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							<form :action="'{{ url('manage_article/delete/category') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" ref="formHTML-modalDelete" @submit="submitDataModalCategory($event, 'modalDelete')">
								
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

										<div v-html="responseMessageModal">
										</div>
									</div>

									<div class="modal-footer pb-5 d-block border-0">
										<div class="row gx-2 justify-content-center">
											<div class="col-auto">	
												<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModalCategory('ph-submit-data-category-modalDelete', 'ph-submit-data-category-modalDelete', true, 'ph-submit-data-category-modalRead', 'categoryList')">{{ t('No, keep it') }}</button>
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

@push('js')
	<script src="{{ url('assets/plugins/ckeditor5/build/ckeditor.js?v=0.0.1') }}"></script>
	<script src="{{ url('assets/plugins/ckfinder/ckfinder.js?v=0.0.1') }}"></script>
	<script src="{{ url('assets/js/vue3/manage_article/vueV3-manage-article-2026.js?v=').time() }}"></script>
@endpush