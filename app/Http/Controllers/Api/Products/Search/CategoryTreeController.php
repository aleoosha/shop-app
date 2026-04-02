<?php

namespace App\Http\Controllers\Api\Products\Search;

use App\Http\Controllers\Controller;
use App\Actions\Categories\GetCategoryTreeAction;
use App\Http\Resources\Categories\CategoryTreeResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryTreeController extends Controller
{
    public function __invoke(GetCategoryTreeAction $action): AnonymousResourceCollection
    {
        return CategoryTreeResource::collection($action->handle());
    }
}
