<?php

namespace App\Http\Requests;

use App\Enums\RoleEnums;
use Illuminate\Validation\Rules\Enum;

class StoreUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => [new Enum(RoleEnums::class)],
        ];
    }
}
