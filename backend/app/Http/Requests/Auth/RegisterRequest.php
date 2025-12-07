<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::defaults()
            ],
        ];
    }

    public function getData(): array
    {
        return $this->merge([
            'password' => Hash::make($this->input('password')),
        ])->only([
            'name',
            'email',
            'password',
        ]);
    }
}

