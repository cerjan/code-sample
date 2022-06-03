<?php

namespace App\Http\Response;

use Nyholm\Psr7\Response;

class JsonResponse extends Response
{
    public function __construct(mixed $data = null, int $status = 200)
    {
        $headers = ['Content-Type' => 'application/json'];
        $body = json_encode($data);

        parent::__construct($status, $headers, $body);
    }
}