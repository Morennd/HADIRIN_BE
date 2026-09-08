<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'nis',
        'phone',
        'major',
        'start_date',
        'end_date',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Company.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relasi ke Attendance.
     * Satu siswa memiliki banyak data absensi.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}