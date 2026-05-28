<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('smtp_settings')) {
            echo "  Table smtp_settings already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table smtp_settings...\n";

        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->integer('id');
            $table->string('smtp_service', 155)->nullable();
            $table->string('smtp_host', 155)->nullable();
            $table->string('smtp_sender_name', 62)->nullable();
            $table->string('smtp_sender_address', 155)->nullable();
            $table->string('smtp_username', 155)->nullable();
            $table->string('smtp_password', 155)->nullable();
            $table->integer('smtp_port')->default(0);
            $table->string('smtp_encryption', 32)->nullable();
        });

        // Seeding data
        echo "  Importing data to table smtp_settings...\n";
        require_once database_path('seeders_new/SmtpSettingsSeeder.php');
        (new SmtpSettingsSeeder())->run();
        echo "  Table smtp_settings imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
