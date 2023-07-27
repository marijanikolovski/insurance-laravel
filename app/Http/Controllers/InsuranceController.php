<?php

namespace App\Http\Controllers;

use App\Models\Age;
use App\Models\City;
use App\Models\Coverage;
use App\Models\Discount;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function calculatePrice(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string',
            'age_id' => 'required|integer|exists:ages,id',
            'city_id' => 'required|integer|exists:cities,id',
            'vehicle_power' => 'required|numeric',
            'voucher' => 'numeric',
            'price_match' => 'numeric',
            'discount_id' => 'array|exists:discounts,id',
            'coverage_id' => 'array|exists:coverages,id'
        ]);

        // Selected coverages and discounts from the request
        $selected_coverages = $request->input('coverage_id', []);
        $selected_discounts = $request->input('discount_id', []);

        $coverages_length = count($selected_coverages);

        // Calculation of the basic price based on the city and age
        $base_price = $this->calculateBasePrice($request->input('city_id'), $request->input('age_id'));

        // Calculating the total price with selected coverages and discounts
        $total_price = $base_price;

        foreach ($selected_coverages as $coverageId) {
            $coverage = Coverage::find($coverageId);

            // Calculation of coveraage for bonus protection
            if ($coverage->name === 'Bonus Protection') {
                $value_bonus_protection = $this->calculateDiscountsCoverages($coverage->value, $base_price);
                $total_price += $value_bonus_protection;
            }
            //Calculation of account coverage if the user is over 30 years old
            if ($request->input('age_id') === 1) {
                if ($coverage->name === 'AO+ < 30') {
                    $total_price += $coverage->value;
                }
            }
            // Calculation of account coverage if the user is under 30 years old
            if ($request->input('age_id') === 2) {
                if ($coverage->name === 'AO+ > 30') {
                    $total_price -= $coverage->value;
                }
            }


            // Calculation of coverage for glass protection
            if ($coverage->name === 'Glass protection') {
                $value_glass_protection = $this->calculateDiscountsCoverages($coverage->value, $request->input('vehicle_power'));
                $total_price += $value_glass_protection;
            }
        }

        foreach ($selected_discounts as $discountId) {
            $discount = Discount::find($discountId);

            // Calculation of discount for strong car surcharge
            if ($discount->name === 'Strong car surcharge') {
                if ($request->input('vehicle_power') > 80) {
                    $value_strong_car_surcharge = $this->calculateDiscountsCoverages($discount->value, $request->input('vehicle_power'));
                    $total_price += $value_strong_car_surcharge;
                }
            }


            // Calculation of discount for commercial discount
            if ($discount->name === 'Commercial discount') {
                $value_commercial_discount = $this->calculateDiscountsCoverages($discount->value, $base_price);
                $total_price -= $value_commercial_discount;
            }

            // Calculation of discount for adviser discount
            if ($coverages_length >= 2) {
                if ($discount->name === 'Adviser discount') {
                    foreach ($selected_coverages as $coverageId) {
                        $coverage = Coverage::find($coverageId);
                        if ($coverage->name === 'Bonus Protection') {
                            $value_adviser_discount_bonus = $this->calculateDiscountsCoverages($discount->value, $value_bonus_protection);
                            $total_price -= $value_adviser_discount_bonus;
                        } elseif ($coverage->name === 'AO+ < 30') {
                            $value_adviser_discount_ao_younger = $this->calculateDiscountsCoverages($discount->value, $coverage->value);
                            $total_price -= $value_adviser_discount_ao_younger;
                        } elseif ($coverage->name === 'AO+ > 30') {
                            $value_adviser_discount_ao_older = $this->calculateDiscountsCoverages($discount->value, $coverage->value);
                            $total_price -= $value_adviser_discount_ao_older;
                        } elseif ($coverage->name === 'Glass protection') {
                            $value_adviser_discount_glass_protection = $this->calculateDiscountsCoverages($discount->value, $value_glass_protection);
                            $total_price -= $value_adviser_discount_glass_protection;
                        }
                    }
                }
            }


            // Calculation of discount for summer discount
            if ($discount->name === 'Summer discount') {
                if ($request->input('vehicle_power') > 80) {
                    $value_sumer_discount = $this->calculateDiscountsCoverages($discount->value, $total_price);
                    $total_price -= $value_sumer_discount;
                }
            }
        }

        //The total price minus the value of the voucher
        if ($request->input('voucher')) {
            $total_price -= $request->input('voucher');
        }

        // Return the calculated total price, base price, dicounts and coverages
        return response()->json([
            //'commercial_discount' => $value_commercial_discount,
            //'sumer_discount' => $value_sumer_discount,
            //'strong_car_surcharge' => $value_strong_car_surcharge,
            //'value_adviser_dicount' => $value_adviser_dicount,
            //'bonus_protection' => $value_bonus_protection,
            //'glass_protection' => $value_glass_protection,
            'base_price' => $base_price,
            'total_price' => $total_price,
        ]);
    }

    private function calculateBasePrice($city, $age)
    {
        $city = City::find($city);
        $age = Age::find($age);

        return $city->value + $age->value;
    }

    private function calculateDiscountsCoverages($percentage, $price)
    {
        return $percentage * $price / 100;
    }
}
