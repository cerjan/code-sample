<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Carrier\ICarrier;
use Psr\Container\ContainerInterface;

class CarrierService
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    public function isHealthy(string $carrierCode): ?bool
    {
        /** @var ICarrier $carrier */
        $carrier = $this->container->get('App\\Services\\Carrier\\' . $carrierCode . 'Carrier');

        return $carrier->isHealthy();
    }
}