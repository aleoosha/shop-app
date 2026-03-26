<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Repositories\Elasticsearch\ProductRepository;
use App\DTOs\ProductSearchDTO;
use Illuminate\Pagination\LengthAwarePaginator;

test('repository returns paginator on search', function () {
    // В сеньор-коде мы часто используем моки для сложных драйверов,
    // но сейчас проверим хотя бы тип возвращаемого значения.
    
    $dto = new ProductSearchDTO(
        query: 'test',
        minPrice: 100,
        maxPrice: 500
    );

    $repository = new ProductRepository();
    
    // Выполняем поиск (Scout/Elastic драйвер должен быть в тестовом режиме)
    $results = $repository->search($dto);

    expect($results)->toBeInstanceOf(LengthAwarePaginator::class);
});
