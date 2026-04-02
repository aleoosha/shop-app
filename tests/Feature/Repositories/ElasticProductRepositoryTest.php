<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Repositories\Elasticsearch\ProductRepository;
use App\DTOs\Search\ProductSearchDTO;
use Illuminate\Pagination\LengthAwarePaginator;

test('repository returns paginator on search', function () {
    $dto = ProductSearchDTO::from([
        'q' => 'test',
        'min_price' => 100,
        'max_price' => 500,
    ]);

    $repository = new ProductRepository();
    
    $results = $repository->search($dto);

    expect($results)->toBeInstanceOf(LengthAwarePaginator::class);
});
