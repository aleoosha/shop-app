<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Elastic\ScoutDriverPlus\Searchable;

class Product extends Model {
    use Searchable, HasFactory;
    
    protected $fillable = ['title', 'price', 'description'];

    public function toSearchableArray(): array
    {
        return [
            'id'    => (int) $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => (float) $this->price,
            // Сюда же можно добавить категорию, если она есть
            // 'category' => $this->category?->name, 
        ];
    }
}
