<?php

namespace App\Console\Commands;

use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\QueryException;

use App\Support\SseStreamOutput;

class InstallCMS extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'install:cms';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Use this command to install LaraPhoenix CMS';

	/**
	 * Execute the console command.
	 */
	public function handle(Request $request)
	{
		try
		{ 
			$this->info("Installing...");

			Artisan::call('db:seed --class=ExportCMSDatabase');

			$this->info("CMS successfully installed");
		}
		catch (QueryException $th) 
		{
			$maxLength = 1150;
			
			if (strlen($th->getMessage()) > $maxLength)
			{
				$truncatedMessage = substr($th->getMessage(), 0, $maxLength).'...';
			}
			else
			{
				$truncatedMessage = $th->getMessage();
			}
			
			$this->error($truncatedMessage);
			return;
		}
	}
}
