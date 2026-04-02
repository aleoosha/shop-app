<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSearchDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->resource->model();

        return [
            'id' => $product->id,
            'title' => $product->title,
            'description' => $product->description,
            'price' => [
                'amount'    => $product->price->amount,
                'decimal'   => $product->price->toFloat(),
                'formatted' => $product->price->formatted(),
            ],
            'category' => $product->category?->title,
        ];
    }
}
