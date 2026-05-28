<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
	use HasFactory;

	protected $table = 'articles';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];

	public function getStatus(): BelongsTo
	{
		return $this->belongsTo(Article_Status::class, 'status', 'id');
	}
}
