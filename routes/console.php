<?php

use App\Services\FileManagerV2\FileManagerV2Storage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function() 
{
	$this->comment(Inspiring::quote());

})->purpose('Display an inspiring quote')->hourly();

Schedule::command('suspend:clear')->hourly();
Artisan::command('filemanager-v2:prune-uploads', function (FileManagerV2Storage $storage): void {
    $deleted = $storage->pruneExpiredUploads();

    $this->info("Removed {$deleted} expired File Manager V2 upload session(s).");
})->purpose('Remove expired File Manager V2 chunk upload sessions')->hourly();