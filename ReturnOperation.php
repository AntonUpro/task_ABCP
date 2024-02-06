<?php

namespace NW\WebService\References\Operations\Notification;

use NW\WebService\References\Operations\Notification\Entity\Client;
use NW\WebService\References\Operations\Notification\Entity\Employee;
use NW\WebService\References\Operations\Notification\Entity\NotificationType;
use NW\WebService\References\Operations\Notification\Entity\Seller;
use NW\WebService\References\Operations\Notification\Entity\Status;
use NW\WebService\References\Operations\Notification\Exceptions\NotFoundException;
use NW\WebService\References\Operations\Notification\Exceptions\ValidateException;
use Throwable;

class ReturnOperation extends ReferencesOperation
{
    /**
     * @return array{
     *     'notificationEmployeeByEmail': bool,
     *     'notificationClientByEmail': bool,
     *     'notificationClientBySms': array{
     *         'isSent': bool,
     *         'message': string
     *     }
     * }
     * @throws NotFoundException
     * @throws ValidateException
     */
    public function doOperation(): array
    {
        $data = $this->getRequest('data');

        $this->validateData($data);

        $resellerId = (int)$data['resellerId'];
        $clientId = (int)$data['clientId'];
        $creatorId = (int)$data['creatorId'];
        $expertId = (int)$data['expertId'];
        $notificationType = (int)$data['notificationType'];
        $differencesTo = (int)$data['differences']['to'] ?? null;
        $differencesFrom = (int)$data['differences']['from'] ?? null;

        $result = [
            'notificationEmployeeByEmail' => false,
            'notificationClientByEmail' => false,
            'notificationClientBySms' => [
                'isSent' => false,
                'message' => '',
            ],
        ];

        $reseller = Seller::getById($resellerId);
        if ($reseller === null) {
            throw new NotFoundException('Seller not found!');
        }

        $client = Client::getById($clientId);
        if ($client === null) {
            throw new NotFoundException('Client not found!');
        }

        if (!$client->isCustomer()) {
            throw new NotFoundException('Client must be customer!');
        }

        if ($client->getSeller()->getId() !== $resellerId) {
            throw new NotFoundException("Reseller does not match the customer's seller!");
        }

        // тут была проверка на наличие полного имени и его переприсваивание не имеющее смысла

        $creator = Employee::getById($creatorId);
        if ($creator === null) {
            throw new NotFoundException('Creator not found!');
        }

        $expert = Employee::getById($expertId);
        if ($expert === null) {
            throw new NotFoundException('Expert not found!');
        }

        switch ($notificationType) {
            case NotificationType::TYPE_NEW:
                $differences = __('NewPositionAdded', null, $resellerId); // Не знакомая конструкция начинающаяся с __
                break;
            case NotificationType::TYPE_CHANGE:
                $differences = __('PositionStatusHasChanged', [
                    'FROM' => Status::getName($differencesFrom),
                    'TO' => Status::getName($differencesTo),
                ], $resellerId);
                break;
            default:
                throw new ValidateException('Unknown notificationType (' . $notificationType . ')');
        }

        $templateData = [
            'COMPLAINT_ID' => (int)$data['complaintId'] ?? null,
            'COMPLAINT_NUMBER' => (string)$data['complaintNumber'] ?? null,
            'CREATOR_ID' => $creatorId,
            'CREATOR_NAME' => $creator->getFullName(),
            'EXPERT_ID' => $expertId,
            'EXPERT_NAME' => $expert->getFullName(),
            'CLIENT_ID' => $clientId,
            'CLIENT_NAME' => $client->getFullName(),
            'CONSUMPTION_ID' => (int)$data['consumptionId'] ?? null,
            'CONSUMPTION_NUMBER' => (string)$data['consumptionNumber'] ?? null,
            'AGREEMENT_NUMBER' => (string)$data['agreementNumber'] ?? null,
            'DATE' => (string)$data['date'] ?? null,
            'DIFFERENCES' => $differences,
        ];

        // Если хоть одна переменная для шаблона не задана, то не отправляем уведомления
        foreach ($templateData as $key => $tempData) {
            if (empty($tempData)) {
                throw new ValidateException("Template Data ({$key}) is empty!");
            }
        }

        $emailFrom = $reseller->getResellerEmailFrom();
        // Получаем email сотрудников из настроек
        $emails = $this->getEmailsByPermit($resellerId, 'tsGoodsReturn'); // функционал этого метода можно вынести в отдельный сервис (класс)
        if (!empty($emailFrom) && !empty($emails)) {
            $messages = [];
            foreach ($emails as $email) {
                $messages[] = [
                    'emailFrom' => $emailFrom,
                    'emailTo' => $email,
                    'subject' => __('complaintEmployeeEmailSubject', $templateData, $resellerId),
                    'message' => __('complaintEmployeeEmailBody', $templateData, $resellerId),
                ];
            }

            try {
                MessagesClient::sendMessage(
                    $messages,   // Так как первый аргумент принимает на вход массив с массивами, предполагаю, что он может отправить сразу несколько сообщений
                    $resellerId,
                    $client->getId(),
                    $notificationType === NotificationType::TYPE_NEW ? NotificationEvents::NEW_RETURN_STATUS : NotificationEvents::CHANGE_RETURN_STATUS
                );
                $result['notificationEmployeeByEmail'] = true;
            } catch (Throwable $exception) {
                // Записать ошибку в логи. В зависимости от условий задачи, отдать 400 ответ, либо валидный ответ без смены флага notificationEmployeeByEmail
                // Так же из метода sendMessage можно получать разные exception и обрабатывать их по-разному.
            }
        }

        if ($notificationType !== NotificationType::TYPE_CHANGE) {
            return $result;
        }
        // Шлём клиентское уведомление, только если произошла смена статуса
        if (!empty($emailFrom) && !empty($client->getEmail())) {
            try {
                MessagesClient::sendMessage(
                    [
                        [
                            'emailFrom' => $emailFrom,
                            'emailTo' => $client->getEmail(),
                            'subject' => __('complaintClientEmailSubject', $templateData, $resellerId),
                            'message' => __('complaintClientEmailBody', $templateData, $resellerId),
                        ],
                    ],
                    $resellerId,
                    $client->getId(),
                    NotificationEvents::CHANGE_RETURN_STATUS,
                    $differencesTo
                );
                $result['notificationClientByEmail'] = true;
            } catch (Throwable $exception) {
                // Записать ошибку в логи. В зависимости от условий задачи, отдать 400 ответ, либо валидный ответ без смены флага notificationClientByEmail
                // Так же из метода sendMessage можно получать разные exception и обрабатывать их по-разному.
            }
        }

        if (!empty($client->getMobile())) {
            $error = '';
            $isSuccessSend = NotificationManager::send(  // В этот метод наверняка должен передаться номер телефона, но он не передается
                $resellerId,
                $client->getId(),
                NotificationEvents::CHANGE_RETURN_STATUS,
                $differencesTo,
                $templateData,
                $error // предполагаю, что этот параметр передается по ссылке и метод предусматривает обработку ошибок внутри, поэтому не помещаю в try - catch
            );

            if ($isSuccessSend) {
                $result['notificationClientBySms']['isSent'] = true;
            }

            if (!empty($error)) {
                $result['notificationClientBySms']['message'] = $error;
            }
        }

        return $result;
    }


    /**
     * @param array $data
     * @throws ValidateException
     */
    private function validateData(array $data): void
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

        if ($data['notificationType'] !== NotificationType::TYPE_CHANGE) {
            return;
        }

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

    public function getEmailsByPermit(int $resellerId, string $event): array
    {
        // fakes the method
        return ['someemeil@example.com', 'someemeil2@example.com'];
    }
}
