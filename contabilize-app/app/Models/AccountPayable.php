<?php

namespace App\Models;

use App\Enums\CategoryEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use SoftDeletes;
    protected $table = "accounts_payable";

    protected $fillable = [
        "user_id",
        "description",
        "value",
        "due_date",
        "status",
        "category"
    ];

    protected $casts = [
        "due_date" => "datetime",
        "status" => StatusEnum::class,
        "category" => CategoryEnum::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, array $filters)
    {
        //Scope to filter the account payable
        $query->when($filters["search"] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where("description", "like", "%{$search}%")
                    ->orWhere("category", "like", "%{$search}%");
            });
        });

        //Scope to filter the account payable by category
        $query->when($filters["category"] ?? null, function ($query, $category) {
            $query->where("category", $category);
        });

        //Scope to filter the account payable by status
        $query->when($filters["status"] ?? null, function ($query, $status) {
            $query->where("status", $status);
        });
    }

    public function scopeWithTrashed($query)
    {
        return $query->withTrashed();
    }

    public function getFormattedValueAttribute()
    {
        return number_format($this->value, 2, ',', '.');
    }

    public function setCategoryAttribute($value)
    {
        if (!CategoryEnum::tryFrom($value)) {
            throw new \InvalidArgumentException("Invalid category value: $value");
        }
        $this->attributes['category'] = $value;
    }
}
