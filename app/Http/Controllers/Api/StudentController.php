<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    protected StudentService $studentService;

    /**
     * Constructor.
     */
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    /**
     * Menampilkan semua siswa.
     */
    public function index(): JsonResponse
    {
        $result = $this->studentService->index();

        return response()->json($result, 200);
    }

    /**
     * Menambahkan siswa baru.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $result = $this->studentService->store($request->validated());

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result, 201);
    }

    /**
     * Detail siswa.
     */
    public function show(Student $student): JsonResponse
    {
        $result = $this->studentService->show($student);

        return response()->json($result, 200);
    }

    /**
     * Update siswa.
     */
    public function update(
        UpdateStudentRequest $request,
        Student $student
    ): JsonResponse {
        $result = $this->studentService->update(
            $request->validated(),
            $student
        );

        if (!$result['success']) {
            return response()->json($result, 500);
        }

        return response()->json($result, 200);
    }

    /**
     * Menampilkan siswa yang berada di perusahaan supervisor yang sedang login.
     */
    public function myStudents(): JsonResponse
    {
        $user = auth()->user();

        $supervisor = $user->supervisor;

        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Data supervisor tidak ditemukan.',
            ], 404);
        }

        $students = $this->studentService->getByCompany(
            $supervisor->company_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Data siswa bimbingan berhasil diambil.',
            'data' => $students,
        ], 200);
    }

    /**
     * Hapus siswa.
     */
    public function destroy(Student $student): JsonResponse
    {
        $result = $this->studentService->destroy($student);

        return response()->json($result, 200);
    }
}