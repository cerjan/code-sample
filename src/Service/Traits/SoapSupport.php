<?php

declare(strict_types=1);

namespace App\Service\Traits;

use Exception;
use Slim\Exception\HttpInternalServerErrorException;
use stdClass;

trait SoapSupport
{
    private function request(string $name, array $parameters): stdClass
    {
        try {
            return $this->soapClient->__soapCall($name, ['parameters' => $parameters]);
        } catch (Exception $e) {
            $message = sprintf(__CLASS__ . ' - __soapCall `%s` - %s', $name, $e->getMessage());
            $this->logger->critical($message, ['request' => $parameters, 'fault' => $e->detail ?? null]);
            throw new HttpInternalServerErrorException($this->request, $message);
        }
    }
}