<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('account_login_history')) {
            echo "  Table account_login_history already exists, skipping...\n";
            return;
        }

        echo "\n  Importing table account_login_history...\n";

        Schema::create('account_login_history', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('ip_address', 15)->nullable();
            $table->string('browser', 255)->nullable();
            $table->string('login_at', 155)->nullable();
            $table->string('login_successful', 5)->nullable();
            $table->string('logout_at', 155)->nullable();
            $table->string('cleared_by_user', 5)->nullable();
            $table->timestamp('updated_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
        });

        echo "  Table account_login_history imported successfully!\n";
    }

    public function down(): void
    {
        Schema::dropIfExists('account_login_history');
    }
};
