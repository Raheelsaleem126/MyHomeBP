<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gmc_number' => $this->gmc_number,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'qualifications' => $this->qualifications,
            'years_of_experience' => $this->years_of_experience,
            'bio' => $this->bio,
            'profile_image' => $this->profile_image,
            'is_active' => $this->is_active,
            'is_available' => $this->is_available,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'specialities' => SpecialityResource::collection($this->whenLoaded('specialities')),
            'primary_speciality' => new SpecialityResource($this->whenLoaded('primarySpeciality')),
            'clinics' => ClinicResource::collection($this->whenLoaded('clinics')),
            'patients_count' => $this->when(isset($this->patients_count), $this->patients_count),
        ];
    }
}