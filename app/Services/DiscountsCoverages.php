<?php

namespace App\Services;

class DiscountsCoverages
{
  public static function calculateDiscountsCoverages(int $percentage, int $price): float
  {
    return $percentage * $price / 100;
  }
}
