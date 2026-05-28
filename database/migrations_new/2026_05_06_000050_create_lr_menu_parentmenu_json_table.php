<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_parentmenu_json')) {
            echo "  Table menu_parentmenu_json already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table menu_parentmenu_json...\n";

        Schema::create('menu_parentmenu_json', function (Blueprint $table) {
            $table->integer('id');
            $table->string('menu_page', 55);
            $table->json('menu_vars')->nullable();
            $table->json('menu_vars_backup')->nullable();
        });

        // Seeding data
        echo "  Importing data to table menu_parentmenu_json...\n";
        require_once database_path('seeders_new/MenuParentmenuJsonSeeder.php');
        (new MenuParentmenuJsonSeeder())->run();
        echo "  Table menu_parentmenu_json imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_parentmenu_json');
    }
};
