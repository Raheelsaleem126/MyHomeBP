<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicalDataResource extends JsonResource
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
            'patient_id' => $this->patient_id,
            'height' => $this->height,
            'weight' => $this->weight,
            'bmi' => $this->bmi,
            'blood_type' => $this->blood_type,
            'smoking_status' => $this->smoking_status,
            'alcohol_consumption' => $this->alcohol_consumption,
            'exercise_frequency' => $this->exercise_frequency,
            'dietary_restrictions' => $this->dietary_restrictions,
            'family_history' => $this->family_history,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'patient' => new PatientResource($this->whenLoaded('patient')),
        ];
    }
}
