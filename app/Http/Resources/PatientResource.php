<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'surname' => $this->surname,
            'full_name' => $this->first_name . ' ' . $this->surname,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'address' => $this->address,
            'postcode' => $this->postcode,
            'mobile_phone' => $this->mobile_phone,
            'email' => $this->email,
            'nhs_number' => $this->nhs_number,
            'gender' => $this->gender,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'medical_conditions' => $this->medical_conditions,
            'medications' => $this->medications,
            'allergies' => $this->allergies,
            'terms_accepted' => $this->terms_accepted,
            'data_sharing_consent' => $this->data_sharing_consent,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'clinical_data' => ClinicalDataResource::collection($this->whenLoaded('clinicalData')),
            'blood_pressure_readings_count' => $this->when(isset($this->blood_pressure_readings_count), $this->blood_pressure_readings_count),
        ];
    }
}
