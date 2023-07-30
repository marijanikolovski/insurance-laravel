<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceRequest;
use App\Models\Age;
use App\Models\City;
use App\Models\Coverage;
use App\Models\Discount;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function calculatePrice(InsuranceRequest $request)
    {
        // Validate the incoming request data
        $request->validate([]);

        // Selected coverages, discounts and price_match from the request
        $selected_coverages = $request->input('coverage_id', []);
        $selected_discounts = $request->input('discount_id', []);
        $price_match = $request->input('price_match');

        $discoutns = Discount::all();
        $coverages = Coverage::all();
        $ages = Age::all();

        $coverages_length = count($selected_coverages);
        $value_bonus_protection = 0;
        $value_user_under30 = 0;
        $value_user_over = 0;
        $value_glass_protection = 0;
        $value_commercial_discount = 0;
        $value_strong_car_surcharge = 0;
        $value_adviser_discount_bonus = 0;
        $value_sumer_discount = 0;
        $value_adviser_discount_ao_younger = 0;
        $value_adviser_discount_ao_older = 0;
        $value_adviser_discount_glass_protection = 0;
        $voucher = 0;

        // Calculation of the basic price based on the city and age
        $base_price = $this->calculateBasePrice($request->input('city_id'), $request->input('age_id'));

        // Calculation of the basic price based on the city without age
        $base_price_without_age = $this->calculateBasePriceWithout($request->input('city_id'));

        if ($price_match) {
            $total_price = $base_price_without_age;

            //The total price minus the value of the voucher
            if ($request->input('voucher')) {
                $voucher = $request->input('voucher');
                $total_price -= $request->input('voucher');
            }

            if (
                $base_price_without_age < $price_match
            ) {
                // Calculation of coveraage for bonus protection
                foreach ($coverages as $coverageBase) {
                    if ($coverageBase->name === 'Bonus Protection') {
                        $value_bonus_protection = $this->calculateDiscountsCoverages($coverageBase->value, $base_price_without_age);
                        $total_price += $value_bonus_protection;
                    }
                }
                if ($total_price < $price_match) {
                    $total_price;
                } else {
                    foreach ($discoutns as $discount) {
                        // Calculation of discount for commercial discount
                        if ($discount->name === 'Commercial discount') {
                            $value_commercial_discount = $this->calculateDiscountsCoverages($discount->value, $base_price_without_age);
                            $total_price -= $value_commercial_discount;
                        }
                    }
                }
                if ($total_price > $price_match) {
                    $total_price = $base_price_without_age;
                }

                $message = 'Price match is greater than the base value.';
            } else {
                $message = 'Price match must be higher than the base price.';
                $base_price_without_age = 0;
                $total_price = 0;
            }

            //The total price minus the value of the voucher
            if ($request->input('voucher')) {

                $total_price -= $request->input('voucher');
            }

            // Return the calculated total price, base price, dicounts and coverages
            return response()->json([
                'message' => $message,
                'price_match' => $price_match,
                'commercial_discount' => $value_commercial_discount,
                'value_bonus_protection' => $value_bonus_protection,
                'voucher' => $voucher,
                'base_price_without_age' => $base_price_without_age,
                'total_price' => $total_price,
            ]);
        } else {
        // Calculating the total price with selected coverages and discounts
        $total_price = $base_price;

            // Calculation of coveraage for bonus protection
        foreach ($selected_coverages as $coverageId) {
                $coverage = Coverage::find($coverageId);
            if ($coverage->name === 'Bonus Protection') {
                    $value_bonus_protection = $this->calculateDiscountsCoverages($coverage->value, $base_price);
                    $total_price += $value_bonus_protection;
            }

                //Calculation of account coverage if the user is under and over 30 years old
            if ($coverage->name === 'AO+') {
                if ($request->input('age_id') == $ages[0]->id) {
                    $value_user_under30 = $coverage->value;
                        $total_price += $value_user_under30;
                } else {
                    $value_user_over = $coverage->value_user_over30;
                        $total_price += $value_user_over;
                }
            }

            // Calculation of coverage for glass protection
            if ($coverage->name === 'Glass protection') {
                $value_glass_protection = $this->calculateDiscountsCoverages($coverage->value, $request->input('vehicle_power'));
                $total_price += $value_glass_protection;
            }
        }
        
        // Calculation of discount for commercial discount
        foreach ($selected_discounts as $discountId) {
                $discount = Discount::find($discountId);
            if ($discount->name === 'Commercial discount') {
                $value_commercial_discount = $this->calculateDiscountsCoverages($discount->value, $base_price);
                $total_price -= $value_commercial_discount;
            }
        }

        // Calculation of discount for strong car surcharge
        if ($request->input('vehicle_power') > 100) {
            foreach ($discoutns as $discountBse) {
                if ($discountBse->name === 'Strong car surcharge') {
                    $value_strong_car_surcharge = $this->calculateDiscountsCoverages($discountBse->value, $request->input('vehicle_power'));
                    $total_price += $value_strong_car_surcharge;
                }
            }
        }

        // Calculation of discount for adviser discount
        if ($coverages_length >= 2) {
            foreach ($discoutns as $discountBse) {
                if ($discountBse->name === 'Adviser discount') {
                    if ($value_bonus_protection) {
                        $value_adviser_discount_bonus = $this->calculateDiscountsCoverages($discountBse->value, $value_bonus_protection);
                        $total_price -= $value_adviser_discount_bonus;
                    }

                    if ($value_user_under30) {
                        $value_adviser_discount_ao_younger = $this->calculateDiscountsCoverages($discountBse->value, $value_user_under30);
                        $total_price -= $value_adviser_discount_ao_younger;
                    }
                        if ($value_user_over) {
                            $value_adviser_discount_ao_older = $this->calculateDiscountsCoverages($discountBse->value, $value_user_over);
                            $total_price -= $value_adviser_discount_ao_older;
                        }
                        if ($value_glass_protection) {
                            $value_adviser_discount_glass_protection = $this->calculateDiscountsCoverages($discountBse->value, $value_glass_protection);
                            $total_price -= $value_adviser_discount_glass_protection;
                        }
                }
            }
        }

        // Calculation of discount for summer discount
        if ($request->input('vehicle_power') > 80) {
            foreach ($discoutns as $discountBse)
                if ($discountBse->name === 'Summer discount') {
                    $value_sumer_discount = $this->calculateDiscountsCoverages($discountBse->value, $total_price);
                    $total_price -= $value_sumer_discount;
                }
        }        
        
        //The total price minus the value of the voucher
        if ($request->input('voucher')) {
                $voucher = $request->input('voucher');
            $total_price -= $request->input('voucher');
        }

            // Return the calculated total price, base price, dicounts and coverages
            return response()->json([
                'value_bonus_protection' => $value_bonus_protection,
                'value_AO+_user_under30' => $value_user_under30,
                'value_AO+_user_over30' => $value_user_over,
                'value_glass_protection' => $value_glass_protection,
                'value_commercial_discount' => $value_commercial_discount,
                'value_strong_car_surcharge' => $value_strong_car_surcharge,
                'value_sumer_discount' => $value_sumer_discount,
                'value_adviser_discount_bonus' => $value_adviser_discount_bonus,
                'value_adviser_discount_ao_younger' => $value_adviser_discount_ao_younger,
                'value_adviser_discount_ao_older' => $value_adviser_discount_ao_older,
                'value_adviser_discount_glass_protection' => $value_adviser_discount_glass_protection,
                'voucher' => $voucher,
                'base_price' => $base_price,
                'total_price' => $total_price,
        ]);
        }
    }

    private function calculateBasePrice(int $city, int $age): float
    {
        $city = City::find($city);
        $age = Age::find($age);

        return $city->value + $age->value;
    }

    private function calculateBasePriceWithout($city)
    {
        $city = City::find($city);

        return $city->value;
    }


    private function calculateDiscountsCoverages($percentage, $price)
    {
        return $percentage * $price / 100;
    }
}
