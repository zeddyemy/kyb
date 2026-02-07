<?php

use App\Exceptions\YouVerifyException;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config([
        'youverify.base_url' => 'https://api.youverify.co',
        'youverify.secret_key' => 'test-secret-key',
        'youverify.timeout' => 30,
    ]);
});

describe('Business Verification', function (): void {
    it('successfully verifies a business', function (): void {
        Http::fake([
            'api.youverify.co/*' => Http::response([
                'success' => true,
                'statusCode' => 200,
                'message' => 'success',
                'data' => [
                    'name' => 'Test Company Ltd',
                    'registrationNumber' => 'RC1234567',
                    'status' => 'found',
                    'companyStatus' => 'ACTIVE',
                    'countryCode' => 'NG',
                    'typeOfEntity' => 'PRIVATE COMPANY LIMITED BY SHARES',
                    'address' => '123 Test Street, Lagos',
                    'keyPersonnel' => [],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/youverify/business-verification', [
            'registration_number' => 'RC1234567',
            'country_code' => 'NG',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Test Company Ltd',
                    'registrationNumber' => 'RC1234567',
                    'status' => 'found',
                ],
            ]);

        Http::assertSent(function ($request) {
            return $request->hasHeader('token', 'test-secret-key')
                && $request['registrationNumber'] === 'RC1234567'
                && $request['countryCode'] === 'NG'
                && $request['isConsent'] === true;
        });
    });

    it('returns validation error when registration number is missing', function (): void {
        $response = $this->postJson('/api/youverify/business-verification', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonPath('errors.registration_number.0', 'The business registration number is required.');
    });

    it('handles YouVerify API errors gracefully', function (): void {
        Http::fake([
            'api.youverify.co/*' => Http::response([
                'success' => false,
                'statusCode' => 404,
                'message' => 'You have attempted to get a resource that does not exist.',
                'name' => 'ResourceNotFoundError',
                'data' => [],
            ], 404),
        ]);

        $response = $this->postJson('/api/youverify/business-verification', [
            'registration_number' => 'RC0000000',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'You have attempted to get a resource that does not exist.',
            ]);
    });

    it('handles invalid credentials', function (): void {
        Http::fake([
            'api.youverify.co/*' => Http::response([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401),
        ]);

        $response = $this->postJson('/api/youverify/business-verification', [
            'registration_number' => 'RC1234567',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid YouVerify API credentials',
            ]);
    });

    it('handles connection failures', function (): void {
        Http::fake([
            'api.youverify.co/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'),
        ]);

        $response = $this->postJson('/api/youverify/business-verification', [
            'registration_number' => 'RC1234567',
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
                'message' => 'Failed to connect to YouVerify API',
            ]);
    });
});
