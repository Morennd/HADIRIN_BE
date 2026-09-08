<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentService
{
    /**
     * Menampilkan semua siswa.
     */
    public function index()
    {
        return [
            'message' => 'Data siswa berhasil diambil',
            'data' => Student::with(['user', 'company'])->get(),
        ];
    }

    /**
     * Menambahkan siswa baru.
     */
    public function store(array $data)
    {
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'siswa',
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'company_id' => $data['company_id'],
                'nis' => $data['nis'],
                'phone' => $data['phone'] ?? null,
                'major' => $data['major'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Siswa berhasil ditambahkan',
                'data' => $student->load(['user', 'company']),
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal menambahkan siswa',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Detail siswa.
     */
    public function show(Student $student)
    {
        return [
            'message' => 'Detail siswa',
            'data' => $student->load(['user', 'company']),
        ];
    }

    /**
     * Update siswa.
     */
    public function update(array $data, Student $student)
    {
        DB::beginTransaction();

        try {

            $student->user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            $student->update([
                'company_id' => $data['company_id'],
                'nis' => $data['nis'],
                'phone' => $data['phone'] ?? null,
                'major' => $data['major'],
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Data siswa berhasil diperbarui',
                'data' => $student->load(['user', 'company']),
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Gagal memperbarui siswa',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Menampilkan siswa berdasarkan perusahaan supervisor.
     */
    public function getByCompany(int $companyId)
    {
        return Student::with(['user', 'company'])
            ->where('company_id', $companyId)
            ->get();
    }

    /**
     * Hapus siswa.
     */
    public function destroy(Student $student)
    {
        $student->user()->delete();

        return [
            'success' => true,
            'message' => 'Siswa berhasil dihapus',
        ];
    }
}