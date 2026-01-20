<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'price' => 'nullable|integer|min:1',
            'category_id' => 'nullable|integer|exists:categories,id',
        ];
    }
}
