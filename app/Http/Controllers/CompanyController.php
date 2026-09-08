<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Menampilkan semua data perusahaan (Bisa diakses publik/bebas)
    public function index()
    {
        $companies = Company::all();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data perusahaan',
            'data' => $companies
        ], 200);
    }

    // Menyimpan data perusahaan baru (Harus login)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ], [
            'name.required' => 'Nama perusahaan wajib diisi.',
            'address.required' => 'Alamat perusahaan wajib diisi.',
        ]);

        $company = Company::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil ditambahkan',
            'data' => $company
        ], 201);
    }

    // Menampilkan detail satu perusahaan
    public function show(Company $company)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data perusahaan',
            'data' => $company
        ], 200);
    }

    // Mengubah data perusahaan
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $company->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil diperbarui',
            'data' => $company
        ], 200);
    }

    // Menghapus data perusahaan
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data perusahaan berhasil dihapus'
        ], 200);
    }
}