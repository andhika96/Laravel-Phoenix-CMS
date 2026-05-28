<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('oauth_auth_codes')) {
            echo "  Table oauth_auth_codes already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table oauth_auth_codes...\n";

        Schema::create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 100);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->text('scopes');
            $table->tinyInteger('revoked');
            $table->dateTime('expires_at')->nullable();
        });

        echo "  Table oauth_auth_codes imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_auth_codes');
    }
};
