<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
{
    /**
     * Разрешаем всем пользователям выполнять этот запрос.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации для поиска и фильтров.
     */
    public function rules(): array
    {
        

        return [
            'q' => ['nullable', 'string', 'max:255'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'category_id' => ['nullable', 'integer'], 
            'sort' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    /**
     * Понятные сообщения об ошибках (опционально).
     */
    public function messages(): array
    {
        return [
            'max_price.gte' => 'Максимальная цена не может быть меньше минимальной.',
            'numeric' => 'Цена должна быть числом.',
        ];
    }
}
