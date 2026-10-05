<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['graph' => 'array', 'draft_revision' => 'integer', 'published_version' => 'integer', 'published_revision' => 'integer', 'published_at' => 'datetime'];
    }
}
