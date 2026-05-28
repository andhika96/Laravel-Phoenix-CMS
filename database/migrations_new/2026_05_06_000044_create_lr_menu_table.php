<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('menu')) {
            echo "  Table menu already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table menu...\n";

        Schema::create('menu', function (Blueprint $table) {
            $table->integer('id');
            $table->string('module', 64);
            $table->enum('menu_with_parent', ['true','false'])->default('false');
            $table->integer('menu_parent_id')->default(0);
            $table->string('menu_parent_code', 64);
            $table->string('menu_parent_name', 55);
            $table->string('menu_name', 55);
            $table->string('url', 155);
            $table->string('icon', 64);
            $table->text('roles');
            $table->tinyInteger('status')->default(0);
        });

        // Seeding data
        echo "  Importing data to table menu...\n";
        require_once database_path('seeders_new/MenuSeeder.php');
        (new MenuSeeder())->run();
        echo "  Table menu imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
