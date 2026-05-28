<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_information')) {
            echo "  Table user_information already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table user_information...\n";

        Schema::create('user_information', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_id')->default(0);
            $table->string('avatar', 155)->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('cover_image_position', 32)->nullable();
            $table->string('birthdate', 12)->nullable();
            $table->tinyInteger('gender')->default(0);
            $table->string('phone_number', 16)->nullable();
            $table->text('about')->nullable();
        });

        // Seeding data
        echo "  Importing data to table user_information...\n";
        require_once database_path('seeders_new/UserInformationSeeder.php');
        (new UserInformationSeeder())->run();
        echo "  Table user_information imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('user_information');
    }
};
