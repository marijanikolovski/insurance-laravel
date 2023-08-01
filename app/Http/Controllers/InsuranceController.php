<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceRequest;
use App\Services\InsuranceWithoutPriceMatch;
use App\Services\InsuranceWithPriceMatch;
use App\ValueObjects\CustomerData;

class InsuranceController extends Controller
{
    public function calculatePrice(
        InsuranceWithPriceMatch $insuranceWithPriceMatch,
        InsuranceWithoutPriceMatch $insuranceWithoutPriceMatch,
        InsuranceRequest $request
    ) {
        $validatedData = $request->validated();

        $customerData = CustomerData::createFromRequest($validatedData);

        if ($request->input('price_match')) {
            return response()->json(
                $insuranceWithPriceMatch->calculationWithPriceMatch($customerData)
            );
        } else {
            return response()->json(
                $insuranceWithoutPriceMatch->calculationWithoutPriceMatch($customerData)
            );
        }
    }
}