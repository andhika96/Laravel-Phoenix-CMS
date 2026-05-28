<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('oauth_clients')) {
            echo "  Table oauth_clients already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table oauth_clients...\n";

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('secret', 100)->nullable();
            $table->string('provider', 255)->nullable();
            $table->text('redirect');
            $table->tinyInteger('personal_access_client');
            $table->tinyInteger('password_client');
            $table->tinyInteger('revoked');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table oauth_clients...\n";
        require_once database_path('seeders_new/OauthClientsSeeder.php');
        (new OauthClientsSeeder())->run();
        echo "  Table oauth_clients imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
