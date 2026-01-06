<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
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
        $userId = $this->route('user')->id;

        return [
            'name' => ['string', 'min:3', 'max:100'],
            'email' => ['string', 'email', Rule::unique('users', 'email')->ignore($userId), 'min:3', 'max:100'],
            'password' => ['string', 'min:3', 'max:100'],
        ];
    }
}
