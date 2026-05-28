<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('oauth_access_tokens')) {
            echo "  Table oauth_access_tokens already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table oauth_access_tokens...\n";

        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id', 100);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->string('name', 255)->nullable();
            $table->text('scopes');
            $table->tinyInteger('revoked');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->dateTime('expires_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table oauth_access_tokens...\n";
        require_once database_path('seeders_new/OauthAccessTokensSeeder.php');
        (new OauthAccessTokensSeeder())->run();
        echo "  Table oauth_access_tokens imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_access_tokens');
    }
};
