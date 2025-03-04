<?php

// NOT IN USE

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequestStore extends FormRequest
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
        return [
            'name' => 'required|string|max:32',
            'phone' => 'string|max:16',
            'age' => 'integer',
            'password' => 'required|string',
            'email' => 'required|email|unique:students,email',
            'gender' => 'string'
        ];
    }
}


