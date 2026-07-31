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

Route::get('/', [ExploreController::class, 'explore']);
// billplz controller
Route::get('billplz', [PaymentController::class, 'createBill'])->name('billplz-create');
Route::post('billplz', [PaymentController::class, 'storeBill'])->name('billplz-store');
Route::get('billplz-callback', [PaymentController::class, 'callback'])->name('billplz-callback');
Route::get('billplz-redirect', [PaymentController::class, 'redirect'])->name('billplz-redirect');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [Controller::class, 'redirectUser'])->name('dashboard');
    Route::get('mockup/standard-tee', [MockupController::class, 'redirectToStudio'])->defaults('type', 'shirt')->name('mockup.shirt');
    Route::get('mockup/sweatshirt', [MockupController::class, 'redirectToStudio'])->defaults('type', 'sweat')->name('mockup.sweat');
    Route::get('mockup/hoodie', [MockupController::class, 'redirectToStudio'])->defaults('type', 'hoodie')->name('mockup.hoodie');
    // upload controller 
    Route::post('upload', [UploadController::class, 'store'])->name('upload');
    Route::post('upload/cover-image', [UploadController::class, 'uploadCover'])->name('upload.cover');
    Route::post('upload/product-collection', [UploadController::class, 'uploadCollection'])->name('upload.collection');
    // order controller
    Route::get('order/index', ManageOrder::class)->name('order.index');
    Route::get('product/manage', ManageProduct::class)->name('product.manage');
    Route::get('sales', ManageSales::class)->name('product.sales');
    Route::get('product/create', [ProductsController::class, 'create'])->name('product.create');
    Route::post('product', [ProductsController::class, 'store'])->name('product.store');
    Route::get('product/{product}/edit', [ProductsController::class, 'edit'])->name('product.edit');
    Route::put('product/{product}', [ProductsController::class, 'update'])->name('product.update');
    Route::delete('product/{product}', [ProductsController::class, 'destroy'])->name('product.destroy');
});
Route::get('product/{product}', [ProductsController::class, 'show'])->name('product.show');
// cart controller
Route::resource('cart', CartController::class);
Route::get('checkout', DeliveryInformation::class)->name('guest.checkout');
// explore controller
Route::get('explore', [ExploreController::class, 'explore'])->name('explore');
Route::get('shop', [ExploreController::class, 'search'])->name('search');
Route::get('shop/all', [ExploreController::class, 'shop'])->name('shop.all');
Route::get('shop/standard-tee', [ExploreController::class, 'shirt'])->name('shop.shirt');
Route::get('shop/sweatshirt', [ExploreController::class, 'sweat'])->name('shop.sweat');
Route::get('shop/hoodie', [ExploreController::class, 'hoodie'])->name('shop.hoodie');
Route::get('collection', [ExploreController::class, 'collection'])->name('shop.collection');
Route::get('{shopname}', [ExploreController::class, 'people'])->name('people');
// google auth
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);
