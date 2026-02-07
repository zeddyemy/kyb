<?php

namespace App\Services\YouVerify;

use App\Exceptions\YouVerifyException;
use App\Services\YouVerify\DTOs\BusinessVerificationDTO;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class YouVerifyClient
{
    private string $baseUrl;

    private string $secretKey;

    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('youverify.base_url'), '/');
        $this->secretKey = config('youverify.secret_key');
        $this->timeout = config('youverify.timeout', 30);
    }

    /**
     * Verify a business using the advanced company check endpoint.
     *
     * @return array<string, mixed>
     *
     * @throws YouVerifyException
     */
    public function verifyBusiness(BusinessVerificationDTO $dto): array
    {
        $endpoint = '/v2/api/verifications/global/company-advance-check';

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->post($this->baseUrl.$endpoint, $dto->toArray());

            return $this->handleResponse($response);
        } catch (ConnectionException $e) {
            throw YouVerifyException::connectionFailed($e);
        }
    }

    /**
     * Get request headers including authentication.
     *
     * @return array<string, string>
     */
    private function getHeaders(): array
    {
        return [
            'token' => $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Handle API response and normalize errors.
     *
     * @return array<string, mixed>
     *
     * @throws YouVerifyException
     */
    private function handleResponse(Response $response): array
    {
        $data = $response->json() ?? [];

        if ($response->status() === 401) {
            throw YouVerifyException::invalidCredentials();
        }

        if ($response->failed()) {
            throw YouVerifyException::fromResponse($data, $response->status());
        }

        return $data;
    }
}
