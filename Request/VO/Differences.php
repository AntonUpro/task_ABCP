<?php

namespace NW\WebService\References\Operations\Notification\Request\VO;

use NW\WebService\References\Operations\Notification\Entity\Status;

class Differences
{
    private Status $from;
    private Status $to;

    public function __construct(
        Status $from,
        Status $to
    ) {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): Status
    {
        return $this->from;
    }

    public function getTo(): Status
    {
        return $this->to;
    }
}