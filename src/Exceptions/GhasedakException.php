<?php

namespace Vestra\Ghasedak\Exceptions;

use RuntimeException;
use Illuminate\Http\Client\Response;

class GhasedakException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $ghasedakCode = null,
        public readonly ?Response $response = null,
        public readonly ?array $payload = null,
    ) {
        parent::__construct($message, $ghasedakCode ?? 0);
    }

    public function isApiError(): bool
    {
        return $this->ghasedakCode !== null;
    }
}
