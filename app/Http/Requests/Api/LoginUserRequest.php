<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string|min:6',
        ];
    }
}
