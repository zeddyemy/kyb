<?php

use App\Http\Controllers\YouVerifyController;
use Illuminate\Support\Facades\Route;

Route::prefix('youverify')->group(function (): void {
    Route::post('/business-verification', [YouVerifyController::class, 'verifyBusiness']);
});
