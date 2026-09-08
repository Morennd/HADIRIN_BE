<?php

namespace App\Services;

use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SupervisorService
{
    /**
     * Menampilkan semua supervisor.
     */
    public function getAll()
    {
        return Supervisor::with(['user', 'company'])->get();
    }

    /**
     * Menambahkan supervisor baru.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'supervisor',
            ]);

            $supervisor = Supervisor::create([
                'user_id' => $user->id,
                'company_id' => $request->company_id,
                'phone' => $request->phone,
                'position' => $request->position,
                'type' => $request->type,
            ]);

            DB::commit();

            return $supervisor->load(['user', 'company']);

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Menampilkan detail supervisor.
     */
    public function show(Supervisor $supervisor)
    {
        return $supervisor->load(['user', 'company']);
    }

    /**
     * Mengubah data supervisor.
     */
    public function update(Request $request, Supervisor $supervisor)
    {
        DB::beginTransaction();

        try {

            $supervisor->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $supervisor->update([
                'company_id' => $request->company_id,
                'phone' => $request->phone,
                'position' => $request->position,
                'type' => $request->type,
            ]);

            DB::commit();

            return $supervisor->load(['user', 'company']);

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Menghapus supervisor.
     */
    public function destroy(Supervisor $supervisor)
    {
        DB::beginTransaction();

        try {

            $supervisor->delete();

            $supervisor->user()->delete();

            DB::commit();

            return true;

        } catch (\Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }
}