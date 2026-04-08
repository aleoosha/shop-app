<?php

namespace App\Casts;

use App\ValueObjects\ProductSpecs;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class ProductSpecsCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?ProductSpecs
    {
        if (! $value) {
            return null;
        }

        $data = json_decode($value, true);

        return ProductSpecs::fromArray(is_array($data) ? $data : []);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductSpecs) {
            return json_encode($value->toArray());
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        throw new InvalidArgumentException('The given value is not a ProductSpecs instance or array.');
    }
}
