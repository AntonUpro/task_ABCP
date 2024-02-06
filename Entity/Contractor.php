<?php

namespace NW\WebService\References\Operations\Notification\Entity;

class Contractor
{
    public int $id;
    public string $type;
    public string $name;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function getById(int $resellerId): ?static
    {
        return new static($resellerId); // fakes the getById method
    }

    public function getFullName(): string
    {
        return $this->name . ' ' . $this->id; // Метод выглядит странным.
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}