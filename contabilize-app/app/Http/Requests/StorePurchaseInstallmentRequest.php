<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseInstallmentRequest extends FormRequest
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
            'credit_card_purchase_id' => 'required|exists:credit_card_purchases,id',
            'installment_number' => 'required|integer|min:1',
            'due_date' => 'required|date|after_or_equal:today',
            'amount' => 'required|numeric|min:0.01',
            'status' => 'nullable|boolean',
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'credit_card_purchase_id' => 'compra com cartão de crédito',
            'installment_number' => 'número da parcela',
            'due_date' => 'data de vencimento',
            'amount' => 'valor',
            'status' => 'status',
        ];
    }
}
