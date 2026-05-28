@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Manage Frontend Submenus') }}
@endsection

@section('content')
	<div id="ph-app-manage-menu">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.menu.fe.submenu') }}
		</div>

		<div class="ph-content rounded p-3 mb-3">
			<div class="row g-3">
				<div class="col-md-6 d-flex align-items-center">
					<h4 class="mb-0">{{ t('Manage Frontend Submenus') }}</h4>
				</div>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-6">
				<div class="ph-content rounded p-4 ph-fetch-listdata-menu" data-url="{{ url('awesome_admin/menu/fe/listdata/submenu') }}">
					<div class="h5 border-bottom pb-3 mb-4"><i class="fad fa-list-ul fa-fw me-1"></i> {{ t('Parent Menu List') }}</div>

					<div v-if="loading" class="text-center p-5">
						<div class="spinner-border text-primary mb-2" role="status">
							<span class="sr-only"></span>
						</div>

						<div class="h6">{{ t('Loading') }} ...</div>
					</div>

					<div v-else-if="responseStatusMenu == 'failed'" class="ph-data-load-status border rounded text-center text-danger p-3 mb-3" style="display: none">
						@{{ responseMessageMenu }}
					</div>

					<div v-else class="ph-data-load-content" style="display: none">
						<div class="list-group list-group-flush mb-3">
							<div class="list-group-item px-0" v-for="(element, index) in responseDataMenu" :key="menu_name">
								<div class="row g-1">
									<div class="col-md-6 mb-3 mb-md-0 d-flex align-items-center">
										<i class="fas fa-arrows-alt-v fa-fw me-1"></i> @{{ element.parent_name }}
									</div>

									<div class="col-md-6 d-flex align-items-center justify-content-end">
										<a :href="'{{ url('awesome_admin/menu/fe/submenu/detail/\'+element.parent_code+\'') }}'" class="btn btn-link text-danger font-size-inherit">View detail</a> |
										<a href="javascript:void(0)" class="btn btn-link text-danger ar-alert-bootbox font-size-inherit" v-on:click="openDeleteModalSubmenu(responseDataMenu, element.id)">Remove</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="ph-content rounded p-4">
					<div class="h5 border-bottom pb-3 mb-3"><i class="fad fa-plus fa-fw me-1"></i> {{ t('Add Submenu') }}</div>

					<div id="ph-form-manage-menu">
						<form action="{{ route('cms.admin.awesome_admin.menu.fe.create_submenu') }}" method="post" ref="formHTML" @submit.prevent="submitDataSubmenu">
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

							<div class="row g-3">
								<div class="col-12 form-group ph-fetch-listdata-parentmenu" data-url="{{ url('awesome_admin/menu/fe/listdata/parentmenuforsubmenu') }}">
									<label class="form-label">{{ t('Select Parent Menu') }}</label>

									<select name="parent_code" class="form-select font-size-inherit" v-on:change="selectParentMenuforSubmenu($event)" aria-label="Select Parent Menu">
										<option value="">{{ t('Select') }}</option>
										
										<option :value="element.parent_code" v-for="(element, index) in responseDataParentMenu">
											@{{ element.parent_name }}
										</option>
									</select>
								</div>

								<div class="col-12 form-group text-end">
									<input type="hidden" class="parent_name_for_submenu" name="parent_name">
									<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="{{ t('Add') }}">
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal Delete Submenu -->
		<Teleport to="body">
			<div class="modal ph-modal-delete fade" id="modalDeleteSubmenu" tabindex="-1" aria-labelledby="modalDeleteSubmenuLabel" aria-hidden="true">
				<div class="modal-dialog ph-modal-dialog modal-dialog-centered">
					<div class="modal-content" id="ph-form-delete-submenu">
						<form action="{{ url('awesome_admin/menu/fe/delete_submenu') }}" method="post" auto-refresh="true" auto-lock-button="true" custom-button-value="<i class='fas fa-trash-alt fa-fw me-1'></i> {{ t('Delete') }}" ref="formHTMLdelete" @submit="submitDataDeleteParentMenu($event)">
							
							<div class="ph-notice" v-cloak>
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
											<div :class="'toast-header '+responseStatusToast+' px-3 pt-3 pb-1 border-0'">
												<strong class="me-auto">{{ t('Notice') }}</strong>
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
								<h5 class="modal-title" id="modalDeleteSubmenuLabel">{{ t('Delete Submenu') }}</h5>
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
								<input type="hidden" name="data_id" class="data-id">

								<div class="row gx-2 justify-content-center">
									<div class="col-md-auto">
										<button type="button" class="btn btn-secondary font-size-inherit" v-on:click="closeDeleteModalSubmenu">{{ t('No, keep it') }}</button>
									</div>

									<div class="col-md-auto">
										<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Yes, Delete') }}</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</Teleport>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_menu/fe/vueV3-manage-frontend-submenu-2026.js?v=').time() }}"></script>
@endpushonce