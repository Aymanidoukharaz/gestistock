<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ExitFormCollection extends ResourceCollection
{
        public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'resource_type' => 'ExitForms',
                'total_count' => $this->count(),
                'pending_count' => $this->collection->where('status', 'pending')->count(),
                'validated_count' => $this->collection->where('status', 'validated')->count(),
                'canceled_count' => $this->collection->where('status', 'canceled')->count(),
            ],
        ];
    }
}
