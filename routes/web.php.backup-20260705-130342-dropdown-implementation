<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Laravel\Socialite\Facades\Socialite;

// require __DIR__.'/experimentalFeaturesWeb.php';
require __DIR__.'/experimentalFeaturesWebv2.php';

// Route::get('/', function() 
// {
// 	return view('welcome');
// });

Route::controller(App\Http\Controllers\Web\Homepage\Homepage_Controller::class)->group(function()
{
	Route::get('/home', 'index')->name('cms.core.homepage');
	Route::get('/home/listdata', 'listData')->name('cms.core.homepage.listdata');
});

/* ──────────────────────────────────────────────────────────
 *  CMS Chat — Real-time messaging antar user (Laravel Reverb)
 * ────────────────────────────────────────────────────────── */
Route::middleware(['auth', 'checkSuspended'])
	->prefix('chat')
	->name('cms.chat.')
	->controller(App\Http\Controllers\Web\Chat\Chat_Controller::class)
	->group(function ()
	{
		// Halaman utama chat
		Route::get('/', 'index')->name('index');

		// API endpoints (return JSON)
		Route::get('/api/users',                     'users')->name('api.users');
		Route::get('/api/conversations',             'conversations')->name('api.conversations');
		Route::post('/api/conversations/open',       'openConversation')->name('api.open');
		Route::get('/api/conversations/{id}/messages', 'messages')->name('api.messages');
		Route::post('/api/conversations/{id}/send',    'send')->name('api.send');
		Route::post('/api/conversations/{id}/typing',  'typing')->name('api.typing');
	});

Route::name('cms.core.')
	->prefix('article')
	->namespace('App\Http\Controllers\Web\Articles')
	->group(function() 
	{
		Route::controller(App\Http\Controllers\Web\Articles\Article_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('article')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/{idOrSlug}', 'detail')->name('article.detail')->middleware('auth', 'checkSuspended', 'permission:update data');
			Route::get('/listdata', 'listData')->name('article.listdata')->middleware('auth', 'checkSuspended', 'permission:read data');
		});
	});

/* New route for asset files URL */

Route::get('/storage/{path}', function ($path)
{
	$file = storage_path('app/public/'.$path);

	if ( ! file_exists($file))
	{
		abort(404);
	}

	$ext      = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	$mimeType = \Symfony\Component\Mime\MimeTypes::getDefault()->getMimeTypes($ext)[0]
	            ?? mime_content_type($file);

	return response()->file($file, ['Content-Type' => $mimeType]);

})->where('path', '.*');

// ------------------------------------------------------------------------

/* Authenticaton Login, Logout & Signup */

// Redirect empty page index auth to auth/login
Route::redirect('/auth', '/auth/login');

// Redirect url default laravel to new custom url
Route::redirect('/login', '/auth/login')->name('login');

Route::controller(App\Http\Controllers\Web\Auth\Auth_Controller::class)->group(function()
{
	Route::get('/', 'login')->name('cms.core.auth');
	Route::get('auth/login', 'login')->name('cms.core.auth.login');
	Route::post('auth/login', 'authenticate')->name('cms.core.auth.login.authenticate');

	Route::get('auth/signup', 'signup')->name('cms.core.auth.signup');
	Route::post('auth/signup', 'signupProcess')->name('cms.core.auth.signup.process');

	Route::get('auth/logout', 'logout')->name('cms.core.auth.logout');
	Route::get('auth/logout_sso', 'logoutSSO')->name('cms.core.auth.logout.sso');

	Route::get('auth/forgotpassword', 'forgotpassword')->name('cms.core.auth.forgotpassword');
	Route::post('auth/forgotpassword', 'forgotpasswordProcess')->name('cms.core.auth.forgotpassword.process');

	Route::get('auth/recoveryaccount', 'resetpassword')->name('cms.core.auth.resetpassword');
	Route::post('auth/recoveryaccount', 'resetpasswordProcess')->name('cms.core.auth.resetpassword.process');

	// Check username and email
	Route::get('/auth/checkdata', 'checkUserData')->name('cms.core.auth.user.checkdata');
});

Route::name('cms.core.')
	->prefix('dashboard')
	->namespace('App\Http\Controllers\Web\Dashboard')
	->group(function() 
	{
		Route::controller(\Dashboard_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('dashboard')->middleware('auth', 'checkSuspended');
			Route::get('/menu', 'menu')->name('dashboard.menu')->middleware('auth', 'checkSuspended');
			Route::get('/setverticalsidebarmenu', 'setVerticalSidebarMenuCollapse')->name('dashboard.setVerticalSidebarMenuCollapse')->middleware('auth', 'checkSuspended');
			Route::get('/removeverticalsidebarmenu', 'removeVerticalSidebarMenuCollapse')->name('dashboard.removeVerticalSidebarMenuCollapse')->middleware('auth', 'checkSuspended');
			Route::get('/getisverticalsidebarmenuCollapse', 'getIsVerticalSidebarMenuCollapse')->name('dashboard.getIsVerticalSidebarMenuCollapse')->middleware('auth', 'checkSuspended');
		});
	});

Route::name('cms.core.')
	->prefix('account')
	->namespace('App\Http\Controllers\Web\Account')
	->group(function() 
	{
		Route::controller(\Account_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('account')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::post('/', 'update')->name('account.update')->middleware('auth', 'checkSuspended', 'permission:submit data');
			Route::post('/update', 'update')->name('account.update2')->middleware('auth', 'checkSuspended', 'permission:submit data');

			Route::get('/general', 'general')->name('account.general')->middleware('auth', 'checkSuspended');
			Route::get('/profilepicture', 'profilePicture')->name('account.profilePicture')->middleware('auth', 'checkSuspended');
			Route::post('/profilepicture', 'updateProfilePicture')->name('account.updateProfilePicture')->middleware('auth', 'checkSuspended');
		});
	});

Route::name('cms.core.')
	->prefix('profile')
	->namespace('App\Http\Controllers\Web\Profile')
	->group(function() 
	{
		Route::controller(\Profile_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('profile')->middleware('auth', 'checkSuspended');
		});
	});

Route::name('cms.core.')
	->prefix('manage_article')
	->namespace('App\Http\Controllers\Web\Manage_Article')
	->group(function() 
	{
		Route::controller(App\Http\Controllers\Web\Manage_Article\Manage_Article_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('manage_article')->middleware('auth', 'checkSuspended', 'permission:read data');
			
			Route::get('/add', 'add')->name('manage_article.add')->middleware('auth', 'checkSuspended', 'permission:add data');
			Route::post('create', 'store')->name('manage_article.store')->middleware('auth', 'checkSuspended', 'permission:submit data');
			Route::post('create/category', 'storeCategory')->name('manage_article.store.category')->middleware('auth', 'checkSuspended', 'permission:submit data');

			Route::get('/edit/{idOrSlug}', 'edit')->name('manage_article.edit')->middleware('auth', 'checkSuspended', 'permission:update data');
			Route::post('update/{idOrSlug}', 'update')->name('manage_article.update')->middleware('auth', 'checkSuspended', 'permission:submit data');
			Route::post('update/category/submit', 'updateCategory')->name('manage_article.update.category')->middleware('auth', 'checkSuspended', 'permission:submit data');
			
			Route::post('bulk_update', 'bulk_update')->name('manage_article.bulk_update')->middleware('auth', 'checkSuspended', 'permission:update data');

			Route::post('/delete/{idOrSlug}', 'delete')->name('manage_article.delete')->middleware('auth', 'checkSuspended', 'permission:delete data');
			Route::post('/delete/category/{idOrSlug}', 'deleteCategory')->name('manage_article.delete.category')->middleware('auth', 'checkSuspended', 'permission:delete data');

			Route::get('/detaildata/{idOrSlug}', 'detailData')->name('manage_article.detaildata')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/detaildata/category/{idOrSlug}', 'detailDataCategory')->name('manage_article.detaildata.category')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/checkdata/category/{idOrSlug}', 'checkDataCategory')->name('manage_article.checkdata.category')->middleware('auth', 'checkSuspended', 'permission:read data');

			Route::get('/listdata', 'listData')->name('manage_article.listdata')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/listdata/category', 'listCategories')->name('manage_article.listdata.category')->middleware('auth', 'checkSuspended', 'permission:read data');
		});
	});

Route::name('cms.core.')
	->prefix('manage_coverimage')
	->namespace('App\Http\Controllers\Web\Manage_CoverImage')
	->group(function() 
	{
		Route::controller(App\Http\Controllers\Web\Manage_CoverImage\Manage_CoverImage_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('manage_coverimage')->middleware('auth', 'checkSuspended', 'permission:read data');
			
			Route::get('/add', 'add')->name('manage_coverimage.add')->middleware('auth', 'checkSuspended', 'permission:add data');
			Route::post('create', 'store')->name('manage_coverimage.store')->middleware('auth', 'checkSuspended', 'permission:submit data');

			Route::get('/edit/{idOrSlug}', 'edit')->name('manage_coverimage.edit')->middleware('auth', 'checkSuspended', 'permission:update data');
			Route::post('update/{idOrSlug}', 'update')->name('manage_coverimage.update')->middleware('auth', 'checkSuspended', 'permission:submit data');

			Route::post('/delete/{idOrSlug}', 'delete')->name('manage_coverimage.delete')->middleware('auth', 'checkSuspended', 'permission:delete data');
			Route::get('/delete_single_data/{idOrSlug}', 'deleteSingleCoverImageData')->name('manage_coverimage.delete.singledata')->middleware('auth', 'checkSuspended', 'permission:delete data');

			Route::get('/detaildata/{idOrSlug}', 'detailData')->name('manage_coverimage.detaildata')->middleware('auth', 'checkSuspended', 'permission:read data');
			Route::get('/listdata', 'listData')->name('manage_coverimage.listdata')->middleware('auth', 'checkSuspended', 'permission:read data');
		});
	});

Route::name('cms.core.')
	->prefix('file_manager')
	->namespace('App\Http\Controllers\Web\File_Manager')
	->group(function() 
	{
		Route::controller(\File_Manager_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('file_manager')->middleware('auth', 'checkSuspended');
		});
	});

Route::name('cms.core.')
	->prefix('notification')
	->namespace('App\Http\Controllers\Web\Notification')
	->group(function() 
	{
		Route::controller(\Notification_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('notification')->middleware('auth', 'checkSuspended');

			Route::get('/empty', 'emptyNotification')->name('notification.empty')->middleware('auth', 'checkSuspended');
			Route::get('/filled', 'filledNotification')->name('notification.filled')->middleware('auth', 'checkSuspended');

			Route::get('/testing', 'notificationTestingPage')->name('notification.testing_page')->middleware('auth', 'checkSuspended');
		});
	});

Route::name('cms.admin.')
	->prefix('awesome_admin')
	->namespace('App\Http\Controllers\Web\Awesome_Admin')
	->group(function() 
	{
		Route::controller(\Awesome_Admin_Controller::class)->group(function()
		{
			Route::get('/', 'index')->name('awesome_admin')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Config_Controller::class)->group(function()
		{
			Route::get('/config', 'index')->name('awesome_admin.config')->middleware('auth', 'checkSuspended');
			Route::post('/config', 'update')->name('awesome_admin.config.update')->middleware('auth', 'checkSuspended');

			Route::get('/config/listdata', 'listData')->name('awesome_admin.config.listdata')->middleware('auth', 'checkSuspended');
			Route::get('/config/listdata/fonts', 'listDataFonts')->name('awesome_admin.config.listdata.fonts')->middleware('auth', 'checkSuspended');
			Route::get('/config/listdata/filefonts', 'listDataFileFonts')->name('awesome_admin.config.listdata.filefonts')->middleware('auth', 'checkSuspended');
			Route::get('/config/listdata/filefonts/{idOrSlug}', 'listDataFileFontsBySlug')->name('awesome_admin.config.listdata.filefontsbyslug')->middleware('auth', 'checkSuspended');
			Route::get('/config/listdata/filefontpreview/{idOrSlug}', 'listDataFileFontForPreview')->name('awesome_admin.config.listdata.filefontpreview')->middleware('auth', 'checkSuspended');

			Route::get('/config/testurl', 'testURL')->name('awesome_admin.config.testurl')->middleware('auth', 'checkSuspended');
		});

		/*
		Route::controller(\Awesome_Admin_Menu_Controller::class)->group(function()
		{
			Route::get('/menu', 'index')->name('awesome_admin.menu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/submenu', 'submenu')->name('awesome_admin.menu.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/submenu/detail/{idOrSlug}', 'submenu_detail')->name('awesome_admin.menu.submenu.detail')->middleware('auth', 'checkSuspended');
			Route::get('/menu/categorymenu', 'categorymenu')->name('awesome_admin.menu.category_menu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/parentmenu', 'parentmenu')->name('awesome_admin.menu.parent_menu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/create_submenu', 'create_submenu')->name('awesome_admin.menu.create_submenu')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/update_submenu/{idOrSlug}', 'update_submenu')->name('awesome_admin.menu.update_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/update_submenu_detail/{idOrSlug}', 'update_submenu_detail')->name('awesome_admin.menu.update_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/delete_submenu/{idOrSlug}', 'delete_submenu')->name('awesome_admin.menu.delete_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/delete_submenu_detail', 'delete_submenu_detail')->name('awesome_admin.menu.delete_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/update_parentmenu', 'update_parentmenu')->name('awesome_admin.menu.update_parentmenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/delete_parentmenu', 'delete_parentmenu')->name('awesome_admin.menu.delete_parentmenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/update_categorymenu', 'update_categorymenu')->name('awesome_admin.menu.update_categorymenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/delete_categorymenu', 'delete_categorymenu')->name('awesome_admin.menu.delete_categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/listdata/roles', 'listDataRoles')->name('awesome_admin.menu.listdata.roles')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/categorymenu', 'listDataCategoryMenu')->name('awesome_admin.menu.listdata.categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/listdata/parentmenu', 'listDataParentMenu')->name('awesome_admin.menu.listdata.parentmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/parentmenuforsubmenu', 'listDataParentMenuForSubmenu')->name('awesome_admin.menu.listdata.parentmenuforsubmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/categorymenuforparentmenu', 'listDataCategoryMenuForParentMenu')->name('awesome_admin.menu.listdata.categorymenuforparentmenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/listdata/submenu', 'listDataSubmenu')->name('awesome_admin.menu.listdata.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/submenu/detail/{idOrSlug}', 'listDataSubmenuDetail')->name('awesome_admin.menu.listdata.submenu.detail')->middleware('auth', 'checkSuspended');

			Route::get('/menu/listdata/routes', 'listDataRoutes')->name('awesome_admin.menu.listdata.routelist')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/permissions', 'listDataPermissions')->name('awesome_admin.menu.listdata.permissionlist')->middleware('auth', 'checkSuspended');

			Route::get('/menu/getversionmenu', 'getVersionMenu')->name('awesome_admin.menu.getVersionMenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/checkparentmenulink', 'checkParentMenuLink')->name('awesome_admin.menu.checkParentMenuLink')->middleware('auth', 'checkSuspended');
			Route::post('/menu/checksubmenulink', 'checkSubmenuLink')->name('awesome_admin.menu.checkSubmenuLink')->middleware('auth', 'checkSuspended');

			Route::get('/menu/listdata/testing1', 'testing1')->name('awesome_admin.menu.listdata.testing1')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/testing2', 'testing2')->name('awesome_admin.menu.listdata.testing2')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/testing3', 'testing3')->name('awesome_admin.menu.listdata.testing3')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/testing4', 'testing4')->name('awesome_admin.menu.listdata.testing4')->middleware('auth', 'checkSuspended');
			Route::get('/menu/listdata/testing5', 'testing5')->name('awesome_admin.menu.listdata.testing5')->middleware('auth', 'checkSuspended');
		});
		*/

		Route::controller(\Awesome_Admin_Menu_Controller::class)->group(function()
		{
			Route::get('/menu', 'index')->name('awesome_admin.menu')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Menu_BE_Controller::class)->group(function()
		{
			Route::get('/menu/be', 'index')->name('awesome_admin.menu.be')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/submenu', 'submenu')->name('awesome_admin.menu.be.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/submenu/detail/{idOrSlug}', 'submenu_detail')->name('awesome_admin.menu.be.submenu.detail')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/categorymenu', 'categorymenu')->name('awesome_admin.menu.be.category_menu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/parentmenu', 'parentmenu')->name('awesome_admin.menu.be.parent_menu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/be/create_submenu', 'create_submenu')->name('awesome_admin.menu.be.create_submenu')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/be/update_submenu/{idOrSlug}', 'update_submenu')->name('awesome_admin.menu.be.update_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/be/update_submenu_detail/{idOrSlug}', 'update_submenu_detail')->name('awesome_admin.menu.be.update_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/be/delete_submenu/{idOrSlug}', 'delete_submenu')->name('awesome_admin.menu.be.delete_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/be/delete_submenu_detail', 'delete_submenu_detail')->name('awesome_admin.menu.be.delete_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/be/update_parentmenu', 'update_parentmenu')->name('awesome_admin.menu.be.update_parentmenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/be/delete_parentmenu', 'delete_parentmenu')->name('awesome_admin.menu.be.delete_parentmenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/be/update_categorymenu', 'update_categorymenu')->name('awesome_admin.menu.be.update_categorymenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/be/delete_categorymenu', 'delete_categorymenu')->name('awesome_admin.menu.be.delete_categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/be/listdata/roles', 'listDataRoles')->name('awesome_admin.menu.be.listdata.roles')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/listdata/categorymenu', 'listDataCategoryMenu')->name('awesome_admin.menu.be.listdata.categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/be/listdata/parentmenu', 'listDataParentMenu')->name('awesome_admin.menu.be.listdata.parentmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/listdata/parentmenuforsubmenu', 'listDataParentMenuForSubmenu')->name('awesome_admin.menu.be.listdata.parentmenuforsubmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/listdata/categorymenuforparentmenu', 'listDataCategoryMenuForParentMenu')->name('awesome_admin.menu.be.listdata.categorymenuforparentmenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/be/listdata/submenu', 'listDataSubmenu')->name('awesome_admin.menu.be.listdata.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/listdata/submenu/detail/{idOrSlug}', 'listDataSubmenuDetail')->name('awesome_admin.menu.be.listdata.submenu.detail')->middleware('auth', 'checkSuspended');

			Route::get('/menu/be/listdata/routes', 'listDataRoutes')->name('awesome_admin.menu.be.listdata.routelist')->middleware('auth', 'checkSuspended');
			Route::get('/menu/be/listdata/permissions', 'listDataPermissions')->name('awesome_admin.menu.be.listdata.permissionlist')->middleware('auth', 'checkSuspended');

			Route::get('/menu/be/getversionmenu', 'getVersionMenu')->name('awesome_admin.menu.be.getVersionMenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/be/checkparentmenulink', 'checkParentMenuLink')->name('awesome_admin.menu.be.checkParentMenuLink')->middleware('auth', 'checkSuspended');
			Route::post('/menu/be/checksubmenulink', 'checkSubmenuLink')->name('awesome_admin.menu.be.checkSubmenuLink')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Menu_FE_Controller::class)->group(function()
		{
			Route::get('/menu/fe', 'index')->name('awesome_admin.menu.fe')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/submenu', 'submenu')->name('awesome_admin.menu.fe.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/submenu/detail/{idOrSlug}', 'submenu_detail')->name('awesome_admin.menu.fe.submenu.detail')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/categorymenu', 'categorymenu')->name('awesome_admin.menu.fe.category_menu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/parentmenu', 'parentmenu')->name('awesome_admin.menu.fe.parent_menu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/fe/create_submenu', 'create_submenu')->name('awesome_admin.menu.fe.create_submenu')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/fe/update_submenu/{idOrSlug}', 'update_submenu')->name('awesome_admin.menu.fe.update_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/fe/update_submenu_detail/{idOrSlug}', 'update_submenu_detail')->name('awesome_admin.menu.fe.update_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/fe/delete_submenu/{idOrSlug}', 'delete_submenu')->name('awesome_admin.menu.fe.delete_submenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/fe/delete_submenu_detail', 'delete_submenu_detail')->name('awesome_admin.menu.fe.delete_submenu_detail')->middleware('auth', 'checkSuspended');
			
			Route::post('/menu/fe/update_parentmenu', 'update_parentmenu')->name('awesome_admin.menu.fe.update_parentmenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/fe/delete_parentmenu', 'delete_parentmenu')->name('awesome_admin.menu.fe.delete_parentmenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/fe/update_categorymenu', 'update_categorymenu')->name('awesome_admin.menu.fe.update_categorymenu')->middleware('auth', 'checkSuspended');
			Route::post('/menu/fe/delete_categorymenu', 'delete_categorymenu')->name('awesome_admin.menu.fe.delete_categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/fe/listdata/roles', 'listDataRoles')->name('awesome_admin.menu.fe.listdata.roles')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/listdata/categorymenu', 'listDataCategoryMenu')->name('awesome_admin.menu.fe.listdata.categorymenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/fe/listdata/parentmenu', 'listDataParentMenu')->name('awesome_admin.menu.fe.listdata.parentmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/listdata/parentmenuforsubmenu', 'listDataParentMenuForSubmenu')->name('awesome_admin.menu.fe.listdata.parentmenuforsubmenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/listdata/categorymenuforparentmenu', 'listDataCategoryMenuForParentMenu')->name('awesome_admin.menu.fe.listdata.categorymenuforparentmenu')->middleware('auth', 'checkSuspended');

			Route::get('/menu/fe/listdata/submenu', 'listDataSubmenu')->name('awesome_admin.menu.fe.listdata.submenu')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/listdata/submenu/detail/{idOrSlug}', 'listDataSubmenuDetail')->name('awesome_admin.menu.fe.listdata.submenu.detail')->middleware('auth', 'checkSuspended');

			Route::get('/menu/fe/listdata/routes', 'listDataRoutes')->name('awesome_admin.menu.fe.listdata.routelist')->middleware('auth', 'checkSuspended');
			Route::get('/menu/fe/listdata/permissions', 'listDataPermissions')->name('awesome_admin.menu.fe.listdata.permissionlist')->middleware('auth', 'checkSuspended');

			Route::get('/menu/fe/getversionmenu', 'getVersionMenu')->name('awesome_admin.menu.fe.getVersionMenu')->middleware('auth', 'checkSuspended');

			Route::post('/menu/fe/checkparentmenulink', 'checkParentMenuLink')->name('awesome_admin.menu.fe.checkParentMenuLink')->middleware('auth', 'checkSuspended');
			Route::post('/menu/fe/checksubmenulink', 'checkSubmenuLink')->name('awesome_admin.menu.fe.checkSubmenuLink')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Role_Controller::class)->group(function()
		{
			// List and Add
			Route::get('/role', 'index')->name('awesome_admin.role')->middleware('auth', 'checkSuspended');
			Route::get('/role/create/v2', 'createV2')->name('awesome_admin.role.createV2')->middleware('auth', 'checkSuspended');
			Route::get('/role/edit/v2/{idOrSlug}', 'editV2')->name('awesome_admin.role.editV2')->middleware('auth', 'checkSuspended');

			Route::post('/role', 'store')->name('awesome_admin.role.store')->middleware('auth', 'checkSuspended');
			Route::post('/roleV2', 'storeV2')->name('awesome_admin.role.storeV2')->middleware('auth', 'checkSuspended');

			// Edit
			Route::post('/role/edit/{idOrSlug}', 'update')->name('awesome_admin.role.update')->middleware('auth', 'checkSuspended');
			Route::post('/role/editV2/{idOrSlug}', 'updateV2')->name('awesome_admin.role.updateV2')->middleware('auth', 'checkSuspended');

			// Delete
			Route::post('/role/delete/{idOrSlug}', 'destroy')->name('awesome_admin.role.delete')->middleware('auth', 'checkSuspended');
			Route::post('/role/delete/v2/{idOrSlug}', 'destroyV2')->name('awesome_admin.role.deleteV2')->middleware('auth', 'checkSuspended');

			// Get List
			Route::get('/role/listdata', 'listData')->name('awesome_admin.role.list')->middleware('auth', 'checkSuspended');

			// Get List Permission
			Route::get('/role/listdatapermission', 'listDataPermission')->name('awesome_admin.role.listpermission')->middleware('auth', 'checkSuspended');
			Route::get('/role/listdatapermissionv2', 'listDataPermissionV2')->name('awesome_admin.role.listpermissionv2')->middleware('auth', 'checkSuspended');

			// Get Detail Role
			Route::get('/role/detaildata/{idOrSlug}', 'detailData')->name('awesome_admin.role.detail')->middleware('auth', 'checkSuspended');

			Route::get('/role/test', 'test')->name('awesome_admin.role.test')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Permission_Controller::class)->group(function()
		{
			// List and Add
			Route::get('/permission', 'index')->name('awesome_admin.permission')->middleware('auth', 'checkSuspended');
			Route::post('/permission', 'store')->name('awesome_admin.permission.store')->middleware('auth', 'checkSuspended');
			
			// Edit
			Route::post('/permission/edit/{idOrSlug}', 'update')->name('awesome_admin.permission.update')->middleware('auth', 'checkSuspended');

			// Delete
			Route::post('/permission/delete/{idOrSlug}', 'destroy')->name('awesome_admin.permission.delete')->middleware('auth', 'checkSuspended');

			// Get List Data
			Route::get('/permission/listdata', 'listData')->name('awesome_admin.permission.list')->middleware('auth', 'checkSuspended');

			// Get Detail Permission
			Route::get('/permission/detaildata/{idOrSlug}', 'detailData')->name('awesome_admin.permission.detail')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_User_Controller::class)->group(function()
		{
			// List and Add
			Route::get('/user', 'index')->name('awesome_admin.user')->middleware('auth', 'checkSuspended');
			Route::post('/user', 'store')->name('awesome_admin.user.store')->middleware('auth', 'checkSuspended');
			Route::post('/user/bulk_update', 'bulk_update')->name('awesome_admin.user.bulk_update')->middleware('auth', 'checkSuspended');
			
			// Edit
			Route::get('/user/edit/{idOrSlug}', 'edit')->name('awesome_admin.user.edit')->middleware('auth', 'checkSuspended');
			Route::post('/user/update', 'update')->name('awesome_admin.user.update')->middleware('auth', 'checkSuspended');
			// Route::post('/user/edit/{idOrSlug}', 'update')->name('awesome_admin.user.update')->middleware('auth', 'checkSuspended');

			// Delete
			Route::post('/user/delete/{idOrSlug}', 'destroy')->name('awesome_admin.user.delete')->middleware('auth', 'checkSuspended');

			// Get List Data
			Route::get('/user/listdata', 'listData')->name('awesome_admin.user.list')->middleware('auth', 'checkSuspended');

			// Get Detail Data
			Route::get('/user/detaildata/{idOrSlug}', 'detailData')->name('awesome_admin.user.detail')->middleware('auth', 'checkSuspended');
			Route::get('/user/getdata', 'getUserData')->name('awesome_admin.user.getdata')->middleware('auth', 'checkSuspended');

			// Check username and email
			Route::get('/user/checkdata', 'checkUserData')->name('awesome_admin.user.checkdata')->middleware('auth', 'checkSuspended');
			Route::get('/user/checkdataforupdate', 'checkUserDataForUpdate')->name('awesome_admin.user.checkdataforupdate')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_SMTP_Controller::class)->group(function()
		{
			Route::get('/smtp', 'index')->name('awesome_admin.smtp')->middleware('auth', 'checkSuspended');
			Route::post('/smtp', 'store')->name('awesome_admin.smtp.store')->middleware('auth', 'checkSuspended');

			Route::get('/smtp/edit/{idOrSlug}', 'edit')->name('awesome_admin.smtp.edit')->middleware('auth', 'checkSuspended');
			Route::post('/smtp/update', 'update')->name('awesome_admin.smtp.update')->middleware('auth', 'checkSuspended');
			Route::post('/smtp/update/service', 'update_service')->name('awesome_admin.smtp.update.service')->middleware('auth', 'checkSuspended');

			// Delete
			Route::post('/smtp/delete/{idOrSlug}', 'destroy')->name('awesome_admin.smtp.delete')->middleware('auth', 'checkSuspended');

			Route::get('/smtp/listdata', 'listData')->name('awesome_admin.smtp.listdata')->middleware('auth', 'checkSuspended');
			Route::get('/smtp/detaildata/{idOrSlug}', 'detaildata')->name('awesome_admin.smtp.detaildata')->middleware('auth', 'checkSuspended');
			Route::get('/smtp/detailsetdata', 'detailDataSetService')->name('awesome_admin.smtp.detaildata.setservice')->middleware('auth', 'checkSuspended');

			Route::get('/smtp/testing', 'testing')->name('awesome_admin.smtp.testing')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Language_Controller::class)->group(function()
		{
			Route::get('/language', 'index')->name('awesome_admin.language')->middleware('auth', 'checkSuspended');
			Route::get('/language/untranslated', 'untranslated')->name('awesome_admin.language.untranslated')->middleware('auth', 'checkSuspended');
			Route::get('/language/translated', 'translated')->name('awesome_admin.language.translated')->middleware('auth', 'checkSuspended');

			Route::post('/language/update/untranslated', 'update_untranslated')->name('awesome_admin.language.update.untranslated')->middleware('auth', 'checkSuspended');
			Route::post('/language/update/translated', 'update_translated')->name('awesome_admin.language.update.translated')->middleware('auth', 'checkSuspended');

			Route::get('/language/listdata', 'listData')->name('awesome_admin.language.listdata')->middleware('auth', 'checkSuspended');
			Route::get('/language/listdata/untranslated', 'listDataUntranslated')->name('awesome_admin.language.listdata.untranslated')->middleware('auth', 'checkSuspended');
			Route::get('/language/listdata/translated', 'listDataTranslated')->name('awesome_admin.language.listdata.translated')->middleware('auth', 'checkSuspended');

			Route::get('/language/setlanguage/{idOrSlug}', 'setLanguage')->name('awesome_admin.language.setlanguage')->middleware('auth', 'checkSuspended');
			Route::get('/language/getlanguage', 'getLanguage')->name('awesome_admin.language.getlanguage')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_Appearance_Controller::class)->group(function()
		{
			Route::get('/appearance', 'index')->name('awesome_admin.appearance')->middleware('auth', 'checkSuspended');
			Route::post('/appearance/update', 'update')->name('awesome_admin.appearance.update')->middleware('auth', 'checkSuspended');
			Route::get('/appearance/pagethemesetting/{idOrSlug}', 'getPageThemeSettingJson')->name('awesome_admin.appearance.listdata.pagethemesetting')->middleware('auth', 'checkSuspended');
			Route::get('/appearance/listdata/pagetheme/{idOrSlug}', 'listDataPageTheme')->name('awesome_admin.appearance.listdata.pagetheme')->middleware('auth', 'checkSuspended');
		});

		Route::controller(\Awesome_Admin_File_Manager_Controller::class)->group(function()
		{
			Route::get('/filemanager', 'index')->name('awesome_admin.file_manager')->middleware('auth', 'checkSuspended');
		});
	});

/*
Route::get('/oauth/azure', function () 
{
	return Socialite::driver('azure')->redirect();
});
 
Route::get('/oauth/azure/callback', function () 
{
	dd($user = Socialite::driver('azure')->stateless()->user());
});
*/