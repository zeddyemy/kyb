<?php

namespace App\Services\YouVerify\DTOs;

readonly class BusinessVerificationDTO
{
    public function __construct(
        public string $registrationNumber,
        public ?string $registrationName = null,
        public string $countryCode = 'NG',
        public bool $premium = false,
        public bool $isConsent = true
    ) {}

    /**
     * Convert DTO to array for API request.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'registrationNumber' => $this->registrationNumber,
            'countryCode' => $this->countryCode,
            'premium' => $this->premium,
            'isConsent' => $this->isConsent,
        ];

        if ($this->registrationName !== null) {
            $data['registrationName'] = $this->registrationName;
        }

        return $data;
    }

    /**
     * Create DTO from request data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            registrationNumber: $data['registration_number'],
            registrationName: $data['registration_name'] ?? null,
            countryCode: $data['country_code'] ?? 'NG',
            premium: $data['premium'] ?? false,
            isConsent: true
        );
    }
}
