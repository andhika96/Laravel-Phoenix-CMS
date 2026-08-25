<?php

namespace App\Http\Requests\Article;

use App\Support\Article\ArticleTemplateCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleTemplateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $catalog = app(ArticleTemplateCatalog::class);

        return [
            'archive_template' => ['required', 'string', Rule::in(array_keys($catalog->archive()))],
            'detail_template' => ['required', 'string', Rule::in(array_keys($catalog->detail()))],
            'archive_per_page' => ['required', 'integer', Rule::in([12, 18, 24])],
            'archive_template_options' => ['nullable', 'array'],
            'detail_template_options' => ['nullable', 'array'],
        ];
    }
}
