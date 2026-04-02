<?php

use App\Models\Category;
use App\Actions\Categories\GetCategoryTreeAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Traits\InteractsWithElasticsearch;

/** @var \Tests\TestCase|\Tests\Traits\InteractsWithElasticsearch $this */

uses(RefreshDatabase::class, InteractsWithElasticsearch::class);

beforeEach(function () {
    $this->setUpElasticsearch();
});

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
    
    $childFromAction = $parentFromAction->children->firstWhere('title', 'Смартфоны');

    expect($parentFromAction->depth)->toBe(0)
        ->and($childFromAction->depth)->toBe(1);
});
