<?php

namespace App\Services;

use App\Models\Age;
use App\Models\City;

class BasePrice
{
  public static function calculateBasePrice(int $cityId, int $ageId): float
  {
    $city = City::find($cityId);
    $age = Age::find($ageId);

    return $city->value + $age->value;
  }

  public static function calculateBasePriceWithoutAge(int $cityIdd): float
  {
    $city = City::find($cityIdd);

    return $city->value;
  }
}
