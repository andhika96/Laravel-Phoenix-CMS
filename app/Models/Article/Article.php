<?php

namespace App\Models\Article;

use App\Models\Awesome_Admin\Account;
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

	public function category(): BelongsTo
	{
		return $this->belongsTo(Article_Categories::class, 'category_id');
	}

	public function author(): BelongsTo
	{
		return $this->belongsTo(Account::class, 'user_id');
	}
}
