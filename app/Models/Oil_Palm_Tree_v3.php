<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Oil_Palm_Tree_v3 extends Model
{
	use HasFactory;

	protected $table = 'oil_palm_tree_v3';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'uuid',
		'uuid_increment',
		'name',
		'region',
		'estate',
		'afdeling',
		'block',
		'ancak',
		'planting_year',
		'hgu',
		'description',
		'type',
		'source',
		'coordinates',
		'geometry'
	];

	protected $guarded = [
		'id',
	];

	// protected $appends = ['geometry_array'];

	// public function getGeometryArrayAttribute()
	// {
	// 	return json_decode($this->attributes['geometry']) ?? [];
	// }
}
