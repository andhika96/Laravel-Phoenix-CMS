<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagebuilder_elementor_v24_form_datasets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1)->index();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->unsignedTinyInteger('schema_version')->default(1);
            $table->json('nodes');
            $table->timestamps();
            $table->unique(['user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagebuilder_elementor_v24_form_datasets');
    }
};
