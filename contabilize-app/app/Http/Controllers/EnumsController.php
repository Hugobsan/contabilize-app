<?php

namespace App\Http\Controllers;

use App\Enums\CategoryEnum;
use App\Enums\ReceivableCategoryEnum;
use App\Enums\RecurrencePeriodEnum;
use App\Enums\StatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnumsController extends Controller
{
    public function getEnumLabels($enum): JsonResponse
    {
        $enums = [
            'category' => CategoryEnum::class,
            'receivable-category' => ReceivableCategoryEnum::class,
            'status' => StatusEnum::class,
            'recurrence-period' => RecurrencePeriodEnum::class,
        ];
        
        $labels = array_map(fn($label) => [
            'value' => $label->value,
            'label' => $label->label(),
        ], $enums[$enum]::cases());

        return response()->json($labels);
    }
}
