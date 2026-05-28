<?php

namespace App\Http\Controllers\Api\v1\Google\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202511\User;
use Google\AdsApi\AdManager\v202511\ServiceFactory;
use Google\AdsApi\AdManager\Util\v202511\StatementBuilder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use App\Services\GoogleAdManagerService;

class User_Controller extends Controller
{
	protected $getNewSession;

	public function __construct(public GoogleAdManagerService $service)
	{
		// $this->getNewSession = new AdManagerSession($this->service->getSession());

		$this->jsonResponse = new Base_API_Rev_Controller();
	}

	public function index()
	{
		echo 'adawdawd';
	}

	public function list()
	{

	}

	public function listRole(ServiceFactory $serviceFactory): JsonResponse
	{
		try
		{
			$userService = $serviceFactory->createUserService($this->service->getSession());

			$results = $userService->getAllRoles();

			// Print out some information for each created company.
			foreach ($results as $i => $role) 
			{
				$newResults['id'] = $role->getId();
				$newResults['name'] = $role->getName();
			}

			$response = $this->jsonResponse->respondOK(['data' => $newResults]);
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

	public function create(Request $request, ServiceFactory $serviceFactory): JsonResponse
	{
		try
		{
			$userService = $serviceFactory->createUserService($this->service->getSession());

			$user = new User();
			$user->setName($request->input('fullname').' #'.uniqid());
			$user->setEmail($request->input('email'));
			$user->setRoleId($request->input('roleId'));

			// Create the company on the server.
			$results = $userService->createUsers([$company]);

			$newResults = [];

			// Print out some information for each created company.
			foreach ($results as $i => $user) 
			{
				$newResults['id'] = $user->getId();
				$newResults['name'] = $user->getName();
			}

			$response = $this->jsonResponse->respondOK(['data' => $newResults]);
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

	public function update(Request $request, ServiceFactory $serviceFactory, $idOrSlug): JsonResponse
	{
		// dd($request->all());

		try 
		{
			// 1. Inisialisasi Service
			$companyService = $serviceFactory->createUserService($this->service->getSession());

			// 2. Buat Statement untuk mencari User lama berdasarkan ID
			$statementBuilder = (new StatementBuilder())
				->where('id = :id')
				->userBy('id ASC')
				->limit(1)
				->withBindVariableValue('id', $idOrSlug);

			// 3. Ambil data company eksisting dari Google
			$page = $companyService->getCompaniesByStatement($statementBuilder->toStatement());
			$existingUser = $page->getResults()[0] ?? null;

			// Jika company tidak ditemukan
			if ( ! $existingUser) 
			{
				// Throw error agar ditangkap catch, atau return response 404 langsung
				throw new \Exception("User dengan ID $id tidak ditemukan di Google Ad Manager.");
			}

			// 4. Update Data (Modify)
			
			// Update Nama jika ada di request
			if ($request->filled('company_name')) 
			{
				// Saya hapus uniqid() agar nama sesuai inputan edit. 
				// Jika Anda tetap ingin menambah uniqid saat edit, tambahkan .' #'.uniqid()
				$existingUser->setName($request->input('company_name'));
			}

			// Update Tipe jika ada di request
			if ($request->filled('company_type') && isset($this->companyType[$request->input('company_type')])) 
			{
				$existingUser->setType($this->companyType[$request->input('company_type')]['code']);
			}

			// 5. Kirim perubahan ke Google (Update)
			// Ingat: methodnya updateCompanies (plural), parameternya array
			$results = $companyService->updateCompanies([$existingUser]);

			$newResults = [];

			// 6. Format Output
			foreach ($results as $i => $company) 
			{
				$newResults['id'] = $company->getId();
				$newResults['name'] = $company->getName();
				$newResults['type'] = $company->getType(); // Menambahkan type untuk konfirmasi
				$newResults['credit_status'] = $company->getCreditStatus();
			}

			$response = $this->jsonResponse->respondOK(['data' => $newResults]);
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

	public function delete()
	{

	}

	public function up()
	{
		$this->service->checkNetwork();

	}
}

?>
