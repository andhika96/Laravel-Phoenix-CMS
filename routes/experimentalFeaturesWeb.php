<?php

Route::name('cms.core.')
	->prefix('experimental')
	->namespace('App\Http\Controllers\Web\Experimental')
	->group(function() 
	{
		Route::controller(App\Http\Controllers\Web\Experimental\Auth\Auth_Controller::class)->group(function()
		{
			Route::get('auth/login', 'login')->name('cms.core.experimental.auth.login');
			Route::post('auth/login', 'authenticate')->name('cms.core.experimental.auth.login.authenticate');

			Route::get('auth/signup', 'signup')->name('cms.core.experimental.auth.signup');
			Route::post('auth/signup', 'signupProcess')->name('cms.core.experimental.auth.signup.process');

			Route::get('auth/logout', 'logout')->name('cms.core.experimental.auth.logout');
			Route::get('auth/logout_sso', 'logoutSSO')->name('cms.core.experimental.auth.logout.sso');

			Route::get('auth/forgotpassword', 'forgotpassword')->name('cms.core.experimental.auth.forgotpassword');
			Route::post('auth/forgotpassword', 'forgotpasswordProcess')->name('cms.core.experimental.auth.forgotpassword.process');

			Route::get('auth/recoveryaccount', 'resetpassword')->name('cms.core.experimental.auth.resetpassword');
			Route::post('auth/recoveryaccount', 'resetpasswordProcess')->name('cms.core.experimental.auth.resetpassword.process');

			// Check username and email
			Route::get('/auth/checkdata', 'checkUserData')->name('cms.core.experimental.auth.user.checkdata');

			Route::get('auth/login_split_left', 'loginSplitLeft')->name('cms.core.experimental.auth.loginSplitLeft');
			Route::get('auth/signup_split_left', 'signupSplitLeft')->name('cms.core.experimental.auth.signupSplitLeft');
			Route::get('auth/forgot_split_left', 'forgotSplitLeft')->name('cms.core.experimental.auth.forgotSplitLeft');
			Route::get('auth/recovery_split_left', 'recoverySplitLeft')->name('cms.core.experimental.auth.recoverySplitLeft');

			Route::get('auth/login_split_right', 'loginSplitRight')->name('cms.core.experimental.auth.loginSplitRight');
			Route::get('auth/signup_split_right', 'signupSplitRight')->name('cms.core.experimental.auth.signupSplitRight');
			Route::get('auth/forgot_split_right', 'forgotSplitRight')->name('cms.core.experimental.auth.forgotSplitRight');
			Route::get('auth/recovery_split_right', 'recoverySplitRight')->name('cms.core.experimental.auth.recoverySplitRight');
		});
	});