<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => ['min:8', 'confirmed'],
            'status' => ['in:active,inactive'],
            'user_type' => ['in:admin,citizen'],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'][] = 'required';
        } else {
            $rules['password'][] = 'nullable';
        }

        return $rules;
    }
}
