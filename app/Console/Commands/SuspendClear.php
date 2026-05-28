<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

use App\Models\Awesome_Admin\Account;

class SuspendClear extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'suspend:clear';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Automatically lift expired suspensions';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		Account::whereNotNull('suspended_until')
			->where('suspended_until', '<=', Carbon::now())
			->update([
				'suspended_until' => null,
			]);
	}
}
