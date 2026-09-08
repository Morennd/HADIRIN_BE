<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     *
     * Menampilkan semua tugas.
     */
    public function index(): JsonResponse
    {
        $tasks = Task::with([
            'student.user',
            'student.company',
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil diambil.',
            'data' => $tasks,
        ], 200);
    }

    /**
     * GET /api/student/tasks
     *
     * Menampilkan tugas milik siswa yang sedang login.
     */
    public function myTasks(): JsonResponse
    {
        $user = Auth::user();

        $student = $user->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $tasks = Task::where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data tugas saya berhasil diambil.',
            'data' => $tasks,
        ], 200);
    }

    /**
     * POST /api/tasks
     *
     * Siswa mengirim tugas.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if (!$request->filled('url') && !$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas harus berupa URL atau file PDF.',
            ], 422);
        }

        if ($request->filled('url') && $request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih salah satu: URL atau file PDF.',
            ], 422);
        }

        $user = Auth::user();

        $student = $user->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan.',
            ], 404);
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store('tasks', 'public');
        }

        $task = Task::create([
            'student_id' => $student->id,
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
            'file' => $filePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dikirim.',
            'data' => $task,
        ], 201);
    }

    /**
     * GET /api/tasks/{task}
     *
     * Detail tugas.
     */
    public function show(Task $task): JsonResponse
    {
        $task->load([
            'student.user',
            'student.company',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Detail tugas berhasil diambil.',
            'data' => $task,
        ], 200);
    }

    /**
     * PUT /api/tasks/{task}
     *
     * Update tugas.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($task->file) {
                Storage::disk('public')->delete($task->file);
            }

            $task->file = $request->file('file')
                ->store('tasks', 'public');

            $task->url = null;
        } elseif ($request->filled('url')) {
            if ($task->file) {
                Storage::disk('public')->delete($task->file);
            }

            $task->file = null;
            $task->url = $request->url;
        }

        if ($request->has('title')) {
            $task->title = $request->title;
        }

        if ($request->has('description')) {
            $task->description = $request->description;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui.',
            'data' => $task,
        ], 200);
    }

    /**
     * DELETE /api/tasks/{task}
     *
     * Hapus tugas.
     */
    public function destroy(Task $task): JsonResponse
    {
        if ($task->file) {
            Storage::disk('public')->delete($task->file);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.',
        ], 200);
    }

    /**
     * PUT /api/tasks/{task}/verify
     *
     * Verifikasi tugas oleh supervisor.
     */
    public function verify(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'supervisor_note' => 'nullable|string',
        ]);

        $task->update([
            'status' => $request->status,
            'supervisor_note' => $request->supervisor_note,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->status === 'approved'
                ? 'Tugas berhasil disetujui.'
                : 'Tugas berhasil ditolak.',
            'data' => $task->fresh(),
        ], 200);
    }
}