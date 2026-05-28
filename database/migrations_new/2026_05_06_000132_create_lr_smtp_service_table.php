<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('smtp_service')) {
            echo "  Table smtp_service already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table smtp_service...\n";

        Schema::create('smtp_service', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('service_id')->default(0);
            $table->string('service_name', 55)->default('');
        });

        // Seeding data
        echo "  Importing data to table smtp_service...\n";
        require_once database_path('seeders_new/SmtpServiceSeeder.php');
        (new SmtpServiceSeeder())->run();
        echo "  Table smtp_service imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('smtp_service');
    }
};
