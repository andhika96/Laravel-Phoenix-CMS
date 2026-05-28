<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fm_quotas')) {
            echo "  Table fm_quotas already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table fm_quotas...\n";

        Schema::create('fm_quotas', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('max_storage')->nullable();
            $table->bigInteger('max_file_size')->nullable();
            $table->unsignedBigInteger('used_storage')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        // Seeding data
        echo "  Importing data to table fm_quotas...\n";
        require_once database_path('seeders_new/FmQuotasSeeder.php');
        (new FmQuotasSeeder())->run();
        echo "  Table fm_quotas imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_quotas');
    }
};
