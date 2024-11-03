<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCreditCardRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nickname' => 'sometimes|required|string|max:255',
            'credit_limit' => 'sometimes|required|numeric|min:0',
            'available_limit' => 'sometimes|required|numeric|min:0|lte:credit_limit', // Verifica se o limite disponível não excede o limite total.
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'nickname' => 'apelido',
            'credit_limit' => 'limite de crédito',
            'available_limit' => 'limite disponível',
        ];
    }
}
