<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_config')) {
            echo "  Table site_config already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table site_config...\n";

        Schema::create('site_config', function (Blueprint $table) {
            $table->integer('id');
            $table->string('site_url', 64)->default('http://127.0.0.1:8000');
            $table->string('site_name', 32)->default('LaraPhoenix');
            $table->string('site_slogan', 64)->default('The Most Powerful CMS for Laravel');
            $table->string('site_keyword', 155)->nullable();
            $table->string('site_description', 155)->nullable();
            $table->string('site_thumbnail', 255)->nullable();
            $table->string('font_family', 32)->default('Nunito');
            $table->integer('font_size')->default(14);
            $table->text('footer_message1');
            $table->text('footer_message2');
            $table->tinyInteger('signup_closed')->default(1);
            $table->tinyInteger('offline_mode')->default(1);
            $table->text('offline_reason');
            $table->tinyInteger('login_multiple_device')->default(0);
            $table->string('management_menu', 155)->default('v1');
            $table->string('gmaps_api_key', 155)->nullable();
            $table->string('recaptcha_site_key', 155)->nullable();
            $table->string('recaptcha_secret_key', 155)->nullable();
            $table->tinyInteger('enable_recaptcha_signin')->default(1);
            $table->tinyInteger('enable_recaptcha_signup')->default(1);
            $table->tinyInteger('enable_autogen_username_signup')->default(1);
            $table->tinyInteger('enable_ratelimit_login')->default(1);
            $table->integer('amount_ratelimit_login')->default(0);
            $table->tinyInteger('enable_ratelimit_signup')->default(1);
            $table->integer('amount_ratelimit_signup')->default(0);
            $table->integer('time_ratelimit_global')->default(60);
            $table->tinyInteger('is_sso_activated')->default(1);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table site_config...\n";
        require_once database_path('seeders_new/SiteConfigSeeder.php');
        (new SiteConfigSeeder())->run();
        echo "  Table site_config imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('site_config');
    }
};
