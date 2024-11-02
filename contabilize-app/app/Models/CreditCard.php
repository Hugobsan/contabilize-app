<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_cards';

    protected $fillable = [
        'user_id',
        'nickname',
        'credit_limit',
        'available_limit',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'available_limit' => 'decimal:2',
    ];

    /**
     * Relacionamento com o usuário.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com as compras realizadas com o cartão.
     */
    public function purchases()
    {
        return $this->hasMany(CreditCardPurchase::class);
    }

    /**
     * Accessor para obter o total de compras com parcelas pendentes.
     */
    public function getTotalOpenPurchasesAttribute()
    {
        return $this->purchases()
            ->join('purchase_installments', 'credit_card_purchases.id', '=', 'purchase_installments.credit_card_purchase_id')
            ->where('purchase_installments.status', 0)
            ->sum('purchase_installments.amount');
    }

    /**
     * Accessor para obter o total de todas as compras (todas as parcelas).
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->purchases()
            ->join('purchase_installments', 'credit_card_purchases.id', '=', 'purchase_installments.credit_card_purchase_id')
            ->sum('purchase_installments.amount');
    }
}
