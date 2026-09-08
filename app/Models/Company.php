<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'supervisor_name',
    ];

    /**
     * Satu perusahaan memiliki banyak siswa.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Satu perusahaan memiliki banyak supervisor.
     */
    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }
}