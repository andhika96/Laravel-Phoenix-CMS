<?php

namespace App\Http\Controllers\Api\v1\Facebook\Ad;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use App\Models\Awesome_Admin\Account;
use App\Models\Awesome_Admin\Account_Status;
use App\Models\Awesome_Admin\Account_Information;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

use Dedoc\Scramble\Support\Generator\SecurityScheme;

use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Ad;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Logger\CurlLogger;

use FacebookAds\Http\Exception\RequestException;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Exception\Exception as SDKException;

class Ad_Controller extends Controller
{
	private $baseUrl = 'https://graph.facebook.com/v24.0';

	// private $appId = '805797799007922';
	private $appId = '853381233810147';

	// private $appSecret = '1bd4f57220ffdf6bb658fccd1b287c2e';
	private $appSecret = 'eeb84bccb88f3524e725ce538b4a9085';

	// private $appToken = '668fde19de22491c40de14a2198f68ae';
	private $appToken = '03716ec40f56f66d169ebf7f85a48d32';

	// private $adAccount = 'act_1214354323898453';
	private $adAccount = 'act_2170735326794786';

	// private $adAccountToken = 'EAALc3muSFrIBP0UZBSJ8VSXIZB9XsnW0q7KAQFnL1PWDVcZCWMGTFpzd4ipm9ydlq2JGsK9aIq4ZBfVEGInd1fsRkdRSzZA7J93RDVtBXnYZA5ZA4U8pcVtHZChOtZBAy32EJqEe5K1EuSPR5Wgom1cK93IlTJrqHMTVuT614xSy1Uk53HPZBbUZB7J9Mu1TG4ZCWBH6ZCU09BOZC7';
	private $adAccountToken = 'EAAMIJU1MmuMBQJvUCSWkdzqvANZCrZCQmsZAW3JqsDAIVLYyRSgZAqQzZAMWrmHSNXcJvDDTGealsvcZCoN8rgYrJX5DhmSUKDlAbv94hIBy29Q5SjeMEd4OwDrPqvDlvU42rAaCfE6pkv4mSbO1ZAoTZAPSjnAzZB3lZCgVa3xdkV4tKQnKxon5mHrZBDgVhApZBclPOAZDZD';

	private $adFields = 
	[
		'id',
		'campaign_id',
		'name',
		'status',
		'start_time',
		'daily_budget',
		'lifetime_budget',
		'effective_status',
		'created_time'
	];

	private $params = 
	[
		'limit' => 2
	];

	public function __construct()
	{
		// Initialize a new Session and instantiate an Api object
		$this->fbApi = Api::init($this->appId, $this->appSecret, $this->adAccountToken);
		$this->fbApi->setLogger(new CurlLogger());

		// The Api object is now available through singleton
		$this->fbApiInstance = Api::instance();

		$this->jsonResponse = new Base_API_Rev_Controller();
	}

	public function index()
	{

	}

	public function list(Request $request): JsonResponse
	{
		try
		{
			$getBeforeCursor = ($request->input('before') && $request->input('before') !== '') ? $this->params['before'] = $request->input('before') : '';
			$getNextCursor = ($request->input('after') && $request->input('after') !== '') ? $this->params['after'] = $request->input('after') : '';

			$account = new AdAccount($this->adAccount);
			$cursor = $account->getAds($this->adFields, $this->params);
			$cursorData = isset($cursor->getResponse()->getContent()['data']) ? $cursor->getResponse()->getContent()['data'] : 'Data not found';
			$cursorPaging = isset($cursor->getResponse()->getContent()['paging']) ? $cursor->getResponse()->getContent()['paging'] : [];

			$response = $this->jsonResponse->respondOK(['data' => $cursorData, 'paging' => $cursorPaging]);
		}
		catch (RequestException $e) 
		{
			$getErrorMessage = 
			[
				'title' => $e->getErrorUserTitle(),
				'message' => $e->getErrorUserMessage()
			];

			$response = $this->jsonResponse->respondInternalError(['message' => $getErrorMessage]);
		} 
		catch (SDKException $e) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $e->getMessage()]);
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

	public function create(Request $request): JsonResponse
	{
		try
		{
			$fields = [];
			$params = 
			[
				'name'			=> $request->input('name'),
				'adset_id'		=> $request->input('adset_id'),
				'creative'		=> $request->input('creative'),
				'status' 		=> $request->input('status')
			];

			$createAd = new AdAccount($this->adAccount);
			$newAd = $createAd->createAd($fields, $params);

			$resultAd = $newAd->exportAllData();
			$adId = $newAd->{AdFields::ID};

			$response = $this->jsonResponse->respondOK(['data' => $adId]);
		}
		catch (RequestException $e) 
		{
			$getErrorMessage = 
			[
				'title' => $e->getErrorUserTitle(),
				'message' => $e->getErrorUserMessage()
			];

			$response = $this->jsonResponse->respondInternalError(['message' => $getErrorMessage]);
		} 
		catch (SDKException $e) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $e->getMessage()]);
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

	public function update(Request $request): JsonResponse
	{
		try
		{
			$fields = [];
			$params = 
			[
				'name'			=> $request->input('name'),
				'adset_id'		=> $request->input('adset_id'),
				'creative'		=> $request->input('creative'),
				'status' 		=> $request->input('status')
			];

			$editAdset = new AdSet($request->input('adset_id'), $this->adAccount);
			$editAdset->updateSelf($fields, $params);

			$resultAdset = $editAdset->exportAllData();

			$response = $this->jsonResponse->respondOK(['data' => $resultAdset]);
		}
		catch (RequestException $e) 
		{
			$getErrorMessage = 
			[
				'title' => $e->getErrorUserTitle(),
				'message' => $e->getErrorUserMessage()
			];

			$response = $this->jsonResponse->respondInternalError(['message' => $getErrorMessage]);
		} 
		catch (SDKException $e) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $e->getMessage()]);
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

	public function delete(Request $request): JsonResponse
	{
		try
		{
			$fields = [];
			$params = [];

			$deleteAdset = new Campaign($request->input('adset_id'), $this->adAccount);
			$deleteAdset->deleteSelf($fields, $params);

			$resultAdset = $deleteAdset->exportAllData();

			$response = $this->jsonResponse->respondOK(['data' => $resultAdset]);
		}
		catch (RequestException $e) 
		{
			$getErrorMessage = 
			[
				'title' => $e->getErrorUserTitle(),
				'message' => $e->getErrorUserMessage()
			];

			$response = $this->jsonResponse->respondInternalError(['message' => $getErrorMessage]);
		} 
		catch (SDKException $e) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $e->getMessage()]);
		} 
		catch (\Throwable $th) 
		{
			$response = $this->jsonResponse->respondInternalError(['message' => $th->getMessage()]);
		} 
		finally 
		{
			return $response;
		}
	}

}