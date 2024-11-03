<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CreditCard;

class StoreCreditCardPurchaseRequest extends FormRequest
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
            'credit_card_id' => 'required|exists:credit_cards,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'category' => 'nullable|string',
            'installments_count' => 'required|integer|min:1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $creditCard = CreditCard::find($this->credit_card_id);

        if ($creditCard && $creditCard->available_limit < $this->amount) {
            abort(422, 'Limite disponível insuficiente no cartão.');
        }
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'credit_card_id' => 'cartão de crédito',
            'description' => 'descrição',
            'amount' => 'valor',
            'purchase_date' => 'data da compra',
            'category' => 'categoria',
            'installments_count' => 'número de parcelas',
        ];
    }
}
