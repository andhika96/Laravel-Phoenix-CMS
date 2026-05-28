<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
	->name('api.v1.')
	->namespace('App\Http\Controllers\Api\v1')
	->group(function() 
	{
		Route::name('auth.')
			->middleware(['guest'])
			->prefix('auth')
			->controller(Auth\Auth_Controller::class)
			->group(function() 
			{
				Route::post('/login', 'authenticate')->name('login');
				Route::post('/signup', 'signupProcess')->name('signup');
			});

		Route::name('cms.core.')
		->middleware(['guest'])
		->prefix('testing')
		->group(function() 
		{
			Route::controller(\Testing\Testing_Controller::class)->group(function()
			{
				Route::get('/benchmark-db', 'benchmark')->name('testing.benchmark')->withoutMiddleware('throttle:api');
				Route::get('/benchmark-db2', 'benchmark2')->name('testing.benchmark2')->withoutMiddleware('throttle:api');
			});
		});

		require base_path('routes/filemanager.php');
    });

	// Route::prefix('v1')->group(function () 
	// {
	// 	require base_path('routes/filemanager.php');
	// });