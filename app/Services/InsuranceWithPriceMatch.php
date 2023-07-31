<?php

namespace App\Services;

use App\Models\Coverage;
use App\Models\Discount;

class InsuranceWithPriceMatch
{
  public function calculationWithPriceMatch(object $request): array
  {
    $value_bonus_protection = 0;
    $value_commercial_discount = 0;
    $voucher = 0;

    $coverages = Coverage::all();
    $discoutns = Discount::all();

    // Calculation of the basic price based on the city without age
    $base_price_without_age = BasePrice::calculateBasePriceWithoutAge($request->input('city_id'));

    $total_price = $base_price_without_age;

    //The total price minus the value of the voucher
    if ($request->input('voucher')) {
      $voucher = $request->input('voucher');
      $total_price -= $request->input('voucher');
    }

    if (
      $base_price_without_age < $request->input('price_match')
    ) {
      // Calculation of coveraage for bonus protection
      foreach ($coverages as $coverageBase) {
        if ($coverageBase->name === 'Bonus Protection') {
          $value_bonus_protection = DiscountsCoverages::calculateDiscountsCoverages($coverageBase->value, $base_price_without_age);
          $total_price += $value_bonus_protection;
        }
      }
      if ($total_price < $request->input('price_match')) {
        $total_price;
      } else {
        foreach ($discoutns as $discount) {
          // Calculation of discount for commercial discount
          if ($discount->name === 'Commercial discount') {
            $value_commercial_discount = DiscountsCoverages::calculateDiscountsCoverages($discount->value, $base_price_without_age);
            $total_price -= $value_commercial_discount;
          }
        }
      }
      if ($total_price > $request->input('price_match')) {
        $total_price = $base_price_without_age;
      }

      $message = 'Price match is greater than the base value.';
    } else {
      $message = 'Price match must be higher than the base price.';
    }

    // Return the calculated total price, base price, dicounts and coverages
    return [
      'message' => $message,
      'price_match' => $request->input('price_match'),
      'value_commercial_discount' => $value_commercial_discount,
      'value_bonus_protection' => $value_bonus_protection,
      'voucher' => $voucher,
      'base_price_without_age' => $base_price_without_age,
      'total_price' => $total_price,
    ];
  }
}
