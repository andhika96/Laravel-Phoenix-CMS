<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('role_has_permissions')) {
            echo "  Table role_has_permissions already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table role_has_permissions...\n";

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        // Seeding data
        echo "  Importing data to table role_has_permissions...\n";
        require_once database_path('seeders_new/RoleHasPermissionsSeeder.php');
        (new RoleHasPermissionsSeeder())->run();
        echo "  Table role_has_permissions imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_permissions');
    }
};
