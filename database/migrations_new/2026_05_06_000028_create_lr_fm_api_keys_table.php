<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fm_api_keys')) {
            echo "  Table fm_api_keys already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table fm_api_keys...\n";

        Schema::create('fm_api_keys', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('key', 64);
            $table->string('secret', 64)->nullable();
            $table->json('allowed_origins')->nullable();
            $table->json('allowed_disks')->nullable();
            $table->tinyInteger('can_upload')->default(1);
            $table->tinyInteger('can_delete')->default(0);
            $table->tinyInteger('can_rename')->default(0);
            $table->tinyInteger('can_move')->default(0);
            $table->tinyInteger('can_create_folder')->default(1);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->unsignedBigInteger('request_count')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        echo "  Table fm_api_keys imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_api_keys');
    }
};
