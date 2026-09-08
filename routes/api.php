<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SupervisorController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TaskController;


/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/logout', [AuthController::class, 'logout']);


    /*
    |--------------------------------------------------------------------------
    | SISWA
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:siswa')->group(function () {

        // Dashboard siswa
        Route::get(
            '/dashboard/student',
            [DashboardController::class, 'student']
        );

        // Melihat absensi milik sendiri
        Route::get(
            '/student/attendances',
            [AttendanceController::class, 'myAttendances']
        );

        // Membuat absensi
        Route::post(
            '/attendances',
            [AttendanceController::class, 'store']
        );


        /*
        |--------------------------------------------------------------------------
        | TASK / PROJECT SISWA
        |--------------------------------------------------------------------------
        */

        // Melihat tugas milik sendiri
        Route::get(
            '/student/tasks',
            [TaskController::class, 'myTasks']
        );

        // Mengirim tugas
        Route::post(
            '/tasks',
            [TaskController::class, 'store']
        );

        // Melihat detail tugas
        Route::get(
            '/tasks/{task}',
            [TaskController::class, 'show']
        );

        // Mengubah tugas
        Route::put(
            '/tasks/{task}',
            [TaskController::class, 'update']
        );

        // Menghapus tugas
        Route::delete(
            '/tasks/{task}',
            [TaskController::class, 'destroy']
        );
    });


    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        // Dashboard admin
        Route::get(
            '/dashboard/admin',
            [DashboardController::class, 'admin']
        );


        /*
        |--------------------------------------------------------------------------
        | STUDENTS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/students',
            [StudentController::class, 'index']
        );

        Route::get(
            '/students/{student}',
            [StudentController::class, 'show']
        );

        Route::post(
            '/students',
            [StudentController::class, 'store']
        );

        Route::put(
            '/students/{student}',
            [StudentController::class, 'update']
        );

        Route::delete(
            '/students/{student}',
            [StudentController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | COMPANIES
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/companies',
            [CompanyController::class, 'index']
        );

        Route::get(
            '/companies/{company}',
            [CompanyController::class, 'show']
        );

        Route::post(
            '/companies',
            [CompanyController::class, 'store']
        );

        Route::put(
            '/companies/{company}',
            [CompanyController::class, 'update']
        );

        Route::delete(
            '/companies/{company}',
            [CompanyController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCES ADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/attendances',
            [AttendanceController::class, 'index']
        );

        Route::get(
            '/attendances/{attendance}',
            [AttendanceController::class, 'show']
        );

        Route::put(
            '/attendances/{attendance}',
            [AttendanceController::class, 'update']
        );

        Route::delete(
            '/attendances/{attendance}',
            [AttendanceController::class, 'destroy']
        );

        Route::put(
            '/attendances/{attendance}/verify',
            [AttendanceController::class, 'verify']
        );


        /*
        |--------------------------------------------------------------------------
        | SUPERVISORS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/supervisors',
            [SupervisorController::class, 'index']
        );

        Route::get(
            '/supervisors/{supervisor}',
            [SupervisorController::class, 'show']
        );

        Route::post(
            '/supervisors',
            [SupervisorController::class, 'store']
        );

        Route::put(
            '/supervisors/{supervisor}',
            [SupervisorController::class, 'update']
        );

        Route::delete(
            '/supervisors/{supervisor}',
            [SupervisorController::class, 'destroy']
        );


        /*
        |--------------------------------------------------------------------------
        | TASKS ADMIN
        |--------------------------------------------------------------------------
        */

        // Admin melihat semua tugas
        Route::get(
            '/tasks',
            [TaskController::class, 'index']
        );
    });


    /*
    |--------------------------------------------------------------------------
    | SUPERVISOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:supervisor')->group(function () {

        // Dashboard supervisor
        Route::get(
            '/dashboard/supervisor',
            [DashboardController::class, 'supervisor']
        );

        // Melihat siswa yang dibimbing
        Route::get(
            '/supervisor/students',
            [StudentController::class, 'myStudents']
        );

        // Melihat absensi siswa yang dibimbing
        Route::get(
            '/supervisor/attendances',
            [AttendanceController::class, 'supervisorAttendances']
        );

        // Verifikasi absensi
        Route::put(
            '/supervisor/attendances/{attendance}/verify',
            [AttendanceController::class, 'verify']
        );


        /*
        |--------------------------------------------------------------------------
        | TASKS SUPERVISOR
        |--------------------------------------------------------------------------
        */

        // Melihat tugas siswa
        Route::get(
            '/supervisor/tasks',
            [TaskController::class, 'index']
        );

        // Verifikasi tugas
        Route::put(
            '/supervisor/tasks/{task}/verify',
            [TaskController::class, 'verify']
        );
    });
});