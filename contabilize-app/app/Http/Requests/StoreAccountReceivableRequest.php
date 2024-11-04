<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountReceivableRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'status' => 'required|boolean',
            'category' => 'nullable|in:' . implode(',', array_map(fn($e) => $e->value, \App\Enums\ReceivableCategoryEnum::cases())),
            'recurrence_period' => 'nullable|in:' . implode(',', array_map(fn($e) => $e->value, \App\Enums\RecurrencePeriodEnum::cases())),
        ];
    }

    public function attributes(): array
    {
        return [
            'description' => 'descrição',
            'value' => 'valor',
            'due_date' => 'data de vencimento',
            'next_due_date' => 'data da próxima recorrência',
            'status' => 'status',
            'category' => 'categoria',
            'recurrence_period' => 'período de recorrência',
        ];
    }
}
