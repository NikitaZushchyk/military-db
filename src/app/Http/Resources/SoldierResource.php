<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoldierResource extends JsonResource
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
            'full_name' => $this->last_name . ' ' . $this->first_name,
            'status' => $this->status,

            'rank' => $this->rank ? $this->rank->name : null,
            'unit' => $this->unit ? $this->unit->name : null,

            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),

            'duties' => DutyRosterResource::collection($this->whenLoaded('duties')),
        ];
    }
}
