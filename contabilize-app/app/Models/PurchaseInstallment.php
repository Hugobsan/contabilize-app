<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInstallment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_installments';

    protected $fillable = [
        'credit_card_purchase_id',
        'installment_number',
        'due_date',
        'amount',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'status' => 'boolean',
    ];

    /**
     * Relacionamento com a compra do cartão de crédito.
     */
    public function creditCardPurchase()
    {
        return $this->belongsTo(CreditCardPurchase::class);
    }

    /**
     * Accessor para formatar a descrição da parcela.
     */
    public function getFormattedDescriptionAttribute()
    {
        return "Parcela {$this->installment_number} de " . $this->creditCardPurchase->description;
    }

    /**
     * Verifica se a parcela está vencida.
     */
    public function getIsOverdueAttribute()
    {
        return !$this->status && $this->due_date->isPast();
    }
}
