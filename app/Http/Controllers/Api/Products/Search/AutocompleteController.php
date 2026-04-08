<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Products\Search;

use App\Http\Controllers\Controller;
use App\Actions\Products\GetAutocompleteSuggestionsAction;
use Illuminate\Http\Request;
use App\Http\Resources\Products\ProductAutocompleteResource;

class AutocompleteController extends Controller
{
    public function __invoke(Request $request, GetAutocompleteSuggestionsAction $action)
    {
        $suggestions = $action->handle($request->get('q'));

        return ProductAutocompleteResource::collection($suggestions);
    }
}
