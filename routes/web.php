<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\MockupController;
use App\Http\Controllers\UploadController;
use App\Http\Livewire\Manage\ManageProduct;
use App\Http\Livewire\Manage\ManageOrder;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\DeliveryInformation;
use App\Http\Livewire\Manage\ManageSales;

Route::redirect('/', 'explore');
// billplz controller
Route::get('billplz', [PaymentController::class, 'createBill'])->name('billplz-create');
Route::post('billplz', [PaymentController::class, 'storeBill'])->name('billplz-store');
Route::get('billplz-callback', [PaymentController::class, 'callback'])->name('billplz-callback');
Route::get('billplz-redirect', [PaymentController::class, 'redirect'])->name('billplz-redirect');
// artist middleware
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [Controller::class, 'redirectUser'])->name('dashboard');
    Route::get('mockup/standard-tee', [MockupController::class, 'shirt'])->name('mockup.shirt');
    Route::get('mockup/oversized', [MockupController::class, 'oversized'])->name('mockup.oversized');
    Route::get('custom/standard-tee', [MockupController::class, 'customShirt'])->name('custom.shirt');
    Route::get('custom/oversized', [MockupController::class, 'customOversized'])->name('custom.oversized');
    // upload controller 
    Route::post('upload', [UploadController::class, 'store'])->name('upload');
    Route::post('upload/cover-image', [UploadController::class, 'uploadCover'])->name('upload.cover');
    Route::post('upload/product-template', [UploadController::class, 'uploadTemplate'])->name('upload.template');
    Route::post('upload/product-collection', [UploadController::class, 'uploadCollection'])->name('upload.collection');
    Route::post('upload/custom', [UploadController::class, 'uploadCustom'])->name('upload.custom');
    // order controller
    Route::get('order/index', ManageOrder::class)->name('order.index');
    Route::get('product/manage', ManageProduct::class)->name('product.manage');
    Route::get('sales', ManageSales::class)->name('product.sales');
});
// route resource product
Route::resource('product', ProductsController::class);
// cart controller
Route::resource('cart', CartController::class);
Route::get('checkout', DeliveryInformation::class)->name('guest.checkout');
// explore controller
Route::get('sellyourart', [ExploreController::class, 'sellyourart'])->name('sellyourart');
Route::get('explore', [ExploreController::class, 'explore'])->name('explore');
Route::get('shop', [ExploreController::class, 'search'])->name('search');
Route::get('shop/all', [ExploreController::class, 'shop'])->name('shop.all');
Route::get('shop/standard-tee', [ExploreController::class, 'shirt'])->name('shop.shirt');
Route::get('shop/oversized-tee', [ExploreController::class, 'oversized'])->name('shop.oversized');
Route::get('collection', [ExploreController::class, 'collection'])->name('shop.collection');
Route::get('{shopname}', [ExploreController::class, 'people'])->name('people');
Route::post('sellyourart/{id}', [ExploreController::class, 'upgrade'])->name('upgrade');
// google auth
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);