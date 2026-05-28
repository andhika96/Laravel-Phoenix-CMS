<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm_api_keys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('NULL = key milik system/global');
            $table->string('name')->comment('Label/nama app yang pakai key ini');
            $table->string('key', 64)->unique()->index()->comment('API Key yang dikirim via ?fm_key= atau header X-FM-Key');
            $table->string('secret', 64)->nullable()->comment('Optional secret untuk HMAC signing');
            $table->json('allowed_origins')->nullable()->comment('Array domain yg boleh akses. NULL = semua.');
            $table->json('allowed_disks')->nullable()->comment('Array disk yg boleh dipakai. NULL = semua.');
            $table->boolean('can_upload')->default(true);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_rename')->default(false);
            $table->boolean('can_move')->default(false);
            $table->boolean('can_create_folder')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('last_used_ip', 45)->nullable();
            $table->unsignedBigInteger('request_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_api_keys');
    }
};
