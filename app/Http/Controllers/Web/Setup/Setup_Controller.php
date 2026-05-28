<?php

namespace App\Http\Controllers\Web\Setup;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

use App\Support\SseStreamOutput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Setup_Controller extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return view('setup.setup');
	}

	public function process(Request $request)
	{
		$rules = [];
		$errorMessages =  
		[
			'db_name.required' => 'Database name required',
			'db_username.required' => 'Database username required',
			'db_password.required' => 'Database password type required',
			'db_hostname.required' => 'Database hostname required',
			'db_port.required' => 'Database port required'
		];

		if ($request->input('db_hostname') !== 'localhost')
		{
			$rules['db_password'] = 'required';
		}

		$rules['db_name'] = 'required';
		$rules['db_username'] = 'required';
		$rules['db_hostname'] = 'required';
		$rules['db_port'] = 'required';

		$validator = Validator::make($request->all(), $rules, $errorMessages);

		if ($validator->fails())
		{
			return response()->stream(function() use($validator)
			{
				$output = new SseStreamOutput();
				$output->writeln('<ul>');

				foreach ($validator->errors()->all() as $error) 
				{
					$output->writeln('<li>'.$error.'</li>');
				}

				$output->writeln('</ul>');

			}, 422, 
			[
				'Content-Type' => 'text/event-stream',
				'Cache-Control' => 'no-cache, no-transform',
				'X-Accel-Buffering' => 'no',
			]);
		}
		else
		{
			return response()->stream(function() use($request)
			{
				$output = new SseStreamOutput();

				Artisan::call('env:set DB_HOST '.$request->input('db_hostname'), [], $output);
				Artisan::call('env:set DB_PORT '.$request->input('db_port'), [], $output);
				Artisan::call('env:set DB_DATABASE '.$request->input('db_name'), [], $output);
				Artisan::call('env:set DB_USERNAME '.$request->input('db_username'), [], $output);
				
				if ($request->input('db_password'))
				{
					Artisan::call('env:set DB_PASSWORD '.$request->input('db_password'), [], $output);
				}

				Artisan::call('install:cms', [], $output);

				if (function_exists('ob_flush')) 
				{
					@ob_flush();
				}

				flush();

			}, 200, 
			[
				'Content-Type' => 'text/event-stream',
				'Cache-Control' => 'no-cache, no-transform',
				'X-Accel-Buffering' => 'no',
			]);
		}
	}
}
