<?php

declare(strict_types=1);

namespace App\Action\Address;

use App\Action\RequestDto;
use App\Http\Request\Dto\PureAddressRequestDto;
use App\Http\Response\JsonResponse;
use App\Service\AddressService;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

final class Validate
{
    #[RequestDto(dto: PureAddressRequestDto::class)]
    public function __invoke(ServerRequest $request, AddressService $service): Response
    {
        return new JsonResponse($service->validate($request->getParsedBody()));
    }
}