<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Session\Middleware\StartSession;

use App\Http\Middleware\ArunaPermission;
use App\Http\Middleware\CheckSuspended;

use Dotenv\Dotenv;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__.'/../routes/web.php',
		api: __DIR__.'/../routes/api.php',
		commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) 
	{
		// $dotenv = Dotenv::createImmutable(dirname(__DIR__));
		// $dotenv->load();

		// $middleware->appendToGroup('web', \App\Http\Middleware\RedirectIfDbDown::class);
		// $middleware->appendToGroup('web', StartSession::class);

		// Added by Andhika Adhitia N - Date 15-01-2025
		$middleware->redirectGuestsTo('/auth/login');

		$middleware->alias(
		[
			'permission' => ArunaPermission::class,
			'checkSuspended' => CheckSuspended::class,
			'fm.auth' => \App\Http\Middleware\FileManagerAuth::class, // Added 28032026
			'fm.admin' => \App\Http\Middleware\FmAdminMiddleware::class, // Added 28032026
		]);

		$middleware->validateCsrfTokens(except: 
		[
			'manage_project/delete/filepond',
		]);
	})
	->withExceptions(function (Exceptions $exceptions) 
	{
		//
	})->create();
