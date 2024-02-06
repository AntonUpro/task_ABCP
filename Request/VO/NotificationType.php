<?php

namespace NW\WebService\References\Operations\Notification\Entity;

use NW\WebService\References\Operations\Notification\Exceptions\ValidateException;

class NotificationType
{
    public const TYPE_NEW = 1;
    public const TYPE_CHANGE = 2;

    private const ALL = [
        self::TYPE_NEW,
        self::TYPE_CHANGE,
    ];

    private int $notificationType;

    public function __construct(int $notificationType)
    {
        if (! isset(self::ALL[$notificationType])) {
            throw new ValidateException('Unknown notificationType (' . $notificationType . ')');
        }

        $this->notificationType = $notificationType;
    }

    public function getValue(): int
    {
        return $this->notificationType;
    }
}