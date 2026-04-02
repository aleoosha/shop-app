<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use App\Infrastructure\Elasticsearch\Indices\ProductIndexConfig;
use Elastic\ScoutDriverPlus\Searchable;
use App\Casts\MoneyCast;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property \App\ValueObjects\Money $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model {
    use Searchable, HasFactory;

    protected $fillable = ['title', 'price', 'description'];

    public function getSearchableConfig(): array
    {
        return app(ProductIndexConfig::class)->getConfig();
    }

    public function toSearchableArray(): array
    {
        $this->loadMissing('category');

        return [
            'id'          => (int) $this->id,
            'title'       => (string) $this->title,
            'description' => (string) $this->description,
            'price'       => (int) ($this->price->amount ?? 0),
            'category_id' => (int) $this->category_id,
            'category'    => (string) ($this->category?->title ?? 'Без категории'),
            'created_at'  => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function searchableAs(): string
    {
        return app(ProductIndexConfig::class)->getName();
    }

    protected function casts(): array
    {
        return [
            'price' => MoneyCast::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
