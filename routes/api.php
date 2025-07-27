<?php

use Illuminate\Support\Facades\Route;
use App\Interfaces\Http\Controllers\User\CreateUserController;
use App\Interfaces\Http\Controllers\Auth\AuthController;
use App\Interfaces\Http\Controllers\DeputyController;
use App\Interfaces\Http\Controllers\ExpenseController;

    Route::post('/user', [CreateUserController::class, 'store']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('deputies',           [DeputyController::class, 'list']);
    Route::post('deputies/fetch',    [DeputyController::class, 'fetchAndStore']);
    Route::get('deputies/{id}/expenses', [DeputyController::class, 'expenses']);
    Route::get('getWithExpenses', [DeputyController::class, 'getWithExpenses']);
    Route::get('totalDeputies', [DeputyController::class, 'sumDeputy']);
    Route::get('sumStateAbbr', [DeputyController::class, 'sumStateAbbr']);
    Route::get('countDeputiesByParty', [DeputyController::class, 'countDeputiesByParty']);

    Route::prefix('expenses')->group(function () {
        Route::get('/',                [ExpenseController::class, 'index']);
        Route::get('{id}',             [ExpenseController::class, 'show']);
        Route::delete('{id}',          [ExpenseController::class, 'destroy']);
        Route::post('delete-older-than',[ExpenseController::class, 'deleteOlderThan']);
    });

    Route::get('sumNetValueTotal', [ExpenseController::class, 'sumNetValueTotal']);
    Route::get('sumExpenseType', [ExpenseController::class, 'sumExpenseType']);
