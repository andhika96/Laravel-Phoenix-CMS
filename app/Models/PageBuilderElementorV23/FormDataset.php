<?php

namespace App\Models\PageBuilderElementorV23;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormDataset extends Model
{
    use HasFactory;

    protected $table = 'pagebuilder_elementor_v23_form_datasets';

    protected $guarded = ['id'];

    protected $casts = [
        'schema_version' => 'integer',
        'nodes' => 'array',
    ];
}
