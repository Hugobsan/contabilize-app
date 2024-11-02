<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountPayableRequest extends FormRequest
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
            'description' => 'sometimes|required|string|max:255',
            'value' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date|after:today',
            'status' => 'sometimes|required|boolean',
            'category' => 'nullable|string|in:' . implode(',', array_map(fn($e) => $e->value, \App\Enums\CategoryEnum::cases())),
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'descrição',
            'value' => 'valor',
            'due_date' => 'data de vencimento',
            'status' => 'status',
            'category' => 'categoria',
        ];
    }
}
