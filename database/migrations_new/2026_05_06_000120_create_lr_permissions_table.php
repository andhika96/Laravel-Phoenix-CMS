<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            echo "  Table permissions already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table permissions...\n";

        Schema::create('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('name', 255);
            $table->string('guard_name', 255);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table permissions...\n";
        require_once database_path('seeders_new/PermissionsSeeder.php');
        (new PermissionsSeeder())->run();
        echo "  Table permissions imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
