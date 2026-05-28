<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('oauth_personal_access_clients')) {
            echo "  Table oauth_personal_access_clients already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table oauth_personal_access_clients...\n";

        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('client_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table oauth_personal_access_clients...\n";
        require_once database_path('seeders_new/OauthPersonalAccessClientsSeeder.php');
        (new OauthPersonalAccessClientsSeeder())->run();
        echo "  Table oauth_personal_access_clients imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_personal_access_clients');
    }
};
