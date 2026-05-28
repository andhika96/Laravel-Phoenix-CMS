<?php

namespace App\Models\Awesome_Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SMTP_Service extends Model
{
	use HasFactory;

	protected $table = 'smtp_service';

	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'service_id',
		'service_name'
	];    

	protected $guarded = [
		'id',
	];

	public function smtp_setting(): BelongsTo
	{
		return $this->belongsTo(SMTP::class, 'service_id', 'id');
	}
}
