<?php

namespace App\Services;

use App\Models\Awesome_Admin\Account;
use App\Enums\QueryAcceptedComparatorEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserService extends BaseService
{
	protected array $urls;

	public function __construct(Account $model)
	{
		parent::__construct($model);

		$this->urls = array(
			'index' => url('awesome_admin/user/'),
			'show' => url('awesome_admin/user/profile/'),
			'create' => url('awesome_admin/user/create'),
			'store' => url('awesome_admin/user/store'),
			'edit' => url('awesome_admin/user/edit/'),
			'update' => url('awesome_admin/user/update/'),
			'destroy' => url('awesome_admin/user/destroy/')
		);
	}

	public function create(array $attr): Account
	{
		$attr['uuid'] ??= Str::uuid();
		$attr['username'] ??= $attr['email'];
		$attr['remember_token'] ??= "";
		$attr['roles'] ??= 1;
		$attr['role_code'] ??= "role_code";
		$attr['recovery_code'] ??= uniqid("laraphoexRecovery_");
		$attr['recovery_code_duration'] ??= now()->addYears(3)->diffInSeconds(now());
		$attr['token'] ??= uniqid("LaraPhoenixPAT_");
		$attr['password'] ??= "LaraPhoenixDev";

		return parent::create($attr);
	}

	/*
	 * Added by Andhika Adhitia N
	 *
	 * Date 09-07-2024
	 */

	public function create_option(array $attr): Account
	{
		$attr['uuid'] ??= Str::uuid();
		$attr['username'] ??= $attr['email'];
		$attr['remember_token'] ??= "";
		$attr['roles'] ??= 1;
		$attr['role_code'] ??= "role_code";
		$attr['recovery_code'] ??= uniqid("laraphoexRecovery_");
		$attr['recovery_code_duration'] ??= now()->addYears(3)->diffInSeconds(now());
		$attr['token'] ??= uniqid("LaraPhoenixPAT_");
		
		if (isset($attr['auto_setpassword']) && $attr['auto_setpassword'] == true)
		{
			$attr['password'] ??= "LaraPhoenixDev";
		}
		elseif (isset($attr['auto_setpassword']) && $attr['auto_setpassword'] == false)
		{
			$attr['password'] ??= Hash::make($attr['password']);
		}

		return parent::create($attr);
	}

	public function update(array $attr, $idOrSlug): Account
	{
		$attr['uuid'] ??= Str::uuid();
		$attr['username'] ??= $attr['email'];
		$attr['remember_token'] ??= "";
		$attr['roles'] ??= 1;
		$attr['role_code'] ??= "role_code";
		$attr['recovery_code'] ??= uniqid("laraphoexRecovery_");
		$attr['recovery_code_duration'] ??= now()->addYears(3)->diffInSeconds(now());
		$attr['token'] ??= uniqid("LaraPhoenixPAT_");
		$attr['password'] ??= "LaraPhoenixDev";

		return parent::update($attr, $idOrSlug);
	}

	/*
	 * Added by Andhika Adhitia N
	 *
	 * Date 09-07-2024
	 */

	public function update_option(array $attr, $idOrSlug): Account
	{
		$attr['uuid'] ??= Str::uuid();
		$attr['username'] ??= $attr['email'];
		$attr['remember_token'] ??= "";
		$attr['roles'] ??= 1;
		$attr['role_code'] ??= "role_code";
		$attr['recovery_code'] ??= uniqid("laraphoexRecovery_");
		$attr['recovery_code_duration'] ??= now()->addYears(3)->diffInSeconds(now());
		$attr['token'] ??= uniqid("LaraPhoenixPAT_");
		
		if (isset($attr['auto_setpassword']) && $attr['auto_setpassword'] == true)
		{
			$attr['password'] ??= "LaraPhoenixDev";
		}

		return parent::update($attr, $idOrSlug);
	}

	public function getUrls(): array
	{
		return $this->urls;
	}

	public function setUrls(array $urls)
	{
		$this->urls = $urls;
		
		return $this;
	}

	/**
	 * Find model instances based on specified indexes.
	 *
	 * @param array $indexes An array of column-value pairs for filtering.
	 * @param bool $any Whether to match any or all of the provided indexes.
	 * @param int|null $limit The maximum number of results to return.
	 * @param array $orderBy An array of columns to order the results by.
	 * @param QueryAcceptedComparatorEnum $comparator The comparison operator for each index.
	 * @return Collection|\Illuminate\Pagination\LengthAwarePaginator The matching model instances.
	 */
	public function findByIndexes(
		array $indexes,
		bool $any,
		?int $limit,
		array $orderBy,
		QueryAcceptedComparatorEnum $comparator = QueryAcceptedComparatorEnum::EQUAL
	) {
		// Get column names of the model's table
		$columns = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($this->model->getTable());
		$query = $this->model->newQuery();

		foreach ($indexes as $column => $value) {
			if ($comparator == QueryAcceptedComparatorEnum::LIKE) {
				$value = "%{$value}%";
			}

			if (in_array($column, $columns)) {
				$query->when($any, function ($query) use ($column, $comparator, $value) {
					$query->orWhere($column, $comparator->value, $value);
				}, function ($query) use ($column, $comparator, $value) {
					$query->where($column, $comparator->value, $value);
				});
			}
		}

		// Add new field or key Roles and Get Status
		// Added by Andhika Adhitia N
		// Date 09-07-2024
		$query->with(['roles', 'GetStatus']);

		// dd($query->get());
		// Ignore specific records if requested
		$query->when(isset($indexes['ignore']), function ($query) use ($indexes) {
			$query->whereNotIn('id', $indexes['ignore']);
		});

		// Apply special sorting (e.g., random)
		$query->when(!empty($indexes['special_sort']) && $indexes['special_sort'] === 'random', function ($query) {
			$query->inRandomOrder();
		});

		// Apply regular sorting if no special sorting is requested
		if (!empty($orderBy) && empty($indexes['special_sort'])) {
			foreach ($orderBy as $orderByColumn) {
				$orderByArray = explode(' ', $orderByColumn);
				$orderByColumn = $orderByArray[0];
				$orderByDirection = isset($orderByArray[1]) ? $orderByArray[1] : 'ASC';

				if (in_array($orderByColumn, $columns)) {
					$query->orderBy($orderByColumn, $orderByDirection);
				}
			}
		}

		// Pagination configuration
		if ($limit === -1) {
			$results = $query->get();
		} else {
			$perPage = $limit;
			$currentPage = request()->get('page', 1);
			$results = $query->paginate($perPage, ['*'], 'page', $currentPage);
		}

		return $results;
	}
}
