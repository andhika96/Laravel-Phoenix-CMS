<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('failed_jobs')) {
            echo "  Table failed_jobs already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table failed_jobs...\n";

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('uuid', 255);
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        echo "  Table failed_jobs imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
