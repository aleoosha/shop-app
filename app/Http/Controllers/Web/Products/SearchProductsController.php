<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Products;

use App\Http\Controllers\Controller;
use App\Actions\Products\SearchProductsAction;
use App\DTOs\ProductSearchDTO;
use App\Http\Requests\ProductSearchRequest;
use Illuminate\View\View;

class SearchProductsController extends Controller
{
    public function __invoke(
        ProductSearchRequest $request,
        SearchProductsAction $action
    ): View {
        $products = $action->handle(
            ProductSearchDTO::fromRequest($request)
        );

        return view('products.index', compact('products'));
    }
}
