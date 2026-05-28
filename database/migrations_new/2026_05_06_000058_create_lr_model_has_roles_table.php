<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('model_has_roles')) {
            echo "  Table model_has_roles already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table model_has_roles...\n";

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type', 255);
            $table->unsignedBigInteger('model_id');
        });

        // Seeding data
        echo "  Importing data to table model_has_roles...\n";
        require_once database_path('seeders_new/ModelHasRolesSeeder.php');
        (new ModelHasRolesSeeder())->run();
        echo "  Table model_has_roles imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_roles');
    }
};
