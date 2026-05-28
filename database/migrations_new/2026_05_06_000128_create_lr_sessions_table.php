<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            echo "  Table sessions already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table sessions...\n";

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent');
            $table->longText('payload');
            $table->integer('last_activity');
        });

        // Seeding data
        echo "  Importing data to table sessions...\n";
        require_once database_path('seeders_new/SessionsSeeder.php');
        (new SessionsSeeder())->run();
        echo "  Table sessions imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
