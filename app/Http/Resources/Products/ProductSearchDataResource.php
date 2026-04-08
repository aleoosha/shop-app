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
            'description' => $this->when($request->routeIs('products.show'), $product->description),
            'price' => [
                'decimal' => $product->price->toFloat(),
                'formatted' => $product->price->formatted(),
                'currency' => $product->price->currency,
            ],
            'category' => [
                'id' => $product->category_id,
                'title' => $product->category?->title,
            ],
            'specs' => [
                'brand' => $product->brand,
                'color' => $product->color,
                'condition' => $product->condition,
                'country' => $product->country,
            ],
            'created_at' => $product->created_at?->toDateTimeString(),
        ];
    }
}
