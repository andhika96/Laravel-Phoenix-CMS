<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('model_has_permissions')) {
            echo "  Table model_has_permissions already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table model_has_permissions...\n";

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type', 255);
            $table->unsignedBigInteger('model_id');
        });

        echo "  Table model_has_permissions imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }
};
