<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jobs')) {
            echo "  Table jobs already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table jobs...\n";

        Schema::create('jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('queue', 255);
            $table->longText('payload');
            $table->tinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        // Seeding data
        echo "  Importing data to table jobs...\n";
        require_once database_path('seeders_new/JobsSeeder.php');
        (new JobsSeeder())->run();
        echo "  Table jobs imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
