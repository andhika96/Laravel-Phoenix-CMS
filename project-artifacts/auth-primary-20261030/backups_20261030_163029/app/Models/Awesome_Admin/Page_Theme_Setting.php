<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page_Theme_Setting extends Model
{
	use HasFactory;

	protected $table = 'page_theme_settings';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'uri',
		'page_name',
		'page_theme',
		'page_color_nuances',
		'page_background_image',
		'updated_at',
		'created_at'
	];    

	protected $guarded = [
		'id',
	];
}
