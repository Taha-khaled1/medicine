<?php

namespace App\Http\Requests;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'indisposable'],
            'password' => 'required|string|min:8',
            'phone' => ['required', 'string', 'max:30', 'unique:users', new ValidPhoneNumber],
            'invitation_code' => 'nullable|string',
            'fcm' => 'nullable|string',
            'login_type' => 'required|string',
            'code' => 'required|string',
            'username' => 'required|string',
            'gander' => ['required', Rule::in(['male', 'female'])],
            'date' => 'required|string',
        ];
    }
}
