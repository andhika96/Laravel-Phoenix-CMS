<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_categorymenu_json')) {
            echo "  Table menu_categorymenu_json already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table menu_categorymenu_json...\n";

        Schema::create('menu_categorymenu_json', function (Blueprint $table) {
            $table->integer('id');
            $table->string('menu_page', 55);
            $table->json('menu_vars');
            $table->json('menu_vars_backup');
        });

        // Seeding data
        echo "  Importing data to table menu_categorymenu_json...\n";
        require_once database_path('seeders_new/MenuCategorymenuJsonSeeder.php');
        (new MenuCategorymenuJsonSeeder())->run();
        echo "  Table menu_categorymenu_json imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_categorymenu_json');
    }
};
