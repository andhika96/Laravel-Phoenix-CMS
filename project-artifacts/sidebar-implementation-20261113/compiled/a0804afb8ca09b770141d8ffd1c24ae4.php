<?php $__env->startSection('title'); ?>
	<?php echo e(t('Account Settings')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
	<div id="ph-app-account">
		<div class="mb-4">
			<h4 class="mb-3"><i class="fad fa-cogs fa-fw me-1"></i> <?php echo e(t('Account Settings')); ?></h4>
		</div>

		<div class="row g-3">
			<div class="col-md-4">
				<div class="ph-content rounded">
					<ul class="list-group list-group-flush">
						<a href="javascript:void(0)" v-on:click="changeUserEditTab('general')" class="list-group-item list-group-item-action p-3 text-decoration-none rounded-top">
							<i class="fas fa-cog fa-fw me-1"></i> <?php echo e(t('General')); ?>

						</a>
						
						<a href="javascript:void(0)" v-on:click="changeUserEditTab('password')" class="list-group-item list-group-item-action p-3 text-decoration-none rounded-bottom">
							<i class="fas fa-lock-alt fa-fw me-1"></i> <?php echo e(t('Password')); ?>

						</a>
					</ul>
				</div>
			</div>

			<div class="col-md-8">
				<div class="ph-content rounded p-4" id="edit-user-page">
					<div id="ph-form-submit-data">
						<form action="<?php echo e(route('cms.core.account.update2')); ?>" method="post" ref="formHTML" @submit.prevent="submitData">
							
							<div class="ph-notice" v-cloak>
								<div aria-live="polite" aria-atomic="true" class="position-relative">
									<div class="toast-container position-fixed top-0 end-0 p-3">
										<div :class="'toast ph-notice-toast ph-callout-no-border '+responseStatusToast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
											<div :class="'toast-header '+responseStatusToast+' pe-3 pt-3 pb-1 border-0'">
												<strong class="toast-header-title toast-header-icon me-auto">Notice</strong>
												<small>just now</small>
												<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close" style="margin-right: calc(-.1 * var(--bs-toast-padding-x));"></button>
											</div>
										
											<div class="toast-body p-3 text-start">
												<div v-if="isArrayMessageAfterSubmit == 1">
													<ul class="ps-3 m-0">
														<li v-for="(item, index) in responseMessageAfterSubmit">
															{{ item[0] }}
														</li>
													</ul>
												</div>

												<div v-else>
													{{ responseMessageAfterSubmit }}
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div v-if="loading" class="text-center p-5">
								<div class="spinner-border text-primary mb-2" role="status">
									<span class="sr-only"></span>
								</div>

								<div class="h6 m-0"><?php echo e(t('Loading')); ?> ...</div>
							</div>

							<div v-else class="ph-data-load-content" v-cloak>
								<div v-if="editUserTabActive == 'general'">
									<div class="row g-3 mb-4">
										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Email Address')); ?></label>
											<input type="text" name="email" class="form-control font-size-inherit" value="<?php echo e($data->email); ?>" disabled>
										</div>

										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Username')); ?></label>
											<input type="text" name="username" class="form-control font-size-inherit" value="<?php echo e($data->username); ?>" disabled>
										</div>

										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Fullname')); ?></label>
											<input type="text" name="fullname" class="form-control font-size-inherit" value="<?php echo e($data->fullname); ?>">
										</div>

										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Birthdate')); ?></label>
											<input type="date" name="birthdate" class="form-control font-size-inherit" value="<?php echo e($data_information->birthdate); ?>">
										</div>

										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Gender')); ?></label>
											<select name="gender" class="form-select font-size-inherit">
												<option value=""><?php echo e(t('Select Gender')); ?></option>
												<option value="1" <?php echo e(($data_information->gender == 1) ? 'selected' : ''); ?>><?php echo e(t('Male')); ?></option>
												<option value="2" <?php echo e(($data_information->gender == 2) ? 'selected' : ''); ?>><?php echo e(t('Female')); ?></option>
											</select>
										</div>

										<div class="col-md-6">
											<label class="form-label"><?php echo e(t('Phone Number')); ?></label>
											<input type="text" name="phone_number" class="form-control font-size-inherit" value="<?php echo e($data_information->phone_number); ?>">
										</div>
									</div>

									<input type="hidden" name="submitType" value="general">
								</div>

								<div v-else-if="editUserTabActive == 'password'">
									<div class="row g-3 mb-4">
										<div class="col-md-12">
											<label class="form-label"><?php echo e(t('Enter New Password')); ?></label>
											<input type="text" name="password" class="form-control font-size-inherit">
										</div>

										<div class="col-md-12">
											<label class="form-label"><?php echo e(t('Re-type New Password')); ?></label>
											<input type="text" name="password_confirmation" class="form-control font-size-inherit">
										</div>
									</div>

									<input type="hidden" name="submitType" value="password">
								</div>
							</div>

							<div class="form-group text-end">
								<input type="submit" class="btn ph-btn-theme btn-submit-data font-size-inherit" value="<?php echo e(t('Save')); ?>">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php if (! $__env->hasRenderedOnce('adb71c1c-3777-45af-82a3-17890427ebb1')): $__env->markAsRenderedOnce('adb71c1c-3777-45af-82a3-17890427ebb1');
$__env->startPush('js'); ?>
	<script src="<?php echo e(url('assets/js/vue3/account/vueV3-account-2026.js?v=').time()); ?>"></script>
<?php $__env->stopPush(); endif; ?>
<?php echo $__env->make('themes.arunika_equinox.cms.cms_layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laragon\www\laravel-13-phoenix\project-artifacts\sidebar-implementation-20261113/compiled/5a36814e9260b73864ee42f1f45dd370.blade.php ENDPATH**/ ?>