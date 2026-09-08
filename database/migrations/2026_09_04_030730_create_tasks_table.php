<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // Siswa yang mengumpulkan tugas
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Judul tugas / project
            $table->string('title');

            // Deskripsi tugas
            $table->text('description')->nullable();

            // Link tugas jika dikumpulkan melalui URL
            $table->text('url')->nullable();

            // File tugas jika dikumpulkan dalam bentuk PDF
            $table->string('file')->nullable();

            // Status tugas
            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            // Catatan dari pembimbing
            $table->text('supervisor_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};