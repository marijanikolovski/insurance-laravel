<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscountRequest;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountsController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();

        return response()->json($discounts);
    }

    public function store(DiscountRequest $request)
    {
        $validated = $request->validated();

        $discount = Discount::create($validated);

        return response()->json($discount, 201);
    }
}
