<?php

namespace App\Http\Controllers;

use App\Enums\CategoryEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function getCategories(): JsonResponse
    {
        $categories = array_map(fn($category) => [
            'value' => $category->value,
            'label' => $category->label(),
        ], CategoryEnum::cases());

        return response()->json($categories);
    }
}
