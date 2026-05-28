<?php

namespace App\Models\Awesome_Admin;

// use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
// use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;

class Account extends Authenticatable
{
	// use HasFactory, HasRoles, Notifiable, HasApiTokens, AuthenticationLoggable;
	use HasFactory, HasRoles, Notifiable, HasApiTokens;

	protected $table = 'accounts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'uuid',
		'username',
		'email',
		'password',
		'fullname',
		'remember_token',
		'recovery_code',
		'recovery_code_duration',
		'token',
		'status',
		'suspended_until',
		'suspended_at'
	];    

	protected $guarded = [
		'id',
	];

	protected $hidden = [
		'password',
	];

	public function isAdmin(): bool
	{
		$user = $this->with('roles')->where('email', auth()->user()->email)->first();

		return $user->hasRole(['Super Admin', 'Administrator']);
	}

	public function checkRole(): bool
	{
		$user = $this->with('roles')->where('email', auth()->user()->email)->first();

		return $user->hasRole(['Super Admin', 'Administrator']);
	}

	public function checkPermission(): bool
	{
		$user = $this->with('roles')->where('email', auth()->user()->email)->first();

		return $user->hasRole(['Super Admin', 'Administrator']);
	}

	public function getCurrentUserRoles()
	{
		$current_user_roles = Account::with('roles')->where('email', auth()->user()->email)->first();

		return $current_user_roles->getRoleNames();
	}

	public function getCurrentUserRoleById()
	{
		$current_user_roles = Account::with('roles')->where('email', auth()->user()->email)->first();

		return $current_user_roles->roles->first()->id;
	}

	public function getStatus(): BelongsTo
	{
		return $this->belongsTo(Account_Status::class, 'status', 'id');
	}

	public function suspended(): bool
	{
		return ! is_null($this->suspended_at);
	}

	public function information()
	{
		return $this->hasOne(Account_Information::class, 'user_id', 'id');
	}
}
