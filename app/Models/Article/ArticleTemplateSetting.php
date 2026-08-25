<?php

namespace App\Models\Article;

use App\Support\Article\ArticleTemplateCatalog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleTemplateSetting extends Model
{
    use HasFactory;

    protected $table = 'article_template_settings';

    protected $fillable = [
        'archive_template',
        'detail_template',
        'archive_per_page',
        'archive_template_options',
        'detail_template_options',
        'updated_by',
    ];

    protected $casts = [
        'archive_template_options' => 'array',
        'detail_template_options' => 'array',
    ];

    public static function current(): self
    {
        $defaults = app(ArticleTemplateCatalog::class)->defaultSettings();

        return static::query()->firstOrCreate(['id' => 1], $defaults);
    }
}
