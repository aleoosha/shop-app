<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, NodeTrait, SoftDeletes;

    protected $touches = ['products'];

    protected $fillable = ['title', 'slug', 'parent_id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::tags(['categories'])->flush());
        static::deleted(fn () => Cache::tags(['categories'])->flush());
    }
}
