<?php

/*
 * Revision by Andhika Adhitia N
 *
 * Date 20-12-2024
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\QueryAcceptedComparatorEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;

class Base_API_Rev_Controller extends Controller
{
	protected $statusCode = 200;
	protected $statusMsg = "success";
	protected $recordLimit = 12;

	public function respond($data, $status = null, $headers = []): JsonResponse
	{
		return Response::json($data, $status ?? $this->statusCode, $headers);
	}

	public function respondDetail($message, $success = true, $extras = []): JsonResponse
	{
		$responseArray = 
		[
			'success' 	=> $success,
			'status' 	=> $this->statusMsg,
			'code' 		=> $this->statusCode,
			'message' 	=> $message,
		];

		if ( ! empty($extras)) 
		{
			$responseArray = array_merge($responseArray, $extras);
		}

		return $this->respond($responseArray);
	}

	public function setStatusCode($statusCode): Base_API_Rev_Controller
	{
		$this->statusCode = $statusCode;

		return $this;
	}

	public function setStatusMsg($message): Base_API_Rev_Controller
	{
		$this->statusMsg = $message;

		return $this;
	}

	public function generateResponse($statusCode, $extras = null, $message = '', $success = true): JsonResponse
	{
		return $this->setStatusCode($statusCode)->respondDetail($message, $success, $extras);
	}

	public function respondOK($extras = null, $message = 'Success!', $success = true): JsonResponse
	{
		return $this->generateResponse(200, $extras, $message, $success);
	}

	public function respondCreated($extras = null, $message = 'The resource has been created', $success = true): JsonResponse
	{
		return $this->generateResponse(201, $extras, $message, $success);
	}

	public function respondDeleted($extras = null, $message = 'The resource has been deleted', $success = false): JsonResponse
	{
		return $this->generateResponse(204, $extras, $message, $success);
	}

	public function respondRedirect(string $url, $status = 302, array $headers = []): JsonResponse
	{
		return $this->generateResponse($status, ['redirect_uri' => $url], null, true, $headers);
	}

	public function respondBadRequest($extras = null, $message = 'Bad request!', $success = false): JsonResponse
	{
		return $this->generateResponse(400, $extras, $message, $success);
	}

	public function respondUnauthorized($extras = null, $message = 'Unauthorized!', $success = false): JsonResponse
	{
		return $this->generateResponse(401, $extras, $message, $success);
	}

	public function respondForbidden($extras = null, $message = 'Forbidden!', $success = false): JsonResponse
	{
		return $this->generateResponse(403, $extras, $message, $success);
	}

	public function respondNotFound($extras = null, $message = 'Not found!', $success = true): JsonResponse
	{
		return $this->generateResponse(404, $extras, $message, $success);
	}

	public function respondInternalError($extras = null, $message = 'Internal error!', $success = false): JsonResponse
	{
		return $this->generateResponse(500, $extras, $message, $success);
	}

	public function paginateResponse(Paginator|CursorPaginator|LengthAwarePaginator|Collection $paginatedData, $resource = null, $fields = null): array
	{
		if ($paginatedData instanceof Paginator)
		{
			if (is_subclass_of($resource, JsonResource::class)) 
			{
				$items = $resource::collection($paginatedData->items());
			} 
			else
			{
				$items = $paginatedData->items();
			}

			if ($fields !== null)
			{
				return 
				[
					'fields'		=> $fields,
					'data' 			=> $items,
					'total' 		=> $paginatedData->count(),
					'limit' 		=> $paginatedData->perPage(),
					'previous_page'	=> $paginatedData->previousPageUrl(),
					'next_page'		=> $paginatedData->nextPageUrl(),
					'current_page' 	=> $paginatedData->currentPage(),
					'total_page' 	=> ceil($paginatedData->count() / $paginatedData->perPage())
				];
			}
			else
			{
				return 
				[
					'data' 			=> $items,
					'total' 		=> $paginatedData->count(),
					'limit' 		=> $paginatedData->perPage(),
					'previous_page'	=> $paginatedData->previousPageUrl(),
					'next_page'		=> $paginatedData->nextPageUrl(),
					'current_page' 	=> $paginatedData->currentPage(),
					'total_page' 	=> ceil($paginatedData->count() / $paginatedData->perPage())
				];
			}
		}	
		elseif ($paginatedData instanceof CursorPaginator)
		{
			if (is_subclass_of($resource, JsonResource::class)) 
			{
				$items = $resource::collection($paginatedData->items());
			} 
			else
			{
				$items = $paginatedData->items();
			}

			if ($fields !== null)
			{
				return 
				[
					'fields'			=> $fields,
					'data' 				=> $items,
					'total' 			=> $paginatedData->count(),
					'limit' 			=> $paginatedData->perPage(),
					'previous_cursor'	=> $paginatedData->previousCursor()?->encode(),
					'previous_page'		=> $paginatedData->previousPageUrl(),
					'next_cursor'		=> $paginatedData->nextCursor()?->encode(),
					'next_page'			=> $paginatedData->nextPageUrl(),
					'first_page'		=> $paginatedData->onFirstPage(),
					'last_page'			=> $paginatedData->onLastPage(),
					'current_page' 		=> $paginatedData->cursor()?->encode(),
				];
			}
			else
			{
				return 
				[
					'data' 				=> $items,
					'total' 			=> $paginatedData->count(),
					'limit' 			=> $paginatedData->perPage(),
					'previous_cursor'	=> $paginatedData->previousCursor()?->encode(),
					'previous_page'		=> $paginatedData->previousPageUrl(),
					'next_cursor'		=> $paginatedData->nextCursor()?->encode(),
					'next_page'			=> $paginatedData->nextPageUrl(),
					'first_page'		=> $paginatedData->onFirstPage(),
					'last_page'			=> $paginatedData->onLastPage(),
					'current_page' 		=> $paginatedData->cursor()?->encode(),
				];
			}
		}		
		elseif ($paginatedData instanceof LengthAwarePaginator) 
		{
			if (is_subclass_of($resource, JsonResource::class)) 
			{
				$items = $resource::collection($paginatedData->items());
			} 
			else
			{
				$items = $paginatedData->items();
			}

			if ($fields !== null)
			{
				return 
				[
					'fields'		=> $fields,
					'data' 			=> $items,
					'total' 		=> $paginatedData->total(),
					'limit' 		=> $paginatedData->perPage(),
					'current_page' 	=> $paginatedData->currentPage(),
					'last_page' 	=> $paginatedData->lastPage(),
					'total_page' 	=> ceil($paginatedData->total() / $paginatedData->perPage())
				];
			}
			else
			{
				return 
				[
					'data' 			=> $items,
					'total' 		=> $paginatedData->total(),
					'limit' 		=> $paginatedData->perPage(),
					'current_page' 	=> $paginatedData->currentPage(),
					'last_page' 	=> $paginatedData->lastPage(),
					'total_page' 	=> ceil($paginatedData->total() / $paginatedData->perPage())
				];
			}
		} 
		elseif ($paginatedData instanceof Collection)  // Case when the data is not paginated / limit is -1
		{
			if (is_subclass_of($resource, JsonResource::class)) 
			{
				$items = $resource::collection($paginatedData->all());
			} 
			else 
			{
				$items = $paginatedData->all();
			}

			return 
			[
				'data' 			=> $items,
				'total' 		=> $paginatedData->count(),
				'limit' 		=> $paginatedData->count(),
				'last_page' 	=> 1,
				'total_page' 	=> 1
			];
		}

		throw new \InvalidArgumentException('Invalid type for $paginatedData');
	}

	public function formatErrors($errors): array
	{
		$bag = [];

		foreach ($errors as $value) 
		{
			$key = explode(' ', $value)[0];

			$bag[] = 
			[
				'name' => $key,
				'message' => $value,
			];
		}

		return $bag;
	}

	/**
	 * Prepare the $request for findByIndexes method.
	 * 
	 * @param Request $request The request object.
	 * @return array The prepared indexes.
	 */

	public function prepareIndexes(Request $request): array
	{
		$indexes = $request->all();
		$orderByColumns = $indexes['orderByColumns'] ?? [];
		$orderBy = $orderByColumns ? explode(",", $orderByColumns) : [];
		$any = $indexes['any'] ?? false;
		$any = filter_var($any, FILTER_VALIDATE_BOOLEAN);
		$limit = $indexes['limit'] ?? 10;
		$comparator = $indexes['comparator'] ?? 'ilike';
		$qcomparator = QueryAcceptedComparatorEnum::tryFrom($comparator) ?? QueryAcceptedComparatorEnum::EQUAL;

		if (isset($indexes['ignoreIds'])) 
		{
			$indexes['ignore'] = explode(',', $indexes['ignoreIds']);
			unset($indexes['ignoreIds']);
		}

		return 
		[
			'indexes' 		=> $indexes,
			'any' 			=> $any,
			'limit' 		=> $limit,
			'orderBy' 		=> $orderBy,
			'qcomparator' 	=> $qcomparator
		];
	}
}

?>