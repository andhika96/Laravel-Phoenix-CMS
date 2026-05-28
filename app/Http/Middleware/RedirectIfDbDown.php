<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RedirectIfDbDown
{
	/** @var string[] connections to check (optional) */
	protected array $connections = ['default']; // or: ['mysql'] / ['mysql','pgsql']

	public function handle(Request $request, Closure $next)
	{
		// Never loop on the fallback page, health checks, etc.
		if ($request->is('setup') || $request->is('setup/process') || App::runningInConsole()) 
		{
			return $next($request);
		}

		// Small debounce so we don’t hammer the DB on every request if it’s really down
		if (Cache::get('db_down_flag') === true) 
		{
			return $this->failResponse($request);
		}

		try 
		{
			// Check one or many connections
			if ($this->isDown()) 
			{
				Cache::put('db_down_flag', true, now()->addSeconds(10));
				return $this->failResponse($request);
			}
		} 
		catch (\Throwable $e) 
		{
			Cache::put('db_down_flag', true, now()->addSeconds(10));
			return $this->failResponse($request);
		}

		return $next($request);
	}

	protected function isDown(): bool
	{
		foreach ($this->connections as $name) 
		{
			$conn = $name === 'default' ? DB::connection() : DB::connection($name);
			// Any call that forces a connection is fine:
			$conn->getPdo(); // or: $conn->select('select 1');
		}

		return false;
	}

	protected function failResponse(Request $request)
	{
		// For API/AJAX, return JSON instead of redirect
		if ($request->expectsJson() || $request->wantsJson()) 
		{
			return response()->json(['message' => 'Database unavailable'], 503);
		}

		return redirect()->route('cms.core.setup', [], 302);
	}
}
