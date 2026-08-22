<?php

namespace App\Models\Page_Builder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page_Builder extends Model
{
	use HasFactory;

	public const EDITOR_VERSION_V20 = '2.0';
	public const EDITOR_VERSION_V23 = '2.3';
	public const EDITOR_VERSION_V24 = '2.4';

	protected $table = 'page_builder';

	public $timestamps = true;    

	protected $guarded = [
		'id',
	];
}
