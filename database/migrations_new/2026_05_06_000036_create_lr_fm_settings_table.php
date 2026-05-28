<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fm_settings')) {
            echo "  Table fm_settings already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table fm_settings...\n";

        Schema::create('fm_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('key', 255);
            $table->text('value')->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table fm_settings...\n";
        require_once database_path('seeders_new/FmSettingsSeeder.php');
        (new FmSettingsSeeder())->run();
        echo "  Table fm_settings imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_settings');
    }
};
