<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cover_image')) {
            echo "  Table cover_image already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table cover_image...\n";

        Schema::create('cover_image', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('user_id')->default(0);
            $table->string('uri', 255)->nullable();
            $table->enum('cover_type', ['background_image','slideshow'])->default('background_image');
            $table->string('cover_page_name', 255)->default('Unknown Page');
            $table->text('cover_bgimage_vars')->nullable();
            $table->text('cover_slideshow_vars')->nullable();
            $table->enum('cover_slideshow_direction', ['horizontal','vertical'])->default('horizontal');
            $table->enum('cover_slideshow_desktop_direction', ['horizontal','vertical'])->default('horizontal');
            $table->enum('cover_slideshow_mobile_direction', ['horizontal','vertical'])->default('horizontal');
            $table->enum('cover_autoplay_slideshow', ['active','inactive'])->default('active');
            $table->bigInteger('cover_autoplay_slideshow_interval')->default(3000);
            $table->enum('cover_looping_slideshow', ['active','inactive'])->default('active');
            $table->enum('cover_is_active', ['active','inactive'])->default('active');
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table cover_image...\n";
        require_once database_path('seeders_new/CoverImageSeeder.php');
        (new CoverImageSeeder())->run();
        echo "  Table cover_image imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('cover_image');
    }
};
