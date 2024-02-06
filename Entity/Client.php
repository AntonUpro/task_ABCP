<?php

namespace NW\WebService\References\Operations\Notification\Entity;

/* По логике бизнеса не понятно, может ли клиент быть подрядчиком.
Возможно клиент должен унаследоваться от другого класса, либо быть самостоятельным классом  */
class Client extends Contractor
{
    public const TYPE_CUSTOMER = 0;
    private int $sellerId;
    private ?string $email;
    private ?string $mobile;

    public function getSeller(): Seller
    {
        return new Seller($this->sellerId);  // метод заглушка
    }

    public function setSellerId(int $sellerId): void
    {
        $this->sellerId = $sellerId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function isCustomer(): bool
    {
        return $this->type === self::TYPE_CUSTOMER;
    }
}