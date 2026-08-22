<?php

namespace App\Models\PageBuilderElementorV24;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormDataset extends Model
{
    use HasFactory;

    protected $table = 'pagebuilder_elementor_v24_form_datasets';

    protected $guarded = ['id'];

    protected $casts = [
        'schema_version' => 'integer',
        'nodes' => 'array',
    ];
}
