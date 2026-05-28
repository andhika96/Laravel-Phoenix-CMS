<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('credits')) {
            echo "  Table credits already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table credits...\n";

        Schema::create('credits', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('creditable_type', 255);
            $table->unsignedBigInteger('creditable_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('running_balance', 10, 2);
            $table->string('description', 255)->nullable();
            $table->string('type', 255);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        echo "  Table credits imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
