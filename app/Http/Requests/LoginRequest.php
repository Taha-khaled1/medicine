<?php

namespace App\Http\Requests;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'max:30', 'exists:users,email', 'indisposable'],
            'password' => 'required|string|min:8',
            'fcm' => 'nullable|string',
        ];
    }
}
