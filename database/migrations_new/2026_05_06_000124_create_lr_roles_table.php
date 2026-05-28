<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('roles')) {
            echo "  Table roles already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table roles...\n";

        Schema::create('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('name', 255);
            $table->string('guard_name', 255);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table roles...\n";
        require_once database_path('seeders_new/RolesSeeder.php');
        (new RolesSeeder())->run();
        echo "  Table roles imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
