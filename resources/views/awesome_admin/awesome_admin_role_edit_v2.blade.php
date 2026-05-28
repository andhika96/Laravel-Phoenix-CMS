@extends('themes.'.custom_theme('cms'))

@section('title')
	{{ t('Edit Role & Permissions') }}
@endsection

@section('content')
	<style>
	.form-check-input
	{
		width: 1.15rem;
		height: 1.15rem;
	}
	</style>

	<div id="ph-app-manage-role">
		<div class="mb-3">
			{{ Breadcrumbs::render('awesome_admin.role.edit') }}
		</div>

		<div id="ph-submit-data-role">
			<form action="{{ route('cms.admin.awesome_admin.role.updateV2', $role->id) }}" method="post" ref="formHTML" auto-lock-button="false" auto-block-button-mobile="true" custom-button-value="Submit" @submit.prevent="submitData($event)">

				<div class="ph-content p-3 mb-3 rounded">
					<div class="row g-3">
						<div class="col-md-6 d-flex align-items-center">
							<h4 class="mb-0">{{ t('Edit Role & Permissions') }}</h4>
						</div>

						<div class="col-md-6 d-flex align-items-center justify-content-end">
							<button type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit">{{ t('Save') }}</button>
						</div>
					</div>
				</div>

				<div class="ph-content p-4 rounded">
					<div class="ph-notice" v-cloak>
						<div aria-live="polite" aria-atomic="true" class="position-relative">
							<div class="toast-container position-fixed top-0 end-0 p-3">
								<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
									<div :class="'toast-header p3-3 pt-3 pb-1 '+responseStatusToast+' border-0'">
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

					<div class="mb-4">
						<div class="row g-3 g-lg-0 mb-1">
							<label class="col-6 form-label d-flex align-items-center">
								<div>
									{{ t('Role Name') }}
								</div>
							</label>

							<div class="col-6 d-flex align-items-center justify-content-end">
								<div class="d-block d-flex align-items-center justify-content-center">
									<div class="form-check m-0">
										<input type="checkbox" style="margin-top: 0.1em" class="form-check-input me-2" id="clickSelectAllPermission" v-on:click="clickSelectAllPermission">
										
										<label class="form-check-label" for="clickSelectAllPermission">
											{{ t('Select All') }}
										</label>
									</div>
								</div>
							</div>
						</div>

						<input type="text" name="role_name" class="form-control font-size-inherit" value="{{ $role->name }}">
					</div>

					<div class="table-responsive" data-simplebar>
						<table class="table">
							<thead>
								<tr>
									<th scope="col" width="50" class="fw-bold text-nowrap">{{ t('Menu Access') }}</th>

									@foreach ($permissions as $permission)
										@php
											$permission_index = $loop->index;
										@endphp

										<th scope="col" class="fw-bold text-nowrap text-center">
											<div>
												{{ Str::title($permission) }}

												<div class="d-block d-flex justify-content-center mt-3">
													<div class="form-check p-0" style="min-height: 0 !important">
														<input type="checkbox" class="form-check-input check-all-permission m-0" id="clickSubSelectAll{{ Str::replace(' ', '', $permission) }}" v-on:click="clickSubSelectAllPermission('{{ Str::replace(' ', '', $permission) }}')">
														<label class="form-check-label" for="clickSubSelectAll{{ Str::replace(' ', '', $permission) }}"></label>
													</div>
												</div>
											</div>											
										</th>
									@endforeach
								</tr>
							</thead>	

							<tbody>
								@foreach ($menus as $menu)

									@if ($menu['is_for_parent_menu'] == 'single')
										<tr>
											<td width="60%" class="fw-bold text-nowrap">
												{{ $menu['parent_name'] }}

												<input type="hidden" name="menu_vars[{{ $menu['index'] }}][category_code]" value="{{ $menu['category_code'] }}">
												<input type="hidden" name="menu_vars[{{ $menu['index'] }}][parent_code]" value="{{ $menu['parent_code'] }}">

												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][category_code]" value="{{ $menu['category_code'] }}">
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_type]" value="{{ $menu['is_for_parent_menu'] }}">
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_name]" value="{{ $menu['parent_name'] }}">
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_link]" value="{{ $menu['parent_link'] }}">
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_code]" value="{{ $menu['parent_code'] }}">
											</td>

											@foreach ($permissions as $permission)
												<td>
													<div class="fw-bold d-flex justify-content-center text-center">
														<div class="form-check p-0">
															<input class="form-check-input check-all-permission check-permission-{{ Str::replace(' ', '', $permission) }} m-0" type="checkbox" name="menu_vars[{{ $menu['index'] }}][parent_permissions][]" value="{{ $permission }}" id="flexCheckDefault" @if (isset(getDataCustomPermissions($role->id, $menu['parent_code'])[$permission])) checked @endif>
														</div>
													</div>
												</td>
											@endforeach
										</tr>
									@endif

									@if ($menu['is_for_parent_menu'] == 'parent')
										<tr>
											<td width="60%" colspan="{{ $permission_index + 2 }}">
												<div class="fw-bold">{{ $menu['parent_name'] }}</div>

												@php
													$parent_index = $loop->index;
												@endphp
												
												<input type="hidden" name="menu_vars[{{ $menu['index'] }}][parent_code]" value="{{ $menu['parent_code'] }}"> 
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_type]" value="{{ $menu['is_for_parent_menu'] }}">						
												<input type="hidden" name="menu_vars_custom[{{ $menu['index'] }}][parent_code]" value="{{ $menu['parent_code'] }}">
											</td>
										</tr>

										@isset($menu['parent_submenu'])
											@if (count($menu['parent_submenu']['list']) > 0)
												@foreach ($menu['parent_submenu']['list'] as $submenu)
													<tr>
														<td width="60%" style="padding-left: 3rem !important">
															{{ $submenu['submenu_name'] }}

															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][submenu_type]" value="submenu">
															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][category_code]" value="{{ $menu['category_code'] }}">
															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][parent_code]" value="{{ $submenu['parent_code'] }}">	
															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][submenu_code]" value="{{ $submenu['submenu_code'] }}">
															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][submenu_name]" value="{{ $submenu['submenu_name'] }}">
															<input type="hidden" name="menu_vars_custom[{{ $parent_index }}][submenu_list][{{ $submenu['index'] }}][submenu_link]" value="{{ $submenu['submenu_link'] }}">							
														</td>

														@foreach ($permissions as $permission)
														<td>
															<div class="fw-bold d-flex justify-content-center text-center">
																<div class="form-check p-0">
																	<input class="form-check-input check-all-permission check-permission-{{ Str::replace(' ', '', $permission) }} m-0" type="checkbox" name="menu_vars[{{ $parent_index }}][parent_submenu][list][{{ $submenu['index'] }}][{{ $submenu['submenu_code'] }}][submenu_permissions][]" value="{{ $permission }}" id="flexCheckDefault" @if (isset(getDataCustomPermissions($role->id, $submenu['submenu_code'])[$permission])) checked @endif>
																</div>
															</div>
														</td>
														@endforeach
													</tr>
												@endforeach
											@endif
										@endisset
										
									@endif

								@endforeach
							</tbody>							
						</table>
					</div>

				</div>
			</form>
		</div>
	</div>
@endsection

@pushonce('js')
	<script src="{{ url('assets/js/vue3/manage_role/vueV3-manage-role-2026.js?v=').time() }}"></script>
@endpushonce