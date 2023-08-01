<?php

namespace App\Services;

use App\Models\Age;
use App\Models\Coverage;
use App\Models\Discount;
use App\ValueObjects\CustomerData;
use App\ValueObjects\NoPriceMatchCalculation;

class InsuranceWithoutPriceMatch
{
  public function calculationWithoutPriceMatch(CustomerData $customerData): array
  {
    $noPriceMatchCalculation = new NoPriceMatchCalculation();
    $toArray = $noPriceMatchCalculation->toArray();

    $selected_coverages = $customerData->getCoveragesIds();
    $selected_discounts = $customerData->getDiscountIds();

    $ages = Age::all();
    $discoutns = Discount::all();

    $coverages_length = count($selected_coverages);


    // Calculation of the basic price based on the city and age
    $base_price = BasePrice::calculateBasePrice(
      $customerData->getCityId(),
      $customerData->getAgeId()
    );

    $total_price = $base_price;

    // Calculation of coveraage for bonus protection
    foreach ($selected_coverages as $coverageId) {
      $coverage = Coverage::find($coverageId);
      if ($coverage->name === 'Bonus Protection') {
        $toArray['value_bonus_protection'] =
          DiscountsCoverages::calculateDiscountsCoverages(
            $coverage->value,
            $base_price
          );
        $total_price += $toArray['value_bonus_protection'];
      }

      //Calculation of account coverage if the user is under and over 30 years old
      if ($coverage->name === 'AO+') {
        if ($customerData->getAgeId() == $ages[0]->id) {
          $toArray['value_AO_user_under30'] = $coverage->value;
          $total_price += $toArray['value_AO_user_under30'];
        } else {
          $toArray['value_AO_user_over30'] = $coverage->value_user_over30;
          $total_price += $toArray['value_AO_user_over30'];
        }
      }

      // Calculation of coverage for glass protection
      if ($coverage->name === 'Glass protection') {
        $toArray['value_glass_protection'] =
          DiscountsCoverages::calculateDiscountsCoverages(
            $coverage->value,
            $customerData->getVehiclePower()
          );
        $total_price += $toArray['value_glass_protection'];
      }
    }

    // Calculation of discount for commercial discount
    foreach ($selected_discounts as $discountId) {
      $discount = Discount::find($discountId);
      if ($discount->name === 'Commercial discount') {
        $toArray['value_commercial_discount'] =
          DiscountsCoverages::calculateDiscountsCoverages(
            $discount->value,
            $base_price
          );
        $total_price -= $toArray['value_commercial_discount'];
      }
    }

    // Calculation of discount for strong car surcharge
    if ($customerData->getVehiclePower() > 100) {
      foreach ($discoutns as $discountBse) {
        if ($discountBse->name === 'Strong car surcharge') {
          $toArray['value_strong_car_surcharge'] =
            DiscountsCoverages::calculateDiscountsCoverages(
              $discountBse->value,
              $customerData->getVehiclePower()
            );
          $total_price += $toArray['value_strong_car_surcharge'];
        }
      }
    }

    // Calculation of discount for adviser discount
    if ($coverages_length >= 2) {
      foreach ($discoutns as $discountBse) {
        if ($discountBse->name === 'Adviser discount') {
          if ($toArray['value_bonus_protection']) {
            $toArray['value_adviser_discount_bonus'] =
              DiscountsCoverages::calculateDiscountsCoverages(
                $discountBse->value,
                $toArray['value_bonus_protection']
              );
            $total_price -= $toArray['value_adviser_discount_bonus'];
          }

          if ($toArray['value_AO_user_under30']) {
            $toArray['value_adviser_discount_ao_younger'] =
            DiscountsCoverages::calculateDiscountsCoverages(
              $discountBse->value,
              $toArray['value_AO_user_over30']
            );
            $total_price -=  $toArray['value_adviser_discount_ao_younger'];
          }
          if ($toArray['value_AO_user_over30']) {
            $toArray['value_adviser_discount_ao_older'] =
            DiscountsCoverages::calculateDiscountsCoverages(
              $discountBse->value,
              $toArray['value_AO_user_over30']
            );
            $total_price -=  $toArray['value_adviser_discount_ao_older'];
          }
          if ($toArray['value_glass_protection']) {
            $toArray['value_adviser_discount_glass_protection'] =
            DiscountsCoverages::calculateDiscountsCoverages(
              $discountBse->value,
              $toArray['value_glass_protection']
            );
            $total_price -= $toArray['value_adviser_discount_glass_protection'];
          }
        }
      }
    }

    // Calculation of discount for summer discount
    if ($customerData->getVehiclePower() > 80) {
      foreach ($discoutns as $discountBse)
        if ($discountBse->name === 'Summer discount') {
        $toArray['value_sumer_discount'] =
          DiscountsCoverages::calculateDiscountsCoverages(
            $discountBse->value,
            $total_price
          );
        $total_price -= $toArray['value_sumer_discount'];
        }
    }

    //The total price minus the value of the voucher
    if ($customerData->getVoucher()) {
      $toArray['voucher'] = $customerData->getVoucher();
      $total_price -= $customerData->getVoucher();
    }

    // Return the calculated total price, base price, dicounts and coverages
    return [
      'value_bonus_protection' => $toArray['value_bonus_protection'],
      'value_AO_user_under30' => $toArray['value_AO_user_under30'],
      'value_AO_user_over30' => $toArray['value_AO_user_over30'],
      'value_glass_protection' => $toArray['value_glass_protection'],
      'value_commercial_discount' => $toArray['value_commercial_discount'],
      'value_strong_car_surcharge' => $toArray['value_strong_car_surcharge'],
      'value_sumer_discount' => $toArray['value_sumer_discount'],
      'value_adviser_discount_bonus' => $toArray['value_adviser_discount_bonus'],
      'value_adviser_discount_ao_younger' => $toArray['value_adviser_discount_ao_younger'],
      'value_adviser_discount_ao_older' => $toArray['value_adviser_discount_ao_older'],
      'value_adviser_discount_glass_protection' => $toArray['value_adviser_discount_glass_protection'],
      'voucher' => $toArray['voucher'],
      'base_price' => $base_price,
      'total_price' => $total_price,
    ];
  }
}
