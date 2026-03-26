<?php

declare(strict_types=1);

test('it creates dto from array', function () {
    $data = [
        'q' => 'nulla',
        'min_price' => 100.50,
        'max_price' => 2000,
        'sort' => 'desc'
    ];
    
    $dto = \App\DTOs\ProductSearchDTO::fromArray($data);

    expect($dto->query)->toBe('nulla')
        ->and($dto->minPrice)->toBe(100.50)
        ->and($dto->maxPrice)->toBe(2000.0);
});
