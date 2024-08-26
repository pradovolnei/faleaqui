<?php
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\OrdersController;

    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('orders', [OrdersController::class, 'store']);
        Route::put('orders/{id}', [OrdersController::class, 'update']);
        Route::get('orders', [OrdersController::class, 'index']);
    });

?>
