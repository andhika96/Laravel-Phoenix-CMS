<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('password_reset_tokens')) {
            echo "  Table password_reset_tokens already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table password_reset_tokens...\n";

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255);
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });

        echo "  Table password_reset_tokens imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
