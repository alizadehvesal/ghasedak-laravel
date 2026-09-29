<?php

namespace Vestra\Ghasedak\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Vestra\Ghasedak\Exceptions\GhasedakException;
use Vestra\Ghasedak\Http\GhasedakResponse;

class GhasedakSms
{
    public function __construct(
        protected readonly ?string $apiKey,
        protected readonly string $baseUrl,
        protected readonly int $timeout,
        protected readonly int $connectTimeout,
        protected readonly array $templates = [],
    ) {
        if (blank($this->apiKey)) {
            throw new GhasedakException('Ghasedak API key is not configured.');
        }
    }

    public function sendTemplate(
        string $receptor,
        string $template,
        array $params = [],
        ?string $checkingIds = null,
        int $type = 1,
    ): GhasedakResponse {
        $receptor = trim($receptor);
        $template = trim($template);

        if ($receptor === '') {
            throw new InvalidArgumentException('Ghasedak receptor cannot be empty.');
        }

        if ($template === '') {
            throw new InvalidArgumentException('Ghasedak template cannot be empty.');
        }

        if ($type !== 1 && $type !== 2) {
            throw new InvalidArgumentException('Ghasedak type must be 1 (SMS) or 2 (voice).');
        }

        if ($params === []) {
            throw new InvalidArgumentException('At least param1 is required by Ghasedak.');
        }

        if (count($params) > 3) {
            throw new InvalidArgumentException('Ghasedak supports at most 3 template parameters.');
        }

        $body = [
            'type' => $type,
            'receptor' => $receptor,
            'template' => $template,
        ];

        foreach (array_values($params) as $index => $value) {
            $body['param'.($index + 1)] = (string) $value;
        }

        if ($checkingIds !== null && $checkingIds !== '') {
            $body['checkingids'] = $checkingIds;
        }

        $response = $this->request()->post('/send/verify', $body);
        $payload = $response->json();

        if (!is_array($payload)) {
            throw new GhasedakException(
                'Ghasedak returned an invalid response.',
                response: $response,
            );
        }

        if ($response->failed()) {
            throw $this->exceptionFromResponse($response, $payload);
        }

        if (($payload['result'] ?? null) !== 'success') {
            throw $this->exceptionFromResponse($response, $payload);
        }

        return new GhasedakResponse($response, $payload);
    }

    public function sendUsingTemplate(
        string $receptor,
        string $templateKey,
        array $params = [],
        ?string $checkingIds = null,
        int $type = 1,
    ): GhasedakResponse {
        $template = $this->template($templateKey);

        return $this->sendTemplate($receptor, $template, $params, $checkingIds, $type);
    }

    public function template(string $key): string
    {
        $template = $this->templates[$key] ?? null;

        if (!is_string($template) || trim($template) === '') {
            throw new InvalidArgumentException("Ghasedak template [{$key}] is not configured.");
        }

        return trim($template);
    }

    public function templates(): array
    {
        return $this->templates;
    }

    protected function request(): PendingRequest
    {
        return Http::withHeaders([
            'apikey' => $this->apiKey,
            'Accept' => 'application/json',
        ])
            ->baseUrl(rtrim($this->baseUrl, '/'))
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout)
            ->asForm();
    }

    protected function exceptionFromResponse($response, array $payload): GhasedakException
    {
        $message = $payload['message'] ?? 'Ghasedak request failed.';
        $code = is_numeric($payload['code'] ?? null) ? (int) $payload['code'] : null;

        return new GhasedakException(
            (string) $message,
            $code,
            $response,
            $payload,
        );
    }
}
