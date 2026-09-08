<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceService $attendanceService;

    /**
     * Constructor
     */
    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * GET /api/attendances
     *
     * Menampilkan semua absensi.
     */
    public function index(): JsonResponse
    {
        $attendances = $this->attendanceService->getAll();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi berhasil diambil.',
            'data' => $attendances,
        ], 200);
    }

    /**
     * POST /api/attendances
     *
     * Membuat absensi siswa.
     */
    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $attendance = $this->attendanceService->store($request);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil ditambahkan.',
            'data' => $attendance,
        ], 201);
    }

    /**
     * GET /api/attendances/{attendance}
     *
     * Detail absensi.
     */
    public function show(Attendance $attendance): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail absensi.',
            'data' => $attendance->load([
                'student.user',
                'student.company',
                'verifier.user',
            ]),
        ], 200);
    }

    /**
     * PUT /api/attendances/{attendance}
     *
     * Update absensi.
     */
    public function update(
        UpdateAttendanceRequest $request,
        Attendance $attendance
    ): JsonResponse {
        $attendance = $this->attendanceService->update(
            $request,
            $attendance
        );

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil diperbarui.',
            'data' => $attendance,
        ], 200);
    }

    /**
     * DELETE /api/attendances/{attendance}
     *
     * Hapus absensi.
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        $this->attendanceService->destroy($attendance);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dihapus.',
        ], 200);
    }

    /**
     * GET /api/student/attendances
     *
     * Absensi siswa yang sedang login.
     */
    public function myAttendances(): JsonResponse
    {
        $attendances = $this->attendanceService->getMyAttendances();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi saya berhasil diambil.',
            'data' => $attendances,
        ], 200);
    }

    /**
     * GET /api/supervisor/attendances
     *
     * Absensi siswa yang dibimbing supervisor yang sedang login.
     */
    public function supervisorAttendances(): JsonResponse
    {
        $attendances = $this->attendanceService->getSupervisorAttendances();

        return response()->json([
            'success' => true,
            'message' => 'Data absensi siswa bimbingan berhasil diambil.',
            'data' => $attendances,
        ], 200);
    }

    /**
     * PUT /api/attendances/{attendance}/verify
     *
     * Verifikasi absensi oleh admin.
     */
    public function verify(
        Request $request,
        Attendance $attendance
    ): JsonResponse {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'verification_note' => 'nullable|string',
        ]);

        $attendance = $this->attendanceService->verify(
            $attendance,
            $request
        );

        $message = $request->status === 'approved'
            ? 'Absensi berhasil diverifikasi.'
            : 'Absensi berhasil ditolak.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $attendance,
        ], 200);
    }
}