<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceRequest;
use App\Services\InsuranceWithoutPriceMatch;
use App\Services\InsuranceWithPriceMatch;

class InsuranceController extends Controller
{
    public function calculatePrice(
        InsuranceWithPriceMatch $insuranceWithPriceMatch,
        InsuranceWithoutPriceMatch $insuranceWithoutPriceMatch,
        InsuranceRequest $request
    )
    {
        if ($request->input('price_match')) {
            return response()->json($insuranceWithPriceMatch->calculationWithPriceMatch($request));
        } else {
            return response()->json($insuranceWithoutPriceMatch->calculationWithoutPriceMatch($request));
        }
    }
}