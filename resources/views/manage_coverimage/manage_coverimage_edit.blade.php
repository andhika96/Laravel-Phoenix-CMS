@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Edit Cover Image') }}
@endsection

@push('css')
	<!-- Spectrum ColorPicker CSS -->
	<link rel="stylesheet" href="{{ url('assets/plugins/spectrum/dist/spectrum.min.css') }}">

	<!-- Datepicker CSS -->
	<link rel="stylesheet" href="{{ url('assets/plugins/vue/plugins/vue-datepicker/css/vue-datepicker-11.0.3.css') }}">

	<style>
	/*	
	.ph-list-form-coverimage
	{
		padding-bottom: 1rem;
		border-bottom: 1px #ccc solid;
	}

	.ph-list-form-coverimage:last-child
	{
		padding-bottom: 0 !important;
		border-bottom: 0 !important;
	}
	*/

	.dp__input
	{
		font-size: inherit;
	}

	.sp-original-input-container
	{
		width: 100%;
	}

	.ph-accordion-item,
	.ph-accordion-item .accordion-header,
	.ph-accordion-item .accordion-button.collapsed
	{
		border-radius: 20px !important;
	}

	.ph-draggable-icon
	{
		cursor: move;
		padding: .75rem 1.5rem !important;
		border-width: 0 !important;
		border-top-left-radius: 20px !important;
		border-bottom-left-radius: 20px !important;
		margin-right: 1px;
		background-color: #F3F3F3 !important;
		transition: var(--bs-accordion-transition);
	}

	.ph-draggable-icon-not-collapsed
	{
		border-bottom-left-radius: 0 !important;
	}

	.ph-end-icon
	{
		padding: .75rem 1.5rem !important;
		border-width: 0 !important;
		border-top-right-radius: 20px !important;
		border-bottom-right-radius: 20px !important;
		margin-left: 1px;
		background-color: #F3F3F3 !important;
		transition: var(--bs-accordion-transition);
	}

	.ph-end-icon-not-collapsed
	{
		border-bottom-right-radius: 0 !important;
	}

	.ph-accordion-item .accordion-button
	{
		flex: 1 1 auto;
		width: 1%;
		font-weight: bold;
		padding: 1.25rem 1.5rem;
		background-color: #F3F3F3;
		border-top-left-radius: 20px !important;
		border-top-right-radius: 20px !important; 
		border-bottom-left-radius: 0;
		border-bottom-right-radius: 0;
	}

	.ph-accordion-item .accordion-button-slideshow.collapsed,
	.ph-accordion-item .accordion-button-slideshow:not(.collapsed)
	{
		border-left: 1px #ccc solid !important;
		border-right: 1px #ccc solid !important;
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
		border-top-right-radius: 0 !important;
		border-bottom-right-radius: 0 !important;
	}

	.ph-accordion-item
	{
		margin-bottom: 1.5rem;
	}

	.ph-accordion-item:last-child
	{
		margin-bottom: 0
	}

	.ph-accordion-item:first-of-type,
	.ph-accordion-item:not(:first-of-type)
	{
		border: var(--bs-accordion-border-width) solid #F3F3F3;
	}

	.sp-original-input-container
	{
		width: 100%;
	}

	.sp-colorize-container.sp-add-on
	{
		width: 40px !important;
	}

	.ghost 
	{
		opacity: 0.5;
		background: #c8ebfb;
	}

	.fade-enter-active,
	.fade-leave-active
	{
		transition: opacity .15s linear; /* Adjust duration and easing as needed */
	}

	.fade-enter-from,
	.fade-leave-to 
	{
		opacity: 0;
	}

	#ph-content-button-submit
	{
		-webkit-transition: all 0.2s ease;
		-o-transition: all 0.2s ease;
		transition: all 0.2s ease;
	}
	</style>
@endpush('css')

@section('content')
	<div id="ph-app-manage-coverimage">
		<div class="mb-3">
			{{ Breadcrumbs::render('manage_coverimage.edit') }}
		</div>

		<div>
			<div id="ph-form-submit-data">
				<form action="{{ route('cms.core.manage_coverimage.update', $data->id) }}" method="post" ref="formHTML" @submit.prevent="submitData" auto-refresh="false" auto-reset="true" auto-lock-button="false" auto-block-button-mobile="false" custom-button-value="{{ t('Save') }}">
					
					<div id="ph-content-button-submit" class="arv7-content rounded p-3 mb-3 ph-fetch-detail-data sticky-top" data-url="{{ url('manage_coverimage/detaildata/'.$data->id) }}">
						<div class="row g-3">
							<div class="col-md-6 d-flex align-items-center">
								<h4 class="mb-0"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Edit Cover Image') }}</h4>
							</div>

							<div class="col-md-6 d-flex align-items-center justify-content-end">
								<a href="javascript:void(0)" v-if="getFormCoverImage.cover_type == 'slideshow'" class="btn btn-outline-larapx me-2" v-on:click="addFormCoverimage" v-cloak>{{ t('Add New Form') }}</a>
								<input type="submit" class="btn ph-btn-theme btn-submit-data" value="{{ t('Submit') }}">
							</div>
						</div>
					</div>

					<div class="ph-notice" v-cloak>
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

					<div class="row g-3">
						<div class="col-12">
						</div>
					</div>

					<div class="arv7-content rounded p-4 mb-3">
						<div class="row g-3">
							<div class="col-md-4">
								<label class="form-label">{{ t('Cover Page URI / Slug') }}</label>
								<input type="text" name="uri" class="form-control font-size-inherit" value="{{ $data->uri }}">
							</div>

							<div class="col-md-4">
								<label class="form-label">{{ t('Cover Page Name') }}</label>
								<input type="text" name="cover_page_name" class="form-control font-size-inherit" value="{{ $data->cover_page_name }}">
							</div>

							<div class="col-md-4">
								<label class="form-label">{{ t('Cover Type') }}</label>
								<select name="cover_type" class="form-select" aria-label="Default select example" v-on:change="changeCoverType($event)">
									<option value="background_image" @if ($data->cover_type == 'background_image') selected @endif>{{ t('Only Background Image') }}</option>
									<option value="slideshow" @if ($data->cover_type == 'slideshow') selected @endif>{{ t('Slideshow') }}</option>
								</select>
							</div>

							<div class="col-md">
								<label class="form-label">{{ t('Desktop Slideshow Direction') }}</label>
								<select name="cover_slideshow_desktop_direction" class="form-select" aria-label="Default select example" :disabled="getFormCoverImage.cover_type == 'slideshow' ? false : true">
									<option value="horizontal" @if ($data->cover_slideshow_desktop_direction == 'horizontal') selected @endif>{{ t('Horizontal') }}</option>
									<option value="vertical" @if ($data->cover_slideshow_desktop_direction == 'vertical') selected @endif>{{ t('Vertical') }}</option>
								</select>
							</div>

							<div class="col-md">
								<label class="form-label">{{ t('Mobile Slideshow Direction') }}</label>
								<select name="cover_slideshow_mobile_direction" class="form-select" aria-label="Default select example" :disabled="getFormCoverImage.cover_type == 'slideshow' ? false : true">
									<option value="horizontal" @if ($data->cover_slideshow_mobile_direction == 'horizontal') selected @endif>{{ t('Horizontal') }}</option>
									<option value="vertical" @if ($data->cover_slideshow_mobile_direction == 'vertical') selected @endif>{{ t('Vertical') }}</option>
								</select>
							</div>

							<div class="col-md">
								<label class="form-label">{{ t('Activate Autoplay Slideshow') }}</label>
								<select name="cover_autoplay_slideshow" class="form-select" aria-label="Default select example" :disabled="getFormCoverImage.cover_type == 'slideshow' ? false : true">
									<option value="active" @if ($data->cover_autoplay_slideshow == 'active') selected @endif>{{ t('Active') }}</option>
									<option value="inactive" @if ($data->cover_autoplay_slideshow == 'inactive') selected @endif>{{ t('Inactive') }}</option>
								</select>
							</div>

							<div class="col-md">
								<label class="form-label">{{ t('Autoplay Slideshow Interval') }}</label>
								<input type="text" name="cover_autoplay_slideshow_interval" class="form-control font-size-inherit" value="{{ $data->cover_autoplay_slideshow_interval }}" :disabled="getFormCoverImage.cover_type == 'slideshow' ? false : true">
							</div>

							<div class="col-md">
								<label class="form-label">{{ t('Activate Looping Slideshow') }}</label>
								<select name="cover_looping_slideshow" class="form-select" aria-label="Default select example" :disabled="getFormCoverImage.cover_type == 'slideshow' ? false : true">
									<option value="active" @if ($data->cover_looping_slideshow == 'active') selected @endif>{{ t('Active') }}</option>
									<option value="inactive" @if ($data->cover_looping_slideshow == 'inactive') selected @endif>{{ t('Inactive') }}</option>
								</select>
							</div>
						</div>
					</div>

					<div v-if="loadingDetailData == true">
						<div class="arv7-content rounded p-4 text-center">
							<div class="spinner-border text-primary mb-2" role="status">
								<span class="sr-only"></span>
							</div>

							<div class="h6 m-0">{{ t('Loading') }} ...</div>
						</div>
					</div>

					<div v-else-if="responseStatusDetailData === 'failed'" class="arv7-content rounded p-4 h-5 text-center text-danger" v-cloak>
						@{{ responseMessageDetailData }}
					</div>

					<div v-else v-cloak>
						<transition name="fade" mode="out-in">
							<div v-if="getFormCoverImage.cover_type == 'background_image'">
								<div class="row g-3">
									<div class="col-12">
										<div class="arv7-content rounded p-4 mb-3">
											<draggable class="accordion" v-model="getListFormBackgroundImage" v-bind="dragOptions" tag="transition-group" @start="drag = true" @end="drag = false" :component-data="{ tag: 'div', type: 'transition-group', name: !drag ? 'flip-list fade' : null }">
												<template #item="{ element, index }">

													<div class="accordion-item ph-accordion-item ph-list-form-coverimage" :key="cover_page_name">

														<h6 class="accordion-header rounded">
															<div class="input-group">
																<span :class="'input-group-text d-none ph-draggable-icon ph-draggable-icon-'+index" id="basic-addon1"><i class="fas fa-arrows-alt"></i></span>
															
																<button class="accordion-button accordion-button-background-image collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#panelsSlideshow-collapse'+index" aria-expanded="true" :aria-controls="'panelsSlideshow-collapse'+index" v-on:click="accordionButton(index)">
																	{{ t('Background Image') }} 

																	<span v-if="getListFormBackgroundImage[index]['title'] !== '' && typeof getListFormBackgroundImage[index]['title'] !== 'undefined'" class="mx-2" v-cloak>( @{{ truncateText(removeHtmlTag(getListFormBackgroundImage[index]['title']), 50) }} )</span>
																	<span :class="getListFormBackgroundImage[index]['cover_is_active'] == 'active' ? 'text-success' : 'text-danger'">@{{ ucFirst(getListFormBackgroundImage[index]['cover_is_active']) }}</span>
																</button>
															</div>
														</h6>

														<div :id="'panelsSlideshow-collapse'+index" class="accordion-collapse collapse show">
															<div class="accordion-body">

																<div class="mt-2 mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Main Settings') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-md-6">
																					<label class="form-label">{{ t('Is Active Slideshow?') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][cover_is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['cover_is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Disable Content') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][disable_content]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['disable_content']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive" selected>{{ t('Inactive') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Main Contents') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Desktop Image') }}</label>		

																					<div class="input-group">
																						<input type="file" :name="'cover_image[background_image]['+index+'][desktop_image]'" class="form-control font-size-inherit" id="formFileImageWeb" aria-label="Form Image For Web" aria-describedby="button-addon1">
																						
																						<span v-if="element.desktop_image_full_url !== ''" class="input-group-text font-size-inherit" v-cloak>
																							<a :href="element.desktop_image_full_url" class="font-size-inherit text-decoration-none text-success" target="_blank">Preview</a>
																						</span>
																					</div>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Mobile Image') }}</label>		

																					<div class="input-group">
																						<input type="file" :name="'cover_image[background_image]['+index+'][mobile_image]'" class="form-control font-size-inherit" id="formFileImageWeb" aria-label="Form Image For Mobile" aria-describedby="button-addon2">
																						
																						<span v-if="element.mobile_image_full_url !== ''" class="input-group-text font-size-inherit" v-cloak>
																							<a :href="element.mobile_image_full_url" class="font-size-inherit text-decoration-none text-success" target="_blank">Preview</a>
																						</span>
																					</div>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Background Overlay') }}</label>		
																					<input type="text" :name="'cover_image[background_image]['+index+'][background_overlay]'" class="form-control font-size-inherit color-picker" id="color-picker" :data-index-form="index" placeholder="Enter your color hex or rgba here" v-model="getListFormBackgroundImage[index]['background_overlay']">
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Background Size') }}</label>		

																					<select :name="'cover_image[background_image]['+index+'][background_size]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['background_size']">
																						<option value="sm_size">{{ t('Small Size') }}</option>
																						<option value="md_size">{{ t('Medium Size') }}</option>
																						<option value="lg_size">{{ t('Large Size') }}</option>
																						<option value="full_size">{{ t('Fullscreen Size') }}</option>
																						<option value="boi_size">{{ t('Based on Image Size') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Desktop Content Position') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][desktop_content_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['desktop_content_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Mobile Content Position') }}</label>
																					<select :name="'cover_image[background_image]['+index+'][mobile_content_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['mobile_content_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<div class="form-group">
																						<label class="form-label">{{ t('Title') }}</label>		
																						<input type="text" :name="'cover_image[background_image]['+index+'][title]'" class="form-control font-size-inherit" placeholder="Enter your title here" v-model="getListFormBackgroundImage[index]['title']">
																					</div>
																				</div>

																				<div class="col-12">
																					<div class="form-group">
																						<label class="form-label">{{ t('Description') }} / {{ t('Caption') }}</label>		
																						<textarea :name="'cover_image[background_image]['+index+'][description]'" rows="7" class="form-control font-size-inherit" placeholder="Enter your title here" v-model="getListFormBackgroundImage[index]['description']"></textarea>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Second Contents') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-md-6">
																					<label class="form-label">{{ t('Is Active') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][second_content][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['second_content']['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Content Type') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][second_content][type]'" class="form-select" aria-label="Default select example" v-on:change="changeSecondContentType(index, $event)" v-model="getListFormBackgroundImage[index]['second_content']['type']">
																						<option value="text">{{ t('Text') }}</option>
																						<option value="link">{{ t('Link') }}</option>
																						<option value="button_link">{{ t('Button Link') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Desktop Position') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][second_content][desktop_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['second_content']['desktop_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Mobile Position') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][second_content][mobile_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['second_content']['mobile_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label" v-if="getListFormBackgroundImage[index]['second_content']['type'] == 'text'" v-cloak>{{ t('Text') }}</label>
																					<label class="form-label" v-if="getListFormBackgroundImage[index]['second_content']['type'] !== 'text'" v-cloak>{{ t('Link Title') }}</label>

																					<input type="text" :name="'cover_image[background_image]['+index+'][second_content][text]'" class="form-control font-size-inherit" placeholder="Enter your text or title" v-model="getListFormBackgroundImage[index]['second_content']['text']">
																				</div>

																				<div class="col-12" v-if="getListFormBackgroundImage[index]['second_content']['type'] !== 'text'" v-cloak>
																					<label class="form-label">{{ t('Link') }}</label>
																					<input type="text" :name="'cover_image[background_image]['+index+'][second_content][link]'" class="form-control font-size-inherit" placeholder="Enter your link here" v-model="getListFormBackgroundImage[index]['second_content']['link']">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Button Settings') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Disable Button 1') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][button][0][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['button'][0]['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Title Button {1}', '1') }}</label>
																					<input type="text" :name="'cover_image[background_image]['+index+'][button][0][title]'" class="form-control font-size-inherit" placeholder="Enter your title button here" v-model="getListFormBackgroundImage[index]['button'][0]['title']">
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Link Button {1}', '1') }}</label>
																					<input type="text" :name="'cover_image[background_image]['+index+'][button][0][link]'" class="form-control font-size-inherit" placeholder="Enter your link button here" v-model="getListFormBackgroundImage[index]['button'][0]['link']">
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Disable Button 2') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][button][1][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['button'][1]['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Title Button {1}', '2') }}</label>
																					<input type="text" :name="'cover_image[background_image]['+index+'][button][1][title]'" class="form-control font-size-inherit" placeholder="Enter your title button here" v-model="getListFormBackgroundImage[index]['button'][1]['title']">
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Link Button {1}', '2') }}</label>
																					<input type="text" :name="'cover_image[background_image]['+index+'][button][1][link]'" class="form-control font-size-inherit" placeholder="Enter your link button here" v-model="getListFormBackgroundImage[index]['button'][1]['link']">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Link for Content') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<label class="form-label">{{ t('Is Active Link for Image?') }}</label>
																			
																			<select :name="'cover_image[background_image]['+index+'][link][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['link']['is_active']">
																				<option value="active">{{ t('Active') }}</option>
																				<option value="inactive">{{ t('Inactive') }}</option>
																			</select>
																		</div>

																		<div class="col-md-6">
																			<label class="form-label">{{ t('Link for Image?') }}</label>
																			<input type="text" :name="'cover_image[background_image]['+index+'][link][content]'" class="form-control font-size-inherit" placeholder="Enter your link here" v-model="getListFormBackgroundImage[index]['link']['content']">
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Countdown for Content') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">																			
																				<div class="col-12">
																					<label class="form-label">{{ t('Is Active Countdown for Image?') }}</label>
																					
																					<select :name="'cover_image[background_image]['+index+'][countdown][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['countdown']['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown for Image') }}</label>
																					<vue-date-picker v-model="getListFormBackgroundImage[index]['countdown']['content_default']" @date-update="updateDateForCountDownBackgroundImage(index, $event)" :format="'MM/dd/yyyy HH:mm'" locale="ID" auto-apply></vue-date-picker>
																					
																					<input type="hidden" :name="'cover_image[background_image]['+index+'][countdown][content]'" class="form-control font-size-inherit" placeholder="Enter your countdown here" v-model="getListFormBackgroundImage[index]['countdown']['content']">
																					<input type="hidden" :name="'cover_image[background_image]['+index+'][countdown][content_default]'" class="form-control font-size-inherit" placeholder="Enter your countdown here" v-model="getListFormBackgroundImage[index]['countdown']['content_default']">
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown Desktop Position') }}</label>

																					<select :name="'cover_image[background_image]['+index+'][countdown][desktop_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['countdown']['desktop_position']">
																						<option value="default" selected>{{ t('Default') }}</option>
																						<option value="center-center">{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown Mobile Position') }}</label>

																					<select :name="'cover_image[background_image]['+index+'][countdown][mobile_position]'" class="form-select" aria-label="Default select example" v-model="getListFormBackgroundImage[index]['countdown']['mobile_position']">
																						<option value="default" selected>{{ t('Default') }}</option>
																						<option value="center-center">{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>

												</template>
											</draggable>
										</div>								
									</div>
								</div>							
							</div>

							<div v-else-if="getFormCoverImage.cover_type == 'slideshow'" v-cloak>
								<div class="row g-3">
									<div class="col-12">
										<div class="arv7-content rounded p-4 mb-3">
											<draggable class="accordion" v-model="getListFormSlideshow" v-bind="dragOptions" tag="transition-group" @start="drag = true" @end="drag = false" :component-data="{ tag: 'div', type: 'transition-group', name: !drag ? 'flip-list fade' : null }">
												<template #item="{ element, index }">

													<div class="accordion-item ph-accordion-item ph-list-form-coverimage" :key="cover_page_name">

														<h6 class="accordion-header rounded">
															<div class="input-group">
																<span :class="'input-group-text ph-draggable-icon ph-draggable-icon-'+index" id="basic-addon1"><i class="fas fa-arrows-alt"></i></span>
															
																<button class="accordion-button accordion-button-slideshow collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#panelsSlideshow-collapse'+index" aria-expanded="true" :aria-controls="'panelsSlideshow-collapse'+index" v-on:click="accordionButton(index)">
																	{{ t('Slideshow') }} @{{ index+1 }} 

																	<span v-if="getListFormSlideshow[index]['title'] !== '' && typeof getListFormSlideshow[index]['title'] !== 'undefined'" class="ms-2" v-cloak>( @{{ truncateText(removeHtmlTag(getListFormSlideshow[index]['title']), 50) }} )</span>
																	<span :class="getListFormSlideshow[index]['cover_is_active'] == 'active' ? 'text-success' : 'text-danger'" class="ms-2">@{{ ucFirst(getListFormSlideshow[index]['cover_is_active']) }}</span>
																</button>

																<a href="javascript:void(0)" :class="'input-group-text ph-end-icon ph-end-icon-'+index" id="basic-addon1" v-on:click="showModalDetailData(index, 'slideshow', 'ph-submit-data-coverimage-modalDelete')"><i class="fas fa-trash-alt text-danger"></i></a>
															</div>
														</h6>

														<div :id="'panelsSlideshow-collapse'+index" class="accordion-collapse collapse">
															<div class="accordion-body">
																<div class="mt-2 mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Main Settings') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-md-6">
																					<label class="form-label">{{ t('Is Active Slideshow?') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][cover_is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['cover_is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Disable Content') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][disable_content]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['disable_content']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive" selected>{{ t('Inactive') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Main Contents') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Desktop Image') }}</label>		

																					<div class="input-group">
																						<input type="file" :name="'cover_image[slideshow]['+index+'][desktop_image]'" class="form-control font-size-inherit" id="formFileImageWeb" aria-label="Form Image For Web" aria-describedby="button-addon1">
																						
																						<span v-if="element.desktop_image_full_url !== ''" class="input-group-text font-size-inherit" v-cloak>
																							<a :href="element.desktop_image_full_url" class="font-size-inherit text-decoration-none text-success" target="_blank">Preview</a>
																						</span>
																					</div>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Mobile Image') }}</label>		

																					<div class="input-group">
																						<input type="file" :name="'cover_image[slideshow]['+index+'][mobile_image]'" class="form-control font-size-inherit" id="formFileImageWeb" aria-label="Form Image For Mobile" aria-describedby="button-addon2">
																						
																						<span v-if="element.mobile_image_full_url !== ''" class="input-group-text font-size-inherit" v-cloak>
																							<a :href="element.mobile_image_full_url" class="font-size-inherit text-decoration-none text-success" target="_blank">Preview</a>
																						</span>
																					</div>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Background Overlay') }}</label>		
																					<input type="text" :name="'cover_image[slideshow]['+index+'][background_overlay]'" class="form-control font-size-inherit color-picker" id="color-picker" :data-index-form="index" v-model="getListFormSlideshow[index]['background_overlay']" placeholder="Enter your color hex or rgba here">
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Background Size') }}</label>		

																					<select :name="'cover_image[slideshow]['+index+'][background_size]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['background_size']">
																						<option value="sm_size">{{ t('Small Size') }}</option>
																						<option value="md_size">{{ t('Medium Size') }}</option>
																						<option value="lg_size">{{ t('Large Size') }}</option>
																						<option value="full_size">{{ t('Fullscreen Size') }}</option>
																						<option value="boi_size">{{ t('Based on Image Size') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Desktop Content Position') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][desktop_content_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['desktop_content_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Mobile Content Position') }}</label>
																					<select :name="'cover_image[slideshow]['+index+'][mobile_content_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['mobile_content_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<div class="form-group mb-4">
																						<label class="form-label">{{ t('Title') }}</label>		
																						<input type="text" :name="'cover_image[slideshow]['+index+'][title]'" class="form-control font-size-inherit" placeholder="Enter your title here" v-model="getListFormSlideshow[index]['title']">
																					</div>
																				</div>

																				<div class="col-12">
																					<div class="form-group mb-4">
																						<label class="form-label">{{ t('Description') }} / {{ t('Caption') }}</label>		
																						<textarea :name="'cover_image[slideshow]['+index+'][description]'" rows="7" class="form-control font-size-inherit" placeholder="Enter your title here" v-model="getListFormSlideshow[index]['description']"></textarea>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Second Contents') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-md-6">
																					<label class="form-label">{{ t('Is Active') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][second_content][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['second_content']['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Content Type') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][second_content][type]'" class="form-select" aria-label="Default select example" v-on:change="changeSecondContentType(index, $event)" v-model="getListFormSlideshow[index]['second_content']['type']">
																						<option value="text">{{ t('Text') }}</option>
																						<option value="link">{{ t('Link') }}</option>
																						<option value="button_link">{{ t('Button Link') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Desktop Position') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][second_content][desktop_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['second_content']['desktop_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-md-6">
																					<label class="form-label">{{ t('Mobile Position') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][second_content][mobile_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['second_content']['mobile_position']">
																						<option value="center-center" selected>{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label" v-if="getListFormSlideshow[index]['second_content']['type'] == 'text'" v-cloak>{{ t('Text') }}</label>
																					<label class="form-label" v-if="getListFormSlideshow[index]['second_content']['type'] !== 'text'" v-cloak>{{ t('Link Title') }}</label>

																					<input type="text" :name="'cover_image[slideshow]['+index+'][second_content][text]'" class="form-control font-size-inherit" placeholder="Enter your text or title" v-model="getListFormSlideshow[index]['second_content']['text']">
																				</div>

																				<div class="col-12" v-if="getListFormSlideshow[index]['second_content']['type'] !== 'text'" v-cloak>
																					<label class="form-label">{{ t('Link') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][second_content][link]'" class="form-control font-size-inherit" placeholder="Enter your link here" v-model="getListFormSlideshow[index]['second_content']['link']">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Button Settings') }}</div>

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				
																				<div class="col-12">
																					<label class="form-label">{{ t('Disable Button 1') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][button][0][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['button'][0]['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Title Button {1}', '1') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][button][0][title]'" class="form-control font-size-inherit" placeholder="Enter your title button here" v-model="getListFormSlideshow[index]['button'][0]['title']">
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Link Button {1}', '1') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][button][0][link]'" class="form-control font-size-inherit" placeholder="Enter your link button here" v-model="getListFormSlideshow[index]['button'][0]['link']">
																				</div>
																				
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Disable Button 2') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][button][1][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['button'][1]['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Title Button {1}', '2') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][button][1][title]'" class="form-control font-size-inherit" placeholder="Enter your title button here" v-model="getListFormSlideshow[index]['button'][1]['title']">
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Link Button {1}', '2') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][button][1][link]'" class="form-control font-size-inherit" placeholder="Enter your link button here" v-model="getListFormSlideshow[index]['button'][1]['link']">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Link for Content') }}</div>	

																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Is Active Link for Image?') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][link][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['link']['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Link for Image?') }}</label>
																					<input type="text" :name="'cover_image[slideshow]['+index+'][link][content]'" class="form-control font-size-inherit" placeholder="Enter your link here" v-model="getListFormSlideshow[index]['link']['content']">
																				</div>
																			</div>
																		</div>
																	</div>
																</div>

																<div class="mb-5">
																	<div class="h6 border-bottom pb-3 mb-3"><i class="fas fa-cog fa-fw me-1"></i> {{ t('Countdown for Content') }}</div>	
																	<div class="row g-3">
																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Is Active Countdown for Image?') }}</label>
																					
																					<select :name="'cover_image[slideshow]['+index+'][countdown][is_active]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['countdown']['is_active']">
																						<option value="active">{{ t('Active') }}</option>
																						<option value="inactive">{{ t('Inactive') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown for Image') }}</label>
																					<vue-date-picker v-model="getListFormSlideshow[index]['countdown']['content_default']" @date-update="updateDateForCountDownSlideshow(index, $event)" :format="'MM/dd/yyyy HH:mm'" locale="ID" auto-apply></vue-date-picker>
																					
																					<input type="hidden" :name="'cover_image[slideshow]['+index+'][countdown][content]'" class="form-control font-size-inherit" placeholder="Enter your countdown here" v-model="getListFormSlideshow[index]['countdown']['content']">
																					<input type="hidden" :name="'cover_image[slideshow]['+index+'][countdown][content_default]'" class="form-control font-size-inherit" placeholder="Enter your countdown here" v-model="getListFormSlideshow[index]['countdown']['content_default']">
																				</div>
																			</div>
																		</div>

																		<div class="col-md-6">
																			<div class="row g-3">
																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown Desktop Position') }}</label>

																					<select :name="'cover_image[slideshow]['+index+'][countdown][desktop_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['countdown']['desktop_position']">
																						<option value="default" selected>{{ t('Default') }}</option>
																						<option value="center-center">{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>

																				<div class="col-12">
																					<label class="form-label">{{ t('Countdown Mobile Position') }}</label>

																					<select :name="'cover_image[slideshow]['+index+'][countdown][mobile_position]'" class="form-select" aria-label="Default select example" v-model="getListFormSlideshow[index]['countdown']['mobile_position']">
																						<option value="default" selected>{{ t('Default') }}</option>
																						<option value="center-center">{{ t('Center Center') }}</option>
																						<option value="top-center">{{ t('Top Center') }}</option>
																						<option value="bottom-center">{{ t('Bottom Center') }}</option>

																						<option value="top-left">{{ t('Top Left') }}</option>
																						<option value="center-left">{{ t('Center Left') }}</option>
																						<option value="bottom-left">{{ t('Bottom Left') }}</option>

																						<option value="top-right">{{ t('Top Right') }}</option>
																						<option value="center-right">{{ t('Center Right') }}</option>
																						<option value="bottom-right">{{ t('Bottom Right') }}</option>
																					</select>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

													</div>

												</template>
											</draggable>
										</div>								
									</div>
								</div>
							</div>
						</transition>
					</div>
				</form>

				<!--- Delete Data Modal--->
				<div class="modal ph-modal-delete fade" id="ph-submit-data-coverimage-modalDelete" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalDeleteLabel" aria-hidden="true">
					<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
						<div class="modal-content">
							
							<form action="{{ url('manage_coverimage/delete_single_data/'.$data->id) }}" method="get" auto-refresh-data="false" auto-reset-form="false" auto-lock-button="true" cs-value-button-cancel="{{ t('Cancel') }}" @submit="submitDataModalDetailData($event, 'modalDelete')" ref="formHTML-modalDelete">

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
											<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeModalBeforeSubmit('ph-submit-data-coverimage-modalDelete', 'ph-submit-data-coverimage-modalDelete')">{{ t('No, keep it') }}</button>
										</div>

										<div class="col-auto">
											<input type="hidden" class="index-form-list">
											<input type="hidden" class="type-form-list">
											<button type="submit" class="btn btn-larapx btn-submit-data font-size-inherit">{{ t('Yes, Delete') }}</button>
										</div>
									</div>
								</div>

							</form>

						</div>
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

	<script>
	const targetElement = document.getElementById('ph-content-button-submit');

	const observer = new IntersectionObserver(([entry]) => 
	{
		if (entry.intersectionRatio < 1) 
		{
			// Element is in its sticky state (pinned)
			targetElement.classList.remove('shadow-sm');
			targetElement.classList.remove('bg-body-secondary');

			// console.log('Pinned');
		} 
		else 
		{
			// Element is not in its sticky state
			targetElement.classList.add('shadow-sm');
			targetElement.classList.add('bg-body-secondary');

			// console.log('Not pinned state');
		}

		// console.log(entry.intersectionRatio)
	}, 
	{
		root: document.documentElement,
		rootMargin: '-70px 0px 0px 0px',
		threshold: [1],
	});

	observer.observe(targetElement);
	</script>
@endpush