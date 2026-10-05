<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->string('category', 24)->default('general')->index();
            $table->string('status', 16)->default('draft')->index();
            $table->json('graph');
            $table->unsignedInteger('draft_revision')->default(1);
            $table->unsignedInteger('published_version')->nullable();
            $table->unsignedInteger('published_revision')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
        Schema::create('workflow_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->unsignedInteger('draft_revision');
            $table->string('name', 160);
            $table->text('description')->nullable();
            $table->string('category', 24);
            $table->json('graph');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();
            $table->unique(['workflow_id', 'version']);
        });
        Schema::create('workflow_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->foreignId('workflow_version_id')->nullable()->constrained('workflow_versions')->nullOnDelete();
            $table->unsignedBigInteger('started_by')->nullable()->index();
            $table->string('mode', 16)->default('simulation');
            $table->string('status', 24)->default('pending')->index();
            $table->string('current_node_id', 80)->nullable();
            $table->string('event_key', 160)->nullable()->unique();
            $table->unsignedInteger('draft_revision');
            $table->json('graph_snapshot');
            $table->json('context');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('resume_at')->nullable()->index();
            $table->timestamps();
        });
        Schema::create('workflow_run_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_run_id')->constrained('workflow_runs')->cascadeOnDelete();
            $table->unsignedInteger('sequence');
            $table->string('node_id', 80)->nullable();
            $table->string('level', 16)->default('info');
            $table->text('message');
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->unique(['workflow_run_id', 'sequence']);
        });
        Schema::create('workflow_approval_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('workflow_run_id')->constrained('workflow_runs')->cascadeOnDelete();
            $table->string('node_id', 80);
            $table->unsignedBigInteger('assignee_id')->nullable()->index();
            $table->string('assignee_role', 120)->nullable()->index();
            $table->string('status', 24)->default('pending')->index();
            $table->unsignedBigInteger('decided_by')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
            $table->unique(['workflow_run_id', 'node_id']);
        });
    }

    public function down(): void
    {
        foreach (['workflow_approval_tasks', 'workflow_run_logs', 'workflow_runs', 'workflow_versions', 'workflows'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
