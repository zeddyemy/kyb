<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyBusinessRequest;
use App\Services\YouVerify\DTOs\BusinessVerificationDTO;
use App\Services\YouVerify\YouVerifyClient;
use Illuminate\Http\JsonResponse;

class YouVerifyController extends Controller
{
    public function __construct(
        private readonly YouVerifyClient $youVerifyClient
    ) {}

    /**
     * Verify a business using YouVerify KYB service.
     */
    public function verifyBusiness(VerifyBusinessRequest $request): JsonResponse
    {
        $dto = BusinessVerificationDTO::fromRequest($request->validated());

        $result = $this->youVerifyClient->verifyBusiness($dto);

        return response()->json($result);
    }
}
