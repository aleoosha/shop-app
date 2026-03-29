<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Products;

use App\Http\Controllers\Controller;
use App\Actions\Products\SearchProductsAction;
use App\Actions\Categories\GetCategoryTreeAction;
use App\DTOs\ProductSearchDTO;
use Illuminate\View\View;

class SearchProductsController extends Controller
{
    public function __invoke(
        ProductSearchDTO $searchDto,
        SearchProductsAction $action,
        GetCategoryTreeAction $categoriesAction
    ): View {
        $products = $action->handle($searchDto);

        $categories = $categoriesAction->handle();

        return view('products.index', compact('products', 'categories'));
    }
}
