<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StockMovementCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'resource_type' => 'StockMovements',
                'total_count' => $this->count(),
                'entries_count' => $this->collection->where('type', 'entry')->count(),
                'exits_count' => $this->collection->where('type', 'exit')->count(),
            ],
        ];
    }
}
