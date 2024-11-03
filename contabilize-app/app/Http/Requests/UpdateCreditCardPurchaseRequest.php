<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CreditCardPurchase;

class UpdateCreditCardPurchaseRequest extends FormRequest
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
            'description' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'purchase_date' => 'sometimes|required|date',
            'category' => 'nullable|string',
            'installments_count' => 'sometimes|required|integer|min:1',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $purchase = $this->route('creditCardPurchase');
        $creditCard = $purchase ? $purchase->creditCard : null;

        if ($creditCard && $this->amount && $this->amount != $purchase->amount) {
            $amountDifference = $this->amount - $purchase->amount;

            if ($amountDifference > 0 && $creditCard->available_limit < $amountDifference) {
                abort(422, 'Limite disponível insuficiente no cartão para a atualização.');
            }
        }
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'description' => 'descrição',
            'amount' => 'valor',
            'purchase_date' => 'data da compra',
            'category' => 'categoria',
            'installments_count' => 'número de parcelas',
        ];
    }
}
