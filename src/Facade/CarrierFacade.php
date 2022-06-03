<?php

declare(strict_types=1);

namespace App\Facade;

use App\Entity\Carrier;
use App\Http\Response\Dto\HealthDto;
use App\Http\Response\Dto\HealthEntryDto;
use App\Http\Response\HealthStatus;
use App\Service\CarrierService;
use Doctrine\ORM\EntityManager;

class CarrierFacade
{
    public function __construct(
        private CarrierService $carrierService,
        private EntityManager $em,
    ) {}

    public function isHealthy(?string $carrierCode = null): HealthDto
    {
        if ($carrierCode) {
            $startTime = microtime(true);
            $isHealthy = $this->carrierService->isHealthy($carrierCode);
            $duration = microtime(true) - $startTime;

            return new HealthDto(self::getHealthStatus($isHealthy), $duration, null);
        } else {
            $totalStartTime = microtime(true);

            $entries = [];
            foreach ($this->em->getRepository(Carrier::class)->createQueryBuilder('c')->select('c.code')->getQuery()->getSingleColumnResult() as $carrierCode) {
                $startTime = microtime(true);
                $isHealthy = $this->carrierService->isHealthy($carrierCode);
                $duration = microtime(true) - $startTime;

                $entries[$carrierCode] = new HealthEntryDto($duration, self::getHealthStatus($isHealthy), [$carrierCode]);
            }

            $totalDuration = microtime(true) - $totalStartTime;

            return new HealthDto(HealthStatus::HEALTHY, $totalDuration, $entries);
        }
    }

    private static function getHealthStatus(?bool $isHealthy): HealthStatus
    {
        return match ($isHealthy) {
            true => HealthStatus::HEALTHY,
            false => HealthStatus::UNAVAILABLE,
            null => HealthStatus::UNKNOWN,
        };
    }
}