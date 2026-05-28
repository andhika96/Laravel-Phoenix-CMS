<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article_Status extends Model
{
	use HasFactory;

	protected $table = 'article_status';

	public $timestamps = false;    

	protected $guarded = [
		'id',
	];
}
