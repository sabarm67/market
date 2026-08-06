<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ScreenerController;
use App\Http\Controllers\ShariahController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{stockCode}', [CompanyController::class, 'show']);
    Route::get('/companies/{stockCode}/fundamentals', [CompanyController::class, 'fundamentals']);
    Route::get('/companies/{stockCode}/technicals', [CompanyController::class, 'technicals']);

    Route::get('/shariah/status/{stockCode}', [ShariahController::class, 'status']);
    Route::get('/shariah/securities', [ShariahController::class, 'securities']);

    Route::get('/screener', [ScreenerController::class, 'index']);
    Route::get('/sectors', [ScreenerController::class, 'sectors']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/user', [AuthController::class, 'user']);

        Route::get('/watchlists', [WatchlistController::class, 'index']);
        Route::post('/watchlists', [WatchlistController::class, 'store']);
        Route::delete('/watchlists/{watchlist}', [WatchlistController::class, 'destroy']);
        Route::post('/watchlists/{watchlist}/items', [WatchlistController::class, 'addItem']);
        Route::delete('/watchlists/{watchlist}/items/{item}', [WatchlistController::class, 'removeItem']);

        Route::get('/alerts/rules', [AlertController::class, 'index']);
        Route::post('/alerts/rules', [AlertController::class, 'store']);
        Route::patch('/alerts/rules/{rule}', [AlertController::class, 'update']);
        Route::delete('/alerts/rules/{rule}', [AlertController::class, 'destroy']);
        Route::get('/alerts/triggers', [AlertController::class, 'triggers']);
        Route::post('/alerts/triggers/{trigger}/read', [AlertController::class, 'markTriggerRead']);

        Route::get('/portfolios', [PortfolioController::class, 'index']);
        Route::post('/portfolios', [PortfolioController::class, 'store']);
        Route::get('/portfolios/{portfolio}', [PortfolioController::class, 'show']);
        Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy']);
        Route::post('/portfolios/{portfolio}/transactions', [PortfolioController::class, 'addTransaction']);
        Route::delete('/portfolios/{portfolio}/transactions/{transaction}', [PortfolioController::class, 'removeTransaction']);

        Route::middleware('admin')->group(function () {
            Route::post('/shariah/import', [ShariahController::class, 'import']);
            Route::post('/shariah/import/{importId}/commit', [ShariahController::class, 'commitImport']);
        });
    });
});
