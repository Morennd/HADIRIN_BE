<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supervisor\StoreSupervisorRequest;
use App\Http\Requests\Supervisor\UpdateSupervisorRequest;
use App\Models\Supervisor;
use App\Services\SupervisorService;
use Illuminate\Http\JsonResponse;

class SupervisorController extends Controller
{
    protected SupervisorService $supervisorService;

    /**
     * Constructor.
     */
    public function __construct(SupervisorService $supervisorService)
    {
        $this->supervisorService = $supervisorService;
    }

    /**
     * Menampilkan semua supervisor.
     */
    public function index(): JsonResponse
    {
        $supervisors = $this->supervisorService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Data supervisor berhasil diambil.',
            'data' => $supervisors,
        ], 200);
    }

    /**
     * Menambahkan supervisor baru.
     */
    public function store(StoreSupervisorRequest $request): JsonResponse
    {
        $supervisor = $this->supervisorService->store($request);

        return response()->json([
            'success' => true,
            'message' => 'Supervisor berhasil ditambahkan.',
            'data' => $supervisor,
        ], 201);
    }

    /**
     * Menampilkan detail supervisor.
     */
    public function show(Supervisor $supervisor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail supervisor.',
            'data' => $supervisor->load(['user', 'company']),
        ], 200);
    }

    /**
     * Mengubah supervisor.
     */
    public function update(UpdateSupervisorRequest $request, Supervisor $supervisor): JsonResponse
    {
        $supervisor = $this->supervisorService->update($request, $supervisor);

        return response()->json([
            'success' => true,
            'message' => 'Supervisor berhasil diperbarui.',
            'data' => $supervisor,
        ], 200);
    }

    /**
     * Menghapus supervisor.
     */
    public function destroy(Supervisor $supervisor): JsonResponse
    {
        $this->supervisorService->destroy($supervisor);

        return response()->json([
            'success' => true,
            'message' => 'Supervisor berhasil dihapus.',
        ], 200);
    }
}