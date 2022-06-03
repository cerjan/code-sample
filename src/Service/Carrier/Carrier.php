<?php

declare(strict_types=1);

namespace App\Service\Carrier;

use App\Http\Request\Dto\PureAddressRequestDto;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Nyholm\Psr7\ServerRequest;

abstract class Carrier implements ICarrier
{
    /** @var EntityManager @Inject */
    protected EntityManager $em;

    /** @var ServerRequest @Inject */
    protected ServerRequest $request;

    /** @var Logger @Inject */
    protected Logger $logger;

    public function possibleDelivery(PureAddressRequestDto $address): ?bool
    {
        return null;
    }

    public function isHealthy(): ?bool
    {
        return null;
    }
}