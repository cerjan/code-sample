<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestBodyMiddleware
{
    public function __construct(
        private ValidatorInterface $validator,
        private Serializer $serializer,
    ) {}

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $router = RouteContext::fromRequest($request);

        $reflect = new ReflectionClass($router->getRoute()->getCallable());
        $attributes = array_filter($reflect->getMethod('__invoke')->getAttributes(), fn($attribute) => substr($attribute->getName(), -10) === 'RequestDto');

        if (count($attributes)) {
            if (isset($attributes[0]->getArguments()['dto']) && class_exists($attributes[0]->getArguments()['dto'])) {
                $dto = $this->serializer->deserialize(file_get_contents('php://input'), $attributes[0]->getArguments()['dto'], 'json');
                $violations = $this->validator->validate($dto);
                $violationsErr = [];

                /** @var ConstraintViolation $violation */
                foreach ($violations as $violation) {
                    $violationsErr[$violation->getPropertyPath()][] = $violation->getMessage();
                }

                if (count($violationsErr)) {
                    throw new HttpBadRequestException($request, json_encode($violationsErr));
                }

                $request = $request->withParsedBody($dto);
            } else {
                throw new HttpBadRequestException($request, sprintf('DTO object `%s` not found.', $attributes[0]->getArguments()['dto'] ?? 'undefined'));
            }
        }

        return $handler->handle($request);
    }
}