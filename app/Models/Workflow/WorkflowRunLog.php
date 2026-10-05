<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowRunLog extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
