<?php

declare(strict_types=1);

namespace App\Action\Address;

use App\Action\RequestDto;
use App\Facade\AddressFacade;
use App\Http\Request\Dto\PureAddressRequestDto;
use App\Http\Response\JsonResponse;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

final class PossibleDelivery
{
    #[RequestDto(dto: PureAddressRequestDto::class)]
    public function __invoke(ServerRequest $request, AddressFacade $addressFacade, ?string $carrierCode = null): Response
    {
        return new JsonResponse($addressFacade->possibleDelivery($request->getParsedBody(), $carrierCode));
    }
}