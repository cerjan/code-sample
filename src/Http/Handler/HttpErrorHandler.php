<?php

declare(strict_types=1);

namespace App\Http\Handler;

use Slim\Handlers\ErrorHandler;

class HttpErrorHandler extends ErrorHandler
{
    protected function logError(string $error): void
    {
        if ($this->logErrorDetails) {
            $renderer = $this->determineRenderer();
            $body = call_user_func($renderer, $this->exception, $this->logErrorDetails);
            $this->logger->error($this->exception->getMessage(), (array)json_decode($body));
        } else {
            $this->logger->error($this->exception->getMessage());
        }
    }
}