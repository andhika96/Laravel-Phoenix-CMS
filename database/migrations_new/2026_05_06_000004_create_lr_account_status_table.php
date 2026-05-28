<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('account_status')) {
            echo "  Table account_status already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table account_status...\n";

        Schema::create('account_status', function (Blueprint $table) {
            $table->integer('id');
            $table->string('name', 32);
            $table->string('code_name', 32);
            $table->string('class_name', 32)->nullable();
            $table->tinyInteger('is_active')->default(0);
        });

        // Seeding data
        echo "  Importing data to table account_status...\n";
        require_once database_path('seeders_new/AccountStatusSeeder.php');
        (new AccountStatusSeeder())->run();
        echo "  Table account_status imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('account_status');
    }
};
