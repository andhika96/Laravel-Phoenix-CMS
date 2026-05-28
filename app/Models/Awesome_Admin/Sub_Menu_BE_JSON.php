<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sub_Menu_BE_JSON extends Model
{
	use HasFactory;

	protected $table = 'menu_submenu_json';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'parent_code',
		'parent_name',
		'menu_vars',
		'menu_vars_backup'
	];    

	protected $guarded = [
		'id',
	];
}
