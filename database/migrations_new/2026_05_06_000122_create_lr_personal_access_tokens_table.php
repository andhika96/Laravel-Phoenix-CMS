<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('personal_access_tokens')) {
            echo "  Table personal_access_tokens already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table personal_access_tokens...\n";

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('tokenable_type', 255);
            $table->unsignedBigInteger('tokenable_id');
            $table->string('name', 255);
            $table->string('token', 64);
            $table->text('abilities');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        echo "  Table personal_access_tokens imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
