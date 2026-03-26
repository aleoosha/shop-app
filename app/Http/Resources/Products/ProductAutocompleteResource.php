<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\Models\Product $resource
 */
class ProductAutocompleteResource extends JsonResource
{
    /**
     * Преобразуем модель в массив для API.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->resource->id,
            'title' => $this->resource->title,
            'url'   => route('products.show', $this->resource->id), 
        ];
    }
}
