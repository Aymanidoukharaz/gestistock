<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntryFormResource extends JsonResource
{
        public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'comments' => $this->notes ?? null,
            'total' => $this->total,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => EntryItemResource::collection($this->whenLoaded('entryItems')),
            'items_count' => $this->whenCounted('entryItems'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
