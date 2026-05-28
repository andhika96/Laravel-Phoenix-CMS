<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            echo "  Table notifications already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table notifications...\n";

        Schema::create('notifications', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('from_id')->default(0);
            $table->string('from_fullname', 155);
            $table->bigInteger('to_id')->default(0);
            $table->string('to_fullname', 155);
            $table->string('type', 55);
            $table->string('icon', 255);
            $table->string('title', 155);
            $table->text('message');
            $table->tinyInteger('hasread')->default(0);
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table notifications...\n";
        require_once database_path('seeders_new/NotificationsSeeder.php');
        (new NotificationsSeeder())->run();
        echo "  Table notifications imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
