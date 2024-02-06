<?php

namespace NW\WebService\References\Operations\Notification\Entity;

use NW\WebService\References\Operations\Notification\Exceptions\ValidateException;

class Status
{
    private const NAMES_STATUS = [
        0 => 'Completed',
        1 => 'Pending',
        2 => 'Rejected',
    ];

    private int $id;
    private string $name;  // не использую

    public function __construct(int $id)
    {
        if (!isset(self::NAMES_STATUS[$id])) {
            throw new ValidateException('Unknown status (' . $id . ')');
        }

        $this->id = $id;
        $this->name = self::NAMES_STATUS[$id];
    }

    public static function getName(int $id): string
    {
        return self::NAMES_STATUS[$id];
    }
}