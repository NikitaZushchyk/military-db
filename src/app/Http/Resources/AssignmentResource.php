<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
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
            'issue_date' => $this->issue_date,
            'return_date' => $this->return_date,
            'is_active' => is_null($this->return_date),

            'item' => [
                'id' => $this->item->id,
                'serial_number' => $this->item->serial_number,
                'name' => $this->item->type->name ?? 'Unknown',
            ],
        ];
    }
}
