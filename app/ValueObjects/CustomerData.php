<?php

namespace App\ValueObjects;

class CustomerData
{
  private string $name;
  private int $ageId;
  private int $cityId;
  private int $vehiclePower;
  private int $voucher = 0;
  private ?int $priceMatch = null;
  private ?array $commercialDiscountIds = [];
  private ?array $protectionIds = [];

  public function __construct(
    string $name,
    int $ageId,
    int $cityId,
    int $vehiclePower
  ) {
    $this->name = $name;
    $this->ageId = $ageId;
    $this->cityId = $cityId;
    $this->vehiclePower = $vehiclePower;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getAgeId(): int
  {
    return $this->ageId;
  }

  public function getCityId(): int
  {
    return $this->cityId;
  }

  public function getVehiclePower(): int
  {
    return $this->vehiclePower;
  }

  public function getVoucher(): int
  {
    return $this->voucher;
  }

  public function setVoucher(int $voucher): void
  {
    $this->voucher = $voucher;
  }

  public function getPriceMatch(): ?int
  {
    return $this->priceMatch;
  }

  public function setPriceMatch(int $priceMatch): void
  {
    $this->priceMatch = $priceMatch;
  }

  public function getDiscountIds(): ?array
  {
    return $this->commercialDiscountIds;
  }

  public function setDiscountIds(array $commercialDiscountIds): void
  {
    $this->commercialDiscountIds = $commercialDiscountIds;
  }

  public function getCoveragesIds(): ?array
  {
    return $this->protectionIds;
  }

  public function setCoveragesIds(array $protectionIds): void
  {
    $this->protectionIds = $protectionIds;
  }

  public static function createFromRequest(array $data): CustomerData
  {
    $customerData = new CustomerData(
      $data['name'],
      $data['age_id'],
      $data['city_id'],
      $data['vehicle_power'],
    );

    $customerData->setVoucher($data['voucher'] ?? null);
    $customerData->setPriceMatch($data['price_match'] ?? null);
    $customerData->setDiscountIds($data['discount_id'] ?? null);
    $customerData->setCoveragesIds($data['coverage_id'] ?? null);

    return $customerData;
  }
}
