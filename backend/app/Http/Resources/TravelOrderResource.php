<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'requester_name' => $this->requester_name,
            'status'         => $this->status,
            'destination'    => $this->destination,
            'departure_date' => $this->departure_date,
            'return_date'    => $this->return_date,
            'user_id'        => $this->user_id,
            'price'          => $this->price,
            'hosting'        => $this->hosting,
            'transportation' => $this->transportation,
            'description'    => $this->description,
            'created_at'     => $this->created_at,
        ];
    }
}
