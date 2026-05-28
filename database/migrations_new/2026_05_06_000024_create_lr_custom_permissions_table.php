<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('custom_permissions')) {
            echo "  Table custom_permissions already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table custom_permissions...\n";

        Schema::create('custom_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->bigInteger('role_id')->default(0);
            $table->string('category_code', 255)->nullable();
            $table->string('parent_code', 255)->nullable();
            $table->string('menu_type', 65)->nullable();
            $table->string('menu_code', 255)->nullable();
            $table->string('menu_name', 255)->nullable();
            $table->string('menu_link', 255)->nullable();
            $table->json('permissions')->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table custom_permissions...\n";
        require_once database_path('seeders_new/CustomPermissionsSeeder.php');
        (new CustomPermissionsSeeder())->run();
        echo "  Table custom_permissions imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_permissions');
    }
};
