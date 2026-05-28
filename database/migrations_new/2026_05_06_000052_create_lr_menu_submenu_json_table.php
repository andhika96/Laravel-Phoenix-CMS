<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu_submenu_json')) {
            echo "  Table menu_submenu_json already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table menu_submenu_json...\n";

        Schema::create('menu_submenu_json', function (Blueprint $table) {
            $table->integer('id');
            $table->string('parent_code', 155);
            $table->string('parent_name', 255)->nullable();
            $table->json('menu_vars')->nullable();
            $table->json('menu_vars_backup')->nullable();
        });

        echo "  Table menu_submenu_json imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_submenu_json');
    }
};
