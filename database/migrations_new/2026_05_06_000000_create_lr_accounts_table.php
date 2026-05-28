<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accounts')) {
            echo "  Table accounts already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table accounts...\n";

        Schema::create('accounts', function (Blueprint $table) {
            $table->integer('id');
            $table->string('uuid', 36)->nullable();
            $table->string('email', 255);
            $table->string('username', 32);
            $table->string('fullname', 64);
            $table->string('password', 255);
            $table->string('remember_token', 100)->nullable();
            $table->tinyInteger('status')->default(2);
            $table->string('recovery_code', 64)->nullable();
            $table->integer('recovery_code_duration')->nullable();
            $table->string('token', 64)->nullable();
            $table->timestamp('suspended_until')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        // Seeding data
        echo "  Importing data to table accounts...\n";
        require_once database_path('seeders_new/AccountsSeeder.php');
        (new AccountsSeeder())->run();
        echo "  Table accounts imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
