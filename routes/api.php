<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Routes for future REST API extension.
| All routes are prefixed with /api and use the api middleware group.
*/

Route::prefix('v1')->group(function () {
    // Public form endpoints
    Route::get('/forms/external', function () {
        return \App\Models\ExternalForm::where('is_public', true)
            ->where('status', 'active')
            ->paginate(15);
    });

    Route::get('/forms/internal', function () {
        return \App\Models\InternalForm::where('is_public', true)
            ->where('status', 'active')
            ->paginate(15);
    });

    // Protected API routes (for future Sanctum auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (\Illuminate\Http\Request $request) {
            return $request->user();
        });
    });
});
