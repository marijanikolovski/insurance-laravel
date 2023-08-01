<?php

namespace App\Services;

use App\Models\Coverage;
use App\Models\Discount;
use App\ValueObjects\CustomerData;
use App\ValueObjects\PriceMatchCalculation;

class InsuranceWithPriceMatch
{
  public function calculationWithPriceMatch(CustomerData $customerData): array
  {

    $priceMathcCalculate = new PriceMatchCalculation();
    $toArray = $priceMathcCalculate->toArray();

    $coverages = Coverage::all();
    $discounts = Discount::all();

    // Calculation of the basic price based on the city without age
    $base_price_without_age = BasePrice::calculateBasePriceWithoutAge(
      $customerData->getCityId()
    );

    $total_price = $base_price_without_age;

    //The total price minus the value of the voucher
    if ($customerData->getVoucher()) {
      $toArray['voucher'] = $customerData->getVoucher();
      $total_price -= $customerData->getVoucher();
    }

    if (
      $base_price_without_age < $customerData->getPriceMatch()
    ) {
      // Calculation of coveraage for bonus protection
      foreach ($coverages as $coverageBase) {
        if ($coverageBase->name !== 'Bonus Protection') {
          continue;
        }

        $toArray['value_bonus_protection'] =
        DiscountsCoverages::calculateDiscountsCoverages(
          $coverageBase->value,
          $base_price_without_age
        );
        $total_price += $toArray['value_bonus_protection'];
      }

      if ($total_price > $customerData->getPriceMatch()) {
        foreach ($discounts as $discount) {
          // Calculation of discount for commercial discount
          if ($discount->name !== 'Commercial discount') {
            continue;
          }

          $toArray['value_commercial_discount'] =
            DiscountsCoverages::calculateDiscountsCoverages(
              $discount->value,
              $base_price_without_age
            );
          $total_price -= $toArray['value_commercial_discount'];
        }
      }

      if ($total_price > $customerData->getPriceMatch()) {
        $total_price = $base_price_without_age;
      }

      $message = 'Price match is greater than the base value.';
    } else {
      $message = 'Price match must be higher than the base price.';
    }

    // Return the calculated total price, base price, dicounts and coverages
    return [
      'message' => $message,
      'price_match' => $customerData->getPriceMatch(),
      'value_commercial_discount' => $toArray['value_commercial_discount'],
      'value_bonus_protection' => $toArray['value_bonus_protection'],
      'voucher' => $toArray['voucher'],
      'base_price_without_age' => $base_price_without_age,
      'total_price' => $total_price,
    ];
  }
}
