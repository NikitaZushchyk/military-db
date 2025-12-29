<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'soldier_count' => $this['soldier_count'],
            'free_weapons_count' => $this['free_weapons_count'],
            'issued_weapons_count' => $this['issued_weapons_count'],
            'in_duty_count' => $this['in_duty_count'],
        ];
    }
}
