<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'check_in',
        'check_out',
        'notes',
        'documentation',
        'status',
        'verified_by',
        'verified_at',
        'verification_note',
    ];

    /**
     * Relasi ke Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relasi ke Supervisor yang melakukan verifikasi
     */
    public function verifier()
    {
        return $this->belongsTo(Supervisor::class, 'verified_by');
    }
}