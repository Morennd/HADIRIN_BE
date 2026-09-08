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
        Schema::table('attendances', function (Blueprint $table) {

            $table->enum('status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending')->after('documentation');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('supervisors')
                ->nullOnDelete()
                ->after('status');

            $table->timestamp('verified_at')
                ->nullable()
                ->after('verified_by');

            $table->text('verification_note')
                ->nullable()
                ->after('verified_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {

            $table->dropForeign(['verified_by']);

            $table->dropColumn([
                'status',
                'verified_by',
                'verified_at',
                'verification_note',
            ]);

        });
    }
};