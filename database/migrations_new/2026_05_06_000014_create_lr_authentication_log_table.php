<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('authentication_log')) {
            echo "  Table authentication_log already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table authentication_log...\n";

        Schema::create('authentication_log', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('authenticatable_type', 255);
            $table->unsignedBigInteger('authenticatable_id');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent');
            $table->timestamp('login_at')->nullable();
            $table->tinyInteger('login_successful')->default(0);
            $table->timestamp('logout_at')->nullable();
            $table->tinyInteger('cleared_by_user')->default(0);
            $table->json('location')->nullable();
        });

        // Seeding data
        echo "  Importing data to table authentication_log...\n";
        require_once database_path('seeders_new/AuthenticationLogSeeder.php');
        (new AuthenticationLogSeeder())->run();
        echo "  Table authentication_log imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('authentication_log');
    }
};
