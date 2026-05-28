<?php

namespace App\Http\Controllers\Api\v1\Google\Company;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Base_API_Rev_Controller;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use Google\AdsApi\AdManager\AdManagerSession;
use Google\AdsApi\AdManager\AdManagerSessionBuilder;
use Google\AdsApi\AdManager\v202511\Company;
use Google\AdsApi\AdManager\v202511\CompanyType;
use Google\AdsApi\AdManager\v202511\CompanyCreditStatus;
use Google\AdsApi\AdManager\v202511\ServiceFactory;
use Google\AdsApi\AdManager\Util\v202511\StatementBuilder;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use App\Services\GoogleAdManagerService;

class Company_Controller extends Controller
{
	protected $getNewSession;

	/*
	 * 1. Pengiklan
	 * 2. Jaringan Iklan
	 * 3. Agensi
	 * 4. Pengiklan Internal
	 * 5. Agensi Internal
	 * 6. Penyedia Visibilitas
	 */
	
	protected $companyType = 
	[
		'advertiser' 			=> ['name' => 'Advertiser', 'code' => CompanyType::ADVERTISER],
		'house_advertiser'		=> ['name' => 'House Advertiser', 'code' => CompanyType::HOUSE_ADVERTISER],
		'ad_network' 			=> ['name' => 'Ad Network', 'code' => CompanyType::AD_NETWORK],
		'agency' 				=> ['name' => 'Agency', 'code' => CompanyType::AGENCY],
		'house_agency' 			=> ['name' => 'House Agency', 'code' => CompanyType::HOUSE_AGENCY],
		'partner' 				=> ['name' => 'Partner', 'code' => CompanyType::PARTNER],
		'viewbility_provider' 	=> ['name' => 'Viewbility Provider', 'code' => CompanyType::VIEWABILITY_PROVIDER],
	];

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

	public function create(Request $request, ServiceFactory $serviceFactory): JsonResponse
	{
		try
		{
			$companyService = $serviceFactory->createCompanyService($this->service->getSession());

			$company = new Company();
			$company->setName($request->input('company_name').' #'.uniqid());
			$company->setType($this->companyType[$request->input('company_type')]['code']);

			/*
			if ($request->filled('company_status')) 
			{
				// Mapping input string ke Class Constant Google
				// Supaya aman jika user kirim huruf kecil
				$statusInput = strtoupper($request->input('company_status'));
				
				$statusMap = [
					'ACTIVE'      => CompanyCreditStatus::ACTIVE,
					'INACTIVE'    => CompanyCreditStatus::INACTIVE,
					'ON_HOLD'     => CompanyCreditStatus::ON_HOLD,
					'CREDIT_STOP' => CompanyCreditStatus::CREDIT_STOP
				];
				
				if (isset($statusMap[$statusInput])) 
				{
					$company->setCreditStatus($statusMap[$statusInput]);
				} 
				else 
				{
					// Opsional: Throw error jika status tidak valid
					throw new \Exception("Status tidak valid. Gunakan: ACTIVE, INACTIVE, ON_HOLD, CREDIT_STOP");
				}
			}
			*/

			// Create the company on the server.
			$results = $companyService->createCompanies([$company]);

			$newResults = [];

			// Print out some information for each created company.
			foreach ($results as $i => $company) 
			{
				$newResults['id'] = $company->getId();
				$newResults['name'] = $company->getName();
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

	public function update(Request $request, ServiceFactory $serviceFactory, $idOrSlug): JsonResponse
	{
		// dd($request->all());

		try 
		{
			// 1. Inisialisasi Service
			$companyService = $serviceFactory->createCompanyService($this->service->getSession());

			// 2. Buat Statement untuk mencari Company lama berdasarkan ID
			$statementBuilder = (new StatementBuilder())
				->where('id = :id')
				->orderBy('id ASC')
				->limit(1)
				->withBindVariableValue('id', $idOrSlug);

			// 3. Ambil data company eksisting dari Google
			$page = $companyService->getCompaniesByStatement($statementBuilder->toStatement());
			$existingCompany = $page->getResults()[0] ?? null;

			// Jika company tidak ditemukan
			if ( ! $existingCompany) 
			{
				// Throw error agar ditangkap catch, atau return response 404 langsung
				throw new \Exception("Company dengan ID $id tidak ditemukan di Google Ad Manager.");
			}

			// 4. Update Data (Modify)
			
			// Update Nama jika ada di request
			if ($request->filled('company_name')) 
			{
				// Saya hapus uniqid() agar nama sesuai inputan edit. 
				// Jika Anda tetap ingin menambah uniqid saat edit, tambahkan .' #'.uniqid()
				$existingCompany->setName($request->input('company_name'));
			}

			// Update Tipe jika ada di request
			if ($request->filled('company_type') && isset($this->companyType[$request->input('company_type')])) 
			{
				$existingCompany->setType($this->companyType[$request->input('company_type')]['code']);
			}

			/*
			if ($request->filled('company_status')) 
			{
				$existingCompany->setCreditStatus(CompanyCreditStatus::ON_HOLD);

				// --- VALIDASI PENTING ---
				// Cek apakah tipe company mendukung Credit Status
				// HOUSE_ADVERTISER dan HOUSE_AGENCY tidak boleh punya Credit Status
				$isHouseCompany = in_array($currentType, 
				[
					CompanyType::ADVERTISER,
					CompanyType::HOUSE_ADVERTISER, 
					CompanyType::HOUSE_AGENCY
				]);

				if ($isHouseCompany) {
					// Opsi A: Abaikan diam-diam (Skip)
					// Opsi B: Throw error jika user memaksa ubah status House Ads (Pilih salah satu)
					
					// Contoh Opsi A (Aman): Tidak melakukan apa-apa.
				} 
				else {
					// Jika BUKAN House Ads, baru boleh set status
					$statusInput = strtoupper($request->input('company_status'));
					
					$statusMap = [
						'ACTIVE'      => CompanyCreditStatus::ACTIVE,
						'INACTIVE'    => CompanyCreditStatus::INACTIVE,
						'ON_HOLD'     => CompanyCreditStatus::ON_HOLD,
						'CREDIT_STOP' => CompanyCreditStatus::CREDIT_STOP
					];

					if (isset($statusMap[$statusInput])) 
					{
						$existingCompany->setCreditStatus($statusMap[$statusInput]);
					}
				}
			}
			*/

			// 5. Kirim perubahan ke Google (Update)
			// Ingat: methodnya updateCompanies (plural), parameternya array
			$results = $companyService->updateCompanies([$existingCompany]);

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
