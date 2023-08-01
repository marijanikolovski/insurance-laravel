<?php

namespace App\ValueObjects;

class PriceMatchCalculation
{
  private int $valueBonusProtection = 0;
  private int $valueCommercialDiscount = 0;
  private int $voucher = 0;

  public function getValueBonusProtection(): int
  {
    return $this->valueBonusProtection;
  }

  public function getValueCommercialDiscount(): int
  {
    return $this->valueCommercialDiscount;
  }

  public function getVoucher(): int
  {
    return $this->voucher;
  }

  public function toArray(): array
  {
    return [
      'value_bonus_protection' => $this->valueBonusProtection,
      'value_commercial_discount' => $this->valueCommercialDiscount,
      'voucher' => $this->voucher
    ];
  }
}
