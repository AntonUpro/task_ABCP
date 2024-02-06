<?php

namespace NW\WebService\References\Operations\Notification;

use Exception;

abstract class ReferencesOperation
{
    abstract public function doOperation(): array;

    /**
     * @return mixed
     */
    public function getRequest(string $paramName)  // ключи для параметров приходят в строке
    {
        if (!isset($_REQUEST[$paramName])) {
            throw new Exception('Not found param with param name (' . $paramName . ')');
        }

        return $_REQUEST[$paramName];
    }
}