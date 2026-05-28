@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Translated') }}
@endsection

@section('content')
	<div>
		<div class="mb-3">			
			{{ Breadcrumbs::render('awesome_admin.language.translated') }}
		</div>

		<div id="ph-app-data-language">
			<div class="ph-fetch-listdata" id="ph-form-submit-data-update" data-url="{{ url('awesome_admin/language/listdata/translated') }}">
				<form action="{{ route('cms.admin.awesome_admin.language.update.translated') }}" method="post" ref="formHTMLupdate" auto-refresh="true" @submit.prevent="submitDataLanguage($event, 'update')">
					
					<div class="ph-content rounded p-3 mb-3">
						<div class="row g-3">
							<div class="col-md-6 d-flex align-items-center">
								<h4 class="mb-0">{{ t('Manage Translated') }}</h4>
							</div>

							<div class="col-md-6 d-flex align-items-center justify-content-end">
								<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Save') }}">							
							</div>
						</div>
					</div>

					<div class="ph-content rounded">
						<div v-if="loading" class="text-center p-5">
							<div class="spinner-border text-primary mb-2" role="status">
								<span class="sr-only"></span>
							</div>

							<div class="h6 m-0">{{ t('Loading') }}...</div>
						</div>

						<div v-else-if="responseStatus === 'failed'" class="ph-data-load-status text-center text-danger h5 p-5" style="display: none">
							@{{ responseMessage }}
						</div>

						<div v-else class="ph-data-load-content" v-cloak>
							<div v-if="loadingNextPage" class="text-center p-5">
								<div class="spinner-border text-primary mb-2" role="status">
									<span class="sr-only"></span>
								</div>

								<div class="h6 m-0">{{ t('Loading') }}...</div>
							</div>

							<div v-else>
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

								<div class="table-responsive">
									<table class="table table-striped table-hover mb-0">
										<thead>
											<tr>
												<th scope="col" style="width: 5%">ID</th>
												<th scope="col">{{ t('Language') }}</th>
												<th scope="col">{{ t('Language From') }}</th>
												<th scope="col">{{ t('Language To') }}</th>
												<th scope="col">{{ t('Status Save') }}</th>
												<th scope="col">{{ t('Options') }}</th>
											</tr>
										</thead>

										<tbody is="transition-group" name="custom-classes-transition" enter-active-class="animate__animated animate__fadeIn animate__faster">
											<tr class="text-nowrap" v-for="(info, index) in responseData" v-bind:key="info.id">
												<td class="align-middle">#@{{ info.id }}</td>
												<td class="align-middle">@{{ info.lang }}</td>
												<td class="align-middle">@{{ info.lang_from }}</td>
												<td class="align-middle">
													<div v-if="responseDataEditable[index] == 'editable'">
														<textarea rows="1" :name="'lang['+index+'][to]'" @input="resizeTextarea(index)" :ref="'textarea_'+index" style="resize: none" class="form-control font-size-inherit" v-model="info.lang_to"></textarea>
														<input type="hidden" :name="'lang['+index+'][id]'" :value="info.id">
													</div>	

													<div v-else>
														@{{ info.lang_to }}
													</div>
												</td>

												<td class="align-middle">
													<div v-if="responseDataEditableStatus[index] == 'success'"><div class="badge text-bg-success">Sentences are saved</div></div>
												</td>

												<td class="align-middle">
													<span v-if="responseDataEditable[index] == 'editable'">
														<a href="javascript:void(0)" v-on:click="cancelEditLanguage($event, index)">Cancel Edit Sentence</a>
													</span>

													<span v-else>
														<a href="javascript:void(0)" v-on:click="editLanguage($event, index)">Edit Sentence</a>
													</span>
												</td>
											</tr>
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
			</div>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_language/vueV3-manage-language-2026.js?v=').time() }}"></script>
@endpushonce