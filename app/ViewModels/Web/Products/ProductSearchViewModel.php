<?php

declare(strict_types=1);

namespace App\ViewModels\Web\Products;

use Elastic\ScoutDriverPlus\Decorators\Hit;

readonly class ProductSearchViewModel
{
    public int $id;
    public string $title;
    public string $description;
    public string $price;

    public function __construct(Hit $hit)
    {
        $product = $hit->model();
        
        $this->id = (int) $product->id;
        $this->title = (string) $product->title;
        
        $this->description = $hit->highlight()?->snippets('description')?->first() 
                         ?? $product->description;

        $this->price = $product->price->formatted();
    }
}
