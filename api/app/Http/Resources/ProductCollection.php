<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
        public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'resource_type' => 'Products',
                'total_count' => $this->count(),
                'low_stock_count' => $this->collection->filter(function($product) {
                    return $product->quantity < $product->min_stock;
                })->count(),
            ],
        ];
    }
}
