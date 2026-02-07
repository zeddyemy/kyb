# Laravel YouVerify Integration Demo

A clean, senior-level Laravel integration with [YouVerify](https://youverify.co) KYB (Know Your Business) verification service.

## Features

- Centralized YouVerify configuration
- Clean service-layer architecture with DTOs
- Business verification endpoint (KYB)
- Custom exception handling with consistent JSON responses
- Comprehensive test coverage

## Requirements

- PHP 8.2+
- Laravel 12
- Composer

## Setup

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

Copy the environment file:

```bash
cp .env.example .env
```

Add your YouVerify credentials to `.env`:

```env
YOUVERIFY_BASE_URL=https://api.youverify.co
YOUVERIFY_API_KEY=your-api-key
YOUVERIFY_SECRET_KEY=your-secret-key
YOUVERIFY_TIMEOUT=30
YOUVERIFY_ENVIRONMENT=sandbox
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Run the Application

```bash
php artisan serve
```

## API Endpoint

### Business Verification

Verify a business registration with YouVerify's KYB service.

**Endpoint**: `POST /api/youverify/business-verification`

**Request Body**:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `registration_number` | string | Yes | Business registration number (e.g., RC1234567 for Nigeria) |
| `registration_name` | string | No | Business name (helps refine search for older companies) |
| `country_code` | string | No | 2-letter country code (default: NG) |
| `premium` | boolean | No | Enable premium checks (NG, ZA, KE only) |

**Example Request**:

```bash
curl -X POST http://localhost:8000/api/youverify/business-verification \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "registration_number": "RC1234567",
    "country_code": "NG"
  }'
```

**Success Response** (200):

```json
{
  "success": true,
  "statusCode": 200,
  "message": "success",
  "data": {
    "name": "Example Company Ltd",
    "registrationNumber": "RC1234567",
    "status": "found",
    "companyStatus": "ACTIVE",
    "typeOfEntity": "PRIVATE COMPANY LIMITED BY SHARES",
    "address": "123 Example Street, Lagos",
    "keyPersonnel": [...]
  }
}
```

**Validation Error** (422):

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "registration_number": ["The business registration number is required."]
  }
}
```

**API Error** (4xx/5xx):

```json
{
  "success": false,
  "message": "Error description from YouVerify",
  "error": {
    "code": 404,
    "data": {}
  }
}
```

## Registration Number Format (Nigeria)

For Nigerian businesses, prefix the registration number:

| Prefix | Entity Type |
|--------|-------------|
| `RC` | Private company limited by shares |
| `BN` | Business name |
| `IT` | Incorporated trustees |
| `LP` | Limited partnership |
| `LLP` | Limited liability partnership |

**Example**: `RC1234567` (no spaces)

## Testing

Run the test suite:

```bash
php artisan test --filter=YouVerify
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── YouVerifyController.php
│   └── Requests/
│       └── VerifyBusinessRequest.php
├── Services/
│   └── YouVerify/
│       ├── YouVerifyClient.php
│       └── DTOs/
│           └── BusinessVerificationDTO.php
└── Exceptions/
    └── YouVerifyException.php

config/
└── youverify.php

routes/
└── api.php

tests/Feature/
└── YouVerifyTest.php
```

## Assumptions & Limitations

1. **API-only**: No Blade views or frontend
2. **Authentication**: No user authentication on the endpoint (add middleware as needed)
3. **Single endpoint**: Only business verification implemented; extend for other KYB/KYC services
4. **Consent**: `isConsent` is always `true` per YouVerify requirements
5. **Rate Limiting**: Not implemented; add Laravel's rate limiting middleware for production

## License

This project is open-sourced software.
