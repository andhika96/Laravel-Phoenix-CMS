<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowRun extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['context' => 'array', 'graph_snapshot' => 'array', 'started_at' => 'datetime', 'finished_at' => 'datetime', 'resume_at' => 'datetime'];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowRunLog::class)->orderBy('sequence');
    }
}
