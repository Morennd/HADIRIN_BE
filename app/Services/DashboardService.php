<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\User;

class DashboardService
{
    /**
     * Dashboard Admin
     */
    public function adminDashboard(): array
    {
        return [
            'total_students' => Student::count(),

            'total_supervisors' => Supervisor::count(),

            'total_companies' => Company::count(),

            'total_attendances' => Attendance::count(),

            'today_attendances' => Attendance::whereDate(
                'attendance_date',
                now()->toDateString()
            )->count(),

            'this_month_attendances' => Attendance::whereMonth(
                'attendance_date',
                now()->month
            )->whereYear(
                'attendance_date',
                now()->year
            )->count(),

            'recent_attendances' => Attendance::with([
                'student.user',
                'student.company'
            ])
            ->latest()
            ->take(5)
            ->get(),
        ];
    }

    /**
     * Dashboard Student
     */
    public function studentDashboard(User $user): array
    {
        $student = Student::with('company')
            ->where('user_id', $user->id)
            ->firstOrFail();

        return [

            'student' => $student,

            'company' => $student->company,

            'attendance_today' => Attendance::where(
                'student_id',
                $student->id
            )
            ->whereDate(
                'attendance_date',
                now()->toDateString()
            )
            ->first(),

            'total_attendances' => Attendance::where(
                'student_id',
                $student->id
            )->count(),

            'recent_attendances' => Attendance::where(
                'student_id',
                $student->id
            )
            ->latest()
            ->take(5)
            ->get(),

        ];
    }

    /**
     * Dashboard Supervisor
     */
    public function supervisorDashboard(User $user): array
    {
        $supervisor = Supervisor::with('company')
            ->where('user_id', $user->id)
            ->firstOrFail();

        $students = Student::with('user')
            ->where('company_id', $supervisor->company_id)
            ->get();

        return [

            'company' => $supervisor->company,

            'total_students' => $students->count(),

            'students' => $students,

            'today_attendances' => Attendance::whereDate(
                'attendance_date',
                now()->toDateString()
            )
            ->whereHas('student', function ($query) use ($supervisor) {
                $query->where('company_id', $supervisor->company_id);
            })
            ->count(),

        ];
    }
}