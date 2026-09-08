<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Supervisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard/admin
     *
     * Dashboard admin.
     */
    public function admin(): JsonResponse
    {
        $totalStudents = Student::count();

        $totalAttendances = Attendance::count();

        $pendingAttendances = Attendance::where(
            'status',
            'pending'
        )->count();

        $approvedAttendances = Attendance::where(
            'status',
            'approved'
        )->count();

        $rejectedAttendances = Attendance::where(
            'status',
            'rejected'
        )->count();

        $totalSupervisors = Supervisor::count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard admin berhasil diambil.',
            'data' => [
                'total_students' => $totalStudents,
                'total_supervisors' => $totalSupervisors,
                'total_attendances' => $totalAttendances,
                'pending_attendances' => $pendingAttendances,
                'approved_attendances' => $approvedAttendances,
                'rejected_attendances' => $rejectedAttendances,
            ],
        ], 200);
    }

    /**
     * GET /api/dashboard/student
     *
     * Dashboard siswa yang sedang login.
     */
    public function student(Request $request): JsonResponse
    {
        $user = $request->user();

        $student = Student::where(
            'user_id',
            $user->id
        )->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Akun siswa belum memiliki data student.',
            ], 404);
        }

        $totalAttendances = Attendance::where(
            'student_id',
            $student->id
        )->count();

        $pendingAttendances = Attendance::where(
            'student_id',
            $student->id
        )->where(
            'status',
            'pending'
        )->count();

        $approvedAttendances = Attendance::where(
            'student_id',
            $student->id
        )->where(
            'status',
            'approved'
        )->count();

        $rejectedAttendances = Attendance::where(
            'student_id',
            $student->id
        )->where(
            'status',
            'rejected'
        )->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard siswa berhasil diambil.',
            'data' => [
                'student' => $student->load([
                    'user',
                    'company',
                ]),
                'total_attendances' => $totalAttendances,
                'pending_attendances' => $pendingAttendances,
                'approved_attendances' => $approvedAttendances,
                'rejected_attendances' => $rejectedAttendances,
            ],
        ], 200);
    }

    /**
     * GET /api/dashboard/supervisor
     *
     * Dashboard supervisor yang sedang login.
     */
    public function supervisor(Request $request): JsonResponse
    {
        $user = $request->user();

        $supervisor = Supervisor::where(
            'user_id',
            $user->id
        )->first();

        if (!$supervisor) {
            return response()->json([
                'success' => false,
                'message' => 'Akun supervisor belum memiliki data supervisor.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil siswa yang dibimbing
        |--------------------------------------------------------------------------
        */

        $students = Student::where(
            'company_id',
            $supervisor->company_id
        )->get();

        $studentIds = $students->pluck('id');

        /*
        |--------------------------------------------------------------------------
        | Statistik absensi
        |--------------------------------------------------------------------------
        */

        $totalStudents = $students->count();

        $totalAttendances = Attendance::whereIn(
            'student_id',
            $studentIds
        )->count();

        $pendingAttendances = Attendance::whereIn(
            'student_id',
            $studentIds
        )->where(
            'status',
            'pending'
        )->count();

        $approvedAttendances = Attendance::whereIn(
            'student_id',
            $studentIds
        )->where(
            'status',
            'approved'
        )->count();

        $rejectedAttendances = Attendance::whereIn(
            'student_id',
            $studentIds
        )->where(
            'status',
            'rejected'
        )->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard supervisor berhasil diambil.',
            'data' => [
                'supervisor' => $supervisor->load([
                    'user',
                    'company',
                ]),

                'total_students' => $totalStudents,

                'total_attendances' => $totalAttendances,

                'pending_attendances' => $pendingAttendances,

                'approved_attendances' => $approvedAttendances,

                'rejected_attendances' => $rejectedAttendances,
            ],
        ], 200);
    }
}