<?php

namespace Vestra\Ghasedak\Http;

use Illuminate\Http\Client\Response;

final class GhasedakResponse
{
    public function __construct(
        public readonly Response $response,
        public readonly array $payload,
    ) {}

    public function successful(): bool
    {
        return ($this->payload['result'] ?? null) === 'success';
    }

    public function result(): ?string
    {
        return $this->payload['result'] ?? null;
    }

    public function messageIds(): array
    {
        $value = $this->payload['messageids'] ?? $this->payload['messageids'] ?? null;

        if ($value === null) {
            return [];
        }

        return is_array($value) ? array_values($value) : [$value];
    }

    public function firstMessageId(): string|int|null
    {
        return $this->messageIds()[0] ?? null;
    }

    public function message(): ?string
    {
        return isset($this->payload['message']) ? (string) $this->payload['message'] : null;
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function json(): array
    {
        return $this->payload;
    }
}
