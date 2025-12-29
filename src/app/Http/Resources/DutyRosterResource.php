<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DutyRosterResource extends JsonResource
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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'duty_type' => $this->dutyType->name ?? 'Unknown',
            'soldier' => $this->soldier ? ($this->soldier->last_name . ' ' . $this->soldier->first_name) : 'Unknown',
        ];
    }
}
