<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;

class ExportCMSDatabase extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$sqlPath = database_path('sql/laravel_11.sql'); // Or any other path

		$this->command->info('Processing...');
		
		if (File::exists($sqlPath)) 
		{
			DB::unprepared(File::get($sqlPath));

			$this->command->info('SQL file imported successfully!');
		}
		else
		{
			$this->command->error('SQL file not found!');
		}
	}
}
