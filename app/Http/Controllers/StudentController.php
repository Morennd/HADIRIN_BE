<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Resources\StudentResource;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('company')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar data siswa berhasil diambil',
            'data'    => StudentResource::collection($students)
        ], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'nis'        => 'required|string|unique:students,nis',
            'name'       => 'required|string',
            'email'      => 'required|email|unique:students,email',
            'phone'      => 'required|numeric',
            'major'      => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
        ];

        $messages = [
            'nis.required'    => 'NIS wajib diisi.',
            'nis.unique'      => 'NIS ini sudah terdaftar, silakan gunakan NIS lain.',
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email sudah digunakan.',
            'phone.required'  => 'Nomor telepon wajib diisi.',
            'phone.numeric'   => 'Nomor telepon hanya boleh berisi angka.',
            'major.required'  => 'Jurusan wajib diisi.',
        ];

        $validated = $request->validate($rules, $messages);

        $student = Student::create($validated);
        $student->load('company');

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil ditambahkan',
            'data'    => new StudentResource($student)
        ], 201);
    }

    public function show(Student $student)
    {
        $student->load('company');

        return response()->json([
            'success' => true,
            'message' => 'Detail data siswa ditemukan',
            'data'    => new StudentResource($student)
        ], 200);
    }

    public function update(Request $request, Student $student)
    {
        $rules = [
            'nis'        => 'sometimes|required|string|unique:students,nis,' . $student->id,
            'name'       => 'sometimes|required|string',
            'email'      => 'sometimes|required|email|unique:students,email,' . $student->id,
            'phone'      => 'sometimes|required|numeric',
            'major'      => 'sometimes|required|string',
            'company_id' => 'nullable|exists:companies,id',
        ];

        $messages = [
            'nis.required'    => 'NIS wajib diisi.',
            'nis.unique'      => 'NIS ini sudah terdaftar, silakan gunakan NIS lain.',
            'name.required'   => 'Nama wajib diisi.',
            'email.required'  => 'Email wajib diisi.',
            'email.email'     => 'Format email tidak valid.',
            'email.unique'    => 'Email sudah digunakan.',
            'phone.required'  => 'Nomor telepon wajib diisi.',
            'phone.numeric'   => 'Nomor telepon hanya boleh berisi angka.',
            'major.required'  => 'Jurusan wajib diisi.',
        ];

        $validated = $request->validate($rules, $messages);

        $student->update($validated);
        $student->load('company');

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui',
            'data'    => new StudentResource($student)
        ], 200);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil dihapus'
        ], 200);
    }
}