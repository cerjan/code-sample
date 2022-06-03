<?php

declare(strict_types=1);

namespace App\Action\Carrier;

use App\Facade\CarrierFacade;
use App\Http\Response\JsonResponse;

final class IsHealthy
{
    public function __invoke(CarrierFacade $carrierFacade, ?string $carrierCode = null)
    {
        $healthDto = $carrierFacade->isHealthy($carrierCode);

        return new JsonResponse($healthDto, $healthDto->status->code());
    }
}