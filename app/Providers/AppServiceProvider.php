<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) 
		{
			$event->extendSocialite('azure', \SocialiteProviders\Azure\Provider::class);
		});

		// Tambahkan Gate ini
		// Gate::define('admin', function($user) 
		// {
			// Silakan sesuaikan dengan kolom di database Account Anda
			// Contoh: return $user->is_admin == true;
			
		// 	return true; // <- Gunakan return true sementara untuk memastikan ini penyebabnya
		// });

		// Paksa semua link asset() menjadi https
		// if ($this->app->environment('local') || 
		// 	$this->app->environment('staging') || 
		// 	$this->app->environment('production')) 
		// {
		// 	URL::forceScheme('https');
		// }
	}
}
