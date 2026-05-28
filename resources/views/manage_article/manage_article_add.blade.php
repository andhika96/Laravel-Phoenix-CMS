@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Add Post') }}
@endsection

@section('content')
	<div id="ph-app-manage-article">
		<div class="mb-3">
			{{ Breadcrumbs::render('manage_article.add') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<h4 class="mb-0"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add Post') }}</h4>
		</div>

		<div>
			<div id="ph-form-submit-data">
				<form action="{{ route('cms.core.manage_article.store') }}" method="post" ref="formHTML" @submit.prevent="submitData" auto-refresh="false" auto-reset="true" auto-lock-button="false" auto-block-button-mobile="false" custom-button-value="{{ t('Save') }}">
					
					<div class="ph-notice" v-cloak>
						<div aria-live="polite" aria-atomic="true" class="position-relative">
							<div class="toast-container position-fixed top-0 end-0 p-3">
								<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
									<div :class="'toast-header '+responseStatusToast+' px-3 pt-3 pb-1 border-0'">
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

					<div class="row g-3">
						<div class="col-md-8">
							<div class="ph-content rounded mb-3">
								<div class="p-4">
									<div class="row g-3">
										<div class="col-12">
											<label class="form-label">{{ t('Title') }}</label>
											<input type="text" name="title" class="form-control font-size-inherit">
										</div>

										<div class="col-12">
											<label class="form-label">{{ t('Slug') }} ({{ t('Permalink') }})</label>
											
											<div class="input-group rounded">
												<span class="input-group-text font-size-inherit" id="basic-addon3">.../article/detail/</span>
												<input type="text" name="uri" placeholder="{{ t('You can customize the post link here or leave it blank.') }}" class="form-control font-size-inherit" v-on:focus="focusForm($event)" v-on:blur="blurForm">
											</div>
										</div>
									</div>
								</div>

								<div class="row g-0 mt-1">
									<div class="col-12 position-relative">
										<textarea name="content" id="editor" rows="15" class="form-control font-size-inherit"></textarea>
									</div>
								</div>

								<div class="d-flex justify-content-start p-4 text-right">
									<div id="word-count"></div>
								</div>

							</div>								
						</div>

						<div class="col-md-4">

							<div class="ph-content card mb-3">
								<div class="card-body">
									<h6 class="card-title border-bottom pb-3 mb-3">{{ t('Publish') }}</h6>

									<div class="mb-3">
										<ul class="list-group list-group-flush">
											<li class="list-group-item px-0">
												<div class="row g-1">
													<div class="col-8 d-flex align-items-center">	
														<span>
															<i class="fas fa-map-pin fa-fw me-1"></i> {{ t('Status') }}: <strong v-cloak>@{{ ucFirst(article.status) }}</strong>
														</span>
													</div>

													<div class="col-4 d-flex align-items-center justify-content-end">
														<a href="#collapseEditStatus" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseEditStatus">{{ t('Edit') }}</a>
													</div>
												</div>

												<div class="collapse" id="collapseEditStatus">
													<div class="card card-body border-0 bg-body-secondary my-2">
														<select name="status" class="form-select mb-3" aria-label="Select status">
															<option value="publish">{{ t('Publish') }}</option>
															<option value="draft">{{ t('Draft') }}</option>
															<option value="pending">{{ t('Pending') }}</option>
														</select>

														<div class="row g-1">
															<div class="col-6 d-flex align-items-center">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonCancelStatusOption">{{ t('Cancel') }}</a>
															</div>

															<div class="col-6 d-flex align-items-center justify-content-end">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonOkStatusOption">{{ t('Ok') }}</a>
															</div>
														</div>
													</div>
												</div>
											</li>

											<li class="list-group-item px-0">
												<div class="row g-1">
													<div class="col-8 d-flex align-items-center">	
														<span>
															<i class="fas fa-eye fa-fw me-1"></i> {{ t('Visibility') }}: <strong v-cloak>@{{ ucWords(strReplace('_', ' ', article.visibility)) }}</strong>
														</span>
													</div>

													<div class="col-4 d-flex align-items-center justify-content-end">
														<a href="#collapseEditVisibility" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseEditVisibility">{{ t('Edit') }}</a>
													</div>
												</div>

												<div class="collapse" id="collapseEditVisibility">
													<div class="card card-body border-0 bg-body-secondary my-2">
														<select name="visibility" class="form-select mb-3" aria-label="Select visibility" v-on:change="selectVisibility($event)">
															<option value="public">{{ t('Public') }}</option>
															<option value="private">{{ t('Private') }}</option>
															<option value="password_protected">{{ t('Password Protected') }}</option>
														</select>

														<div v-if="temporary_article.visibility == 'password_protected'" class="form-group mb-3" v-cloak>
															<label for="enterPasswordFormControlInput1" class="form-label">{{ t('Password') }}</label>
															<input type="text" name="password_protected" maxlength="32" class="form-control font-size-inherit" id="enterPasswordFormControlInput1">
														</div>

														<div class="row g-1">
															<div class="col-6 d-flex align-items-center">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonCancelVisibilityOption">{{ t('Cancel') }}</a>
															</div>

															<div class="col-6 d-flex align-items-center justify-content-end">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonOkVisibilityOption">{{ t('Ok') }}</a>
															</div>
														</div>
													</div>
												</div>
											</li>

											<li class="list-group-item px-0">
												<div class="row g-1">
													<div class="col-8 d-flex align-items-center">	
														<span>
															<i class="fas fa-calendar-alt fa-fw me-1"></i> {{ t('Publish') }}: 
															<span v-if="article.created_at == ''" class="fw-bold" v-cloak>{{ t('Immediately') }}</span>
															<span v-else-if="article.created_at !== ''" class="fw-bold" v-cloak>@{{ article.created_at }} @{{ article.hours }}:@{{ article.minutes }}</span>
														</span>
													</div>

													<div class="col-4 d-flex align-items-center justify-content-end">
														<a href="#collapseEditPublish" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="collapseEditPublish">{{ t('Edit') }}</a>
													</div>
												</div>

												<div class="collapse" id="collapseEditPublish">
													<div class="card card-body border-0 bg-body-secondary my-2">
														<div class="row g-1 mb-3">
															<div class="col-6">
																{{ t('Date') }}:
															</div>

															<div class="col-6">
																{{ t('Time') }}:
															</div>

															<div class="col-12 col-md-6">
																<input type="date" name="created_at" class="form-control font-size-inherit">
															</div>

															<div class="col-6 col-md-3">
																<select name="hours" class="form-select" aria-label="Select hours">
																	@foreach (range(0, 23) as $hour)
																		<option value="{{ sprintf('%02d', $hour) }}">{{ sprintf('%02d', $hour) }}</option>
																	@endforeach	
																</select>															
															</div>

															<div class="col-6 col-md-3">
																<select name="minutes" class="form-select" aria-label="Select minutes">
																	@foreach (range(0, 59) as $minute)
																		<option value="{{ sprintf('%02d', $minute) }}">{{ sprintf('%02d', $minute) }}</option>
																	@endforeach	
																</select>		
															</div>
														</div>

														<div class="row g-1">
															<div class="col-6 d-flex align-items-center">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonCancelPublishOption">{{ t('Cancel') }}</a>
															</div>

															<div class="col-6 d-flex align-items-center justify-content-end">
																<a href="javascript:void(0)" class="btn ph-btn-theme-outline" v-on:click="buttonOkPublishOption">{{ t('Ok') }}</a>
															</div>
														</div>
													</div>
												</div>
											</li>
										</ul>
									</div>

									<div class="row g-1">
										<div class="col-6 d-flex align-items-center d-none">
											<a href="#!" class="btn ph-btn-theme-outline">{{ t('Save as Draft') }}</a>
										</div>

										<div class="col-12 d-flex align-items-center justify-content-end">
											<input type="submit" class="btn ph-btn-theme btn-submit-data" value="{{ t('Submit') }}">
										</div>
									</div>
								</div>
							</div>

							<div class="ph-content card mb-3">
								<div class="card-body">
									<h6 class="card-title border-bottom pb-3 mb-3">{{ t('Categories') }}</h6>

									<div>
										<select name="category_id" class="form-select" aria-label="Select category">
											@foreach ($categories as $item)
												<option value="{{ $item->id }}">{{ $item->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>

							<div class="ph-content card mb-3">
								<div class="card-body">
									<h6 class="card-title border-bottom pb-3 mb-3">{{ t('Thumbnail') }}</h6>

									<div>
										<div class="input-group rounded mb-3">
											<input type="file" id="formFile" name="thumbnail" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" class="form-control font-size-inherit" v-on:focus="focusForm($event)" v-on:blur="blurForm" @change="previewImage($event)">
											<button v-if="showButtonRemoveImage == true" v-on:click="removePreviewImage" class="btn btn-outline-danger" type="button" id="inputGroupFileAddon04" v-cloak><i class="fas fa-trash-alt fa-fw"></i></button>
										</div>

										<div class="position-relative text-center d-flex justify-content-center" style="width: auto;height: 350px;background-image: linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7),linear-gradient(45deg,#c3c4c7 25%,transparent 25%,transparent 75%,#c3c4c7 75%,#c3c4c7);background-position: 0 0,10px 10px;background-size: 20px 20px;">
											<img :src="imageEncoded" id="img-preview" class="img-fluid object-fit-contain">
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('js')
	<script src="{{ url('assets/plugins/ckeditor5/build/ckeditor.js?v=0.0.1') }}"></script>
	<script src="{{ url('assets/plugins/ckfinder/ckfinder.js?v=0.0.1') }}"></script>
	<script src="{{ url('assets/js/vue3/manage_article/vueV3-manage-article-2026.js?v=').time() }}"></script>
@endpush