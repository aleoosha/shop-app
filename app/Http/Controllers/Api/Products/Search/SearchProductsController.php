<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Products\Search;

use App\Http\Controllers\Controller;
use App\Actions\Products\SearchProductsAction;
use App\Http\Resources\Products\ProductSearchDataResource;
use App\DTOs\Search\ProductSearchDTO;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SearchProductsController extends Controller
{
    public function __invoke(
        ProductSearchDTO $searchDto,
        SearchProductsAction $action
    ): AnonymousResourceCollection {
        $products = $action->handle($searchDto);

        return ProductSearchDataResource::collection($products);
    }
}
