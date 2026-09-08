<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    /**
     * Menampilkan semua absensi.
     */
    public function getAll()
    {
        return Attendance::with([
            'student.user',
            'student.company',
            'verifier.user',
        ])
        ->latest()
        ->get();
    }


    /**
     * Membuat absensi siswa.
     *
     * student_id otomatis berdasarkan user yang login.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $student = $user->student;

        if (!$student) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akun siswa belum memiliki data student.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALISASI WAKTU
        |--------------------------------------------------------------------------
        |
        | Input yang diperbolehkan:
        |
        | 09:00
        | 09:00:00
        | 09.00
        | 09.00.00
        |
        | Semua akan disimpan sebagai:
        |
        | 09:00:00
        |
        */

        $checkIn = $this->normalizeTime(
            $request->input('check_in')
        );

        $checkOut = $this->normalizeTime(
            $request->input('check_out')
        );


        /*
        |--------------------------------------------------------------------------
        | BUAT ABSENSI
        |--------------------------------------------------------------------------
        */

        $attendance = Attendance::create([
            'student_id' => $student->id,

            'attendance_date' => $request->input(
                'attendance_date'
            ),

            'check_in' => $checkIn,

            'check_out' => $checkOut,

            'notes' => $request->input('notes'),

            'documentation' => $request->hasFile('documentation')
                ? $request->file('documentation')
                    ->store('attendance', 'public')
                : null,

            'status' => 'pending',

            'verified_by' => null,

            'verified_at' => null,

            'verification_note' => null,
        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD RELATION
        |--------------------------------------------------------------------------
        */

        return $attendance->load([
            'student.user',
            'student.company',
            'verifier.user',
        ]);
    }


    /**
     * Mengubah absensi.
     */
    public function update(
        Request $request,
        Attendance $attendance
    ) {

        /*
        |--------------------------------------------------------------------------
        | NORMALISASI WAKTU
        |--------------------------------------------------------------------------
        */

        $checkIn = $this->normalizeTime(
            $request->input('check_in')
        );

        $checkOut = $this->normalizeTime(
            $request->input('check_out')
        );


        /*
        |--------------------------------------------------------------------------
        | DATA UPDATE
        |--------------------------------------------------------------------------
        */

        $data = [
            'attendance_date' => $request->input(
                'attendance_date'
            ),

            'check_in' => $checkIn,

            'check_out' => $checkOut,

            'notes' => $request->input('notes'),
        ];


        /*
        |--------------------------------------------------------------------------
        | UPLOAD DOKUMENTASI BARU
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('documentation')) {

            /*
            |--------------------------------------------------------------------------
            | Hapus dokumentasi lama.
            |--------------------------------------------------------------------------
            */

            if (
                $attendance->documentation &&
                Storage::disk('public')->exists(
                    $attendance->documentation
                )
            ) {
                Storage::disk('public')->delete(
                    $attendance->documentation
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan dokumentasi baru.
            |--------------------------------------------------------------------------
            */

            $data['documentation'] =
                $request->file('documentation')
                    ->store('attendance', 'public');
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE DATABASE
        |--------------------------------------------------------------------------
        */

        $attendance->update($data);


        /*
        |--------------------------------------------------------------------------
        | LOAD RELATION
        |--------------------------------------------------------------------------
        */

        return $attendance->load([
            'student.user',
            'student.company',
            'verifier.user',
        ]);
    }


    /**
     * Menghapus absensi.
     */
    public function destroy(Attendance $attendance)
    {
        /*
        |--------------------------------------------------------------------------
        | HAPUS FILE DOKUMENTASI
        |--------------------------------------------------------------------------
        */

        if (
            $attendance->documentation &&
            Storage::disk('public')->exists(
                $attendance->documentation
            )
        ) {
            Storage::disk('public')->delete(
                $attendance->documentation
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HAPUS DATA
        |--------------------------------------------------------------------------
        */

        return $attendance->delete();
    }


    /**
     * Mengambil absensi siswa yang sedang login.
     */
    public function getMyAttendances()
    {
        $user = Auth::user();

        $student = $user->student;

        if (!$student) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akun siswa belum memiliki data student.',
            ], 422));
        }


        return Attendance::with([
            'student.user',
            'student.company',
            'verifier.user',
        ])
        ->where('student_id', $student->id)
        ->latest()
        ->get();
    }


    /**
     * Mengambil absensi siswa yang dibimbing supervisor.
     */
    public function getSupervisorAttendances()
    {
        $user = Auth::user();

        $supervisor = $user->supervisor;

        if (!$supervisor) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akun supervisor belum memiliki data supervisor.',
            ], 422));
        }


        return Attendance::with([
            'student.user',
            'student.company',
            'verifier.user',
        ])
        ->whereHas('student', function ($query) use ($supervisor) {
            $query->where(
                'company_id',
                $supervisor->company_id
            );
        })
        ->latest()
        ->get();
    }


    /**
     * Verifikasi absensi.
     */
    public function verify(
        Attendance $attendance,
        Request $request
    ) {

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA SUPERVISOR
        |--------------------------------------------------------------------------
        */

        $supervisor = $user->supervisor;

        if (!$supervisor) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akun supervisor belum memiliki data supervisor.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE VERIFIKASI
        |--------------------------------------------------------------------------
        |
        | verified_by menggunakan ID dari tabel supervisors,
        | bukan ID dari tabel users.
        |
        */

        $attendance->update([
            'status' => $request->input('status'),

            'verified_by' => $supervisor->id,

            'verified_at' => now(),

            'verification_note' =>
                $request->input('verification_note'),
        ]);


        /*
        |--------------------------------------------------------------------------
        | LOAD RELATION
        |--------------------------------------------------------------------------
        */

        return $attendance->load([
            'student.user',
            'student.company',
            'verifier.user',
        ]);
    }


    /**
     * Normalisasi format waktu.
     *
     * Input yang diterima:
     *
     * 09:00
     * 09:00:00
     * 09.00
     * 09.00.00
     *
     * Output:
     *
     * 09:00:00
     */
    private function normalizeTime(?string $time): ?string
    {
        if ($time === null || trim($time) === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Bersihkan spasi
        |--------------------------------------------------------------------------
        */

        $time = trim($time);


        /*
        |--------------------------------------------------------------------------
        | Ubah titik menjadi titik dua
        |--------------------------------------------------------------------------
        */

        $time = str_replace('.', ':', $time);


        /*
        |--------------------------------------------------------------------------
        | Pecah jam, menit, detik
        |--------------------------------------------------------------------------
        */

        $parts = explode(':', $time);


        /*
        |--------------------------------------------------------------------------
        | Hanya HH:mm
        |--------------------------------------------------------------------------
        */

        if (count($parts) === 2) {

            $hour = $parts[0];

            $minute = $parts[1];

            $second = '00';
        }


        /*
        |--------------------------------------------------------------------------
        | HH:mm:ss
        |--------------------------------------------------------------------------
        */

        elseif (count($parts) === 3) {

            $hour = $parts[0];

            $minute = $parts[1];

            $second = $parts[2];
        }


        /*
        |--------------------------------------------------------------------------
        | Format tidak dikenal
        |--------------------------------------------------------------------------
        */

        else {

            abort(response()->json([
                'success' => false,
                'message' => 'Format waktu tidak valid. Gunakan HH:MM atau HH:MM:SS.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan angka
        |--------------------------------------------------------------------------
        */

        if (
            !ctype_digit($hour) ||
            !ctype_digit($minute) ||
            !ctype_digit($second)
        ) {

            abort(response()->json([
                'success' => false,
                'message' => 'Format waktu harus berupa angka.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | Konversi ke integer
        |--------------------------------------------------------------------------
        */

        $hour = (int) $hour;

        $minute = (int) $minute;

        $second = (int) $second;


        /*
        |--------------------------------------------------------------------------
        | Validasi JAM
        |--------------------------------------------------------------------------
        */

        if ($hour < 0 || $hour > 23) {

            abort(response()->json([
                'success' => false,
                'message' => 'Jam harus antara 00 sampai 23.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi MENIT
        |--------------------------------------------------------------------------
        */

        if ($minute < 0 || $minute > 59) {

            abort(response()->json([
                'success' => false,
                'message' => 'Menit harus antara 00 sampai 59.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi DETIK
        |--------------------------------------------------------------------------
        */

        if ($second < 0 || $second > 59) {

            abort(response()->json([
                'success' => false,
                'message' => 'Detik harus antara 00 sampai 59.',
            ], 422));
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT FINAL
        |--------------------------------------------------------------------------
        |
        | Contoh:
        |
        | 9, 0, 0
        |
        | menjadi:
        |
        | 09:00:00
        |
        */

        return sprintf(
            '%02d:%02d:%02d',
            $hour,
            $minute,
            $second
        );
    }
}