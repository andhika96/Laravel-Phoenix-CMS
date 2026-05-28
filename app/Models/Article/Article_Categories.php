<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article_Categories extends Model
{
	use HasFactory;

	protected $table = 'article_categories';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];
}
