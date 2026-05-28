<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Global quota settings (key-value)
        Schema::create('fm_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Per-user quota override
        Schema::create('fm_quotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->index();
            // null = inherit from global, -1 = unlimited
            $table->bigInteger('max_storage')->nullable()->comment('Total storage in bytes. NULL=global, -1=unlimited');
            $table->bigInteger('max_file_size')->nullable()->comment('Max single file size in bytes. NULL=global, -1=unlimited');
            $table->unsignedBigInteger('used_storage')->default(0)->comment('Bytes used so far');
            $table->timestamps();
        });

        // Insert default global settings
        DB::table('fm_settings')->insert([
            ['key' => 'global_max_storage',   'value' => '10737418240', 'description' => 'Default max total storage per user (bytes). -1 = unlimited', 'created_at' => now(), 'updated_at' => now()],  // 10 GB
            ['key' => 'global_max_file_size', 'value' => '104857600',  'description' => 'Default max single file upload size (bytes). -1 = unlimited', 'created_at' => now(), 'updated_at' => now()],  // 100 MB
            ['key' => 'allowed_mime_types',   'value' => null,          'description' => 'JSON array of allowed MIME types. NULL = all allowed', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_disk',         'value' => 'public',      'description' => 'Default storage disk: local | public | s3', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fm_quotas');
        Schema::dropIfExists('fm_settings');
    }
};
