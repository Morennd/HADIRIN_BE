<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    protected CompanyService $companyService;

    /**
     * Constructor.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Menampilkan semua data perusahaan.
     */
    public function index(): JsonResponse
    {
        $result = $this->companyService->index();

        return response()->json($result, 200);
    }

    /**
     * Menyimpan data perusahaan baru.
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $result = $this->companyService->store($request->validated());

        return response()->json($result, 201);
    }

    /**
     * Menampilkan detail satu perusahaan.
     */
    public function show(Company $company): JsonResponse
    {
        $result = $this->companyService->show($company);

        return response()->json($result, 200);
    }

    /**
     * Mengubah data perusahaan.
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $result = $this->companyService->update(
            $request->validated(),
            $company
        );

        return response()->json($result, 200);
    }

    /**
     * Menghapus data perusahaan.
     */
    public function destroy(Company $company): JsonResponse
    {
        $result = $this->companyService->destroy($company);

        return response()->json($result, 200);
    }
}