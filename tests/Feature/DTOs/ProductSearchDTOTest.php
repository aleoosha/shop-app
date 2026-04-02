<?php

declare(strict_types=1);

use \App\DTOs\Search\ProductSearchDTO;

test('it creates dto from array with snake_case mapping', function () {
    $data = [
        'q' => 'nulla',
        'min_price' => 100.50,
        'max_price' => 2000,
        'sort' => 'desc',
        'category_id' => 5,
    ];
    
    $dto = ProductSearchDTO::from($data);

    expect($dto->query)->toBe('nulla')
        ->and($dto->minPrice)->toBe(100.50)
        ->and($dto->maxPrice)->toBe(2000.0)
        ->and($dto->categoryId)->toBe(5);
});

test('it uses default values when fields are missing', function () {
    $dto = ProductSearchDTO::from([]);

    expect($dto->sortField)->toBe('price')
        ->and($dto->sortOrder)->toBe('asc')
        ->and($dto->perPage)->toBe(15);
});

test('it maps title to title.keyword for elasticsearch', function () {
    $dto = ProductSearchDTO::from(['sort_field' => 'title']);

    expect($dto->getElasticSortField())->toBe('title.keyword');
});

test('it keeps created_at as is for elasticsearch', function () {
    $dto = ProductSearchDTO::from(['sort_field' => 'created_at']);

    expect($dto->getElasticSortField())->toBe('created_at');
});
