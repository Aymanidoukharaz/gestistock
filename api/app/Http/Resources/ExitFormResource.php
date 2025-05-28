<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExitFormResource extends JsonResource
{
        public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'comments' => $this->notes ?? null,
            'destination' => $this->destination,
            'reason' => $this->reason ?? null,
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => ExitItemResource::collection($this->whenLoaded('exitItems')),
            'items_count' => $this->whenCounted('exitItems'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
