<?php

namespace App\Http\Controllers\Api\v1\Facebook\Campaign;

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
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\CampaignFields;
use FacebookAds\Logger\CurlLogger;

use FacebookAds\Http\Exception\RequestException;
use FacebookAds\Http\Exception\AuthorizationException;
use FacebookAds\Exception\Exception as SDKException;

class Campaign_Controller extends Controller
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
	private $adAccountToken = 'EAAMIJU1MmuMBQPuRpa55cPtRBENk0nNw08J6IucRohhTMLtpvuRRLXiXhySI3is92JDwFo95xKMKfVUd67WFLen0WeklL23beFKs98Yu2y9Ofo0TDvglsflY5uOOVfGungHaPRHD6A1Xb9hVKwHQcFSJqAoetV08juX30U7oGxIJMjvO4LScf8dECuZCN5QZDZD';

	private $campaignFields = 
	[
		'id',
		'name',
		'objective',
		'status',
		'start_time',
		'stop_time',
		'daily_budget',
		'lifetime_budget',
		'remaining_budget',
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
			$cursor = $account->getCampaigns($this->campaignFields, $this->params);
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
				'name'								=> $request->input('name'),
				'objective'							=> $request->input('objective'),
				'effective_status'					=> $request->input('effective_status'),
				'special_ad_categories'				=> $request->input('special_ad_categories'),
				'is_adset_budget_sharing_enabled' 	=> $request->input('is_adset_budget_sharing_enabled'),
			];

			$createCampaign = new AdAccount($this->adAccount);
			$newCampaign = $createCampaign->createCampaign($fields, $params);

			$resultCampaign = $newCampaign->exportAllData();
			$campaignId = $newCampaign->{CampaignFields::ID};

			$response = $this->jsonResponse->respondOK(['data' => $campaignId]);
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
				'name'								=> $request->input('name'),
				'objective'							=> $request->input('objective'),
				'effective_status'					=> $request->input('effective_status'),
				'special_ad_categories'				=> $request->input('special_ad_categories'),
				'is_adset_budget_sharing_enabled' 	=> $request->input('is_adset_budget_sharing_enabled'),
			];

			$editCampaign = new Campaign($request->input('campaign_id'), $this->adAccount);
			$editCampaign->updateSelf($fields, $params);

			$resultCampaign = $editCampaign->exportAllData();

			$response = $this->jsonResponse->respondOK(['data' => $resultCampaign]);
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

			$deleteCampaign = new Campaign($request->input('campaign_id'), $this->adAccount);
			$deleteCampaign->deleteSelf($fields, $params);

			$resultCampaign = $deleteCampaign->exportAllData();

			$response = $this->jsonResponse->respondOK(['data' => $resultCampaign]);
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