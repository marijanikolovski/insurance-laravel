<?php

namespace App\ValueObjects;

class NoPriceMatchCalculation
{
  private int $valueBonusProtection = 0;
  private int $valueUserUnder30 = 0;
  private int $valueUserOver30 = 0;
  private int $valueGlassProtection = 0;
  private int $valueCommercialDiscount = 0;
  private int $valueStrongCarSurcharge = 0;
  private int $valueAdviserDiscountBonus = 0;
  private int $valueSumerDiscount = 0;
  private int $valueAdviserDiscountAoYounger = 0;
  private int $valueAdviserDiscountAoOlder = 0;
  private int $valueAdviserDiscountGlassProtection = 0;
  private int $voucher = 0;

  public function getValueBonusProtection(): int
  {
    return $this->valueBonusProtection;
  }

  public function getValueUserUnder30(): int
  {
    return $this->valueUserUnder30;
  }

  public function getValueUserOver30(): int
  {
    return $this->valueUserOver30;
  }

  public function getValueGlassProtection(): int
  {
    return $this->valueGlassProtection;
  }

  public function getValueCommercialDiscount(): int
  {
    return $this->valueCommercialDiscount;
  }

  public function getValueStrongCarSurcharge(): int
  {
    return $this->valueStrongCarSurcharge;
  }

  public function getValueAdviserDiscountBonus(): int
  {
    return $this->valueAdviserDiscountBonus;
  }

  public function getValueSumerDiscount(): int
  {
    return $this->valueSumerDiscount;
  }

  public function getValueAdviserDiscountAoYounger(): int
  {
    return $this->valueAdviserDiscountAoYounger;
  }

  public function getValueAdviserDiscountAoOlder(): int
  {
    return $this->valueAdviserDiscountAoOlder;
  }

  public function getVoucher(): int
  {
    return $this->valueAdviserDiscountGlassProtection;
  }

  public function getValueAdviserDiscountGlassProtection(): int
  {
    return $this->voucher;
  }

  public function toArray(): array
  {
    return [
      'value_bonus_protection' => $this->valueBonusProtection,
      'value_AO_user_under30' => $this->valueUserUnder30,
      'value_AO_user_over30' => $this->valueUserOver30,
      'value_glass_protection' => $this->valueGlassProtection,
      'value_commercial_discount' => $this->valueCommercialDiscount,
      'value_strong_car_surcharge' => $this->valueStrongCarSurcharge,
      'value_sumer_discount' => $this->valueSumerDiscount,
      'value_adviser_discount_bonus' => $this->valueAdviserDiscountBonus,
      'value_adviser_discount_ao_younger' => $this->valueAdviserDiscountAoYounger,
      'value_adviser_discount_ao_older' => $this->valueAdviserDiscountAoOlder,
      'value_adviser_discount_glass_protection' => $this->valueAdviserDiscountGlassProtection,
      'voucher' => $this->voucher,
    ];
  }
}
