<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_builder_elementor_form_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('page_builder_id')->nullable()->index();
            $table->string('page_uri');
            $table->string('node_id');
            $table->string('form_name');
            $table->json('fields');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_builder_elementor_form_submissions');
    }
};
