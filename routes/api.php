<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandProductController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DivisionsController;
use App\Http\Controllers\ProductImagesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ShippingAreasController;
use App\Http\Controllers\SlidesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VendorsController;
use App\Http\Controllers\WishlistController;
use App\Models\CategoryProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/test', function () {
    $user = User::find(1);
    // $roles = $user->getRoleNames();
    return $user->role;
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::patch('users/toggle-active/{user}', [UsersController::class, 'toggleActive']);
    Route::apiResource('users', UsersController::class);
    Route::apiResource('vendors', VendorsController::class)->except(['store', 'update']);
    Route::apiResource('brands', BrandsController::class);
    Route::apiResource('categories', CategoriesController::class);
    Route::patch('products/delete-image/{product}', [ProductsController::class, 'deleteProductImage']);
    Route::patch('products/toggle-active/{product}', [ProductsController::class, 'toggleActive']);
    Route::apiResource('products', ProductsController::class);
    Route::apiResource('categories.products', CategoryProductController::class)->shallow()->only('index');
    Route::apiResource('brands.products', BrandProductController::class)->shallow()->only('index');
    Route::apiResource('product-images', ProductImagesController::class)->only('destroy');
    Route::apiResource('slides', SlidesController::class); 
    Route::post('cart/apply-coupon', [CartController::class, 'applyCoupon']);
    Route::post('cart/remove-coupon', [CartController::class, 'removeCoupon']);
    Route::apiResource('cart', CartController::class); 
    Route::apiResource('wishlist', WishlistController::class); 
    Route::apiResource('coupons', CouponController::class); 
    Route::apiResource('divisions', DivisionsController::class)->except('destroy');
    Route::apiResource('districts', DistrictController::class)->except('destroy');
    Route::apiResource('shipping-areas', ShippingAreasController::class)->except('destroy');
});
