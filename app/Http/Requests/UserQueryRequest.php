<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserQueryRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_like' => 'nullable|string',
            'page' => 'nullable|string',
            'limit' => 'nullable|string'
        ];
    }
}
