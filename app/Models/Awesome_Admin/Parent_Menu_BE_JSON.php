<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Parent_Menu_BE_JSON extends Model
{
	use HasFactory;

	protected $table = 'menu_parentmenu_json';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'menu_page',
		'menu_vars',
		'menu_vars_backup'
	];    

	protected $guarded = [
		'id',
	];
}
