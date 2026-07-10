<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Header_Navigation_Setting extends Model
{
	use HasFactory;

	protected $table = 'header_navigation_settings';

	protected $fillable = [
		'menu_page',
		'is_active',
		'config_json'
	];

	protected $guarded = [
		'id',
	];

	protected $casts = [
		'is_active' => 'boolean',
		'config_json' => 'array',
	];
}
