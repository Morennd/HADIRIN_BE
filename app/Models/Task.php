<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'student_id',
        'title',
        'description',
        'url',
        'file',
        'status',
        'supervisor_note',
    ];

    /**
     * Relasi ke siswa
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}