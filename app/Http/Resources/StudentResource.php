<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => $this->id,
            'nis' => $this->nis,
            
            // Dikelompokkan ke dalam satu blok agar terlihat rapi
            'biodata' => [
                'name'  => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'major' => $this->major,
            ],
            
            // Data perusahaan tempat magang
            'company' => $this->whenLoaded('company', function () {
                return [
                    'id'      => $this->company->id,
                    'name'    => $this->company->name,
                    'address' => $this->company->address,
                ];
            }),
            
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}