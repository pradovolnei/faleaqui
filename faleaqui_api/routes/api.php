<?php 
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\OrderController;
    
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('orders', [OrderController::class, 'store']);
        Route::put('orders/{id}', [OrderController::class, 'update']);
    });
    
?>