@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Cover Image') }}
@endsection

@push('css')
	<!-- Datepicker CSS -->
	<link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-datepicker/css/vue-datepicker-11.0.3.css') }}">
@endpush('css')

@section('content')
	<div>
		<div id="ph-app-manage-coverimage">
			<div id="ph-form-submit-data" class="ph-fetch-listdata-coverimage" data-url="{{ url('manage_coverimage/listdata') }}">
				<form action="#!" method="post" ref="formHTML" auto-refresh="false" auto-lock-button="false" auto-block-button-mobile="true" custom-button-value="Submit" @submit.prevent="submitData">

					<div class="ph-content rounded p-3 mb-3">
						<div class="row g-3">
							<div class="col-md-7 d-flex align-items-center">
								<h4 class="mb-0">{{ t('Manage Cover Image') }}</h4>
							</div>

							<div class="col-md-5 d-flex align-items-center justify-content-end">
								<div class="row gx-0 gy-3 g-lg-3 w-100">
									<div class="col-md d-flex align-items-center">
										<div class="input-group rounded">
											<input type="text" name="search_coverimage" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" class="form-control form-control-username font-size-inherit bg-body-tertiary" placeholder="{{ t('Search Cover Image by Page Name') }}" v-model="getQuerySearchUser" v-on:focus="focusForm($event)" v-on:blur="blurForm" @keyup="searchData">
											<button v-if="showButtonRemoveQuerySearch == true" v-on:click="removeQuerySearch" class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04" v-cloak><i class="fas fa-times fa-fw"></i></button>
										</div>
									</div>

									<div class="col-md-auto d-flex align-items-center">
										<a href="{{ url('manage_coverimage/add') }}" class="btn ph-btn-theme font-size-inherit"><i class="fas fa-plus fa-fw me-1"></i> {{ t('Add Cover Image') }}</a>
									</div>
								</div>
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

						<div v-else-if="responseStatus === 'failed'" class="ph-data-load-status text-center text-danger h5 p-5" v-cloak>
							@{{ responseMessage }}
						</div>

						<div v-else-if="responseStatus === 'success'" class="ph-data-load-content" v-cloak>
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

								<div class="table-responsive rounded-top">
									<table class="table table-hover rounded mb-0" style="table-layout: fixed;">
										<thead class="table-light rounded-top">
											<tr>
												<th scope="col" style="width: 5%">
													<div class="d-flex justify-content-center">
														<div class="form-check m-0" style="min-height: 0 !important">
															<input type="checkbox" class="form-check-input" id="clickSelectAll" v-on:click="clickSelectAll">
															<label class="form-check-label" for="clickSelectAll"></label>
														</div>
													</div>
												</th>

												<th scope="col" style="width: 30%">{{ t('Page Name') }}</th>
												<th scope="col" style="width: 18%">{{ t('Page URI') }}</th>
												<th scope="col" style="width: 20%">{{ t('Cover Image Type') }}</th>
												<th scope="col">{{ t('Options') }}</th>
											</tr>
										</thead>

										<tbody>
											<tr v-for="(info, index) in responseData" v-bind:key="info.id">
												<td class="align-middle">
													<div class="d-flex justify-content-center">
														<div class="form-check m-0">
															<input type="checkbox" name="getSelected[]" class="form-check-input checkids" v-on:click="clickCheckbox(info.id, $event)" v-bind:id="'user_'+info.id" v-bind:value="info.id">
															<label class="form-check-label" v-bind:for="'user_'+info.id"></label>
														</div>
													</div>
												</td>

												<td class="align-middle text-wrap" style="word-wrap: break-word;">@{{ info.cover_page_name }}</td>
												<td class="align-middle">@{{ info.uri }}</td>
												<td class="align-middle">@{{ info.cover_type }}</td>

												<td class="align-middle">
													<a :href="'{{ url('manage_coverimage/edit/\'+info.id+\'') }}'" class="btn btn-sm ph-btn-theme-outline py-2 px-3 me-2" style="font-size: .64rem !important;"><i class="fas fa-pencil-alt fa-fw"></i></a>
													<a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ph-submit-data-coverimage-modalDelete" :class="'ar-fetch-detail-data-coverimage-modalDelete-'+info.id" data-url="{{ url('manage_coverimage/detaildata') }}" v-on:click="showModal('modalDelete', info.id)" class="btn btn-sm btn-outline-danger py-2 px-3" style="font-size: .64rem !important;"><i class="fas fa-trash fa-fw"></i></a>
												</td>
											</tr>
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

			<!--- Delete Cover Image Modal--->
			<div class="modal ph-modal-delete fade" id="ph-submit-data-coverimage-modalDelete" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeleteLabel" aria-hidden="true">
				<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<form :action="'{{ url('manage_coverimage/delete') }}/'+responseDataModal.delete.id" method="post" auto-refresh-data="true" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModal($event, 'modalDelete')" ref="formHTML-modalDelete">
							
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
											<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModal('ph-submit-data-coverimage-modalDelete', 'ph-submit-data-coverimage-modalDelete')">{{ t('No, keep it') }}</button>
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

		</div>
	</div>
@endsection

@push('js')
	<script src="{{ url('assets/js/global/jquery/jquery-3.7.0.min.js') }}"></script>
	<script src="{{ url('assets/plugins/spectrum/dist/spectrum.min.js') }}"></script>
	<script src="{{ url('assets/plugins/vue/plugins/vue-datepicker/js/vue-datepicker-11.0.3.js') }}"></script>
	<script src="{{ url('assets/js/vue3/manage_coverimage/vueV3-manage-coverimage-2026.js?v=').time() }}"></script>
@endpush