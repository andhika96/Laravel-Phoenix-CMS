<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page_Theme extends Model
{
	use HasFactory;

	protected $table = 'page_themes';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'uri',
		'theme_group',
		'theme_code',
		'theme_name',
		'theme_preview_image',
		'updated_at',
		'created_at'
	];    

	protected $guarded = [
		'id',
	];
}
