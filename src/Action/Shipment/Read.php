<?php

declare(strict_types=1);

namespace App\Action\Shipment;

use App\Http\Response\JsonResponse;
use App\Repository\ShipmentRepository;
use Exception;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpBadRequestException;

final class Read
{
    public function __invoke(ServerRequest $request, ShipmentRepository $repository): Response
    {
        $processor = new Processor();
        $query = $processor->process(Expect::structure([
            'filters' => Expect::arrayOf(Expect::arrayOf('string', Expect::anyOf('eq'))),
        ]), $request->getQueryParams());

        try {
            return new JsonResponse($repository->findByFilters($query->filters));
        } catch (Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        }
    }
}