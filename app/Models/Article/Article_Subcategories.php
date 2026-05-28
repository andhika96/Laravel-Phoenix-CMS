<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article_Subcategories extends Model
{
	use HasFactory;

	protected $table = 'article_subcategories';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];
}
