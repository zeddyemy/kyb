<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class YouVerifyException extends Exception
{
    /**
     * @param  array<string, mixed>  $responseData
     */
    public function __construct(
        string $message,
        int $code = 0,
        public readonly array $responseData = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for network/connection failures.
     */
    public static function connectionFailed(Throwable $previous): self
    {
        return new self(
            message: 'Failed to connect to YouVerify API',
            code: 503,
            responseData: [],
            previous: $previous
        );
    }

    /**
     * Create exception for invalid API credentials.
     */
    public static function invalidCredentials(): self
    {
        return new self(
            message: 'Invalid YouVerify API credentials',
            code: 401,
            responseData: []
        );
    }

    /**
     * Create exception from API error response.
     *
     * @param  array<string, mixed>  $response
     */
    public static function fromResponse(array $response, int $statusCode): self
    {
        $message = $response['message'] ?? 'YouVerify API request failed';

        return new self(
            message: $message,
            code: $statusCode,
            responseData: $response
        );
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'error' => [
                'code' => $this->getCode(),
                'data' => $this->responseData,
            ],
        ], $this->getCode() >= 400 && $this->getCode() < 600 ? $this->getCode() : 500);
    }
}
