<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    /**
     * Menampilkan semua data perusahaan.
     */
    public function index()
    {
        return [
            'success' => true,
            'message' => 'Data perusahaan berhasil diambil.',
            'data' => Company::all(),
        ];
    }

    /**
     * Menyimpan data perusahaan baru.
     */
    public function store(array $data)
    {
        $company = Company::create($data);

        return [
            'success' => true,
            'message' => 'Perusahaan berhasil ditambahkan.',
            'data' => $company,
        ];
    }

    /**
     * Menampilkan detail perusahaan.
     */
    public function show(Company $company)
    {
        return [
            'success' => true,
            'message' => 'Detail perusahaan.',
            'data' => $company,
        ];
    }

    /**
     * Mengubah data perusahaan.
     */
    public function update(array $data, Company $company)
    {
        $company->update($data);

        return [
            'success' => true,
            'message' => 'Perusahaan berhasil diperbarui.',
            'data' => $company,
        ];
    }

    /**
     * Menghapus perusahaan.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return [
            'success' => true,
            'message' => 'Perusahaan berhasil dihapus.',
        ];
    }
}