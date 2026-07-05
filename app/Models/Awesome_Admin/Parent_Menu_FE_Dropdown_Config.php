<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parent_Menu_FE_Dropdown_Config extends Model
{
	use HasFactory;

	protected $table = 'menu_fe_parentmenu_dropdown_configs';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'menu_page',
		'parent_code',
		'dropdown_type',
		'mega_layout',
		'config_json'
	];

	protected $guarded = [
		'id',
	];

	protected $casts = [
		'config_json' => 'array',
	];
}
