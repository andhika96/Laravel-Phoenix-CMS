<?php

namespace App\Models\CoverImage;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoverImage extends Model
{
	use HasFactory;

	protected $table = 'cover_image';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];
}
