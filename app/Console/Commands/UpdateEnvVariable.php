<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateEnvVariable extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	// protected $signature = 'app:update-env-variable';
	protected $signature = 'env:set {key} {value}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$key = $this->argument('key');
		$value = $this->argument('value');
		$envPath = base_path('.env');

		if ( ! File::exists($envPath)) 
		{
			$this->error('.env file not found!');
			return;
		}

		$contents = File::get($envPath);
		$newContents = preg_replace("/^{$key}=.*\n/m", "{$key}={$value}\n", $contents);

		// if ($newContents !== $contents) 
		// {
		// 	// Key not found, append it
		// 	$newContents .= "\n{$key}={$value}\n";
		// }

		File::put($envPath, $newContents);

		// $this->info("{$key} updated to {$value} in .env file.");

		// Clear config cache if used
		$this->callSilent('config:clear');
		$this->callSilent('config:cache');
	}
}
