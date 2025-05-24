<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EntryFormCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */    public function toArray($request): array
    {
        // The main 'data' key will be automatically wrapped by Laravel,
        // and pagination links/meta will be included by default.
        // We just need to ensure our collection items are transformed if needed.
        // If EntryFormResource is used, it will transform each item.
        // If not, $this->collection will be returned as is.
        return parent::toArray($request);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta_custom' => [ // Renamed to avoid conflict with Laravel's 'meta'
                'resource_type' => 'EntryForms',
                // These counts are for the current page's collection if that's intended.
                // If global counts are needed, they should be calculated separately.
                'current_page_pending_count' => $this->collection->where('status', 'pending')->count(),
                'current_page_validated_count' => $this->collection->where('status', 'validated')->count(),
                'current_page_canceled_count' => $this->collection->where('status', 'canceled')->count(),
            ],
        ];
    }
}
