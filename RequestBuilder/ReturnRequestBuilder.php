<?php

namespace NW\WebService\References\Operations\Notification\RequestBuilder;

use NW\WebService\References\Operations\Notification\Entity\NotificationType;
use NW\WebService\References\Operations\Notification\Entity\Status;
use NW\WebService\References\Operations\Notification\Exceptions\ValidateException;
use NW\WebService\References\Operations\Notification\Request\ReturnRequest;
use NW\WebService\References\Operations\Notification\Request\VO\Differences;

class ReturnRequestBuilder
{
    /**
     * @param array $data
     * @return ReturnRequest
     * @throws ValidateException
     */
    public function build(array $data): ReturnRequest
    {
        if (empty($data['resellerId'])) {
            throw new ValidateException('Empty resellerId');
        }

        if (empty($data['notificationType'])) {
            throw new ValidateException('Empty notificationType');
        }

        if (empty($data['clientId'])) {
            throw new ValidateException('Empty clientId');
        }

        if (empty($data['creatorId'])) {
            throw new ValidateException('Empty creatorId');
        }

        if (empty($data['expertId'])) {
            throw new ValidateException('Empty expertId');
        }

        if (empty($data['complaintId'])) {
            throw new ValidateException('Empty complaintId');
        }

        if (empty($data['complaintNumber'])) {
            throw new ValidateException('Empty complaintNumber');
        }

        if (empty($data['consumptionId'])) {
            throw new ValidateException('Empty consumptionId');
        }

        if (empty($data['consumptionNumber'])) {
            throw new ValidateException('Empty consumptionNumber');
        }

        if (empty($data['agreementNumber'])) {
            throw new ValidateException('Empty agreementNumber');
        }

        if (empty($data['date'])) {
            throw new ValidateException('Empty date');
        }

        if ($data['notificationType'] === NotificationType::TYPE_CHANGE) {
            if (empty($data['differences'])) {
                throw new ValidateException('Empty differences');
            }

            if (empty($data['differences']['from'])) {
                throw new ValidateException('Empty differences from');
            }

            if (empty($data['differences']['to'])) {
                throw new ValidateException('Empty differences to');
            }
        }

        return new ReturnRequest(
            (int)$data['resellerId'],
            (int)$data['clientId'],
            (int)$data['creatorId'],
            (int)$data['expertId'],
            new NotificationType((int)$data['notificationType']),
            (int)$data['complaintId'],
            (string)$data['complaintNumber'],
            (int)$data['consumptionId'],
            (string)$data['consumptionNumber'],
            (string)$data['agreementNumber'],
            (string)$data['date'],
            isset($data['differences'])
                ? new Differences(
                    new Status((int)$data['differences']['from']),
                    new Status((int)$data['differences']['to'])
                )
                : null,
        );
    }
}