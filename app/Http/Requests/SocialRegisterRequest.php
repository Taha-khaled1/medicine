<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:100', 'regex:/^[\p{Arabic}\p{L}\s]+$/u'],
            'image' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'indisposable'],
            'password' => 'required|string|min:8',
            'login_type' => 'required|string',
            'fcm' => 'nullable|string',
        ];
    }
}
