<?php

use App\Models\Category;
use App\Actions\Categories\GetCategoryTreeAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns categories with correct depth from action', function () {
    Cache::tags(['categories'])->flush();

    $parent = Category::factory()->create(['title' => 'Электроника']);
    
    Category::factory()->create([
        'title' => 'Смартфоны', 
        'parent_id' => $parent->id
    ]);

    Category::fixTree();

    $categories = app(GetCategoryTreeAction::class)->handle();

    $parentFromAction = $categories->firstWhere('title', 'Электроника');
    $childFromAction = $categories->firstWhere('title', 'Смартфоны');

    expect($parentFromAction->depth)->toBe(0)
        ->and($childFromAction->depth)->toBe(1);
});
