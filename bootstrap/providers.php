<?php

use Mailjet\LaravelMailjet\MailjetServiceProvider;

return [
	App\Providers\AppServiceProvider::class,
	\Torann\GeoIP\GeoIPServiceProvider::class,
	\SocialiteProviders\Manager\ServiceProvider::class,
	\KitLoong\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,
	Brokenice\LaravelMysqlPartition\PartitionServiceProvider::class,
	MailjetServiceProvider::class,
];
