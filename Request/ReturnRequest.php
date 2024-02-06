<?php

namespace NW\WebService\References\Operations\Notification\Request;

use NW\WebService\References\Operations\Notification\Entity\NotificationType;
use NW\WebService\References\Operations\Notification\Request\VO\Differences;

class ReturnRequest
{
    private int $resellerId;
    private int $clientId;
    private int $creatorId;
    private int $expertId;
    private NotificationType $notificationType;
    private int $complaintId;
    private string $complaintNumber;
    private int $consumptionId;
    private string $consumptionNumber;
    private string $agreementNumber;
    private string $date;
    private ?Differences $differences;

    public function __construct(
        int $resellerId,
        int $clientId,
        int $creatorId,
        int $expertId,
        NotificationType $notificationType,
        int $complaintId,
        string $complaintNumber,
        int $consumptionId,
        string $consumptionNumber,
        string $agreementNumber,
        string $date,
        ?Differences $differences
    ) {
        $this->resellerId = $resellerId;
        $this->clientId = $clientId;
        $this->creatorId = $creatorId;
        $this->expertId = $expertId;
        $this->notificationType = $notificationType;
        $this->differences = $differences;
        $this->complaintId = $complaintId;
        $this->complaintNumber = $complaintNumber;
        $this->consumptionId = $consumptionId;
        $this->consumptionNumber = $consumptionNumber;
        $this->agreementNumber = $agreementNumber;
        $this->date = $date;
    }

    public function getResellerId(): int
    {
        return $this->resellerId;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }

    public function getExpertId(): int
    {
        return $this->expertId;
    }

    public function getNotificationType(): NotificationType
    {
        return $this->notificationType;
    }

    public function getComplaintId(): int
    {
        return $this->complaintId;
    }

    public function getComplaintNumber(): string
    {
        return $this->complaintNumber;
    }

    public function getConsumptionId(): int
    {
        return $this->consumptionId;
    }

    public function getConsumptionNumber(): string
    {
        return $this->consumptionNumber;
    }

    public function getAgreementNumber(): string
    {
        return $this->agreementNumber;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDifferences(): ?Differences
    {
        return $this->differences;
    }
}