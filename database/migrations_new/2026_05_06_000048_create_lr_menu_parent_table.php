<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_parent')) {
            echo "  Table menu_parent already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table menu_parent...\n";

        Schema::create('menu_parent', function (Blueprint $table) {
            $table->integer('id');
            $table->enum('is_parent', ['true','false'])->default('false');
            $table->string('module', 64);
            $table->string('parent_name', 64);
            $table->string('parent_code', 32);
            $table->string('icon', 64);
            $table->text('roles');
        });

        // Seeding data
        echo "  Importing data to table menu_parent...\n";
        require_once database_path('seeders_new/MenuParentSeeder.php');
        (new MenuParentSeeder())->run();
        echo "  Table menu_parent imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_parent');
    }
};
