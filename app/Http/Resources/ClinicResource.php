<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'postcode' => $this->postcode,
            'phone' => $this->phone,
            'email' => $this->email,
            'type' => $this->type,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'doctors_count' => $this->when(isset($this->doctors_count), $this->doctors_count),
            'patients_count' => $this->when(isset($this->patients_count), $this->patients_count),
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'patients' => PatientResource::collection($this->whenLoaded('patients')),
        ];
    }
}