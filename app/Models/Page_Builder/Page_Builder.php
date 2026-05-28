<?php

namespace App\Models\Page_Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page_Builder extends Model
{
	use HasFactory;

	protected $table = 'page_builder';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];
}
