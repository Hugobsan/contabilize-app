<?php

namespace App\Models;

use App\Enums\CategoryEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCardPurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_card_purchases';

    protected $fillable = [
        'credit_card_id',
        'description',
        'amount',
        'purchase_date',
        'category',
        'installments_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'purchase_date' => 'date',
        'category' => CategoryEnum::class,
    ];

    /**
     * Relacionamento com o cartão de crédito.
     */
    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }

    /**
     * Relacionamento com as parcelas da compra.
     */
    public function installments()
    {
        return $this->hasMany(PurchaseInstallment::class);
    }

    /**
     * Relacionamento com o usuário através do cartão de crédito.
     */
    public function user()
    {
        return $this->hasOneThrough(User::class, CreditCard::class, 'id', 'id', 'credit_card_id', 'user_id');
    }

    /**
     * Accessor para exibir a descrição com o número de parcelas.
     */
    public function getDescriptionWithInstallmentsAttribute()
    {
        return $this->installments_count > 1 
            ? "{$this->description} ({$this->installments_count} parcelas)" 
            : $this->description;
    }
}
