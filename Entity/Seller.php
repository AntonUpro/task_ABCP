<?php

namespace NW\WebService\References\Operations\Notification\Entity;

class Seller extends Contractor
{
    public function getResellerEmailFrom(): string
    {
        return 'contractor@example.com';
    }
}