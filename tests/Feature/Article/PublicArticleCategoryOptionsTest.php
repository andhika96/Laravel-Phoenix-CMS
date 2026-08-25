<?php

namespace Tests\Feature\Article;

use App\Models\Article\Article;
use App\Models\Article\Article_Categories;
use App\Support\Article\PublicArticleCategoryOptions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicArticleCategoryOptionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('article_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 32);
            $table->string('code', 32);
            $table->string('status', 16)->default('active');
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('uri', 255);
            $table->unsignedBigInteger('user_id')->default(0);
            $table->unsignedBigInteger('category_id')->default(0);
            $table->unsignedBigInteger('subcategory_id')->default(0);
            $table->string('title', 255);
            $table->text('content');
            $table->string('tags', 255)->nullable();
            $table->string('thumb_s', 255)->nullable();
            $table->string('thumb_l', 255)->nullable();
            $table->string('visibility', 32)->default('public');
            $table->string('password_protected', 32)->nullable();
            $table->string('status', 32)->default('draft');
            $table->string('scheduled', 8)->default('false');
            $table->timestamps();
        });
    }

    public function test_it_only_returns_active_categories_with_eligible_public_articles_in_name_order(): void
    {
        $zebra = Article_Categories::create(['name' => 'Zebra', 'code' => 'zebra', 'status' => 'active']);
        $alpha = Article_Categories::create(['name' => 'Alpha', 'code' => 'alpha', 'status' => 'active']);
        $inactive = Article_Categories::create(['name' => 'Inactive', 'code' => 'inactive', 'status' => 'inactive']);
        $empty = Article_Categories::create(['name' => 'Empty', 'code' => 'empty', 'status' => 'active']);
        $private = Article_Categories::create(['name' => 'Private', 'code' => 'private', 'status' => 'active']);

        foreach ([$zebra, $alpha, $inactive] as $index => $category) {
            Article::create([
                'uri' => 'public-category-'.$index,
                'category_id' => $category->id,
                'title' => $category->name.' article',
                'content' => '<p>Visible</p>',
                'status' => 'publish',
                'visibility' => 'public',
                'created_at' => now()->subMinute(),
                'updated_at' => now()->subMinute(),
            ]);
        }
        Article::create([
            'uri' => 'private-category',
            'category_id' => $private->id,
            'title' => 'Private article',
            'content' => '<p>Hidden</p>',
            'status' => 'publish',
            'visibility' => 'private',
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $options = app(PublicArticleCategoryOptions::class)->all();

        $this->assertSame([
            $alpha->id => 'Alpha',
            $zebra->id => 'Zebra',
        ], $options->pluck('name', 'id')->all());
        $this->assertFalse($options->contains('id', $inactive->id));
        $this->assertFalse($options->contains('id', $empty->id));
        $this->assertFalse($options->contains('id', $private->id));
    }
}
